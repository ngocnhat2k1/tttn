<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\BulkInsertProductRequest;
use App\Http\Resources\V1\ProductDetailResource;
use App\Http\Resources\V1\ProductListCollection;
use App\Http\Resources\V1\ProductListResource;
use App\Http\Resources\V1\ProductReviewResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
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

        $count = DB::table("products")->count();

        // return response()->json([
        //     "success" => true,
        //     "total" => $count,
        //     "data" => new ProductListCollection($data)
        // ]);

        return new ProductListCollection($data->paginate(9)->appends($request->query()));
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

        // Check if the existence of name product in database
        if ($check_existed) {
            return response()->json([
                'success' => false,
                'errors' => "Product is already existed"
            ]);
        }

        $filtered = $request->except(['deletedAt', "percentSale"]);

        $data = Product::create($filtered);

        // Checking if insert into database is isSuccess
        if (empty($data->id)) {
            return response()->json([
                "success" => false,
                "errors" => "An unexpected error has occurred"
            ]);
        }

        // Add each categories to pivot table "category_product"
        for ($i = 0; $i < sizeof($filtered['category']); $i++) {
            $category_id = $filtered['category'][$i]['id'];

            $category = Category::find($category_id);

            // Checking category id - If it doesn't exist just skip
            if (empty($category)) {
                continue;
            }

            $data->categories()->attach($category_id);
        }

        return response()->json([
            'success' => true,
            "message" => "Successfully created product"
        ]);
    }

    public function bulkStore(BulkInsertProductRequest $request)
    {
        // Main Data use for blueprint
        $bulk = collect($request->all())->map(function ($arr, $key) {
            return Arr::except($arr, ["category", "percentSale", "deletedAt"]);
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
                    "errors" => "Something went wrong"
                ]);
            }

            // Insert each category id to pivot table "category_product"
            for ($j = 0; $j < sizeof($data[$i]['category']); $j++) {
                // Find Category ID in category table using $data variable
                $category_id = $data[$i]['category'][$j]["id"];
                $category = Category::find($category_id);
                $product = Product::find($result->id);

                $product->categories()->attach($category);
            }

            $count++;
        }

        return response()->json([
            "success" => true,
            "message" => "Added " . $count . " products to database successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $data = Product::find($request->id);

        if (empty($data)) {
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
        $data = $request->except(['category', 'deletedAt', "percentSale"]);

        // Checking Product ID
        $product = Product::find($productId);
        if (empty($product)) {
            return response()->json([
                'success' => false,
                'errors' => "Can't update product with invalid id"
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
                "errors" => "An unexpected error has occurred"
            ]);
        }

        // Remove all existed categories from product to readd everything back
        $product->categories()->detach();

        // Checking all categories that product has to decide to attach new categories or skip
        for ($i = 0; $i < sizeof($request['category']); $i++) {
            $category_id = $request['category'][$i]['id'];

            $category = Category::find($category_id);
            $product->categories()->attach($category);
        }

        return response()->json([
            'success' => true,
            "message" => "Updated product successfully"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */

    // This is SOFT DELETE not permanent delete
    public function destroy(Request $request, $productId)
    {
        $data = Product::find($productId);

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'errors' => "Product can not be deleted"
            ]);
        }

        // Check state variable to switch between 2 mode: (Soft) Delete and Reverse Delete
        // If value is 1, it will be (Soft) Delete
        if ((int)$request->state === 1) {

            // Check if product Has already been deleted?
            if ($data->deleted_at !== null) {
                return response()->json([
                    "success" => false,
                    "errors" => "Product with ID = " . $productId . " has already been (Soft) deleted"
                ]);
            }

            $data->{"deleted_at"} = 1;

            $result = $data->save();

            if (!$result) {
                return response()->json([
                    "success" => false,
                    "errors" => "An unexpected error has occurred"
                ]);
            }

            return response()->json(
                [
                    'success' => true,
                    'errors' => "Sucessfully hide this product with ID = " . $productId
                ]
            );

            // If value is not 1, it will be Reverse Delete
        } else {
            // Check if product Has already been reversed delete?
            if ($data->deleted_at === null) {
                return response()->json([
                    "success" => false,
                    "errors" => "Product with ID = " . $productId . " has already been (Soft) deleted"
                ]);
            }

            $data->{"deleted_at"} = null;

            $result = $data->save();

            if (!$result) {
                return response()->json([
                    "success" => false,
                    "errors" => "An unexpected error has occurred"
                ]);
            }

            return response()->json(
                [
                    'success' => true,
                    'errors' => "Sucessfully reverse deleted_at value for product with ID = " . $productId
                ]
            );
        }
    }

    public function destroyCategory(Category $category, Product $product)
    {
        $result = $product->categories()->detach($category);

        if (empty($result)) {
            return response()->json([
                "success" => false,
                "errors" => "Invalid Category ID"
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Category has successfully been removed from product"
        ]);
    }
}
