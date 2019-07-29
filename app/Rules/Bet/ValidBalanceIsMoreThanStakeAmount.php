<?php

namespace App\Rules\Bet;

use Illuminate\Contracts\Validation\Rule;

class ValidBalanceIsMoreThanStakeAmount implements Rule
{
    private $value;
    private $stakeAmount;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($stakeAmount)
    {
        $this->stakeAmount = $stakeAmount;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->value = $value;
        if ($this->value >= $this->stakeAmount) {
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return array(["code" => 11, "message" => "Insufficient balance"]);
    }
}
