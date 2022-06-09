<?php

namespace Blog\Core;

use Exception;

class Validator
{
    public function __construct(
        private readonly array $params
    ) {
    }

    /**
     * @throws Exception
     */
    public function required(string ...$keys): static
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value)) {
                throw new Exception(sprintf("The field '%s' is required!", $key));
            }
        }
        return $this;
    }

    /**
     * @throws Exception
     */
    public function notEmpty(string ...$keys): static
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (empty($value)) {
                throw new Exception(sprintf("The field '%s' must not be empty!", $key));
            }
        }
        return $this;
    }

    /**
     * @throws Exception
     */
    public function length(string $key, ?int $min = null, ?int $max = null): static
    {
        $length = mb_strlen($this->getValue($key));
        if (!is_null($min) && !is_null($max) && ($length < $min || $length > $max)) {
            throw new Exception(sprintf("The field '%s' must be between %d and %d characters!", $key, $min, $max));
        } elseif (!is_null($min) && ($length < $min)) {
            throw new Exception(sprintf("The field '%s' must have at least %d characters!", $key, $min));
        } elseif (!is_null($max) && ($length > $max)) {
            throw new Exception(sprintf("The field '%s' must have maximum %d characters!", $key, $max));
        }
        return $this;
    }

    /**
     * @throws Exception
     */
    public function isEquals(string $first, string $second): static
    {
        if ($this->getValue($first) !== $this->getValue($second)) {
            throw new Exception(sprintf("The fields '%s' and '%s' must be identical!", $first, $second));
        }
        return $this;
    }

    private function getValue(string $key): ?string
    {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
        return null;
    }
}
