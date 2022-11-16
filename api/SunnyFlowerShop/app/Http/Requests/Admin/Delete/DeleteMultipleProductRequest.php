<?php

namespace App\Http\Requests\Admin\Delete;

use Illuminate\Foundation\Http\FormRequest;

class DeleteMultipleProductRequest extends FormRequest
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
            "*.id" => [
                "required",
                "integer"
            ]
        ];
    }
}
