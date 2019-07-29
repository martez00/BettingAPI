<?php

namespace App\Rules\Bet;

use Illuminate\Contracts\Validation\Rule;

class ValidMinOdd implements Rule
{
    private $value;
    private $minOdd;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($minOdd)
    {
        $this->minOdd = $minOdd;
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
        if ($this->value >= $this->minOdd) {
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
        return array(["code" => 6, "message" => "Minimum odds are $this->minOdd"]);
    }
}
