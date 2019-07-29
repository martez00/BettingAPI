<?php

namespace App\Rules\Bet;

use Illuminate\Contracts\Validation\Rule;

class ValidMinSelections implements Rule
{
    private $minSelections;
    private $value;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($minSelections)
    {
        $this->minSelections = $minSelections;
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
        if (sizeof($this->value) >= $this->minSelections) {
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
        return array(
            [
                "code" => 4,
                "message" => "Minimum number of selections is $this->minSelections"
            ]
        );
    }
}
