<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Models\Admin;
use App\Models\AdminAuth;
use App\Models\Customer;
use App\Models\CustomerAuth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserAuthController extends Controller
{
    // ******* CUSOMTER ******* \\
    public function __construct()
    {
        $this->middleware("auth:sanctum", ["except" => ["register", "login"]]);
    }

    public function register(Request $request)
    {
        $data = Validator::make($request->all(), [
            "first_name" => "required|string|min:2|max:50",
            "last_name" => "required|string|min:2|max:50",
            "email" => "required|email",
            "password" => "required|min:6|max:24",
            "phone_number" => "required|string"
        ]);

        if ($data->fails()) {
            $errors = $data->errors();

            return response()->json([
                "success" => false,
                "errors" => $errors,
            ]);
        }

        $check = Customer::where("email", '=', $request->email)->get()->count();
        if ($check != 0) {
            return response()->json([
                "success" => false,
                "errors" => "Email already exists"
            ]);
        }

        if ($data->passes()) {
            $customer = CustomerAuth::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                "phone_number" => $request->phone_number
            ]);

            // token abilities will be detemined later
            $token = $customer->createToken("customer" . $customer->id, ["none"])->plainTextToken;

            return response()->json([
                "success" => true,
                "token" => $token,
                "token_type" => "Bearer",
                "user" => $customer
            ]);
        }
    }

    public function login(Request $request)
    {
        if (!Auth::guard("customer")->attempt($request->only("email", "password"))) {
            return response()->json([
                "success" => false,
                "errors" => "Invalid credential"
            ]);
        }

        $customer = CustomerAuth::where('email', "=", $request->email)->firstOrFail();

        $token = $customer->createToken("customer" . $customer->id, ["none"])->plainTextToken;

        $customer->token = $token;
        $customer->save();

        return response()->json([
            "success" => true,
            "user" => $customer,
            "token_type" => "Bearer",
            "token" => $token
        ]);
    }

    public function logout(Request $request)
    {
        // Will remove later
        $customer = CustomerAuth::where('email', "=", $request->user()->email)->firstOrFail();
        $customer->token = null;
        $customer->save;

        Auth::guard("customer")->logout();

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
