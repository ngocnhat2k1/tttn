<?php

namespace App\Http\Requests\Admin\Store;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = $this->user();

        return $user != null && $user->tokenCan('super_admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "userName" => [
                "required",
                "string",
                "min:2",
            ],
            "email" => [
                "required",
                "email",
            ],
            "password" => [
                "required",
                "string",
                "min:6",
            ],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'user_name' => $this->userName,
        ]);
    }
}
