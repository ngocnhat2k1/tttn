<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Delete\DeleteAdminBasicRequest;
use App\Http\Requests\Admin\Delete\DeleteAdminRequest;
use App\Http\Requests\Admin\Get\GetAdminBasicRequest;
use App\Http\Requests\Admin\Store\StoreAvatarAdminRequest;
use App\Http\Requests\Admin\Store\StoreCustomerAdminRequest;
use App\Http\Requests\Admin\Update\UpdateCustomerAdminRequest;
use App\Http\Requests\Admin\Update\UpdatePasswordCustomerRequest;
use App\Http\Resources\V1\CustomerDetailResource;
use App\Http\Resources\V1\CustomerOverviewCollection;
use App\Models\Customer;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index(GetAdminBasicRequest $request)
    {
        $check = Customer::get()->count();

        if ($check === 0) {
            return response()->json([
                "success" => false,
                "errors" => "User list is empty"
            ]);
        }

        $customers = Customer::paginate(10);

        return new CustomerOverviewCollection($customers);
    }

    public function show(GetAdminBasicRequest $request, Customer $customer)
    {
        return response()->json([
            "success" => true,
            "data" => new CustomerDetailResource($customer)
        ]);
    }

    public function store(StoreCustomerAdminRequest $request)
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

    public function disable(Customer $customer, DeleteAdminBasicRequest $request)
    {
        $customer = Customer::find($customer->id);

        if (empty($customer)) {
            return response()->json([
                "success" => false,
                "errors" => "User ID is invalid"
            ]);
        }

        // Checking state value to switch between: Disable or Reverse Disable
        // If state value is 1, it will be Disable
        if ((int)$request->state === 1) {
            if ($customer->disabled !== null) {
                return response()->json([
                    "success" => false,
                    "errors" => "Customer with ID = " . $customer->id . " has already been disabled"
                ]);
            }

            $customer->disabled = 0;
            $result = $customer->save();

            // Log out account
            Auth::guard("customer")->logout();
            $name = "Customer - " . $customer->id;

            // Delete token in "personal_access_tokens"
            DB::table("personal_access_tokens")
                ->where("name", "=", $name)
                ->delete();

            // Delete all available tokens in Token table
            Token::where("customer_id", "=", $customer->id)->delete();

            if (!$result) {
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
        }
        // if state value is not 1, it will Reverse Disable
        else {
            if ($customer->disabled === null) {
                return response()->json([
                    "success" => false,
                    "errors" => "Customer with ID = " . $customer->id . " has already been reversed disabled"
                ]);
            }

            $customer->disabled = null;
            $result = $customer->save();

            if (!$result) {
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

    public function update(UpdateCustomerAdminRequest $request, Customer $customer)
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

        $update = $customer->update($filtered);

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

    // Use this api to change password Admin
    public function changePassword(UpdatePasswordCustomerRequest $request, Customer $customer)
    {
        if (Hash::check($request->password, $customer->password)) {
            return response()->json([
                "success" => false,
                "errors" => "Can't replace password with the same old one"
            ]);
        }

        $customer->password = Hash::make($request->password);
        $result = $customer->save();

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

    public function upload(StoreAvatarAdminRequest $request, Customer $customer)
    {
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

    public function destroyAvatar(DeleteAdminBasicRequest $request, Customer $customer)
    {
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
