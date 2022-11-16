<?php

namespace App\Http\Requests\Admin\Update;

use App\Enums\OrderStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = $this->user();

        $tokenCan = $user->tokenCan('admin') || $user->tokenCan('super_admin');

        return $user != null && $tokenCan;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "voucherId" => [
                "integer"
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
            "products" => [
                "*.id" => [
                    "required",
                    "integer"
                ],
                "*.quantity" => [
                    "required",
                    "integer"
                ]
            ]
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'voucher_id' => $this->voucherId,
            'date_order' => $this->dateOrder,
            'name_receiver' => $this->nameReceiver,
            'phone_receiver' => $this->phoneReceiver,
            'paid_type' => $this->paidType,
            // "product" => $data,
        ]);
    }
}
