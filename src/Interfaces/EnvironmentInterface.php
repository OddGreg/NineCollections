<?php namespace Nine\Collections\Interfaces;

/**
 * @package Nine
 * @version 0.4.2
 * @author  Greg Truesdell <odd.greg@gmail.com>
 */

interface EnvironmentInterface extends Retrievable
{
    /**
     * @return array - the environment settings data.
     */
    public function detectEnvironment() : array;
}
