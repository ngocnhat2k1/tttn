<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Product;
use App\Http\Requests\Admin\Store\StoreProductRequest;
use App\Http\Requests\Admin\Update\UpdateProductRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Delete\DeleteAdminBasicRequest;
use App\Http\Requests\Admin\Delete\DeleteMultipleProductRequest;
use App\Http\Requests\Admin\Get\GetAdminBasicRequest;
use App\Http\Requests\BulkInsertProductRequest;
use App\Http\Resources\V1\ProductDetailResource;
use App\Http\Resources\V1\ProductListCollection;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(GetAdminBasicRequest $request)
    {
        // $data = Product::with("categories")->paginate();
        $data = Product::with("categories");
        $count = $data->get()->count();

        if (empty($count)) {
            return response()->json([
                "success" => false,
                "errors" => "Product list is empty"
            ]);
        }

        // Will change later, this is just temporary
        if (!empty($request->get("q"))) {
            $check = (int)$request->get("q");
            $column = "";
            $operator = "";
            $value = "";

            if ($check == 0) {
                $column = "name";
                $operator = "like";
                $value = "%" . $request->get("q") . "%";
            } else {
                $column = "id";
                $operator = "=";
                $value = $request->get("q");
            }

            $search = Product::where("$column", "$operator", "$value")->get();
        }

        // $count = DB::table("products")->count();

        // return response()->json([
        //     "success" => true,
        //     "total" => $count,
        //     "data" => new ProductListCollection($data)
        // ]);

        return new ProductListCollection($data->paginate(12)->appends($request->query()));
    }

    public function paginator($arr, $request)
    {
        $total = count($arr);
        $per_page = 12;
        $current_page = $request->input("page") ?? 1;

        $starting_point = ($current_page * $per_page) - $per_page;

        $arr = array_slice($arr, $starting_point, $per_page, true);

        $arr = new LengthAwarePaginator($arr, $total, $per_page, $current_page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return $arr;
    }

    public function indexAdmin(GetAdminBasicRequest $request)
    {
        $data = Product::with("categories")->get();

        if (!empty($request->get('orderBy'))) {
            $order_type = $request->get('orderBy');

            $data = Product::with("categories")->orderBy("price", $order_type)->get();
        }

        $arr = [];
        // $arr['customer_id'] = $customer->id;

        for ($i = 0; $i < sizeof($data); $i++) {
            if ($data[$i]->deleted_at !== null) {
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
            $arr[$i]['createdAt'] = date_format($data[$i]->created_at, "d/m/Y");

            for ($j = 0; $j < sizeof($data[$i]->categories); $j++) {
                $arr[$i]['categories'][$j]['id'] = $data[$i]->categories[$j]->id;
                $arr[$i]['categories'][$j]['name'] = $data[$i]->categories[$j]->name;
            }
        }

        // return $arr;
        return $this->paginator($arr, $request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProductRequest  $request
     * @return \Illuminate\Http\Response
     */

    public function store(StoreProductRequest $request)
    {
        $check_existed = Product::where("name", "=", $request->name)->exists();
        $check_existed_category = Category::where("id", "=", $request->categoryId)->exists();

        // Check if the existence of name product in database
        if ($check_existed) {
            return response()->json([
                'success' => false,
                'errors' => "Tên sản phẩm đã tồn tại."
            ]);
        }

        if (!$check_existed_category) {
            return response()->json([
                "success" => false,
                "errors" => "Tên danh mục sản phẩm không hợp lệ."
            ]);
        }

        $filtered = $request->except(["percentSale"]);
        $filtered['percent_sale'] = $request->percent_sale ?? 0; // Just in case percent_sale doesn't get filled

        $data = Product::create($filtered);

        // Checking if insert into database is isSuccess
        if (empty($data->id)) {
            return response()->json([
                "success" => false,
                "errors" => "Đã có lỗi xẩy ra trong quá trình vận hành!!"
            ]);
        }

        $data->categories()->attach($request->categoryId);

        // // Add each categories to pivot table "category_product"
        // for ($i = 0; $i < sizeof($filtered['category']); $i++) {
        //     $category_id = $filtered['category'][$i]['id'];

        //     $category = Category::find($category_id);

        //     // Checking category id - If it doesn't exist just skip
        //     if (empty($category)) {
        //         continue;
        //     }

        //     $data->categories()->attach($category_id);
        // }

        return response()->json([
            'success' => true,
            "message" => "Tạo sản phẩm thành công."
        ]);
    }

    // This function has been updated but haven't been tested yet
    public function bulkStore(BulkInsertProductRequest $request)
    {
        // Main Data use for blueprint
        $bulk = collect($request->all())->map(function ($arr, $key) {
            return Arr::except($arr, ["categoryId", "percentSale"]);
        });

        // Data use for searching in category table to insert to intermediate (category_product) table - $data is an array
        $data = $request->toArray();

        // Data use for insert into product table - $product is an array
        $products = $bulk->toArray();

        // Count variable to check how many product successfully added to database
        $count = 0;

        for ($i = 0; $i < sizeof($products); $i++) {
            // Check if data is already in database
            $check = Product::where("name", "=", ($products[$i]['name']))->first();

            // If product has already existed ==> skip
            if ($check) continue;

            // Insert value into product table with $products at $i index
            $result = Product::create($products[$i]);

            if (!$result) {
                return response()->json([
                    "success" => false,
                    "errors" => "Đã có lỗi xảy ra trong quá trình vận hành!!"
                ]);
            }

            $check_existed_category = Category::where("id", "=", $request->categoryId)->exists();
            if (!$check_existed_category) {
                continue;
            }

            $result->categories()->attach($request->categoryId);

            // Insert each category id to pivot table "category_product"
            // for ($j = 0; $j < sizeof($data[$i]['category']); $j++) {
            //     // Find Category ID in category table using $data variable
            //     $category_id = $data[$i]['category'][$j]["id"];
            //     $category = Category::find($category_id);
            //     $product = Product::find($result->id);

            //     $product->categories()->attach($category);
            // }

            $count++;
        }

        return response()->json([
            "success" => true,
            "message" => "Đã thêm thành công " . $count . " sản phẩm."
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(GetAdminBasicRequest $request)
    {
        $data = Product::find($request->id);

        if (empty($data) || $data->deleted_at !== null) {
            return response()->json([
                "success" => false,
                "errors" => "Product doesn't not exist"
            ]);
        }

        $average_quality = DB::table("customer_product_feedback")
            ->where("product_id", "=", $data->id);

        // calculate average of total quality that product has
        $quality = 0;

        /** Checking if quality of feedback has been made */
        // If not then average of total quality is 0
        if (!$average_quality->exists()) {
            $quality = 0;
        }
        // If so then calculate it
        else {
            $total = $average_quality->get(); // Get all quality feedback

            for ($i = 0; $i < sizeof($total); $i++) { // Sum all quality to make an average calculation
                $quality += $total[$i]->quality;
            }

            $quality = $quality / sizeof($total);

            $float_point = explode(".", $quality);

            if (sizeof($float_point) >= 2) {
                $decimal_number = (int)$float_point[1];

                while ($decimal_number > 10) {
                    $decimal_number = $decimal_number / 10;
                }

                if ($decimal_number >= 5) {
                    $quality = ceil($quality);
                } else {
                    $quality = floor($quality);
                }
            }
        }

        $data['quality'] = $quality;

        return response()->json([
            "success" => true,
            "data" => new ProductDetailResource($data)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */

    public function update(UpdateProductRequest $request, $productId)
    {
        $data = $request->except(['categoryId', "percentSale"]);

        // Checking Product ID
        $product = Product::find($productId);
        if (empty($product)) {
            return response()->json([
                'success' => false,
                'errors' => "ID Sản phẩm không hợp lệ."
            ]);
        }

        $check_existed = Product::where("name", "=", $request->name)->exists();
        $check_existed_category = Category::where("id", "=", $request->categoryId)->exists();

        if (!$check_existed_category) {
            return response()->json([
                "success" => false,
                "errors" => "ID Danh mục sản phẩm không hợp lệ."
            ]);
        }

        // Check if the existence of name product in database
        if ($check_existed) {
            return response()->json([
                'success' => false,
                'errors' => "Tên sản phẩm đã tồn tại."
            ]);
        }

        // Save all value was changed
        foreach ($data as $key => $value) {
            $product->{$key} = $value;
        }

        $result = $product->save();

        // If result is false, that means save process has occurred some issues
        if (!$result) {
            return response()->json([
                'success' => false,
                "errors" => "Đã có lỗi xảy ra trong quá trình vận hành!!"
            ]);
        }

        // Remove all existed categories from product to readd everything back
        $product->categories()->detach();

        $product->categories()->attach($request->categoryId);

        // Check product status
        if ($data['status'] === 0) { // if new status product is 0, then proceed to delete product out of "customer_product_cart"
            DB::table("customer_product_cart")
                ->where("product_id", "=", $product->id)
                ->delete();
        }

        // Checking all categories that product has to decide to attach new categories or skip
        // for ($i = 0; $i < sizeof($request['category']); $i++) {
        //     $category_id = $request['category'][$i]['id'];

        //     $category = Category::find($category_id);
        //     $product->categories()->attach($category);
        // }

        return response()->json([
            'success' => true,
            "message" => "Cập nhật thông tin sản phẩm thành công."
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */

    public function updateNoRequired(GetAdminBasicRequest $request)
    {
        // Validate value
        $data = Validator::make($request->all(), [
            "name" => "string|min:2|max:100",
            "description" => "string|min:10",
            "price" => "integer",
            "percentSale" => "integer|min:1|max:100",
            "img" => "string",
            "quantity" => "integer",
            "status" => "boolean",
            "categoryId" => "integer",
        ]);

        if ($data->fails()) {
            $errors = $data->errors();

            return response()->json([
                "success" => false,
                "errors" => $errors,
            ]);
        }

        // Checking Product ID
        $product = Product::find($request->id);
        if (empty($product)) {
            return response()->json([
                'success' => false,
                'errors' => "ID Sản phẩm không hợp lệ."
            ]);
        }

        $check_existed = Product::where("name", "=", $request->name)
            ->where("id", "<>", $request->id)
            ->exists();

        // Check if the existence of name product in database
        if ($check_existed) {
            return response()->json([
                'success' => false,
                'errors' => "Tên sản phẩm đã tồn tại."
            ]);
        }

        $product->name = $request->name ?? $product->name;
        $product->description = $request->description ?? $product->description;
        $product->price = $request->price ?? $product->price;
        $product->percent_sale = $request->percentSale ?? $product->percent_sale;
        $product->img = $request->img ?? $product->img;
        $product->quantity = $request->quantity ?? $product->quantity;
        $product->status = $request->status ?? $product->status;

        $result = $product->save();

        // If result is false, that means save process has occurred some issues
        if (!$result) {
            return response()->json([
                'success' => false,
                "errors" => "Đã có lỗi xảy ra trong quá trình vận hành!!"
            ]);
        }

        if (!empty($request->categoryId)) {
            $check_existed_category = Category::where("id", "=", $request->categoryId)->exists();

            if (!$check_existed_category) {
                return response()->json([
                    "success" => false,
                    "errors" => "ID Danh mục sản phẩm không hợp lệ."
                ]);
            }
            // Remove all existed categories from product to readd everything back
            $product->categories()->detach();
    
            $product->categories()->attach($request->categoryId);
        }        

        // Check product status
        if ($product->status === 0) { // if new status product is 0, then proceed to delete product out of "customer_product_cart"
            DB::table("customer_product_cart")
                ->where("product_id", "=", $product->id)
                ->delete();
        }

        // Checking all categories that product has to decide to attach new categories or skip
        // for ($i = 0; $i < sizeof($request['category']); $i++) {
        //     $category_id = $request['category'][$i]['id'];

        //     $category = Category::find($category_id);
        //     $product->categories()->attach($category);
        // }

        return response()->json([
            'success' => true,
            "message" => "Cập nhật thông tin sản phẩm thành công."
        ]);
    }

    // This is SOFT DELETE not permanent delete
    public function destroy(DeleteAdminBasicRequest $request)
    {
        $data = Product::find($request->id);

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'errors' => "ID Sản phẩm không hợp lệ."
            ]);
        }

        // Check state variable to switch between 2 mode: (Soft) Delete and Reverse Delete
        // If value is 1, it will be (Soft) Delete
        if ((int)$request->state === 1) {

            // Check if product Has already been deleted?
            if ($data->deleted_at !== null) {
                return response()->json([
                    "success" => false,
                    "errors" => "Sản phẩm với ID = " . $request->id . " đã được xóa."
                ]);
            }

            $data->{"deleted_at"} = 1;
            $result = $data->save();

            if (!$result) {
                return response()->json([
                    "success" => false,
                    "errors" => "Đã có lỗi xảy ra trong quá trình vận hành."
                ]);
            }

            // Delete product out of "customer_product_cart"
            DB::table("customer_product_cart")
                ->where("product_id", "=", $data->id)
                ->delete();

            return response()->json(
                [
                    'success' => true,
                    'errors' => "Xóa thành công với sản phẩm có ID = " . $request->id
                ]
            );

            // If value is not 1, it will be Reverse Delete
        } else {
            // Check if product Has already been reversed delete?
            if ($data->deleted_at === null) {
                return response()->json([
                    "success" => false,
                    "errors" => "Sản phẩm với ID = " . $request->id . " đã được hoàn tác xóa."
                ]);
            }

            $data->{"deleted_at"} = null;

            $result = $data->save();

            if (!$result) {
                return response()->json([
                    "success" => false,
                    "errors" => "Đã có lỗi xảy ra trong quá trình vận hành."
                ]);
            }

            return response()->json(
                [
                    'success' => true,
                    'errors' => "Đã hoàn tác việc xóa thành công với sản phẩm có ID = " . $request->id
                ]
            );
        }
    }

    public function destroyBulk(DeleteMultipleProductRequest $request)
    {
        $count = 0;
        $invalid_count = 0;
        $invalid_product_id_array = [];
        $errors_product_id_array = [];
        $errors_count = 0;

        $products = $request->all();

        // If state is 1, then display is "deleted"
        if ((int) $request->state === 1) {
            $display = "xóa";
        }
        // If state is 0, then display is "reversed deleted"
        else {
            $display = "hoàn tác việc xóa";
        }

        for ($i = 0; $i < sizeof($products); $i++) {
            $query = Product::where("id", "=", $products[$i]['id']);

            if (!$query->exists()) {
                $invalid_product_id_array[] = $products[$i]['id'];
                $invalid_count++;
                continue;
            }

            $product = $query->first();

            // If state is 0, then proceed to reverse delete
            if ((int) $request->state === 0) {

                if ($product->deleted_at === null) {
                    continue;
                }

                $product->deleted_at = null;
                $result = $product->save();

                if (!$result) {
                    $errors_product_id_array[] = $products[$i]['id'];
                    $errors_count++;
                }

                $count++;
            }
            // If state is 1, then proceed to delete
            else {
                if ($product->deleted_at === 1) {
                    continue;
                }

                $product->deleted_at = 1;
                $result = $product->save();

                if (!$result) {
                    $errors_product_id_array[] = $products[$i]['id'];
                    $errors_count++;
                }

                $count++;
            }
        }

        if ($invalid_count !== 0) {
            return response()->json([
                "success" => false,
                "errors" => "Có tổng cộng " . $invalid_count . " sản phẩm có ID không hợp lệ. Các ID đó là: " . implode(", ", $invalid_product_id_array)
            ]);
        }

        if ($errors_count !== 0) {
            return response()->json([
                "success" => false,
                "errors" => "Có tổng cộng " . $errors_count . " lỗi xảy ra trong quá trình xóa sản phẩm. Những ID sản phẩm sau gây ra vấn đề: " . implode(", ", $errors_product_id_array)
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Có tổng cộng " . $count . " sản phẩm đã được " . $display
        ]);
    }

    public function changeCategory(DeleteAdminBasicRequest $request, Category $category, Product $product)
    {
        $product->categories()->detach();
        $product->categories()->attach($category->id);

        return response()->json([
            "success" => true,
            "message" => "Danh mục đã được đổi thành công."
        ]);
    }
}
