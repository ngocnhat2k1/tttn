<?php

namespace App\Http\Requests\Admin\Update;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductToCartCustomerRequest extends FormRequest
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
            "productId" => [
                "required",
                "integer"
            ],
            "quantity" => [
                "required",
                "integer"
            ]
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'product_id' => $this->productId,
            'quantity_sale' => $this->quantity,
        ]);
    }
}
