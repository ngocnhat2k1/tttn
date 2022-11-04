<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
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
            "firstName" => [
                "required",
                "string",
                "min:2",
                "max:50",
            ],
            "lastName" => [
                "required",
                "string",
                "min:2",
                "max:50",
            ],
            "email" => [
                "required",
                "email",
            ],
            "avatar" => [
                // "file",
                // "image"
            ],
            "password" => [
                "required",
                "string",
                "min:6",
                "max:24",
            ],
            "subscribed" => [
                "required",
                "boolean",
            ]
            // "phoneNumber" => [
            //     "required",
            //     "string",
            // ],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            // 'category_id' => $this->categoryId,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            // 'phone_number' => $this->phoneNumber
        ]);
    }
}
