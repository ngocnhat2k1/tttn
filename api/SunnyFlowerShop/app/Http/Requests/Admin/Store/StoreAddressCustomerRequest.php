<?php

namespace App\Http\Requests\Admin\Store;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressCustomerRequest extends FormRequest
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
            "firstNameReceiver" => [
                "required",
                "string",
                "min:2",
                "max:100",
            ],
            "lastNameReceiver" => [
                "required",
                "string",
                "min:2",
                "max:100",
            ],
            "phoneReceiver" => [
                "required",
                "string",
                "min:8",
            ],
            "streetName" => [
                "required",
                "string",
                "min:2",
            ],
            "district" => [
                "required",
                "string",
            ],
            "ward" => [
                "required",
                "string",
            ],
            "city" => [
                "required",
                "string",
            ],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'first_name_receiver' => $this->firstNameReceiver,
            'last_name_receiver' => $this->lastNameReceiver,
            'phone_receiver' => $this->phoneReceiver,
            'street_name' => $this->streetName,
        ]);
    }
}
