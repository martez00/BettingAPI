<?php

namespace App\Http\Requests;

use App\Rules\ValidColumnExist;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UsersRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'order_by' => [new ValidColumnExist("users")],
            'order_by_keyword' => 'in:DESC,ASC',
            'limit' => 'integer'
        ];
    }

    public function messages()
    {
        return [
            'order_by_keyword.in' => [
                "code" => 0,
                "message" => "Order by must be DESC or ASC!"
            ],
            'limit.integer' => [
                "code" => 0,
                "message" => "Limit must be integer"
            ]
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            $validator->errors()
        ], 400));
    }
}
