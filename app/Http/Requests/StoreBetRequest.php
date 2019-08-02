<?php

namespace App\Http\Requests;

use App\Rules\Bet\ValidBalanceIsMoreThanStakeAmount;
use App\Rules\Bet\ValidMaxOdd;
use App\Rules\Bet\ValidMaxSelections;
use App\Rules\Bet\ValidMaxStakeAmount;
use App\Rules\Bet\ValidMaxWinAmount;
use App\Rules\Bet\ValidMinOdd;
use App\Rules\Bet\ValidMinSelections;
use App\Rules\Bet\ValidMinStakeAmount;
use App\Rules\Bet\ValidOddsFormat;
use App\Rules\Bet\ValidStakeAmountFormat;
use App\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreBetRequest extends FormRequest
{
    public function __construct()
    {

    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'another_bet_request_initialized' => 'in:0',
            'user_id' => 'required|integer',
            'stake_amount' => [
                'required',
                new ValidStakeAmountFormat(),
                new ValidMinStakeAmount(0.3),
                new ValidMaxStakeAmount(10000)
            ],
            'selections' => [
                'required',
                new ValidMinSelections(1),
                new ValidMaxSelections(20)
            ],
            'selections.*.id' => 'required|exists:selections,id|distinct',
            'selections.*.odds' => [
                'required',
                new ValidOddsFormat(),
                new ValidMinOdd(1),
                new ValidMaxOdd(10000)
            ],
            'user_balance' => [
                new ValidBalanceIsMoreThanStakeAmount($this->stake_amount)
            ],
            'max_win' => [
                new ValidMaxWinAmount(10000)
            ]
        ];
    }

    public function messages()
    {
        return [
            'another_bet_request_initialized.in' => [
                "code" => 10,
                "message" => "Your previous action is not finished yet"
            ],
            'user_id.required' => ["code" => 0, "message" => "User ID field is required"],
            'user_id.integer' => ["code" => 0, "message" => "User ID field must be integer"],
            'stake_amount.required' => ["code" => 0, "message" => "Stake amount field is required"],
            'selections.required' => ["code" => 4, "message" => "Minimum number of selections is 1"],
            'selections.*.id.required' => ["code" => 0, "message" => "Selection ID is required"],
            'selections.*.id.exists' => ["code" => 0, "message" => "Selection does not exist"],
            'selections.*.id.distinct' => ["code" => 8, "message" => "Duplicate selection found"],
            'selections.*.odds.required' => ["code" => 0, "message" => "Selection :attribute odds is required"],
        ];
    }

    /**
     * Extend the default getValidatorInstance method
     * so fields can be modified or added before validation
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        if ( ! session()->exists('bet_requested')) {
            session()->put('bet_requested', '1');
            session()->save();
            $this->merge([
                'another_bet_request_initialized' => 0
            ]);
        } else {
            $this->merge([
                'another_bet_request_initialized' => 1
            ]);
        }

        if ($this->input('stake_amount')) {
            $this->merge([
                'stake_amount' => str_replace(',', '.', $this->input('stake_amount')),
            ]);
        }

        if ($this->input('selections')) {
            foreach ($this->input('selections') as $selection) {
                $selectionOdd = str_replace(',', '.', $selection['odds']);
                $selection['odds'] = $selectionOdd;
                $formatedSelections[] = $selection;
            }
            $this->merge([
                'selections' => $formatedSelections
            ]);
        }

        if ($this->input('user_id')) {
            $user = User::find($this->input('user_id'));
            if ($user) {
                $userBalance = $user->balance;
            } else {
                $userBalance = 1000;
            } //because docummentation says that default balance is 1000
            $this->merge([
                'user_balance' => $userBalance
            ]);
        }

        if ($this->input('selections') && $this->input('stake_amount')) {
            $oddsSum = 1;
            foreach ($this->input('selections') as $selection) {
                $oddsSum = $oddsSum * $selection['odds'];
            }
            $maxWin = $this->input('stake_amount') * $oddsSum;
        } else {
            $maxWin = 0;
        }
        $this->merge([
            'max_win' => $maxWin,
        ]);

        return parent::getValidatorInstance();

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
        $previoussActionError = false;
        $mainErrors = array();
        $selectionsErrors = array();
        $selectionsArray = array();
        foreach ($validator->errors()->getMessages() as $key => $value) {
            if (preg_match("/selections./", $key)) {
                $isSelection = true;
                $selectionKey = explode('.', $key);
            } else {
                $isSelection = false;
            }
            $arr = json_decode(json_encode($value), true);
            foreach ($arr as $keyB => $valueB) {
                if ($isSelection == true) {
                    if (intval($selectionKey[1]) || intval($selectionKey[1]) == 0) {
                        $selectionsErrors[$selectionKey[1]][] = $valueB;
                    } else {
                        $mainErrors[] = $valueB;
                    }
                } else {
                    $mainErrors[] = $valueB;
                    if ($valueB['code'] == 10) {
                        $previoussActionError = true;
                    }
                }
            }
        }
        $selections = $this->input('selections');
        if ($selections) {
            $i = 0;
            foreach ($selections as $selection) {
                if (array_key_exists($i, $selectionsErrors)) {
                    $selectionErrorsReturn = $selectionsErrors[$i];
                } else {
                    $selectionErrorsReturn = [];
                }
                $selectionsArray[] = [
                    'id' => $selection['id'],
                    'odds' => $selection['odds'],
                    'errors' => $selectionErrorsReturn
                ];
                $i++;
            }
        }
        $mainErrors = json_decode(json_encode($mainErrors, JSON_FORCE_OBJECT), true);

        if ($previoussActionError != true) {
            session()->forget(['bet_requested']);
            session()->save();
        }

        throw new HttpResponseException(response()->json([
            'user_id' => $this->input('user_id'),
            'stake_amount' => $this->input('stake_amount'),
            'errors' => $mainErrors,
            'selections' => $selectionsArray
        ], 400));
    }
}
