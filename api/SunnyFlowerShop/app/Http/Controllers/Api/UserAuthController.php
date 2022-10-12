<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CustomerDetailResource;
use App\Http\Resources\V1\CustomerRegisterResource;
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
            "firstName" => "required|string|min:2|max:50",
            "lastName" => "required|string|min:2|max:50",
            "email" => "required|email",
            "password" => "required|min:6|max:24",
            "phoneNumber" => "required|string"
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
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                "phone_number" => $request->phoneNumber
            ]);

            // token abilities will be detemined later
            $token = $customer->createToken("customer-$customer->id", ["update_profile", "fav_product", "place_order", "make_feedback", "create_address", "update_address", "remove_address"])->plainTextToken;

            return response()->json([
                "success" => true,
                "token" => $token,
                "tokenType" => "Bearer",
                "user" => new CustomerRegisterResource($customer)
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

        $token = $customer->createToken("Customer - " . $customer->id, ["update_profile", "fav_product", "place_order", "make_feedback", "create_address", "update_address", "remove_address"])->plainTextToken;

        $customer->token = $token;
        $customer->save();

        return response()->json([
            "success" => true,
            "tokenType" => "Bearer",
            "token" => $token,
            "data" => new CustomerDetailResource($customer)
        ]);
    }

    public function logout(Request $request)
    {
        // **** Will change later **** \\
        $customer = CustomerAuth::where('token', "=", $request->user()->token)->first();
        
        // Check ID Customer
        if (empty($customer)) {
            return response()->json([
                "success" => false,
                "errors" => "Customer ID is invalide"
            ]);
        }

        $customer->token = null;
        $customer->save();

        Auth::guard("customer")->logout();

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            "success" => true,
            "message" => "Log out successfully"
        ]);
    }

    public function profile(Request $request)
    {
        return new CustomerDetailResource($request->user());
    }
}
