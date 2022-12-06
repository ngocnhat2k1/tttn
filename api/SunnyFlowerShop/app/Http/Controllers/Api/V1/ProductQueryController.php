<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CategoryListResource;
use App\Http\Resources\V1\ProductDetailResource;
use App\Http\Resources\V1\ProductListCollection;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductQueryController extends Controller
{
    // Use for Paginate
    public function paginator($arr, $request, $amount)
    {
        $total = count($arr);
        $per_page = $amount;
        $current_page = $request->input("page") ?? 1;

        $starting_point = ($current_page * $per_page) - $per_page;

        $arr = array_slice($arr, $starting_point, $per_page, true);;
        $arr = new LengthAwarePaginator($arr, $total, $per_page, $current_page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return $arr;
    }

    public function arrival()
    {
        $products = Product::orderBy("created_at", "DESC")
            ->where("status", "<>", 0)
            ->where("deleted_at", "=", null)
            ->take(8)->get();

        return new ProductListCollection($products);
    }

    public function sale()
    { // Base on new arrival
        $products_sale = Product::where("percent_sale", "<>", "0")
            ->where("status", "<>", 0)
            ->where("deleted_at", "=", null)
            ->orderBy("created_at", "DESC")->take(8)->get();

        return new ProductListCollection($products_sale);
    }

    public function mostfavoriteProducts()
    {
        // Count duplicate products
        $products_filter = DB::table("customer_product_favorite")
            ->select("product_id", DB::raw('count(product_id) as count'))
            ->groupBy('product_id')
            ->orderBy('count', 'DESC')
            ->get()
            ->take(8);

        $products_most_favorite = [];

        for ($i = 0; $i < sizeof($products_filter); $i++) {
            $product = Product::where("id", "=", $products_filter[$i]->product_id)->first();

            if ($product->status === 0 || $product->deleted_at !== null) continue;

            $products_most_favorite[$i]["productId"] = $product->id;
            $products_most_favorite[$i]["name"] = $product->name;
            $products_most_favorite[$i]["description"] = $product->description;
            $products_most_favorite[$i]["price"] = $product->price;
            $products_most_favorite[$i]["percentSale"] = $product->percent_sale;
            $products_most_favorite[$i]["img"] = $product->img;
            $products_most_favorite[$i]["quantity"] = $product->quantity;
        }

        return $products_most_favorite;
    }

    public function best()
    {
        // Count duplicate products
        $products_filter = DB::table("order_product")
            ->select("product_id", DB::raw('count(product_id) as count'))
            ->groupBy('product_id')
            ->orderBy('count', 'DESC')
            ->get()
            ->take(8);

        $products_best_seller = [];

        for ($i = 0; $i < sizeof($products_filter); $i++) {
            $product = Product::where('id', "=", $products_filter[$i]->product_id)->first();

            if ($product->status === 0 || $product->deleted_at !== null) continue;

            $products_best_seller[$i]["id"] = $product->id;
            $products_best_seller[$i]["name"] = $product->name;
            $products_best_seller[$i]["description"] = $product->description;
            $products_best_seller[$i]["price"] = $product->price;
            $products_best_seller[$i]["percentSale"] = $product->percent_sale;
            $products_best_seller[$i]["img"] = $product->img;
            $products_best_seller[$i]["quantity"] = $product->quantity;
        }

        return $products_best_seller;
    }

    public function trending(Request $request)
    {
        $orders = Order::orderBy("created_at", "ASC")->take(100)->get();

        // Array used to store 100 orders ordered by "created_at" in ascending
        $arr_orders = [];
        $arr_orders_2 = [];
        $current_date = date("Y-m-d H:i:s");
        $index = 0;

        // Get all recent orders meet the require days (base on {day} calculated on second)
        for ($i = 0; $i < sizeof($orders); $i++) {
            if ((strtotime($current_date) - strtotime($orders[$i]['created_at'])) >= ((int) $request->day) * 60 * 60 * 24) {
                // $arr_orders[] = $orders[$i]['id'];
                $arr_orders[] = $orders[$i]['id'];
                $arr_orders_2[] = strtotime($current_date) - strtotime($orders[$i]['created_at']);
                $index++;
            }
        }

        $arr_products = [];
        // Get all products store in order
        for ($i = 0; $i < sizeof($arr_orders); $i++) {
            // dd($arr_orders[$i]);
            $products_in_order = DB::table("order_product")
                ->select("product_id")
                ->where("order_id", "=", $arr_orders[$i])
                ->get();

            if ($products_in_order->count() === 0) {
                // return false and quit
                continue;
            }

            if (sizeof($arr_products) === 0) { // Run first time
                for ($j = 0; $j < sizeof($products_in_order); $j++) {
                    $arr_products[] = $products_in_order[$j]->product_id;
                }
                continue;
            }

            for ($k = 0; $k < sizeof($products_in_order); $k++) {
                // dd($products_in_order[$k]);
                if (!in_array($products_in_order[$k]->product_id, $arr_products)) {
                    $arr_products[] = $products_in_order[$k]->product_id;
                }
            }
        }

        $arr_products_filter = [];
        $index = 0;

        // Check if array is below 10 then use it without product filter
        if (sizeof($arr_products) <= 10) {
            for ($i = 0; $i < sizeof($arr_products); $i++) {
                $product = Product::where("id", "=", $arr_products[$i])->first();

                if ($product->status === 0 || $product->deleted_at !== null) continue;

                $arr_products_filter[$index]['id'] = $product->id;
                $arr_products_filter[$index]['name'] = $product->name;
                $arr_products_filter[$index]['description'] = $product->description;
                $arr_products_filter[$index]['price'] = $product->price;
                $arr_products_filter[$index]['img'] = $product->img;
                $arr_products_filter[$index]['percentSale'] = $product->percent_sale;
                $arr_products_filter[$index]['quantity'] = $product->quantity;
                $index++;
            }

            return $arr_products_filter;
        }

        for ($i = 0; $i < sizeof($arr_products); $i++) {
            $products_filter = DB::table("order_product")
                ->select("product_id", DB::raw('count(product_id) as count'))
                ->groupBy('product_id')
                ->where("product_id", "=", $arr_products[$i])
                ->orderBy('count', 'DESC')
                ->get();

            if ($products_filter[0]->count >= 2) {
                $product = Product::where("id", "=", $products_filter[0]->product_id)->first();

                if ($product->status === 0 || $product->deleted_at !== null) continue;

                $arr_products_filter[$index]['id'] = $product->id;
                $arr_products_filter[$index]['name'] = $product->name;
                $arr_products_filter[$index]['description'] = $product->description;
                $arr_products_filter[$index]['price'] = $product->price;
                $arr_products_filter[$index]['img'] = $product->img;
                $arr_products_filter[$index]['percentSale'] = $product->percent_sale;
                $arr_products_filter[$index]['quantity'] = $product->quantity;
                $index++;
            }
        }

        // If after product filter there still no product then reuse on sale product function
        if (sizeof($arr_products_filter) === 0) {
            $this->sale();
        }

        // If everything went well then return result
        return $arr_products_filter;
    }

    public function indexCustomer(Request $request)
    {
        $data = Product::with("categories")->get();

        if (!empty($request->get('orderBy'))) {
            $order_type = $request->get('orderBy');

            $data = Product::with("categories")->orderBy("price", $order_type)->get();
        }

        $arr = [];
        // $arr['customer_id'] = $customer->id;

        for ($i = 0; $i < sizeof($data); $i++) {
            if ($data[$i]->deleted_at !== null || $data[$i]->status !== 1) {
                continue;
            }
            $arr[$i]['id'] = $data[$i]->id;
            $arr[$i]['name'] = $data[$i]->name;
            $arr[$i]['description'] = $data[$i]->description;
            $arr[$i]['price'] = $data[$i]->price;
            $arr[$i]['percentSale'] = $data[$i]->percent_sale;
            $arr[$i]['img'] = $data[$i]->img;
            $arr[$i]['quantity'] = $data[$i]->quantity;
            $arr[$i]['status'] = $data[$i]->status;

            for ($j = 0; $j < sizeof($data[$i]->categories); $j++) {
                $arr[$i]['categories'][$j]['id'] = $data[$i]->categories[$j]->id;
                $arr[$i]['categories'][$j]['name'] = $data[$i]->categories[$j]->name;
            }
        }

        return $this->paginator($arr, $request, 8);
    }

    public function show(Request $request)
    {
        $data = Product::find($request->id);

        if (empty($data) || $data->deleted_at !== null) {
            return response()->json([
                "success" => false,
                "errors" => "Sản phẩm không tồn tại."
            ]);
        }

        $average_quality = DB::table("customer_product_feedback")
            ->where("product_id", "=", $data->id);

        // calculate average of total quality that product has
        // $quality = 0;

        // /** Checking if quality of feedback has been made */
        // // If not then average of total quality is 0
        // if (!$average_quality->exists()) {
        //     $quality = 0;
        // }
        // // If so then calculate it
        // else {
        //     $total = $average_quality->get(); // Get all quality feedback

        //     for ($i = 0; $i < sizeof($total); $i++) { // Sum all quality to make an average calculation
        //         $quality += $total[$i]->quality;
        //     }

        //     $quality = $quality / sizeof($total);

        //     $float_point = explode(".", $quality);

        //     if (sizeof($float_point) >= 2) {
        //         $decimal_number = (int)$float_point[1];

        //         while ($decimal_number > 10) {
        //             $decimal_number = $decimal_number / 10;
        //         }

        //         if ($decimal_number >= 5) {
        //             $quality = ceil($quality);
        //         } else {
        //             $quality = floor($quality);
        //         }
        //     }
        // }

        // $data['quality'] = $quality;

        return response()->json([
            "success" => true,
            "data" => new ProductDetailResource($data)
        ]);
    }

    public function filterProducts(Request $request)
    {
        $search_category = Category::where("id", "like", $request->filter)->get();

        $data = [];
        $index = 0;

        if ($search_category->count() !== 0) {
            for ($i = 0; $i < sizeof($search_category); $i++) {
                $products = DB::table("category_product")
                    ->where("category_id", "=", $search_category[$i]->id)
                    ->get();

                for ($j = 0; $j < sizeof($products); $j++) {
                    $product = Product::where("id", "=", $products[$j]->id)->first();
                    if ($product->deleted_at !== null) {
                        continue;
                    }
                    $data[$index] = $product;
                    $index++;
                }
            }
        }
        return new ProductListCollection($this->paginator($data, $request, 8));
    }

    // Use for searching bar
    public function searchProduct(Request $request)
    {
        $value = "%" . $request->value . "%";

        $search = Product::where("name", "like", "$value")
            ->where("deleted_at", "=", null);

        if ($search->get()->count() === 0) {
            $category_value = "%" . $request->value . "%";

            $search_category = Category::where("name", "like", "$category_value")->get();

            if ($search_category->count() !== 0) {
                $data = [];
                $index = 0;

                for ($i = 0; $i < sizeof($search_category); $i++) {
                    $products = DB::table("category_product")
                        ->where("category_id", "=", $search_category[$i]->id)
                        ->get();

                    for ($j = 0; $j < sizeof($products); $j++) {
                        $product = Product::where("id", "=", $products[$j]->id)->first();
                        if ($product->deleted_at !== null) {
                            continue;
                        }
                        $data[$index] = $product;
                        $index++;
                    }
                }
                return new ProductListCollection($this->paginator($data, $request, 8));
            }

            return response()->json([
                "success" => false,
                "errors" => "Không sản phẩm nào được tìm thấy sản phẩm."
            ]);
        }

        return new ProductListCollection($search->paginate(8));
    }

    public function searchTopBar(Request $request)
    {
        $value = "%" . $request->value . "%";

        $search = Product::where("name", "like", "$value");

        if ($search->get()->count() === 0) {
            $category_value = "%" . $request->value . "%";

            $search_category = Category::where("name", "like", "$category_value")->get();

            if ($search_category->count() !== 0) {
                $data = [];
                $index = 0;

                for ($i = 0; $i < sizeof($search_category); $i++) {
                    $products = DB::table("category_product")
                        ->where("category_id", "=", $search_category[$i]->id)
                        ->get();

                    for ($j = 0; $j < sizeof($products); $j++) {
                        $product = Product::where("id", "=", $products[$j]->id)->first();
                        $data[$index] = $product;
                        $index++;
                    }
                }

                if (sizeof($data) <= 5) {
                    $display_more = null;
                } else {
                    $data = array_slice($data, 0, 5, true);
                    $display_more = route("filter.search", ['value' => $request->value]);
                }


                return [
                    "data" => new ProductListCollection($data),
                    "moreProduct" => $display_more
                ];
            }

            return response()->json([
                "success" => false,
                "errors" => "Không tìm thấy sản phẩm."
            ]);
        }

        if (sizeof($search->get()) <= 5) {
            $display_more = null;
        } else {
            $display_more = route("filter.search", ['value' => $request->value]);
        }

        return [
            "data" => new ProductListCollection($search->take(5)->get()),
            "moreProduct" => $display_more
        ];
    }

    public function allCategories() {
        $categories = Category::all();

        return CategoryListResource::collection($categories);
    }

    // View all feedback attach selected product
    public function feedbacksProduct(Request $request)
    {
        // $request->id is Feedback ID in customer_product_feedback table
        $queryProduct = Product::where("id", "=", $request->id);

        if (!$queryProduct->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Sản phẩm không tồn tại."
            ]);
        }

        $product = $queryProduct->first();

        $query = DB::table("customer_product_feedback")
            ->where("product_id", "=", $product->id);

        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Sản phẩm chưa có phản hồi."
            ]);
        }

        $data = $query->get();
        $arr = [];

        for ($i = 0; $i < sizeof($data); $i++) {
            $customer = Customer::find($data[$i]->customer_id);

            if ($customer->disabled !== null) continue; // If customer is disabled then skip

            $arr[$i]['customerId'] = $customer->id;
            $arr[$i]['firstName'] = $customer->first_name;
            $arr[$i]['lastName'] = $customer->last_name;
            $arr[$i]['avatar'] = $customer->avatar;
            $arr[$i]['defaultAvatar'] = $customer->default_avatar;

            // $arr[$i]['quality'] = $data[$i]->quality;
            // $arr[$i]['rating'] = QualityStatusEnum::getQualityAttribute($data[$i]->quality);
            $arr[$i]['comment'] = $data[$i]->comment;
            $arr[$i]['createdAt'] = date("d/m/Y H:i:s", strtotime($data[$i]->created_at));
            $arr[$i]['updatedAt'] = date("d/m/Y H:i:s", strtotime($data[$i]->updated_at));
        }

        return $this->paginator($arr, $request, 5);
    }
}
