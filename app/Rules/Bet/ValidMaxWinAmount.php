<?php

namespace App\Rules\Bet;

use Illuminate\Contracts\Validation\Rule;

class ValidMaxWinAmount implements Rule
{
    private $value;
    private $maxWin;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($maxWin)
    {
        $this->maxWin = $maxWin;
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
        if ($this->value <= $this->maxWin) {
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
        return array(["code" => 9, "message" => "Maximum win amount is $this->maxWin"]);
    }
}
