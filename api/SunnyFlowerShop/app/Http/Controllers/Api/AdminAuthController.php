<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdminAuth;
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
        if (!Auth::guard("admin")->attempt($request->only("email", "password"))) {
            return response()->json([
                "success" => false,
                "errors" => "Invalid credential"
            ]);
        }

        $admin = AdminAuth::where("email", "=", $request->email)->firstOrFail();

        if ($admin->level == 0) {
            $token = $admin->createToken("admin", ['create', 'update'])->plainTextToken;
        } else {
            $token = $admin->createToken("super-admin", ['create', 'update', 'delete'])->plainTextToken;
        }

        $admin->token = $token;
        $admin->save();

        return response()->json([
            "success" => true,
            "token_type" => "Bearer",
            "token" => $token,
        ]);
    }

    public function logout(Request $request)
    {
        // Will remove later
        // $admin = AdminAuth::where("email", "=", $request->user()->email)->firstOrFail();
        // $admin->token = null;
        // $admin->save();

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
