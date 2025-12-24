<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TaxNumberValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (empty($value)) {
            return;
        }

        $patterns = [
            '/^DE\d{9}$/',    // Германия
            '/^IT\d{11}$/',   // Италия
            '/^GR\d{9}$/',    // Греция
            '/^FR[A-Z]{2}\d{9}$/', // Франция
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return;
            }
        }

        $this->context->buildViolation($constraint->message)->addViolation();
    }
}