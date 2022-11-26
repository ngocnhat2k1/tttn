<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\QualityStatusEnum;
use App\Models\Customer;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Get\GetAdminBasicRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class FeedBackAdminController extends Controller
{
    // **** Feedback **** \\
    public function paginator($arr, $request)
    {
        $total = count($arr);
        $per_page = 10;
        $current_page = $request->input("page") ?? 1;

        $starting_point = ($current_page * $per_page) - $per_page;

        $arr = array_slice($arr, $starting_point, $per_page, true);

        $arr = new LengthAwarePaginator($arr, $total, $per_page, $current_page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return $arr;
    }

    public function all(GetAdminBasicRequest $request)
    {
        $customer_product_feedback = Customer::with("customer_product_feedback")->get();

        $data = [];
        $index = 0;

        // First loop for customer
        for ($i = 0; $i < sizeof($customer_product_feedback); $i++) {
            // Second loop for Products
            for ($j = 0; $j < sizeof($customer_product_feedback[$i]['customer_product_feedback']); $j++) {
                $data[$index]['id'] = $customer_product_feedback[$i]['customer_product_feedback'][$j]['pivot']->id;

                $data[$index]['customerId'] = $customer_product_feedback[$i]->id;
                $data[$index]['firstName'] = $customer_product_feedback[$i]->first_name;
                $data[$index]['lastName'] = $customer_product_feedback[$i]->last_name;

                $data[$index]['productId'] = $customer_product_feedback[$i]['customer_product_feedback'][$j]->id;
                $data[$index]['productName'] = $customer_product_feedback[$i]['customer_product_feedback'][$j]->name;
                $data[$index]['img'] = $customer_product_feedback[$i]['customer_product_feedback'][$j]->img;

                // $categories = DB::table("category_product")
                //     ->where("product_id", "=", $customer_product_feedback[$i]['customer_product_feedback'][$j]->id)
                //     ->get();

                // for ($k = 0; $k < sizeof($categories); $k++) {
                //     $category = Category::where("id", "=", $categories[$k]->id)->first();
                //     $data[$i]['products'][$j]['categories'][$k]['id']= $category->id;
                //     $data[$i]['products'][$j]['categories'][$k]['name']= $category->name;
                // }

                $data[$index]['quality'] = $customer_product_feedback[$i]['customer_product_feedback'][$j]['pivot']->quality;
                $data[$index]['rating'] = QualityStatusEnum::getQualityAttribute($data[$i]['quality']);
                $data[$index]['comment'] = $customer_product_feedback[$i]['customer_product_feedback'][$j]['pivot']->comment;
                $data[$index]['createdAt'] = date_format($customer_product_feedback[$i]['customer_product_feedback'][$j]['pivot']->created_at, "d/m/Y H:i:s");
                $data[$index]['updatedAt'] = date_format($customer_product_feedback[$i]['customer_product_feedback'][$j]['pivot']->updated_at, "d/m/Y H:i:s");
                $index++;
            }
        }

        return $this->paginator($data, $request);
    }

    public function show(GetAdminBasicRequest $request)
    {
        $query = DB::table("customer_product_feedback")->where("id", "=", $request->id);

        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Something went wrong, please rechack Feedback ID"
            ]);
        }

        $customer_product_feedback = $query->first();

        // Query to get customer and product info
        $customer_query = Customer::find($customer_product_feedback->customer_id);
        $product = Product::where("id", "=", $customer_product_feedback->product_id);

        if (!$customer_query->exists() || !$product->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Feedback has some invalid information, please double check database before displaying"
            ]);
        }

        $customer = $customer_query->first();
        $product = $product->first();

        $data = [
            "customerId" => $customer->id,
            "firstName" => $customer->first_name,
            "lastName" => $customer->last_name,
            "productId" => $product->id,
            "productName" => $product->name,
            "img" => $product->img,
            "quality" => $customer_product_feedback->quality,
            "rating" => QualityStatusEnum::getQualityAttribute($customer_product_feedback->quality),
            "comment" => $customer_product_feedback->comment,
            "createdAt" => date_format($customer->customer_product_feedback[0]->pivot->created_at, "d/m/Y H:i:s"),
            "updatedAt" => date_format($customer->customer_product_feedback[0]->pivot->updated_at, "d/m/Y H:i:s"),
        ];

        return response()->json([
            "success" => true,
            "data" => $data
        ]);

        // return response()->json([
        //     "success" => true,
        //     "data" => new FeedBackDetailResource($query->first())
        // ]);
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
