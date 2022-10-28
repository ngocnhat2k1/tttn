<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\V1\CustomerDetailResource;
use App\Http\Resources\V1\CustomerOverviewCollection;
use App\Models\Customer;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::paginate(10);

        return new CustomerOverviewCollection($customers);
    }

    public function show(Customer $customer)
    {
        // Checking User ID
        $customer_data = Customer::find($customer->id);

        if (empty($customer_data)) {
            return response()->json([
                "success" => false,
                "errors" => "User ID is invalid"
            ]);
        }

        return response()->json([
            "success" => true,
            "data" => new CustomerDetailResource($customer_data)
        ]);
    }

    public function store(StoreCustomerRequest $request)
    {
        $filtered = $request->except(["firstName", "lastName"]);

        // Check existence of email
        $check_email = Customer::where("email", "=", $filtered['email'])->exists();
        if ($check_email) {
            return response()->json([
                "success" => false,
                "errors" => "Email has already been used"
            ]);
        }

        $filtered['password'] = Hash::make($filtered['password']);

        $result = Customer::create($filtered);

        if (empty($result->id)) {
            return response()->json([
                "success" => false,
                "errors" => "Something went wrong"
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Create account succesfully"
        ]);
    }

    public function disable(Customer $customer, Request $request)
    {
        $data = Customer::find($customer->id);

        if (empty($data)) {
            return response()->json([
                "success" => false,
                "errors" => "User ID is invalid"
            ]);
        }

        // Checking state value to switch between: Disable or Reverse Disable
        // If state value is 1, it will be Disable
        if ((int)$request->state === 1) {
            if ($data->disabled !== null) {
                return response()->json([
                    "success" => false,
                    "errors" => "Customer with ID = " . $customer->id . " has already been disabled"
                ]);
            }

            $data->disabled = 0;
            $data->save();

            // Log out account
            Auth::guard("customer")->logout();
            $name = "Customer - " . $customer->id;

            // Delete token in "personal_access_tokens"
            DB::table("personal_access_tokens")
                ->where("name", "=", $name)
                ->delete();

            // Delete all available tokens in Token table
            Token::where("customer_id", "=", $customer->id)->delete();

            if (!$data) {
                return response()->json([
                    "success" => false,
                    "errors" => "An unexpected error has occurred"
                ]);
            }

            return response()->json(
                [
                    'success' => true,
                    'errors' => "Sucessfully disabled account customer with ID = " . $customer->id
                ]
            );

            // if state value is not 1, it will Reverse Disable
        } else {
            if ($data->disabled === null) {
                return response()->json([
                    "success" => false,
                    "errors" => "Customer with ID = " . $customer->id . " has already been reversed disabled"
                ]);
            }

            $data->disabled = null;
            $data->save();

            if (!$data) {
                return response()->json([
                    "success" => false,
                    "errors" => "An unexpected error has occurred"
                ]);
            }

            return response()->json(
                [
                    'success' => true,
                    'errors' => "Sucessfully reversed disable account customer with ID = " . $customer->id
                ]
            );
        }
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        // Check email belong to customer that being check
        $customer_email = Customer::where("email", "=", $request->email)
            ->where("id", "=", $customer->id)->exists();

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

        $filtered = $request->except(["firstName", "lastName"]);

        // Checking if user make chane to password
        if ($request->password !== null) {
            $filtered['password'] = Hash::make($filtered['password']);
        }

        $update = Customer::where("id", "=", $customer->id)->update($filtered);

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

    public function updateValue(Request $request, Customer $customer)
    {
        if(empty($request->all())) {
            return response()->json([
                "success" => true,
                "message" => "No value is requested to change/ No value is changed"
            ]);
        }

        $data = Validator::make($request->all(), [
            "firstName" => "string|min:2|max:50",
            "lastName" => "string|min:2|max:50",
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

        $customer_data = Customer::where("email", "=", $customer->email)
            ->where("id", "=", $customer->id);

        // Check existence of email
        if (!$customer_data->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Customer doesn't exist"
            ]);
        }

        $customer_get = $customer_data->first();

        $customer_get['first_name'] = $request->firstName ?? $customer_get['first_name'];
        $customer_get['last_name'] = $request->lastName ?? $customer_get['last_name'];
        $customer_get['email'] = $request->email ?? $customer_get['email'];

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
}
