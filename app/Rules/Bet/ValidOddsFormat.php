<?php

namespace App\Rules\Bet;

use Illuminate\Contracts\Validation\Rule;

class ValidOddsFormat implements Rule
{
    private $value;
    private $attribute;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $this->attribute = $attribute;
        return preg_match('/^\d{0,8}(\.\d{1,3})?$/', $this->value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return array(["code" => 0, "message" => "Selection $this->attribute odd format is invalid"]);
    }
}
