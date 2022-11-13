<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Customer;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFeedBackRequest;
use App\Http\Requests\UpdateFeedBackRequest;
use App\Http\Resources\V1\FeedBackDetailCollection;
use App\Http\Resources\V1\FeedBackDetailResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeedBackController extends Controller
{
    // **** Feedback **** \\
    public function viewFeedBack(Request $request)
    {
        $customer = Customer::find($request->user()->id);

        $feedbacks = DB::table("customer_product_feedback")
            ->where("customer_id", "=", $customer->id)
            ->exists();

        if (!$feedbacks) {
            return response()->json([
                "success" => false,
                "errors" => "This user hasn't made any feedback yet"
            ]);
        }

        return response()->json([
            "success" => true,
            "data" => new FeedBackDetailCollection($customer->customer_product_feedback)
        ]);
    }

    public function feedbackDetail(Request $request)
    {
        // $request->id is Feedback ID in customer_product_feedback table
        $customer = Customer::find($request->user()->id);

        $query = $customer->customer_product_feedback()
            ->wherePivot("customer_product_feedback.id", "=", $request->id);

        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Something went wrong, please rechack Feedback ID"
            ]);
        }

        return response()->json([
            "success" => true,
            "data" => new FeedBackDetailResource($query->first())
        ]);
    }

    public function storeFeedBack(StoreFeedBackRequest $request)
    {
        // Check validation for customer_id 
        $customer = Customer::find($request->user()->id);

        // Check customer has bought product yet before created a feedback
        $orders_customers = Order::where("customer_id", "=", $customer->id)->get();

        for ($i = 0; $i < sizeof($orders_customers); $i++) {
            $product = Product::find($request->product_id);
            $check = DB::table("order_product")
                ->where("order_id", "=", $orders_customers[$i]->id)
                ->where("product_id", "=", $product->id)
                ->exists();

            if (!$check) continue;
            // Can't do a foreach loop to check value in pivot table for some reason. It can't check null value

            $customer->customer_product_feedback()->attach($product, [
                "quality" => $request->quality,
                "comment" => $request->comment,
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ]);

            return response()->json([
                "success" => true,
                "message" => "Created feedback product Successfully"
            ]);
        }

        return response()->json([
            "success" => false,
            "errors" => "You have to bought this product before making a feedback for it"
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
                "updated_at" => date("Y-m-d H:i:s"),
            ]);

        if (!$result) {
            return response()->json([
                "success" => false,
                "errors" => "Either data not change or Product ID/ order_product ID is invalid"
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
                "errors" => "Product ID is invalid"
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Deleted feedback of Product ID = " . $productId->id . " successfully"
        ]);
    }
}
