<?php

namespace App\Rules\Bet;

use Illuminate\Contracts\Validation\Rule;

class ValidMaxSelections implements Rule
{
    private $maxSelections;
    private $value;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($maxSelection)
    {
        $this->maxSelections = $maxSelection;
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
        if (sizeof($this->value) <= $this->maxSelections) {
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
                "code" => 5,
                "message" => "Maximum number of selections is :max_selections"
            ]
        );
    }
}
