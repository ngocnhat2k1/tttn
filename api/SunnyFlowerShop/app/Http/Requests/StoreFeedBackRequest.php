<?php

namespace App\Http\Requests;

use App\Enums\QualityStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFeedBackRequest extends FormRequest
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
            // "customer_id" => [
            //     "required",
            //     "integer",
            // ],
            "productId" => [
                "required",
                "integer",
            ],
            "quality" => [
                "required",
                "integer",
                Rule::in(QualityStatusEnum::asArray()),
            ],
            "comment" => [
                "required",
                "string",
                "nullable"
            ],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'product_id' => $this->productId,
        ]);
    }
}
