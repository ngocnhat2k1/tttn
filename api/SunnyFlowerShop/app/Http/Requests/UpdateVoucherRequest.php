<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVoucherRequest extends FormRequest
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
                "min:2"
            ],
            "percent" => [
                "required",
                "integer",
                "max:100",
                "min:0"
            ],
            "usage" => [
                "required",
                "integer",
                "min:5"
            ],
            "expiredDate" => [
                "required",
                "date_format:Y-m-d H:i:s"
            ]
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            // 'category_id' => $this->categoryId,
            'expired_date' => $this->expiredDate,
        ]);
    }
}
