<?php declare(strict_types=1);

namespace App\Library\Api\Parameters;

abstract class ParameterAbstract {
    private array $defaultValue = [];

    private bool $isRequired = true;

    private bool $isNullable = false;

    final public function isRequired(): bool {
        return $this->isRequired;
    }

    final public function isNullable(): bool {
        return $this->isNullable;
    }

    public function setDefault(mixed $value): static {
        $this->defaultValue['default'] = $value;
        if ($value === null) {
            $this->setNullable();
        }
        return $this;
    }

    final public function setRequired(bool $isRequired): static {
        $this->isRequired = $isRequired;
        return $this;
    }

    final public function setNullable(bool $isNullable = true): static {
        $this->isNullable = $isNullable;
        return $this;
    }

    public function validate(string $parameterName, array $unverifiedData, array &$validatedData, array &$errors): void {
        $isValueSet = array_key_exists($parameterName, $unverifiedData);
        if (!$isValueSet && $this->isRequired && empty($this->defaultValue)) {
            $errors[$parameterName][] = ApiParameterErrors::REQUIRED;
            return;
        }

        $unverifiedParameterValue = $unverifiedData[$parameterName] ?? null;
        $defaultValue             = $this->defaultValue['default'] ?? null;

        if ($unverifiedParameterValue === null && $defaultValue !== null) {
            $unverifiedParameterValue = $this->defaultValue;
            $isValueSet               = true;
        }

        if ($unverifiedParameterValue === null && !$this->isNullable) {
            $errors[$parameterName][] = ApiParameterErrors::NOT_NULLABLE;
            return;
        }

        if ($unverifiedParameterValue === null) {
            if ($isValueSet) {
                $validatedData[$parameterName] = null;
            }
            return;
        }

        $this->validateInternal($parameterName, $unverifiedParameterValue, $validatedData, $errors);
    }

    abstract protected function validateInternal(string $parameterName, mixed $unverifiedParameterValue, array &$validatedData, array &$errors): void;
}
