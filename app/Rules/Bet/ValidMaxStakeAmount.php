<?php

namespace App\Rules\Bet;

use Illuminate\Contracts\Validation\Rule;

class ValidMaxStakeAmount implements Rule
{
    private $value;
    private $maxValue;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($maxValue)
    {
        $this->maxValue = $maxValue;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->value = $value;
        if ($this->value <= $this->maxValue) {
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
        return array(["code" => 3, "message" => "Maximum stake amount is $this->maxValue"]);
    }
}
