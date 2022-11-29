<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordMail;
use App\Mail\ResetPasswordSuccessMail;
use App\Models\Customer;
use App\Models\PasswordReset;
use App\Models\Token;
use App\Notifications\PasswordResetNotification;
use App\Notifications\PasswordResetSuccessNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    public function mail($user, $resetCode)
    {
    }

    public function forgot(Request $request)
    {
        $data = Validator::make($request->all(), [
            "email" => "required|email"
        ]);

        if ($data->fails()) {
            $errors = $data->errors();

            return response()->json([
                "success" => false,
                "errors" => $errors,
            ]);
        }

        $query = Customer::where("email", "=", $request->email);

        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Email không tồn tại."
            ]);
        }

        $resetCode = str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        $expiredTime = now()->addMinute(10); // code will expired after 10 minutes

        $userExistsReset = PasswordReset::where("email", "=", $request->email);
        if (!$userExistsReset->exists()) {
            PasswordReset::create([
                "email" => $request->email,
                "token" => $resetCode,
                "expired" => date_format($expiredTime, "Y-m-d H:i:s"),
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ]);
        } else {
            $userExistsReset->first()->update([
                "email" => $request->email,
                "token" => $resetCode,
                "expired" => date_format($expiredTime, "Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s")
            ]);
        }

        $user = $query->first();

        // Send email
        $userName = $user->first_name . " " . $user->last_name;
        $title = "Yêu cầu đổi mật khẩu";
        Mail::to($user->email)->send(new ForgotPasswordMail($userName, $resetCode, $title, $title));

        return response()->json([
            "success" => true,
            "email" => $request->email,
            "message" => "Một đoạn mã xác thực đã được gửi đến email."
        ]);
    }

    public function checkTimeValid($time)
    {
        $currentTime = strtotime(now());

        // Check if code still valid (if it results as negative value then it valids and vice versa)
        if ($currentTime - strtotime($time) >= 0) {
            return false;
        }
        return true;
    }

    public function checkCode(Request $request)
    {
        // Check reset password is connect to email that being requested at the moment or not
        $query = PasswordReset::where("token", '=', $request->code)
            ->where("email", "=", $request->email);

        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Mã xác thực không hợp lệ."
            ]);
        }

        $user = $query->first();

        if (!$this->checkTimeValid($user->expired)) {
            return response()->json([
                "success" => false,
                "errors" => "Mã xác thực đã hết hạn, vui lòng gửi lại mã xác thực để tiến hành thực hiện đổi mật khẩu."
            ]);
        }

        return response()->json([
            "success" => true,
            "email" => $request->email,
            "message" => "Mã xác thực hợp lệ. Bạn sẽ được chuyển đến trang đổi mật khẩu."
        ]);
    }

    public function reset(Request $request)
    {
        // Check reset password is connect to email that being requested at the moment or not
        $queryPasswordReset = PasswordReset::where("email", "=", $request->email);
        $queryUser = Customer::where("email", "=", $request->email);

        if (!$queryPasswordReset->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Đi đâu vậy anh bạn?!"
            ]);
        }

        if (!$queryUser->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Không thể thực hiện đổi mật khẩu với email không tồn tại."
            ]);
        }

        // Check confirm password and password are the same or not
        if ($request->password !== $request->confirmPassword) {
            return response()->json([
                "success" => false,
                "errors" => "Mật khẩu không khớp."
            ]);
        }

        $user = $queryUser->first();
        $userName = $user->first_name . " " . $user->last_name;
        // Troll section
        if (Hash::check($request->password, $user->password)) {
            return response()->json([
                "success" => false,
                "errors" => "Mật khẩu mới không thể giống với mật khẩu cũ."
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save(); // Need to create check for errors during update new password

        // Delete reset password code
        $queryPasswordReset->first()->delete();

        // Delete all login token
        Token::where("customer_id", "=", $user->id)->delete();
        DB::table("personal_access_tokens")->where("name", "=", "Customer - " . $user->id)->delete();

        // Send mail to notify password has changed
        $userName = $user->first_name . " " . $user->last_name;
        $title = "Mật khẩu của quý khách đã được thay đổi";
        Mail::to($user->email)->send(new ResetPasswordSuccessMail($userName, $title, $title));

        return response()->json([
            "success" => true,
            "message" => "Đổi mật khẩu thành công."
        ]);
    }
}
