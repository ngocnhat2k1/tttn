<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Delete\DeleteAdminBasicRequest;
use App\Http\Requests\Admin\Get\GetAdminBasicRequest;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Resources\V1\AddressListCollection;
use App\Http\Resources\V1\AddressOverviewCollection;
use App\Http\Resources\V1\AddressOverviewResource;
use App\Http\Resources\V1\CustomerOverviewResource;
use App\Models\Address;
use App\Models\Customer;

class AddressAdminController extends Controller
{
    public function index(GetAdminBasicRequest $request)
    {
        $addresses = Address::with("customers");

        $count = $addresses->get()->count();

        if (empty($count)) {
            return response()->json([
                "success" => false,
                "errors" => "Address list is empty"
            ]);
        }

        return new AddressListCollection($addresses->paginate(10));
    }

    public function show(GetAdminBasicRequest $request, Address $address)
    {
        // $request->id is for customer
        // $customer_data = Customer::where("id", "=", $customer->id);
        $customer = Customer::where("id", "=", $address->customer_id);

        if (!$customer->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Address has some invalid information, please double check database before displaying"
            ]);
        }
        
        $address['customer'] = $customer->first();

        return response()->json([
            "success" => true,
            "data" => [
                "id" => $address->id,
                // "customterId" => $address->pivot->customer_id,
                "firstNameReceiver" => $address->first_name_receiver,
                "lastNameReceiver" => $address->last_name_receiver,
                "phoneReceiver" => $address->phone_receiver,
                "streetName" => $address->street_name,
                "district" => $address->district,
                "ward" => $address->ward,
                "city" => $address->city,
                "customer" => new CustomerOverviewResource($address->customer)
            ]
        ]);

        // return response()->json([
        //     "success" => true,
        //     "data" => new AddressOverviewResource($addresses->first())
        // ]);
    }

    public function destroy(DeleteAdminBasicRequest $request, Address $address) {
        $address->delete();

        return response()->json([
            "success" => true,
            "message" => "Successfully Deleted Address with Address ID = " . $address->id
        ]);
    }
}
