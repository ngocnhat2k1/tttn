<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\V1\CustomerDetailResource;
use App\Models\Customer;
use App\Models\CustomerAuth;
use App\Models\Token;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserAuthController extends Controller
{
    // ******* CUSOMTER ******* \\
    public function __construct()
    {
        $this->middleware("auth:sanctum", ["except" => ["register", "login", "retrieveToken", "upload"]]);
    }

    public function register(Request $request)
    {
        $data = Validator::make($request->all(), [
            "firstName" => "required|string|min:2|max:50",
            "lastName" => "required|string|min:2|max:50",
            "email" => "required|email",
            "password" => "required|min:6|max:24",
            "confirmPassword" => "required|string",
            "subscribed" => "required|boolean"
        ]);

        if ($data->fails()) {
            $errors = $data->errors();

            return response()->json([
                "success" => false,
                "errors" => $errors,
            ]);
        }

        // Check existence of email in database
        $check = Customer::where("email", '=', $request->email)->exists();
        if ($check) {
            return response()->json([
                "success" => false,
                "errors" => "Email already exists"
            ]);
        }

        // Check password and confirm password are the same
        if ($request->password !== $request->confirmPassword) {
            return response()->json([
                "success" => false,
                "errors" => "Password are changing. Please make sure your information is consistent"
            ]);
        }

        if ($data->passes()) {
            $customer = CustomerAuth::create([
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'subscribed' => $request->subscribed,
            ]);

            // token abilities will be detemined later - i mean will be consider to be deleted or not
            // $token = $customer->createToken("customer-$customer->id", ["update_profile", "fav_product", "place_order", "make_feedback", "create_address", "update_address", "remove_address"])->plainTextToken;

            return response()->json([
                "success" => true,
                // "token" => $token,
                // "tokenType" => "Bearer",
                // "user" => new CustomerRegisterResource($customer)
                "message" => "Successfully created account"
            ]);
        }
    }

    public function login(Request $request)
    {

        // if (!Auth::guard("customer")->attempt($request->only("email", "password"))) {
        if (!Auth::guard("customer")->attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json([
                "success" => false,
                "errors" => "Invalid credential"
            ]);
        }

        // Set to Vietnam timezone
        // date_default_timezone_set('Asia/Ho_Chi_Minh');

        // Using Auth model to check User to create Token
        $customer = CustomerAuth::where('email', "=", $request->email)->first();

        // Checking if account is disabled?
        if ($customer->disabled !== null) {

            // Log out account
            Auth::guard("customer")->logout();

            return response()->json([
                "success" => false,
                "errors" => "This account has already been disabled by admin"
            ]);
        }

        // Token ability base from admin perspective, "none" for not allow to do anything what admin can
        $token = $customer->createToken("Customer - " . $customer->id, ["none"])->plainTextToken;

        // Use normal model to check User to store token
        $customer_token = Customer::where('email', "=", $request->email)->first();

        $token_data = [
            "customer_id" => $customer_token->id,
            "token" => $token,
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ];

        $check = Token::insert($token_data);

        if (empty($check)) {
            return response()->json([
                "success" => false,
                "errors" => "Something went wrong"
            ]);
        }

        return response()->json([
            "success" => true,
            "tokenType" => "Bearer",
            "token" => $token,
            // "data" => new CustomerDetailResource($customer)
            "data" => [
                "customerId" => $customer->id,
                "firstName" => $customer->first_name,
                "lastName" => $customer->last_name,
                "email" => $customer->email,
                "avatar" => $customer->avatar,
                "defaultAvatar" => $customer->default_avatar,
                "subscribed" => $customer->subscribed,
            ]
        ]);
    }

    public function logout(Request $request)
    {
        Token::where('token', "=", $request->bearerToken())->delete();

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

    public function update(UpdateCustomerRequest $request)
    {
        // Check email belong to customer that being check
        $customer_email = Customer::where("email", "=", $request->email)
            ->where("id", "=", $request->user()->id)->exists();

        // If new email doesn't belong to current customer
        if (!$customer_email) {

            // Check existence of email in database
            $check = Customer::where("email", "=", $request->email)->exists();
            if ($check) {
                return response()->json([
                    "success" => false,
                    "errors" => "Email has already been used"
                ]);
            }
        }

        // Get customer data
        // $customer = Customer::find($request->user()->id);

        $filtered = $request->except(["firstName", "lastName"]);

        // Checking if user make chane to password
        if ($request->password !== null) {
            $filtered['password'] = Hash::make($filtered['password']);
        }

        $update = Customer::where("id", "=", $request->user()->id)->update($filtered);

        if (empty($update)) {
            return response()->json([
                "success" => false,
                "errors" => "An unexpected error has occurred"
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Updated Customer information successfully"
        ]);
    }

    // Use this api to update any value
    public function updateValue(Request $request)
    {
        if (empty($request->all())) {
            return response()->json([
                "success" => true,
                "message" => "No change was made"
            ]);
        }

        $data = Validator::make($request->all(), [
            "firstName" => "string|min:2|max:50",
            "lastName" => "string|min:2|max:50",
            "email" => "email",
            "password" => "string|min:6|max:24",
            "subscribed" => "boolean"
        ]);

        if ($data->fails()) {
            $errors = $data->errors();

            return response()->json([
                "success" => false,
                "errors" => $errors,
            ]);
        }

        $customer_data = Customer::where("email", "=", $request->user()->email)
            ->where("id", "=", $request->user()->id);

        // Check existence of email
        if (!$customer_data->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Customer doesn't exist"
            ]);
        }

        // Check email belong to customer that being check
        $customer_email = Customer::where("email", "=", $request->email)
            ->where("id", "=", $request->user()->id)->exists();

        // If new email doesn't belong to current customer
        if (!$customer_email) {

            // Check existence of email in database
            $check = Customer::where("email", "=", $request->email)->exists();
            if ($check) {
                return response()->json([
                    "success" => false,
                    "errors" => "Email has already been used"
                ]);
            }
        }

        $customer_get = $customer_data->first();

        $customer_get['first_name'] = $request->firstName ?? $customer_get['first_name'];
        $customer_get['last_name'] = $request->lastName ?? $customer_get['last_name'];
        $customer_get['email'] = $request->email ?? $customer_get['email'];
        $customer_get['subscribed'] = $request->subscribed ?? $customer_get['subscribed'];

        /* Check password and avatar first */
        // Create check for password
        if ($request->password !== null) {
            $customer_get['password'] = Hash::make($request->password);
        } else {
            $customer_get['password'] = $customer_get['password'];
        }

        $result = $customer_get->save();

        // If result is false, that means save process has occurred some issues
        if (!$result) {
            return response()->json([
                'success' => false,
                "errors" => "An unexpected error has occurred"
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Updated name customer successfully"
        ]);
    }

    // Use when user first enter website
    public function retrieveToken(Request $request)
    {
        // Checking token existence
        $token = Token::where("token", "=", $request->bearerToken())->first();

        if ($token === null) {
            return response()->json([
                "success" => false,
                "errors" => "No token found"
            ]);
        }

        return response()->json([
            "success" => true,
            "token" => $request->bearerToken(),
            "tokenType" => "Bearer Token",
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

        $customer = Customer::where("id", "=", $request->user()->id)->first();
        $customer->avatar = $request->avatar;

        $result = $customer->save();

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
        $customer = Customer::find($request->user()->id);

        $customer->avatar = null;
        $result = $customer->save();

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
