<?php namespace Nine\Structure\Traits;

use Nine\Structure\Exceptions\ImmutableException;

trait WithArrayAccess
{
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    public function offsetSet(
        /** @noinspection PhpUnusedParameterInspection */
        $offset, $value)
    {
        throw ImmutableException::cannotModify(get_class($this));
    }

    public function offsetUnset(
        /** @noinspection PhpUnusedParameterInspection */
        $offset)
    {
        throw ImmutableException::cannotModify(get_class($this));
    }
}
