<?php namespace Nine\Structure;

use Nine\Structure\Exceptions\ValidationException;
use Nine\Structure\Interfaces\DictionaryInterface;
use Nine\Structure\Traits\WithStructure;

class Dictionary implements DictionaryInterface
{
    use WithStructure;

    public function getValue($key, $default = NULL)
    {
        if ($this->hasValue($key)) {
            return $this->items[$key];
        }

        return $default;
    }

    public function hasValue($key)
    {
        return array_key_exists($key, $this->items);
    }

    public function withValue($key, $value)
    {
        if ($this->hasValue($key) && $this->getValue($key) === $value) {
            return $this;
        }

        $this->assertValid([$key => $value]);

        $copy = clone $this;
        $copy->items[$key] = $value;

        return $copy;
    }

    public function withValues(array $values)
    {
        if ($this->items === $values) {
            return $this;
        }

        $this->assertValid($values);

        $copy = clone $this;
        $copy->items = $values;

        return $copy;
    }

    public function withoutValue($key)
    {
        if ( ! $this->hasValue($key)) {
            return $this;
        }

        $copy = clone $this;
        unset($copy->items[$key]);

        return $copy;
    }

    protected function assertValid(array $values)
    {
        if (0 === count($values)) {
            return;
        }

        $keys = array_keys($values);
        $items = array_values($values);

        if ($keys === array_keys($items)) {
            throw ValidationException::invalid(
                'Dictionary values must have distinct keys'
            );
        }
    }
}
