<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ShowValueEnum;
use App\Models\Voucher;
use App\Http\Requests\Admin\Store\StoreVoucherRequest;
use App\Http\Requests\Admin\Update\UpdateVoucherRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Delete\DeleteAdminBasicRequest;
use App\Http\Requests\Admin\Get\GetAdminBasicRequest;
use App\Http\Resources\V1\VoucherDetailCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(GetAdminBasicRequest $request)
    {
        $count = Voucher::query()->get()->count();

        if (empty($count)) {
            return response()->json([
                "success" => false,
                "errors" => "Danh sách mã giảm giá hiện đang trống."
            ]);
        }

        $vouchers = Voucher::orderBy("show", "DESC")->paginate(10);

        return new VoucherDetailCollection($vouchers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreVoucherRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreVoucherRequest $request)
    {
        $voucher = Voucher::where("name", "=", $request->name)->exists();

        if ($voucher) {
            return response()->json([
                "success" => false,
                "errors" => "Tên mã giảm giá đã tồn tại."
            ]);
        }

        $filtered = $request->except(["expiredDate"]);

        $result = Voucher::create($filtered);

        if (empty($result)) {
            return response()->json([
                "success" => false,
                "errors" => "Đã có lỗi xảy ra trong quá trình vận hành!!"
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Tạo mã giảm giá mới thành công."
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function show(GetAdminBasicRequest $request)
    {
        $voucher = Voucher::where("id", "=", $request->id);

        if (!$voucher->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Mã giảm giá không tồn tại."
            ]);
        }

        $data = $voucher->first();

        return [
            "success" => true,
            "data" => [
                "voucherID" => $data->id,
                "name" => $data->name,
                "percent" => $data->percent,
                "usage" => $data->usage,
                "expiredDate" => $data->expired_date,
                "show" => ShowValueEnum::getShowValueAttribute($data->show),
                "showInt" => $data->show,
                "deleted" => $data->deleted,
                "createdAt" => date_format($data->created_at, "d/m/Y H:i:s"),
                "updatedAt" => date_format($data->updated_at, "d/m/Y H:i:s")
            ]
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateVoucherRequest  $request
     * @param  \App\Models\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateVoucherRequest $request)
    {
        $voucher = Voucher::where("name", "=", $request->name)->exists();

        if ($voucher) {
            return response()->json([
                "success" => false,
                "errors" => "Tên mã giảm giá đã tồn tại."
            ]);
        }

        $query = Voucher::where("id", "=", $request->id);

        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Không thể cập nhật thông tin của mã giảm giá không tồn tại."
            ]);
        }

        $filtered = $request->except(["expiredDate"]);
        $voucher_get = $query->first();

        $result = $voucher_get->update($filtered);

        if (empty($result)) {
            return response()->json([
                "success" => false,
                "errors" => "Đã có lỗi xảy ra trong quá trình vận hành!!"
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Cập nhật thành công thông tin của Mã giảm giá có ID = " . $request->id
        ]);
    }

    // Update value without any restriction
    public function updateNoRequired(GetAdminBasicRequest $request)
    {
        $data = Validator::make($request->all(), [
            "name" => "string|min:2",
            "percent" => "integer|min:0|max:100",
            "usage" => "integer|min:5",
            "show" => "boolean",
            "expiredDate" => "date_format:Y-m-d H:i:s",
        ]);

        if ($data->fails()) {
            $errors = $data->errors();

            return response()->json([
                "success" => false,
                "errors" => $errors,
            ]);
        }

        $voucher = Voucher::where("name", "=", $request->name)
            ->where("id", "<>", $request->id)
            ->exists();

        if ($voucher) {
            return response()->json([
                "success" => false,
                "errors" => "Tên mã giảm giá đã tồn tại."
            ]);
        }

        if ($request->show) {
            $voucherShowed = Voucher::where("show", "=", 1)->get();

            if ($voucherShowed->count() >= 1) {

                $arr = [];

                for ($i = 0; $i < sizeof($voucherShowed); $i++) {
                    if ($voucherShowed[$i]->show === 1) {
                        $arr[$i] = $voucherShowed[$i]->name;
                    }
                }

                return response()->json([
                    "success" => false,
                    "errors" => "Thay đổi không thành không ở phần chỉnh giá trị hiển thị. Mã giảm giá hiện đang được hiển thị: " . implode(", ", $arr)
                ]);
            }
        }

        $query = Voucher::where("id", "=", $request->id);

        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Không thể cập nhật thông tin của mã giảm giá không tồn tại."
            ]);
        }

        $voucher_get = $query->first();

        $voucher_get->name = $request->name ?? $voucher_get->name;

        $voucher_get->show = $request->show ?? $voucher_get->show;
        $voucher_get->percent = $request->percent ?? $voucher_get->percent;
        $voucher_get->usage = $request->usage ?? $voucher_get->usage;
        $voucher_get->expired_date = $request->expiredDate ?? $voucher_get->expired_date;

        $voucher_get->save();

        return response()->json([
            "success" => true,
            "message" => "Cập nhật thành công thông tin của Mã giảm giá có ID = " . $request->id
        ]);
    }

    public function showVoucher(GetAdminBasicRequest $request)
    {
        $voucherShowed = Voucher::where("show", "=", 1)->get();

        $voucherSelected = Voucher::find($request->id);

        // If state is 1, then proceed to show voucher
        if ((int) $request->state === 1) {

            if ($voucherShowed->count() >= 1) {
                $arr = [];

                for ($i = 0; $i < sizeof($voucherShowed); $i++) {
                    if ($voucherShowed[$i]->show === 1) {
                        $arr[$i] = $voucherShowed[$i]->name;
                    }
                }

                return response()->json([
                    "success" => false,
                    "errors" => "Những mã giảm giá hiện đang được hiển thị " . implode(", ", $arr)
                ]);
            }

            if ($voucherSelected->show === 1) {
                return response()->json([
                    "success" => false,
                    "errors" => "Mã giảm giá ĐÃ ĐƯỢC chuyển sang trạng thái hiển thị."
                ]);
            }
            $voucherSelected->show = 1;
            $voucherSelected->save();

            return response()->json([
                "success" => true,
                "message" => "Mã giảm giá được chuyển sang trạng thái hiển thị."
            ]);
        }
        // If state is 0, then proceed to hide voucher
        else {
            if ($voucherSelected->show === 0) {
                return response()->json([
                    "success" => false,
                    "errors" => "Mã giảm giá ĐÃ ĐƯỢC chuyển sang trạng thái ẩn."
                ]);
            }
            $voucherSelected->show = 0;
            $voucherSelected->save();

            return response()->json([
                "success" => true,
                "message" => "Mã giảm giá được chuyển sang trạng thái ẩn."
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function destroy(GetAdminBasicRequest $request)
    {
        $voucherID = $request->state;
        $query = Voucher::where("id", "=", $request->id);

        if (!$query->exists()) {
            return response()->json([
                "success" => false,
                "errors" => "Mã giảm giá không tồn tại."
            ]);
        }

        $voucher = $query->first();

        // If state is equal to 1, then (Soft) Delete voucher
        if ((int) $request->state === 1) {

            // Check if voucher has already been deleted yet
            if ($voucher->deleted !== null) {
                return response()->json([
                    "success" => false,
                    "errors" => "Mã giảm giá với ID = " . $request->id . " đã được xóa (mềm)"
                ]);
            }

            $voucher->deleted = 1;

            $result = $voucher->save();

            if (!$result) {
                return response()->json([
                    "success" => false,
                    "errors" => "Đã có lỗi xảy ra trong quá trình vận hành!!"
                ]);
            }

            return response()->json([
                "success" => true,
                "message" => "Xóa thành công Mã giảm giá với ID = " . $request->id
            ]);
        }
        // If not then Reverse (Soft) Delete
        else {
            // Check if voucher has already been reversed deletetion yet
            if ($voucher->deleted !== 1) {
                return response()->json([
                    "success" => false,
                    "errors" => "Mã giảm giá với ID = " . $request->id . " đã được hoàn tác việc xóa."
                ]);
            }

            $voucher->deleted = null;

            $result = $voucher->save();

            if (!$result) {
                return response()->json([
                    "success" => false,
                    "errors" => "Đã có lỗi xảy ra trong quá trình vận hành!!"
                ]);
            }

            return response()->json([
                "success" => true,
                "message" => "Hoàn tác thành công việc xóa Mã giảm giá với ID = " . $request->id
            ]);
        }
    }
}
