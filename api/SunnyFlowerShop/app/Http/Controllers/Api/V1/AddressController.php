<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Address;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AddressDetailResource;
use App\Http\Resources\V1\AddressOverviewCollection;
use App\Http\Resources\V1\AddressOverviewResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $address = Address::where("customer_id", "=", $request->user()->id);

        if (!$address->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "This use hasn't created any address yet"
            ]);
        }

        return new AddressOverviewCollection($address->paginate(10));

        // return response()->json([
        //     "success" => true,
        //     "data" => new AddressOverviewCollection($address->get())
        // ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAddressRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAddressRequest $request)
    {
        // $customer = Customer::find($request->user()->id);

        $check = Address::where("customer_id", "=", $request->user()->id)
            ->where("first_name_receiver", "=", $request->firstNameReceiver)
            ->where("last_name_receiver", "=", $request->lastNameReceiver)
            ->where("street_name", $request->streetName)
            ->where("district", $request->district)
            ->where("ward", $request->ward)
            ->where("city", $request->city)->exists();

        if ($check) {
            return response()->json([
                "success" => false,
                "errors" => "Address is already existed"
            ]);
        }

        $filtered = $request->except(['firstNameReceiver', 'lastNameReceiver', "phoneReceiver", 'streetName']);
        $filtered['customer_id'] = $request->user()->id;

        $data = Address::create($filtered);
        if (empty($data->id)) {
            return response()->json([
                "success" => false,
                "errors" => "An unexpected error has occurred"
            ]);
        }

        // $data->customers()->attach($customer);

        return response()->json([
            "success" => true,
            "message" => "Created Address successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // $customer = Customer::find($request->user()->id);

        // For some reason, i can't use normal Many-to-Many relationship query
        $query = Address::where("id", "=", $request->id)
            ->where("customer_id", "=", $request->user()->id);
        // $query = DB::table("address_customer")
        //     ->where("address_id", "=", $request->id)
        //     ->where("customer_id", "=", $customer->id)
        //     ->join("addresses", "address_customer.id", "=", "addresses.id");

        $check = $query->exists();

        if (empty($check)) {
            // if (empty($check)) {
            return response()->json([
                "success" => false,
                "errors" => "Something went wrong, Address ID doesn't exist"
            ]);
        }

        $data = $query->first();

        return response()->json([
            "success" => true,
            "data" => new AddressDetailResource($data)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAddressRequest  $request
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAddressRequest $request, $addressId)
    {
        // $customer = Customer::find($request->user()->id);

        // For some reason, i can't use normal Many-to-Many relationship query
        // $check = DB::table("address_customer")
        //     ->where("address_id", "=", $request->id)
        //     ->where("customer_id", "=", $customer->id)
        //     ->exists();

        $query = Address::where("id", "=", $addressId)
            ->where("customer_id", "=", $request->user()->id);

        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Something went wrong, Address_id is invalid"
            ]);
        }

        // Check address existence in database
        $check = Address::where("customer_id", "=", $request->user()->id)
            ->where("first_name_receiver", "=", $request->firstNameReceiver)
            ->where("last_name_receiver", "=", $request->lastNameReceiver)
            ->where("street_name", $request->streetName)
            ->where("district", $request->district)
            ->where("ward", $request->ward)
            ->where("city", $request->city)
            ->exists();

        // If address is existed, then check does it belong to current customer is updating their address
        if ($check) {
            return response()->json([
                "success" => false,
                "errors" => "Address is already associated with this account"
            ]);
        }

        $filtered = $request->except(['firstNameReceiver', 'lastNameReceiver', "phoneReceiver", 'streetName']);
        $filtered['customer_id'] = $request->user()->id;

        $address = $query->first();

        foreach ($filtered as $key => $value) {
            $address->{$key} = $value;
        }

        $result = $address->save();

        if (!$result) {
            return response()->json([
                'success' => false,
                "errors" => "An unexpected error has occurred"
            ]);
        }

        return response()->json([
            'success' => true,
            "message" => "Updated address successfully"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        // $customer = Customer::find($request->user()->id);

        // $check = DB::table("address_customer")
        //     ->where("address_id", "=", $request->id)
        //     ->where("customer_id", "=", $customer->id)
        //     ->exists();

        $check = Address::where("id", "=", $request->id)
            ->where("customer_id", "=", $request->user()->id)
            ->exists();

        if (empty($check)) {
            return response()->json([
                "success" => false,
                "errors" => "Something went wrong, Address ID is invalid"
            ]);
        }

        $address = Address::where("id", "=", $request->id)
            ->where("customer_id", "=", $request->user()->id);

        // $customer->addresses()->detach($address);
        $address->delete();

        $check = Address::where("id", "=", $request->id)->first();

        if (empty($check)) {
            return response()->json([
                "success" => true,
                "message" => "Deleted address successfully"
            ]);
        }

        return response()->json([
            "success" => false,
            "errors" => "An unexpected error has occurred"
        ]);
    }
}
