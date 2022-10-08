<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductDetailResource;
use App\Http\Resources\V1\ProductListCollection;
use App\Http\Resources\V1\ProductListResource;
use App\Http\Resources\V1\ProductReviewResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

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
        $data = Product::with("categories")->get();
        $count = $data->count();

        // dd($data);

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

        return response()->json([
            "success" => true,
            "total" => $count,
            "data" => new ProductListCollection($data)
        ]);

        // return new ProductListCollection($data->appends($request->query()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {
        // $data = collect($request->all())->map(function ($arr, $key) {
        //     return Arr::except($arr, ["categoryId", "percentSale", "deletedAt"]);
        // });

        $check_existed = Product::where("name", "=", $request->name)->get()->count();

        // Check if the existence of name product in database
        // 0 for none; anything beside 0 is already existed
        if ($check_existed !== 0) {
            return response()->json([
                'success' => false,
                'errors' => "Product is already existed"
            ]);
        }

        $filtered = $request->except(['category_id', "categoryId", 'deletedAt', "percentSale"]);

        $category = Category::find($request->category_id);

        if (empty($category)) {
            return response()->json([
                "success" => false,
                "errors" => "An unexpected error has occurred - Category doesn't exist"
            ]);
        }

        $data = Product::create($filtered);

        // Checking if insert into database is isSuccess
        if (empty($data->id)) {
            return response()->json([
                "success" => false,
                "errors" => "An unexpected error has occurred"
            ]);
        }

        // checking $category variable is empty or not

        $data->categories()->attach($category);

        return response()->json([
            'success' => true,
            "data" => $data,
            "category" => $category
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
        // dd($data);

        if (empty($data)) {
            return response()->json([
                "success" => false,
                "errors" => "Product doesn't not exist"
            ]);
        }

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

    public function checkOldCategory($product, $request)
    {
        // Retrieve old value in category_product table
        $category_array = [];
        $index = 0;

        foreach ($product->categories as $category) {
            $category_array[$index]['category_id'] = $category->pivot->category_id;
            $index++;
        }

        foreach ($category_array as $item) {
            if ($item["category_id"] === $request->category_id) {
                return True;
            }
        }
        return False;
    }

    public function update(UpdateProductRequest $request, $productId)
    {
        $data = $request->except(['category_id', "categoryId", 'deletedAt', "percentSale"]);

        $product = Product::find($productId);

        if (empty($product)) {
            return response()->json([
                'success' => false,
                'errors' => "Can't update product with invalid id"
            ]);
        }

        foreach ($data as $key => $value) {
            $product->{$key} = $value;
        }

        $result = $product->save();

        if (!$this->checkOldCategory($product, $request)) {
            $category = Category::find($request->category_id);
            $product->categories()->attach($category);
        }

        if (!$result) {
            return response()->json([
                'success' => false,
                "errors" => "An unexpected error has occurred"
            ]);
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
    public function destroy($productId)
    {
        $data = Product::find($productId);

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'errors' => "Product can not be deleted"
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
                // "data" => $data
                'errors' => "Sucessfully hide this product"
            ]
        );
    }

    public function destroyCategory(Category $category, Product $product) {
        // $category is category_id
        // $product is product_id

        $product = Product::find($product->id);

        $category = Category::find($category->id);

        $result = $product->categories()->detach($category);

        if (empty($result)) {
            return response()->json([
                "success" => false,
                "errors" => "Category has already been removed from product"
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Category has successfully been removed from product"
        ]);
    }
}
