<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Voucher;
use App\Http\Requests\StoreVoucherRequest;
use App\Http\Requests\UpdateVoucherRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\VoucherDetailCollection;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $count = Voucher::query()->get()->count();

        if (empty($count)) {
            return response()->json([
                "success" => false,
                "errors" => "Address list is empty"
            ]);
        }

        $vouchers = Voucher::paginate(10);

        return new VoucherDetailCollection($vouchers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreVoucherRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreVoucherRequest $request)
    {
        $voucher = Voucher::where("name", "=", $request->name)->exists();

        if ($voucher) {
            return response()->json([
                "success" => false,
                "errors" => "Voucher name has already existsed"
            ]);
        }

        $filtered = $request->except(["expiredDate"]);

        $result = Voucher::create($filtered);

        if (empty($result)) {
            return response()->json([
                "success" => false,
                "errors" => "An unexpected errors has occurred"
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Created new Voucher successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $voucher = Voucher::where("id", "=", $request->id);

        if (!$voucher->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Voucher ID is invalid"
            ]);
        }

        $data = $voucher->first();

        return [
            "voucherID" => $data->id,
            "name" => $data->name,
            "percent" => $data->percent,
            "usage" => $data->usage,
            "expiredDate" => $data->expired_date,
            "deleted" => $data->deleted,
            "createdAt" => $data->created_at,
            "updatedAt" => $data->updated_at
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateVoucherRequest  $request
     * @param  \App\Models\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateVoucherRequest $request)
    {
        $voucher = Voucher::where("name", "=", $request->name)->exists();

        if ($voucher) {
            return response()->json([
                "success" => false,
                "errors" => "Voucher name has already existsed"
            ]);
        }

        $query = Voucher::where("id", "=", $request->id);

        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Can't update with invalid Voucher ID"
            ]);
        }

        $filtered = $request->except(["expiredDate"]);
        $voucher_get = $query->first();

        $result = $voucher_get->update($filtered);

        if (empty($result)) {
            return response()->json([
                "success" => false,
                "errors" => "An unexpected errors has occurred"
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Updated Voucher with ID = " . $request->id . " successfully"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $voucherID)
    {
        $query = Voucher::where("id", "=", $voucherID);

        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Can't delete with invalid Voucher ID"
            ]);
        }

        $voucher = $query->first();

        // If state is equal to 1, then (Soft) Delete voucher
        if ((int) $request->state === 1) {
            
            // Check if voucher has already been deleted yet
            if ($voucher->deleted !== null) {
                return response()->json([
                    "success" => false,
                    "errors" => "Voucher with ID = " . $voucherID . " has already been (Soft) deleted"
                ]);
            }

            $voucher->deleted = 1;

            $result = $voucher->save();

            if (!$result) {
                return response()->json([
                    "success" => false,
                    "errors" => "An unexpected errors has occurred"
                ]);
            }

            return response()->json([
                "success" => true,
                "message" => "Deleted Voucher with ID = " . $voucherID . " successfully"
            ]);
        }
        // If not then Reverse (Soft) Delete
        else {
            // Check if voucher has already been reversed deletetion yet
            if ($voucher->deleted !== 1) {
                return response()->json([
                    "success" => false,
                    "errors" => "Voucher with ID = " . $voucherID . " has already been reversed (Soft) deleteion process"
                ]);
            }

            $voucher->deleted = null;

            $result = $voucher->save();

            if (!$result) {
                return response()->json([
                    "success" => false,
                    "errors" => "An unexpected errors has occurred"
                ]);
            }

            return response()->json([
                "success" => true,
                "message" => "Reversed Deletion new Voucher with ID = " . $voucherID . " successfully"
            ]);
        }
    }
}
