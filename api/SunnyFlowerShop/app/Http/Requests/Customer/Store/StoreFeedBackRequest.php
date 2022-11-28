<?php

namespace App\Http\Requests\Customer\Store;

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
        $user = $this->user();

        $tokenCan = $user->tokenCan('none');

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
