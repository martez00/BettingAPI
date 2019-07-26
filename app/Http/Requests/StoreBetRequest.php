<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Factory as ValidationFactory;

class StoreBetRequest extends FormRequest
{
    public function __construct(ValidationFactory $validationFactory)
    {
        $validationFactory->extend(
            'more_than_stake_amount',
            function ($attribute, $value, $parameters) {
                if ($value > $this->stake_amount) return true;
            },
            'Insufficient balance'
        );

        $validationFactory->extend(
            'max_win_amount',
            function ($attribute, $value, $parameters) {
                if ($value <= $parameters[0]) return true;
            },
            'Maximum win amount is :max_win_amount'
        );

        $validationFactory->replacer('max_win_amount', function ($message, $attribute, $rule, $parameters) {
            $maxWinAmount = $parameters[0];

            return str_replace(':max_win_amount', $maxWinAmount, $message);
        });

        $validationFactory->extend(
            'min_selections',
            function ($attribute, $value, $parameters) {
                if (sizeof($value) >= $parameters[0]) return true;
            },
            "There must be at least :min_selections selections!"
        );


        $validationFactory->extend(
            'max_selections',
            function ($attribute, $value, $parameters) {
                if (sizeof($value) <= $parameters[0]) return true;
            },
            'There must be less than :max_selections selections!'
        );

        $validationFactory->replacer('min_selections', function ($message, $attribute, $rule, $parameters) {
            $minSelections = $parameters[0];

            return str_replace(':min_selections', $minSelections, $message);
        });

        $validationFactory->replacer('max_selections', function ($message, $attribute, $rule, $parameters) {
            $maxSelections = $parameters[0];

            return str_replace(':max_selections', $maxSelections, $message);
        });


        $validationFactory->extend(
            'odds_format',
            function ($attribute, $value, $parameters) {
                return preg_match('/^\d{0,8}(\.\d{1,3})?$/', $value);
            },
            'Odds format is incorrect!'
        );

        $validationFactory->extend(
            'min_odds',
            function ($attribute, $value, $parameters) {
                if ($value >= $parameters[0]) return true;
            },
            "Odds must be at least :min_odds!"
        );


        $validationFactory->extend(
            'max_odds',
            function ($attribute, $value, $parameters) {
                if ($value <= $parameters[0]) return true;
            },
            'Odds must be less than :max_odds!'
        );

        $validationFactory->replacer('min_odds', function ($message, $attribute, $rule, $parameters) {
            $minOdds = $parameters[0];

            return str_replace(':min_odds', $minOdds, $message);
        });

        $validationFactory->replacer('max_odds', function ($message, $attribute, $rule, $parameters) {
            $maxOdds = $parameters[0];

            return str_replace(':max_odds', $maxOdds, $message);
        });

        $validationFactory->extend(
            'amount_format',
            function ($attribute, $value, $parameters) {
                return preg_match('/^\d{0,8}(\.\d{1,2})?$/', $value);
            },
            'Stake amount format is incorrect!'
        );

        $validationFactory->extend(
            'min_amount',
            function ($attribute, $value, $parameters) {
                if ($value >= $parameters[0]) return true;
            },
            "Stake amount must be at least :min_amount!"
        );


        $validationFactory->extend(
            'max_amount',
            function ($attribute, $value, $parameters) {
                if ($value <= $parameters[0]) return true;
            },
            'Stake amount must be less than :max_amount!'
        );

        $validationFactory->replacer('min_amount', function ($message, $attribute, $rule, $parameters) {
            $minAmount = $parameters[0];

            return str_replace(':min_amount', $minAmount, $message);
        });

        $validationFactory->replacer('max_amount', function ($message, $attribute, $rule, $parameters) {
            $maxAmount = $parameters[0];

            return str_replace(':max_amount', $maxAmount, $message);
        });

    }

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
            'another_bet_request_initialized' => 'in:0',
            'user_id' => 'required',
            'stake_amount' => 'required|amount_format|min_amount:0.3|max_amount:10000',
            'selections' => 'required|min_selections:1|max_selections:20',
            'selections.*.id' => 'required|exists:selections,id|distinct',
            'selections.*.odds' => 'required|odds_format|min_odds:1|max_odds:10000',
            'user_balance' => 'more_than_stake_amount',
            'max_win' => 'max_win_amount:10000'
        ];
    }

    public function messages()
    {
        return [
            'another_bet_request_initialized.in' => ["code" => 10, "message" => "Your previous action is not finished yet"],
            'user_id.required' => ["code" => 0, "message" => "User ID field is required"],
            'stake_amount.required' => ["code" => 0, "message" => "Stake amount field is required"],
            'stake_amount.amount_format' => ["code" => 0, "message" => "Stake amount format is invalid!"],
            'stake_amount.min_amount' => ["code" => 2, "message" => "Minimum stake amount is :min_amount"],
            'stake_amount.max_amount' => ["code" => 3, "message" => "Maximum stake amount is :max_amount"],
            'selections.required' => ["code" => 4, "message" => "Minimum number of selections is 1"],
            'selections.min_selections' => ["code" => 4, "message" => "Minimum number of selections is :min_selections"],
            'selections.max_selections' => ["code" => 5, "message" => "Maximum number of selections is :max_selections"],
            'selections.*.id.required' => ["code" => 0, "message" => "Selection ID is required"],
            'selections.*.id.exists' => ["code" => 0, "message" => "Selection does not exist"],
            'selections.*.id.distinct' => ["code" => 8, "message" => "Duplicate selection found"],
            'selections.*.odds.required' => ["code" => 0, "message" => "Selection :attribute odds is required"],
            'selections.*.odds.odds_format' => ["code" => 0, "message" => "Selection :attribute odd format is invalid"],
            'selections.*.min_odds' => ["code" => 6, "message" => "Minimum odds are :min_odds"],
            'selections.*.max_odds' => ["code" => 7, "message" => "Maximum odds are :max_odds"],
            'user_balance.more_than_stake_amount' => ["code" => 11, "message" => "Insufficient balance"],
            'max_win.max_win_amount' => ["code" => 9, "message" => "Maximum win amount is :max_win_amount"],
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
        if (!session()->exists('bet_requested')) {
            session()->put('bet_requested', '1');
            session()->save();
            $this->merge([
                'another_bet_request_initialized' => 0
            ]);
        }
        else {
            $this->merge([
                'another_bet_request_initialized' => 1
            ]);
        }
        if ($this->input('user_id')) {
            $user = User::find($this->input('user_id'));
            if ($user) {
                $userBalance = $user->balance;
            } else $userBalance = 1000; //because docummentation says that default balance is 1000
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
        } else $maxWin = 0;

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
        $mainErrors = array();
        $selectionsErrors = array();
        $selectionsArray = array();
        foreach ($validator->errors()->getMessages() as $key => $value) {
            if (preg_match("/selections./", $key)) {
                $isSelection = true;
                $selectionKey = explode('.', $key);
            } else $isSelection = false;
            $arr = json_decode(json_encode($value), true);
            foreach ($arr as $keyB => $valueB) {
                if ($isSelection == true) {
                    if (intval($selectionKey[1]) || intval($selectionKey[1]) == 0)
                        $selectionsErrors[$selectionKey[1]][] = $valueB;
                    else $mainErrors[] = $valueB;
                } else $mainErrors[] = $valueB;
            }
        }
        $selections = $this->input('selections');
        if ($selections) {
            $i = 0;
            foreach ($selections as $selection) {
                if (array_key_exists($i, $selectionsErrors)) $selectionErrorsReturn = $selectionsErrors[$i];
                else $selectionErrorsReturn = [];
                $selectionsArray[] = [
                    'id' => $selection['id'],
                    'odds' => $selection['odds'],
                    'errors' => $selectionErrorsReturn
                ];
                $i++;
            }
        }
        $mainErrors = json_decode(json_encode($mainErrors, JSON_FORCE_OBJECT), true);
        session()->forget(['bet_requested']);
        session()->save();
        throw new HttpResponseException(response()->json([
            'user_id' => $this->input('user_id'),
            'stake_amount' => $this->input('stake_amount'),
            'errors' => $mainErrors,
            'selections' => $selectionsArray
        ], 400));
    }
}
