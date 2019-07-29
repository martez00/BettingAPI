<?php

namespace App\Rules\Bet;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Factory as ValidationFactory;

class ValidMinStakeAmount implements Rule
{
    private $value;
    private $minValue;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($minValue)
    {
        $this->minValue = $minValue;
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
        if ($this->value >= $this->minValue) {
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
        return array(["code" => 2, "message" => "Minimum stake amount is $this->minValue"]);
    }
}
