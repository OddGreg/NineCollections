<?php namespace Nine\Structure\Exceptions;

use RuntimeException;

/**
 * @package Nine Collections
 * @version 0.4.2
 *
 *
 */
class ImmutableException extends RuntimeException
{
    /**
     * @param string $class
     *
     * @return static
     */
    public static function cannotModify($class)
    {
        return new static(sprintf(
            'Cannot modify immutable class `%s` using array methods',
            $class
        ));
    }
}
