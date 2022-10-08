<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            "name" => [
                "required",
                "string",
                "min:2",
                "max:100",
            ],
            "description" => [
                "required",
                "string",
                "min:10",
            ],
            "price" => [
                "required",
                "integer",
            ],
            "percentSale" => [
                "required",
                "integer",
                "min:1",
                "max:100",
            ],
            "quantity" => [
                "required",
                "integer"
            ],
            "status" => [
                "required",
                "boolean",
            ],
            "categoryId" => [
                "required",
                "integer",
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
