<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFeedBackRequest;
use App\Http\Requests\UpdateFeedBackRequest;
use App\Http\Resources\V1\FeedBackDetailCollection;
use App\Http\Resources\V1\FeedBackDetailResource;
use App\Models\Product;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function viewFeedBack(Request $productId)
    {
        $customer = Customer::find($productId->user()->id);

        return response()->json([
            "success" => true,
            "data" => new FeedBackDetailCollection($customer->customer_product_feedback)
        ]);
    }

    public function feedbackDetail(Request $request)
    {
        $customer = Customer::find($request->user()->id);

        $data = $customer->customer_product_feedback()->wherePivot("customer_product_feedback.id", "=", "$request->id")->first();

        if (!$data) {
            return response()->json([
                "success" => false,
                "errors" => "Something went wrong"
            ]);
        }

        return response()->json([
            "success" => true,
            "data" => new FeedBackDetailResource($data)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCustomerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function storeFavorite(StoreCustomerRequest $request)
    {
        //
    }

    public function storeFeedBack(StoreFeedBackRequest $request)
    {
        // Will add condition for product are purchased or not (check in orders table)
        // Check validation for customer_id 
        $customer = Customer::find($request->user()->id);

        $request['customer_id'] = $customer->id;

        $product = Product::find($request->product_id);

        // Can't do a foreach loop to check value in pivot table for some reason. It can't check null value

        $customer->customer_product_feedback()->attach($product, [
            "quality" => $request->quality,
            "comment" => $request->comment,
        ]);

        return response()->json([
            "success" => true,
            "message" => "Created feedback product Successfully"
        ]);
    }

    public function updateFeedBack(UpdateFeedBackRequest $request)
    {
        // "$request" is Feedback ID
        // Can't do a foreach loop to check value in pivot table for some reason. It can't check null value
        $customer = Customer::find($request->user()->id);

        $request['customer_id'] = $customer->id;

        $product = Product::find($request->product_id);

        $result = $customer->customer_product_feedback()
            ->wherePivot("id", "=", $request->id)
            ->updateExistingPivot($product, [
                "quality" => $request->quality,
                "comment" => $request->comment,
            ]);

        if (!$result) {
            return response()->json([
                "success" => false,
                "errors" => "Either data not change or product id/ order_product id is invalid"
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Updated feedback product Successfully"
        ]);
    }

    public function destroyFeedBack(Request $productId)
    {
        // REMEMBER: This is a real delete not a soft delete.

        $customer = Customer::find($productId->user()->id);

        $product = Product::find($productId->id);

        $result = $customer->customer_product_feedback()->detach($product);

        if (empty($result)) {
            return response()->json([
                "success" => false,
                "errors" => "Feedback of Product ID " . $productId->id . " has already been removed"
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Deleted feedback of Product ID " . $productId->id . " successfully"
        ]);
    }

    public function storeAddress()
    {
        //
    }
}
