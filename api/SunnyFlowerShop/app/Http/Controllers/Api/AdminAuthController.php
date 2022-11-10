<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminAuth;
use App\Models\AdminToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminAuthController extends Controller
{
    // ******* Admin ******* \\
    public function __construct()
    {
        $this->middleware("auth:sanctum", ["except" => ["setup", "login", "retrieveToken"]]);
    }

    public function setup()
    {
        $admin = Admin::where("email", "=", "admin@email.com")->exists();
        $superAdmin = Admin::where("email", "=", "sadmin@email.com")->exists();

        if ($admin && $superAdmin) {
            return response()->json([
                "success" => false,
                "errors" => "Admin and Super Admin account have already created"
            ]);
        }

        $data = [
            'user_name' => 'admin',
            'email' => 'admin@email.com',
            'password' => Hash::make('123'),
            'level' => '0',
        ];

        AdminAuth::create($data);

        $data = [
            'user_name' => 'Super Admin',
            'email' => 'sadmin@email.com',
            'password' => Hash::make('123'),
            'level' => '1',
        ];

        AdminAuth::create($data);

        return response()->json([
            "success" => true,
            "message" => "Created Admin and Super Admin account successfully"
        ]);
    }

    public function login(Request $request)
    {
        if (!Auth::guard("admin")->attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json([
                "success" => false,
                "errors" => "Invalid credential"
            ]);
        }

        // Set to Vietnam timezone
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $admin = AdminAuth::where("email", "=", $request->email)->firstOrFail();

        if ($admin->level == 0) {
            $token = $admin->createToken("admin", ['create', 'update'])->plainTextToken;
        } else {
            $token = $admin->createToken("super-admin", ['create', 'update', 'delete'])->plainTextToken;
        }

        // Update token in admin_token table
        $admin_token = Admin::where('email', "=", $request->email)->first();

        $token_data = [
            "admin_id" => $admin_token->id,
            "token" => $token,
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ];

        $check = AdminToken::insert($token_data);

        if (empty($check)) {
            return response()->json([
                "success" => false,
                "errors" => "Something went wrong"
            ]);
        }

        return response()->json([
            "success" => true,
            "token_type" => "Bearer",
            "token" => $token,
            "data" => [
                "id" => $admin->id,
                "userName" => $admin->user_name,
                "email" => $admin->email,
                "avatar" => $admin->avatar,
                "defaultAvatar" => $admin->default_avatar,
                "level" => $admin->level,
            ]
        ]);
    }

    public function logout(Request $request)
    {
        AdminToken::where('token', "=", $request->bearerToken())->delete();

        Auth::guard("admin")->logout();

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            "success" => true,
            "message" => "Log out successfully"
        ]);
    }

    public function update(Request $request)
    {
        $data = Validator::make($request->all(), [
            "userName" => "string|min:2|max:50",
            "email" => "email",
            "password" => "string|min:6|max:24",
        ]);

        if ($data->fails()) {
            $errors = $data->errors();

            return response()->json([
                "success" => false,
                "errors" => $errors,
            ]);
        }

        $query = Admin::where("id", "=", $request->user()->id);

        $admin_data = $query->first(); // For some reason, don't know why can't put this instance after if condition

        // If new email doesn't belong to current customer
        if (!$query->where("email", "=", $request->email)->exists()) {

            // Check existence of email in database
            $check = Admin::where("email", "=", $request->email)->exists();
            if ($check) {
                return response()->json([
                    "success" => false,
                    "errors" => "Email has already been used"
                ]);
            }
        }

        $password = "";
        $user_name = $request->userName ?? $request->user()->user_name;
        // Checking if user make chane to password
        if ($request->password !== null) {
            $password = Hash::make($request->password);
        } else {
            $password = $admin_data->password;
        }

        // Check field is null or not to decide to udpate with old value or new value
        $user_name = $request->userName ?? $admin_data->user_name;
        $email = $request->email ?? $admin_data->email;

        $check = Admin::find($request->user()->id)->update([
            "user_name" => $user_name,
            "email" => $email,
            "password" => $password,
        ]);

        if (!$check) {
            return response()->json([
                "success" => false,
                "errors" => "An unexpected error had occurred"
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Change admin information successfully"
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json([
            "userName" => $request->user()->user_name,
            "email" => $request->user()->email,
            "avatar" => $request->user()->avatar,
            "defaultAvatar" => $request->user()->default_avatar,
            "level" => $request->user()->level
        ]);
    }

    // Use when user first enter website (Admin site)
    public function retrieveToken(Request $request)
    {
        // Checking token existence
        $token = AdminToken::where("token", "=", $request->bearerToken())->first();

        if ($token === null) {
            return response()->json([
                "success" => false,
                "errors" => "No token found"
            ]);
        }

        return response()->json([
            "success" => true,
            "token" => $request->bearerToken() ?? null,
            "tokenType" => "Bearer Token"
        ]);
    }

    public function upload(Request $request)
    {
        $data = Validator::make($request->all(), [
            "avatar" => "required|string"
        ]);

        if ($data->fails()) {
            $errors = $data->errors();

            return response()->json([
                "success" => false,
                "errors" => $errors,
            ]);
        }

        $query = Admin::where("id", "=", $request->user()->id);
        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Can't upload avatar with invalid Customer ID"
            ]);
        }

        $admin = $query->first();
        $admin->avatar = $request->avatar;

        $result = $admin->save();

        // If result is false, that means save process has occurred some issues
        if (!$result) {
            return response()->json([
                'success' => false,
                "errors" => "An unexpected error has occurred"
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Uploaded avatar successfully"
        ]);
    }

    public function destroyAvatar(Request $request)
    {
        $admin = Admin::find($request->user()->id);

        $admin->avatar = null;
        $result = $admin->save();

        // If result is false, that means save process has occurred some issues
        if (!$result) {
            return response()->json([
                'success' => false,
                "errors" => "An unexpected error has occurred"
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Remove avatar successfully"
        ]);
    }
}
