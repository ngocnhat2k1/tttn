<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkInsertProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    public function authorize() {
        return true;
    }

    public function rules()
    {
        return [
            "*.name" => [
                "required",
                "string",
                "min:2",
                "max:100",
            ],
            "*.description" => [
                "required",
                "string",
                "min:10",
            ],
            "*.price" => [
                "required",
                "integer",
            ],
            "*.percentSale" => [
                "required",
                "integer",
                "min:1",
                "max:100",
            ],
            "*.quantity" => [
                "required",
                "integer"
            ],
            "*.img" => [
                "required",
                "string",
            ],
            "*.category" => [
                "*.id" => [
                    "required",
                    "integer",
                ]
            ],
        ];
    }

    protected function prepareForValidation()
    {
        $data = [];

        foreach ($this->toArray() as $obj) {
            // $obj['category_id'] = $obj['categoryId'] ?? null;
            $obj['percent_sale'] = $obj['percentSale'] ?? null;
            $obj['deleted_at'] = $obj['deletedAt'] ?? null;

            $data[] = $obj;
        }

        $this->merge($data);
    }
}
