<?php

/**
 * @package Nine
 * @version 0.4.2
 * @author  Greg Truesdell <odd.greg@gmail.com>
 */
namespace Nine\Collections;

use Nine\Collections\Interfaces\RetrievableInterface;
use Nine\Collections\Interfaces\AssignableInterface;

/**
 * **Scope is a context container.**
 */
interface ScopeInterface extends AssignableInterface, RetrievableInterface
{
    /**
     * **Merge the scope with the provided array-able items.**
     *
     * @param  mixed $items
     *
     * @return $this
     */
    public function merge($items);

    /**
     * **Register a plugin.**
     *
     * Plugins are stored callable items identifiable by name.
     *
     * @param  string   $name
     * @param  callable $plugin
     *
     * @return void
     */
    public function plugin($name, callable $plugin);

}
