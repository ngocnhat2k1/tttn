<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\QualityStatusEnum;
use App\Models\Customer;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Delete\DeleteCustomerRequest;
use App\Http\Requests\Customer\Get\GetCustomerBasicRequest;
use App\Http\Requests\Customer\Store\StoreFeedBackRequest;
use App\Http\Requests\Customer\Update\UpdateFeedBackRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class FeedBackController extends Controller
{
    // **** Feedback **** \\
    public function paginator($arr, $request, $number_per_page)
    {
        $total = count($arr);
        $per_page = $number_per_page;
        $current_page = $request->input("page") ?? 1;

        $starting_point = ($current_page * $per_page) - $per_page;

        $arr = array_slice($arr, $starting_point, $per_page, true);

        $arr = new LengthAwarePaginator($arr, $total, $per_page, $current_page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return $arr;
    }

    public function viewFeedBack(GetCustomerBasicRequest $request)
    {
        $feedbacks = DB::table("customer_product_feedback")
            ->where("customer_id", "=", $request->user()->id)
            ->exists();

        if (!$feedbacks) {
            return response()->json([
                "success" => false,
                "errors" => "Người dùng này chưa tạo phản hồi cho bất kỳ sản phẩm nào."
            ]);
        }

        $customer_product_feedback = Customer::with("customer_product_feedback")->where("id", "=", $request->user()->id)->get();

        $data = [];

        // Second loop for Products
        for ($j = 0; $j < sizeof($customer_product_feedback[0]['customer_product_feedback']); $j++) {
            $data[$j]['id'] = $customer_product_feedback[0]['customer_product_feedback'][$j]['pivot']->id;
            $data[$j]['productId'] = $customer_product_feedback[0]['customer_product_feedback'][$j]->id;
            $data[$j]['productName'] = $customer_product_feedback[0]['customer_product_feedback'][$j]->name;
            $data[$j]['img'] = $customer_product_feedback[0]['customer_product_feedback'][$j]->img;

            // $categories = DB::table("category_product")
            //     ->where("product_id", "=", $customer_product_feedback[0]['customer_product_feedback'][$j]->id)
            //     ->get();

            // for ($k = 0; $k < sizeof($categories); $k++) {
            //     $category = Category::where("id", "=", $categories[$k]->id)->first();
            //     $data[0]['products'][$j]['categories'][$k]['id']= $category->id;
            //     $data[0]['products'][$j]['categories'][$k]['name']= $category->name;
            // }


            // $data[$j]['quality'] = $customer_product_feedback[0]['customer_product_feedback'][$j]['pivot']->quality;
            // $data[$j]['rating'] = QualityStatusEnum::getQualityAttribute($data[$j]['quality']);
            $data[$j]['comment'] = $customer_product_feedback[0]['customer_product_feedback'][$j]['pivot']->comment;
            $data[$j]['createdAt'] = date_format($customer_product_feedback[0]['customer_product_feedback'][$j]['pivot']->created_at, "d/m/Y H:i:s");
            $data[$j]['updatedAt'] = date_format($customer_product_feedback[0]['customer_product_feedback'][$j]['pivot']->updated_at, "d/m/Y H:i:s");
        }

        return $this->paginator($data, $request, 10);
    }

    // View all feedback attach selected product
    public function feedbacksProduct(GetCustomerBasicRequest $request)
    {
        // $request->id is Feedback ID in customer_product_feedback table
        $customer = Customer::find($request->user()->id);
        $queryProduct = Product::where("id", "=", $request->id);

        if (!$queryProduct->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Sản phẩm không tồn tại."
            ]);
        }

        $product = $queryProduct->first();

        $query = DB::table("customer_product_feedback")
            ->where("customer_id", "=", $customer->id)
            ->where("product_id", "=", $product->id);

        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Phản hồi của sản phẩm không tồn tại."
            ]);
        }

        $data = $query->get();
        $arr = [];
        
        for ($i = 0; $i < sizeof($data); $i++) {
            $arr[$i]['id'] = $data[$i]->id;
            $arr[$i]['productId'] = $product->id;
            $arr[$i]['name'] = $product->name;
            $arr[$i]['img'] = $product->img;
            // $arr[$i]['quality'] = $data[$i]->quality;
            // $arr[$i]['rating'] = QualityStatusEnum::getQualityAttribute($data[$i]->quality);
            $arr[$i]['comment'] = $data[$i]->comment;
            $arr[$i]['createdAt'] = date("d/m/Y H:i:s", strtotime($data[$i]->created_at));
            $arr[$i]['updatedAt'] = date("d/m/Y H:i:s", strtotime($data[$i]->updated_at));
        }

        return $this->paginator($arr, $request, 5);
    }

    // View feedback detail
    public function feedbackDetail(GetCustomerBasicRequest $request)
    {
        // $request->id is Feedback ID in customer_product_feedback table
        $customer = Customer::find($request->user()->id);

        $query = $customer->customer_product_feedback()
            ->wherePivot("customer_product_feedback.id", "=", $request->id);

        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Phản hồi không tồn tại."
            ]);
        }

        $customer_product_feedback = $query->first();

        $customer = Customer::where("id", "=", $customer_product_feedback->pivot->customer_id);
        $product = Product::where("id", "=", $customer_product_feedback->pivot->product_id);

        if (!$customer->exists() || !$product->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Phản hồi sản phẩm có vài thông tin không hợp lệ, vui lòng kiểm tra trước khi hiển thị lên màn hình."
            ]);
        }

        $customer = $customer->first();
        $product = $product->first();

        $data = [
            "customerId" => $customer->id,
            "firstName" => $customer->first_name,
            "lastName" => $customer->last_name,
            "productId" => $product->id,
            "productName" => $product->name,
            "img" => $product->img,
            // "quality" => $customer_product_feedback->pivot->quality,
            // "rating" => QualityStatusEnum::getQualityAttribute($customer_product_feedback->pivot->quality),
            "comment" => $customer_product_feedback->pivot->comment,
            "createdAt" => date_format($customer_product_feedback->pivot->created_at, "d/m/Y H:i:s"),
            "updatedAt" => date_format($customer_product_feedback->pivot->updated_at, "d/m/Y H:i:s"),
        ];

        return response()->json([
            "success" => true,
            "data" => $data
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
                // "quality" => $request->quality,
                "comment" => $request->comment,
            ]);

            return response()->json([
                "success" => true,
                "message" => "Tạo thành công Phản hồi của Sản phẩm có ID = " . $product->id
            ]);
        }

        return response()->json([
            "success" => false,
            "errors" => "Bạn cần phải mua sản phẩm này trước khi thực hiện tạo phản hồi cho sản phẩm này."
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
                // "quality" => $request->quality,
                "comment" => $request->comment,
            ]);

        if (!$result) {
            return response()->json([
                "success" => false,
                "errors" => "Thông tin không thay đổi hoặc ID Sản phẩm/ ID Khách hàng không hợp lệ."
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Cập nhật thông tin Phản hồi của sản phẩm thành công."
        ]);
    }

    public function destroyFeedBack(DeleteCustomerRequest $productId)
    {
        // REMEMBER: This is a real delete not a soft delete.

        $customer = Customer::find($productId->user()->id);

        $product = Product::find($productId->id);

        $result = $customer->customer_product_feedback()->detach($product);

        if (empty($result)) {
            return response()->json([
                "success" => false,
                "errors" => "Phản hồi của Sản phẩm có ID = " . $productId->id . " không tồn tại."
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Xóa thành công phản hồi của Sản phẩm có ID = " . $productId->id
        ]);
    }
}
