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

        // Если значение не передано и нет значения по умолчанию,
        // то ошибка, только если параметр обязательный.
        if (!$isValueSet && !$this->defaultValue) {
            if ($this->isRequired) {
                $errors[$parameterName][] = ApiParameterErrors::REQUIRED;
            }
            return;
        }

        // Значение по умолчанию подставляем только если параметр не передан
        $unverifiedParameterValue = $unverifiedData[$parameterName] ?? null;
        if (!$isValueSet && $this->defaultValue) {
            $unverifiedParameterValue = $this->defaultValue;
        }

        // Некоторые параметры не принимают null
        if ($unverifiedParameterValue === null && !$this->isNullable) {
            $errors[$parameterName][] = ApiParameterErrors::NOT_NULLABLE;
            return;
        }

        // Мы разрешили честный null, нет смысла валидировать дальше
        if ($unverifiedParameterValue === null) {
            $validatedData[$parameterName] = null;
            return;
        }

        $this->validateInternal($parameterName, $unverifiedParameterValue, $validatedData, $errors);
    }

    abstract protected function validateInternal(string $parameterName, mixed $unverifiedParameterValue, array &$validatedData, array &$errors): void;
}
