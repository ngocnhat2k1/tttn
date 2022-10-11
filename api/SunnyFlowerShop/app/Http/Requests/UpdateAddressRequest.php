<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
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
            "nameReceiver" => [
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
                "min:10",
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
            'name_receiver' => $this->nameReceiver,
            'phone_receiver' => $this->phoneReceiver,
            'street_name' => $this->streetName,
        ]);
    }
}
