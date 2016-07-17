<?php namespace Nine\Structure;

use Nine\Structure\Exceptions\ValidationException;
use Nine\Structure\Interfaces\ListInterface;
use Nine\Structure\Traits\WithStructure;

class UnorderedList implements ListInterface
{
    use WithStructure;

    public function hasValue($value)
    {
        return in_array($value, $this->items, TRUE);
    }

    public function withValue($value)
    {
        $this->assertValid([$value]);

        $copy = clone $this;
        $copy->items[] = $value;

        return $copy;
    }

    public function withValues(array $values)
    {
        $this->assertValid($values);

        if ($this->items === $values) {
            return $this;
        }

        $copy = clone $this;
        $copy->items = $values;

        return $copy;
    }

    public function withoutValue($value)
    {
        $key = array_search($value, $this->items, TRUE);

        if ($key === FALSE) {
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

        if ($values !== array_values($values)) {
            throw ValidationException::invalid(
                'List structures cannot have distinct keys'
            );
        }
    }
}
