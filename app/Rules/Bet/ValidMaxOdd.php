<?php

namespace App\Rules\Bet;

use Illuminate\Contracts\Validation\Rule;

class ValidMaxOdd implements Rule
{
    private $value;
    private $maxOdd;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($maxOdd)
    {
        $this->maxOdd = $maxOdd;
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
        if ($this->value <= $this->maxOdd) {
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
        return array(["code" => 7, "message" => "Maximum odds are $this->maxOdd"]);
    }
}
