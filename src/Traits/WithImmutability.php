<?php namespace Nine\Traits;

use Nine\Collections\Exceptions\ImmutableViolationException;

/**
 * This trait exposes immutability violation exceptions for common
 * methods.
 *
 * @see     Nine\Attributes
 *
 * @package Nine Traits
 * @version 0.4.2
 * @author  Greg Truesdell <odd.greg@gmail.com>
 */
trait WithImmutability
{
    /**
     * @param string $key
     * @param mixed  $value
     *
     * @throws ImmutableViolationException
     */
    public function __set($key, $value)
    {
        if (NULL !== $key || NULL !== $value)
        {
            throw new ImmutableViolationException();
        }
    }

    /**
     * @param       $key
     * @param mixed $value
     *
     * @throws ImmutableViolationException
     */
    public function offsetSet($key, $value)
    {
        if (NULL !== $key || NULL !== $value)
        {
            throw new ImmutableViolationException();
        }
    }

    /**
     * @param mixed|string $key
     *
     * @throws ImmutableViolationException
     */
    public function offsetUnset($key)
    {
        if (NULL !== $key)
        {
            throw new ImmutableViolationException('Cannot remove an immutable item.');
        }
    }

}
