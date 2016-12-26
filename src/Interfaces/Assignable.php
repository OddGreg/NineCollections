<?php namespace Nine\Collections\Interfaces;

/**
 * @package Nine
 * @version 0.4.2
 * @author  Greg Truesdell <odd.greg@gmail.com>
 */

interface Assignable
{
    /**
     * Put an item in storage by key.
     *
     * @param  string $key
     * @param  mixed $value
     *
     * @return $this
     */
    public function set(string $key, $value);
}
