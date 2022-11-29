<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Category;
use App\Http\Requests\Admin\Store\StoreCategoryRequest;
use App\Http\Requests\Admin\Update\UpdateCategoryRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Delete\DeleteAdminBasicRequest;
use App\Http\Requests\Admin\Get\GetAdminBasicRequest;
use App\Http\Resources\V1\CategoryDetailCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(GetAdminBasicRequest $request)
    {
        // Check amount of categories before paginating
        $categories = Category::get()->count();

        if ($categories === 0) {
            return response()->json([
                "success" => false,
                "errors" => "Danh sách danh mục sản phẩm hiện đang trống."
            ]);
        }

        $categories = Category::paginate(10);

        return new CategoryDetailCollection($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryRequest $request)
    {
        $check = Category::where("name", "=", $request->name)->exists();

        if ($check) {
            return response()->json([
                "success" => false,
                "errors" => "Danh mục sản phẩm đã tồn tại, vui lòng sử dụng tên khác."
            ]);
        }

        $result = Category::create($request->all());

        if (!$result) {
            return response()->json([
                "success" => false,
                "errors" => "Đã có lỗi xảy ra trong quá trình vận hành!!"
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Tạo thành công danh mục sản phẩm mới."
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(GetAdminBasicRequest $request)
    {
        $query = Category::where("id", "=", $request->id);

        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Danh mục sản phẩm không tồn tại."
            ]);
        }

        $data = $query->first();

        return [
            "success" => true,
            "data" => [
                "categoryId" => $data->id,
                "name" => $data->name,
                "createdAt" => date_format($data->created_at,"d/m/Y H:i:s"),
                "updatedAt" => date_format($data->updated_at,"d/m/Y H:i:s"),
            ]
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCategoryRequest  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategoryRequest $request)
    {
        $query = Category::where("id", "=", $request->id);

        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Danh mục sản phẩm không tồn tại."
            ]);
        }

        // Check exists category
        $check = Category::where("name", "=", $request->name)->exists();

        if ($check) {
            return response()->json([
                "success" => false,
                "errors" => "Danh mục sản phẩm đã tồn tại, vui lòng sử dụng tên khác."
            ]);
        }

        $update = $query->update($request->all());

        if (empty($update)) {
            return response()->json([
                "success" => false,
                "errors" => "Đã có lỗi xảy ra trong quá trình vận hành."
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Cập nhật thành công Danh mục sản phẩm có ID = " . $request->id
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteAdminBasicRequest $request)
    {
        $category = Category::where("id", "=", $request->id);

        if (!$category->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Danh mục sản phẩm không tồn tại."
            ]);
        }

        // Remove categories attached to product first
        $category_product = DB::table("category_product")
            ->where("category_id", "=", $request->id);

        if ($category_product->exists()) {
            // $category_product->delete(); // Need to check delete state
            return response()->json([
                "success" => false,
                "errors" => "Không thể xóa Danh mục đang được liên kết với sản phẩm."
            ]);
        }

        $delete = $category->delete();

        // if (empty($delete) || empty($detach)) {
        if (empty($delete)) {
            return response()->json([
                "success" => false,
                "errors" => "Đã có lỗi xảy ra trong quá trình vận hành!!"
            ]);
        }

        return response()->json([
            "success" => true,
            "messagee" => "Xóa danh mục sản phẩm thành công."
        ]);
    }
}
