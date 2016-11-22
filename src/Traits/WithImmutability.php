<?php namespace Nine\Traits;

use Nine\Collections\Exceptions\ImmutableViolationException;

/**
 * This trait exposes immutability violation exceptions for common
 * methods.
 *
 * @see     \Nine\Collections\Attributes
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
        if (array_key_exists($key, $this->{'items'})) //if (NULL !== $key || NULL !== $value)
        {
            throw new ImmutableViolationException('Cannot replace an immutable item. (' . __LINE__ . ')');
        }

        $this->{'items'}[$key] = $value;
    }

    /**
     * @param       $key
     * @param mixed $value
     *
     * @throws ImmutableViolationException
     */
    public function offsetSet($key, $value)
    {
        if (isset($this->{'items'}[$key])) //if (NULL !== $key || NULL !== $value)
        {
            throw new ImmutableViolationException('Cannot replace an immutable item. (' . __LINE__ . ')');
        }

        $this->{'items'}[$key] = $value;
    }

    /**
     * @param mixed|string $key
     *
     * @throws ImmutableViolationException
     */
    public function offsetUnset($key)
    {
        if (array_key_exists($key, $this->{'items'})) {
            throw new ImmutableViolationException('Cannot remove an immutable item. (' . __LINE__ . ')');
        }
    }

}
