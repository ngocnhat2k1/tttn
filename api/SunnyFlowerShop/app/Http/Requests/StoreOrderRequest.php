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
            "customer_id" => [
                "required",
                "integer",
            ],
            "voucher_id" => [
                "required",
                "integer"
            ],
            "date_order" => [
                "required",
                "date_format:Y-m-d H:i:s",
            ],
            "address" => [
                "required",
                "string",
            ],
            "name_receiver" => [
                "required",
                "string",
            ],
            "phone_receiver" => [
                "required",
                "string",
            ],
            "total_price" => [
                "required",
                "integer",
            ],
            "status" => [
                "required",
                "integer",
                Rule::in(OrderStatusEnum::asArray()),
            ],
            "paid_type" => [
                "required",
                "boolean",
            ],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'category_id' => $this->categoryId,
            'percent_sale' => $this->percentSale,
            'deleted_at' => $this->deletedAt,
        ]);
    }
}
