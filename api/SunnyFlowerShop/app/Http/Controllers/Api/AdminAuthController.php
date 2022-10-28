<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminAuth;
use App\Models\AdminToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    // ******* Admin ******* \\
    public function __construct()
    {
        $this->middleware("auth:sanctum", ["except" => ["setup", "login"]]);
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

    public function profile(Request $request)
    {
        return $request->user();
    }
}
