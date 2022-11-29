<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Admin;
use App\Http\Requests\Admin\Store\StoreAdminRequest;
use App\Http\Requests\Admin\Update\UpdateAdminRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Delete\DeleteAdminRequest;
use App\Http\Requests\Admin\Get\GetAdminBasicRequest;
use App\Http\Resources\V1\AdminDetailResource;
use App\Models\AdminToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(GetAdminBasicRequest $request)
    {
        $admins = Admin::paginate(5);

        return AdminDetailResource::collection($admins);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAdminRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAdminRequest $request)
    {
        $userCheck = Admin::where("user_name", "=", $request->user_name)->exists();
        $emailCheck = Admin::where("email", "=", $request->email)->exists();

        if ($userCheck) {
            return response()->json([
                "success" => false,
                "errors" => "Username đã tồn tại, vui lòng chọn tên khác."
            ]);
        }

        if ($emailCheck) {
            return response()->json([
                "success" => false,
                "errors" => "Email đã được sử dụng, vui lòng sử dụng email khác."
            ]);
        }

        $filtered = $request->except(["userName", 'password']);
        $filtered['password'] = Hash::make($request->password);
        $data = Admin::create($filtered);

        // Checking if insert into database is isSuccess
        if (empty($data->id)) {
            return response()->json([
                "success" => false,
                "errors" => "Đã có lỗi xảy ra trong quá trình vận hành!!"
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Tạo tài khoản Admin mới thành công."
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function show(GetAdminBasicRequest $request, Admin $admin)
    {
        if ($admin->level === 0) {
            $display_level = "Admin";
        } else {
            $display_level = "Super Admin";
        }

        return response()->json([
            "success" => true,
            "data" => [
                "userName" => $admin->user_name,
                "email" => $admin->email,
                "avatar" => $admin->avatar,
                "defaultAvatar" => $admin->default_avatar,
                "level" => $display_level
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAdminRequest  $request
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        $userCheck = Admin::where("user_name", "=", $request->user_name)->exists();
        $emailCheck = Admin::where("email", "=", $request->email)->exists();

        if ($userCheck) {
            return response()->json([
                "success" => false,
                "errors" => "Username đã tồn tại, vui lòng chọn tên khác."
            ]);
        }

        if ($emailCheck) {
            return response()->json([
                "success" => false,
                "errors" => "Email đã được sử dụng, vui lòng sử dụng email khác."
            ]);
        }

        $filtered = $request->except(["userName", 'password']);

        if (!empty($request->password)) {
            $filtered['password'] = Hash::make($request->password);
        }

        $result = $admin->update($filtered);

        // Checking data is updated in database is isSuccess
        if (empty($result)) {
            return response()->json([
                "success" => false,
                "errors" => "Đã có lỗi xảy ra trong quá trình vận hành!!"
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Cập nhật thành công thông tin Tài khoản Admin có ID = " . $admin->id
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteAdminRequest $request, Admin $admin)
    {
        // Can't delete Super Admin account
        if ($admin->level === 1) {
            return response()->json([
                "success" => false,
                "errors" => "Không thể xóa tài khoản Super Admin."
            ]);
        }

        // Delete all token related to current account being deleted
        Auth::guard("customer")->logout();
        $name = "Admin - " . $admin->id;

        // Delete token in "personal_access_tokens"
        DB::table("personal_access_tokens")
            ->where("name", "=", $name)
            ->delete();

        AdminToken::where("admin_id", "=", $admin->id)->delete();
        $admin->delete();

        return response()->json([
            "success" => false,
            "message" => "Xóa thành công tài khoản Admin có ID = " . $admin->id
        ]);
    }
}
