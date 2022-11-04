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
use Illuminate\Support\Facades\Storage;
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

        // if ($request->avatar !== null) {
        //     // Delete all old file before add new one
        //     $delete_dir = "avatars/" . $customer->id . "-" . $customer->first_name . "_" . $customer->last_name;

        //     Storage::disk('public')->deleteDirectory($delete_dir);

        //     // Move and then rename new/ old image file
        //     $newImageName = $this->moveAndRenameImageName($request, $customer);
        //     $filtered['avatar'] = $newImageName;
        // }

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

    // Use this api to update any value
    public function updateValue(Request $request, Customer $customer)
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
        ]);

        if ($data->fails()) {
            $errors = $data->errors();

            return response()->json([
                "success" => false,
                "errors" => $errors,
            ]);
        }

        // Check existence of email belong user
        $customer_data = Customer::where("email", "=", $customer->email)
            ->where("id", "=", $customer->id);

        if (!$customer_data->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Customer doesn't exist"
            ]);
        }

        // Check email belong to customer that being check (from request)
        $customer_email = Customer::where("email", "=", $request->email)
            ->where("id", "=", $customer->id)->exists();

        // Check If new email doesn't belong to current customer
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

        // Create check for password
        if ($request->password !== null) {
            $customer_get['password'] = Hash::make($request->password);
        } else {
            $customer_get['password'] = $customer_get['password'];
        }

        $customer_get['first_name'] = $request->firstName ?? $customer_get['first_name'];
        $customer_get['last_name'] = $request->lastName ?? $customer_get['last_name'];
        $customer_get['email'] = $request->email ?? $customer_get['email'];
        $customer_get['subscribed'] = $request->subscribed ?? $customer_get['subscribed'];

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

    /** UPLOAD AVATAR **/
    public function moveAndRenameImageName($request, $customer)
    {
        // Set timezone to Vietname/ Ho Chi Minh City
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $destination = "avatars/" . time() . "_" . $customer->id;

        // Delete all image relate to this product first before put new image in public file
        Storage::disk('public')->deleteDirectory($destination);
        $oldPath = Storage::disk("public")->putFile($destination, $request->avatar);

        /** 
         * These below code basically did this:
         * - Create new image name through explode function
         * - Create new destination image (in case if needed in future)
         * - Then move and rename old existed image to new (old) existed name image
         */
        $imageName = explode("/", $oldPath);
        $imageType = explode('.', end($imageName));

        $newImageName = time() . "_" . $customer->id . "." . end($imageType);
        $newDestination = "";

        for ($i = 0; $i < sizeof($imageName) - 1; $i++) {
            if (rtrim($newDestination) === "") {
                $newDestination = $imageName[$i];
                continue;
            }
            $newDestination = $newDestination . "/" . $imageName[$i];
        }

        $newDestination = $newDestination . "/" . $newImageName;

        // $checkPath return True/ False value
        $checkPath = Storage::disk("public")->move($oldPath, $destination . "/" . $newImageName);

        // Check existend Path (?)
        if (!$checkPath) {
            return false;
        }

        return $newImageName;
    }

    public function upload(Request $request, Customer $customer) {
        $data = Validator::make($request->all(), [
            "avatar" => "file|image"
        ]);

        if ($data->fails()) {
            $errors = $data->errors();

            return response()->json([
                "success" => false,
                "errors" => $errors,
            ]);
        }

        $query = Customer::where("id", "=", $customer->id);
        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Can't upload avatar with invalid Customer ID"
            ]);
        }

        $customer_data = $query->first();

        // If in column value is not default then proceed to delete old value in order to put new one in
        if ($customer_data->avatar !== "customer_default.jpg") {
            $image = explode('.', $customer_data->avatar);
            $dir = "avatars/" . $image[0];

            // Delete all old file before add new one
            Storage::disk('public')->deleteDirectory($dir);
        }

        $newImageName = $this->moveAndRenameImageName($request, $customer_data);
        $customer_data->avatar = $newImageName;

        $result = $customer_data->save();

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

    public function destroyAvatar() {
        // Delete already existed (not default value) to default value (avatar)
    }
}
