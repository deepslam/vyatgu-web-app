<?php declare(strict_types=1);

namespace App\Library\Api\Parameters;

class ColorParameter extends StringParameter {
    public function __construct() {
        $this
            ->setMaxLength(6)
            ->setMinLength(6);
    }

    protected function validateInternal(string $parameterName, mixed $unverifiedParameterValue, array &$validatedData, array &$errors): void {
        parent::validateInternal($parameterName, $unverifiedParameterValue, $validatedData, $errors);
        if ($errors) {
            return;
        }

        $value = strtolower($unverifiedParameterValue);

        if (!preg_match('#^[a-f0-9]{6}$#', $value)) {
            $errors[$parameterName][] = ApiParameterErrors::INVALID_COLOR;
            return;
        }

        $validatedData[$parameterName] = $value;
    }
}
