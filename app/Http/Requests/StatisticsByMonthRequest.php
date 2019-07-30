<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StatisticsByMonthRequest extends FormRequest
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
            'last_timestamp' => 'date_format:Y-m-d H:i:s',
            'quantity' => 'required|integer',
            'table' => 'required|in:bets,users,balance_transactions'
        ];
    }

    public function messages()
    {
        return [
            'last_timestamp.date_format' => [
                "code" => 0,
                "message" => "Statistics by month [GET] date format must be 'Y-m-d H:i:s'"
            ],
            'quantity.required' => [
                "code" => 0,
                "message" => "Statistics by month [GET] months is required"
            ],
            'quantity.integer' => [
                "code" => 0,
                "message" => "Statistics by month [GET] months type must be integer"
            ],
            'table.required' => [
                "code" => 0,
                "message" => "Statistics by month [GET] table is required"
            ],
            'table.in' => [
                "code" => 0,
                "message" => "Statistics by month [GET] table is invalid"
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
