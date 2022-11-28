<?php

namespace App\Http\Requests\Customer\Store;

use Illuminate\Foundation\Http\FormRequest;

class StoreAvatarCustomerRequest extends FormRequest
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
            "avatar" => [
                "required",
                "string",
            ]
        ];
    }
}
