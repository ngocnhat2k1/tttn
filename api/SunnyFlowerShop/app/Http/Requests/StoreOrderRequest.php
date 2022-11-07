<?php

namespace App\Http\Requests;

use App\Enums\OrderStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "voucherCode" => [
                "string",
                "nullable"
            ],
            "idDelivery" => [
                "required",
                "string"
            ],
            "dateOrder" => [
                "required",
                "date_format:Y-m-d H:i:s",
            ],
            "address" => [
                "required",
                "string",
            ],
            "nameReceiver" => [
                "required",
                "string",
            ],
            "phoneReceiver" => [
                "required",
                "string",
            ],
            "status" => [
                "required",
                "integer",
                Rule::in(OrderStatusEnum::asArray()),
            ],
            "paidType" => [
                "required",
                "boolean",
            ],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->voucherCode) {
            $this->merge([
                'voucher_code' => $this->voucherCode,
            ]);
        }
        $this->merge([
            "id_delivery" => $this->idDelivery,
            'date_order' => $this->dateOrder,
            'name_receiver' => $this->nameReceiver,
            'phone_receiver' => $this->phoneReceiver,
            'paid_type' => $this->paidType,
            // "product" => $data,
        ]);
    }
}
