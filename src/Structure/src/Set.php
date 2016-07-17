<?php namespace Nine\Structure;

use Nine\Structure\Exceptions\ValidationException;
use Nine\Structure\Interfaces\SetInterface;
use Nine\Structure\Traits\WithStructure;

class Set implements SetInterface
{
    use WithStructure;

    public function hasValue($value)
    {
        return in_array($value, $this->items, TRUE);
    }

    public function withValue($value)
    {
        if ($this->hasValue($value)) {
            return $this;
        }

        $this->assertValid([$value]);

        $copy = clone $this;
        $copy->items[] = $value;

        return $copy;
    }

    public function withValueAfter($value, $search)
    {
        if ($this->hasValue($value)) {
            return $this;
        }

        $this->assertValid([$value]);
        $copy = clone $this;

        $key = array_search($search, $this->items, TRUE);
        if ($key === FALSE) {
            $copy->items[] = $value;

            return $copy;
        }

        array_splice($copy->items, $key + 1, 0, $value);

        return $copy;
    }

    public function withValueBefore($value, $search)
    {
        if ($this->hasValue($value)) {
            return $this;
        }

        $this->assertValid([$value]);

        $copy = clone $this;

        $key = array_search($search, $this->items, TRUE);
        if ($key === FALSE) {
            array_unshift($copy->items, $value);
        }
        else {
            array_splice($copy->items, $key, 0, $value);
        }

        return $copy;
    }

    public function withValues(array $values)
    {
        $this->assertValid($values);

        $copy = clone $this;
        $copy->items = array_unique($values, SORT_REGULAR);

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
                'Set structures cannot have distinct keys'
            );
        }
    }
}
