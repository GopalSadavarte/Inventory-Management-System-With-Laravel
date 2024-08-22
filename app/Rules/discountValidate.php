<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class discountValidate implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!empty($value)) {
            if ($value < 0) {
                $fail(':attribute cannot be less then Zero..!');
            } else if ($value > 100) {
                $fail(':attribute cannot be greater than 100..!');
            }
        }
    }
}
