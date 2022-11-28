<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Delete\DeleteAdminBasicRequest;
use App\Http\Requests\Admin\Get\GetAdminBasicRequest;
use App\Http\Requests\Admin\Store\StoreAdminRequest;
use App\Http\Requests\Admin\Store\StoreAvatarAdminRequest;
use App\Http\Requests\Admin\Update\UpdateAdminIndividualRequest;
use App\Http\Requests\Admin\Update\UpdatePasswordAdminRequest;
use App\Http\Resources\V1\OrderListCollection;
use App\Models\Admin;
use App\Models\AdminAuth;
use App\Models\AdminToken;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminAuthController extends Controller
{
    // ******* Admin ******* \\
    public function __construct()
    {
        $this->middleware("auth:sanctum", ["except" => ["setup", "login", "retrieveToken"]]);
    }

    // Thuc tap to nghiep
    public function dashboard(GetAdminBasicRequest $request)
    {
        $total_sales = Order::where("status", "=", 2)->get(); // Orders have status Completed considered as Sales

        $sum_price = 0;
        for ($i = 0; $i < sizeof($total_sales); $i++) {
            $sum_price = $sum_price + $total_sales[$i]->total_price;
        }
        
        $products = Product::where("deleted_at", "=", null)->get()->count(); // Total products has been created so far
        $pending_orders = Order::where("status", "=", 0)->get()->count(); // Orders have status = 0 will be considered as Pending
        $recent_orders = Order::orderBy('created_at', 'DESC')->get()->count();
        
        return response()->json([
            "totalSales" => $sum_price,
            "totalProducts" => $products,
            "totalOrdersPending" => $pending_orders,
            "recentOrders" => $recent_orders
        ]);
    }

    // XD OOP
    public function dashboardOop(GetAdminBasicRequest $request)
    {
        if (Order::get()->count() === 0) {
            $display_recent_orders = "There is no any Recent Orders";
        } else {
            $recent_orders = Order::orderBy('created_at', 'DESC')->take(10)->get(); // Take top 10 orders are created ordered by "created_at" in descending order
            $display_recent_orders = new OrderListCollection($recent_orders);
        }

        $orders = Order::where("status", "=", 2)->get()->count(); // Orders have status Completed considered as Sales
        $products = Product::get()->count(); // Total products has been created so far
        $pending_orders = Order::where("status", "=", 0)->get()->count(); // Orders have status = 0 will be considered as Pending

        return response()->json([
            "totalSales" => $orders,
            "totalProducts" => $products,
            "totalOrdersPending" => $pending_orders,
            "recentOrders" => $display_recent_orders
        ]);
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
            $token = $admin->createToken("Admin - " . $admin->id, ['admin'])->plainTextToken;
        } else {
            $token = $admin->createToken("Super admin", ['super_admin'])->plainTextToken;
        }

        $token_encrypt = Crypt::encryptString($token);

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

        // Display level
        if ($admin->level === 0) {
            $display_level = "Admin";
        } else {
            $display_level = "Super Admin";
        }

        return response()->json([
            "success" => true,
            // "token_type" => "Encrypted",
            "token" => $token,
            // "encryptedToken" => $token_encrypt,
            "data" => [
                "id" => $admin->id,
                "userName" => $admin->user_name,
                "email" => $admin->email,
                "avatar" => $admin->avatar,
                "defaultAvatar" => $admin->default_avatar,
                "level" => $display_level,
            ]
        ]);
    }

    public function logout(GetAdminBasicRequest $request)
    {
        AdminToken::where('token', "=", $request->bearerToken())->delete();

        Auth::guard("admin")->logout();

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            "success" => true,
            "message" => "Log out successfully"
        ]);
    }

    public function update(UpdateAdminIndividualRequest $request)
    {
        $query = Admin::where("id", "=", $request->user()->id);
        $userCheck = Admin::where("user_name", "=", $request->user()->user_name)->exists();
        $emailCheck = Admin::where("email", "=", $request->user()->email);

        $admin_data = $query->first(); // For some reason, don't know why can't put this instance after if condition

        if ($userCheck) {
            return response()->json([
                "success" => false,
                "errors" => "Username was taken, Please choose another one"
            ]);
        }

        if ($emailCheck) {
            return response()->json([
                "success" => false,
                "errors" => "Email was taken, Please choose another one"
            ]);
        }

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

    // Use this api to change password Admin
    public function changePassword(UpdatePasswordAdminRequest $request)
    {
        $admin = Admin::where("id", "=", $request->user()->id)->first();

        if (Hash::check($request->password, $admin->password)) {
            return response()->json([
                "success" => false,
                "errors" => "Can't replace password with the same old one"
            ]);
        }

        $admin->password = Hash::make($request->password);
        $result = $admin->save();

        if (empty($result)) {
            return response()->json([
                "success" => false,
                "errors" => "An unexpected error has occurred"
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Successfully changed password"
        ]);
    }

    public function profile(GetAdminBasicRequest $request)
    {
        return response()->json([
            "userName" => $request->user()->user_name,
            "email" => $request->user()->email,
            "avatar" => $request->user()->avatar,
            "defaultAvatar" => $request->user()->default_avatar,
            "level" => $request->user()->level
        ]);
    }

    public function userInfo(GetAdminBasicRequest $request)
    {
        return response()->json([
            "success" => true,
            "data" => [
                "userName" => $request->user()->user_name,
                "email" => $request->user()->email,
                "avatar" => $request->user()->avatar,
                "defaultAvatar" => $request->user()->default_avatar,
                "level" => $request->user()->level,
            ]
        ]);
    }

    // Use when user first enter website (Admin site)
    public function retrieveToken(Request $request)
    {
        // Checking token existence
        // $decrypt_token = Crypt::decryptString($request->token); // Decrypt first
        // $token = AdminToken::where("token", "=", $decrypt_token)->first();
        $token = AdminToken::where("token", "=", $request->bearerToken())->first();

        if ($token === null) {
            return response()->json([
                "success" => false,
                "errors" => "No token found"
            ]);
        }

        return response()->json([
            "success" => true,
            "token" => $token->token,
            "tokenType" => "Bearer Token"
        ]);
    }

    public function upload(StoreAvatarAdminRequest $request)
    {
        $admin = Admin::where("id", "=", $request->user()->id)->first();
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

    public function destroyAvatar(DeleteAdminBasicRequest $request)
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
