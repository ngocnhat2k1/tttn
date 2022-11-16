<?php

namespace App\Http\Requests\Admin\Store;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerAdminRequest extends FormRequest
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
