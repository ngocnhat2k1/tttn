<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Delete\DeleteAdminBasicRequest;
use App\Http\Requests\Admin\Get\GetAdminBasicRequest;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class FavoriteProductCustomerController extends Controller
{
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

    public function index(Customer $customer, GetAdminBasicRequest $request)
    {
        $favorite_products = DB::table("customer_product_favorite")
            ->where("customer_id", "=", $customer->id)->get();

        if ($favorite_products->count() === 0) {
            return response()->json([
                "success" => false,
                "message" => "Chưa có sản phẩm nào được thêm vào mục yêu thích."
            ]);
        }

        $data = [];
        // First loop for get all favorite product from pivot table
        for ($i = 0; $i < sizeof($favorite_products); $i++) {
            $product = Product::where("id", "=", $favorite_products[$i]->product_id)->first();

            // Get all category connect to product
            $categories = DB::table("category_product")
                ->where("product_id", "=", $product->id)
                ->get();

            $data[$i]['id'] = $product->id;
            $data[$i]['name'] = $product->name;
            $data[$i]['price'] = $product->price;
            $data[$i]['percentSale'] = $product->percent_sale;
            $data[$i]['quantity'] = $product->quantity;
            $data[$i]['status'] = $product->status;
            $data[$i]['deletedAt'] = $product->deleted_at;

            for ($j = 0; $j < sizeof($categories); $j++) { // Second loop for category
                $category = Category::where("id", "=", $categories[$j]->category_id)->first();
                
                $data[$i]['categories'][$j]['id'] = $category->id;
                $data[$i]['categories'][$j]['name'] = $category->name;
            }
        }

        return $this->paginator($data, $request);
        // return new ProductListCollection($customer->customer_product_favorite);
    }

    public function destroy(DeleteAdminBasicRequest $request, Customer $customer, Product $product)
    {
        $product_data = Product::find($product->id);

        $check = DB::table("customer_product_favorite")
            ->where("customer_id", "=", $customer->id)
            ->where("product_id", "=", $product_data->id)
            ->exists();

        if (empty($check)) {
            return response()->json([
                "success" => false,
                "errors" => "Không thể xóa sản phẩm không tồn tại khỏi mục yêu thích."
            ]);
        }

        $data = $customer->customer_product_favorite()->detach($product_data);

        if (empty($data)) {
            return response()->json([
                "success" => false,
                "errors" => "Đã có lỗi xảy ra trong quá trình vận hành."
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Xóa sản phẩm khỏi mục yêu thích thành công."
        ]);
    }
}
