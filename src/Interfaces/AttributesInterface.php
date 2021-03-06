<?php namespace Nine\Collections;

/**
 * @package Nine
 * @version 0.4.2
 * @author  Greg Truesdell <odd.greg@gmail.com>
 */

use ArrayAccess;
use Nine\Collections\Exceptions\ImmutableViolationException;
use Nine\Collections\Interfaces\Retrievable;
use Nine\Collections\Interfaces\Assignable;

/**
 * **A simple immutable attribute collection. **
 *
 * Set once and never again.
 *
 * Attributes may be used anywhere Scope (or context) is useful. The class
 * Adds context-appropriate access methods to the array of featured
 * provided by Scope.
 *
 * @package Nine Collections
 * @version 0.4.2
 * @author  Greg Truesdell
 */
interface AttributesInterface extends ArrayAccess
{
    /**
     * **Returns an array-able clone of this class.**
     *
     * @return static
     */
    public function getAttributes();

    /**
     * **Set attributes. This will destroy and replace the current items.**
     *
     * @param $attributes
     *
     * @return $this
     *
     * @throws ImmutableViolationException
     */
    public function setAttributes($attributes);

    /**
     * **Get the collection of items as JSON.**
     *
     * @param  int $options
     *
     * @return string
     */
    public function toJson($options = 0) : string;
}
