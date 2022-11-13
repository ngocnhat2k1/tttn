<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Customer;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFeedBackRequest;
use App\Http\Requests\UpdateFeedBackRequest;
use App\Http\Resources\V1\FeedBackDetailCollection;
use App\Http\Resources\V1\FeedBackDetailResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeedBackAdminController extends Controller
{
    // **** Feedback **** \\
    public function all()
    {
        // Using Query builder to query all data from pivot table "customer_product_feedback"
    }

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
