<?php namespace Nine\Collections;

/**
 * @package Nine
 * @version 0.4.2
 * @author  Greg Truesdell <odd.greg@gmail.com>
 */

use BadMethodCallException;
use Closure;
use Nine\src\Traits\WithItemsToArray;
use Nine\Traits\WithItemArrayAccess;
use Nine\Traits\WithItemImport;
use Nine\Traits\WithItemTransforms;

/**
 * **Scope is a context container.**
 */
class Scope implements ScopeInterface, \ArrayAccess, \Countable, \JsonSerializable
{
    use WithItemImport;
    use WithItemTransforms;
    use WithItemArrayAccess;
    use WithItemsToArray;

    /** @var array */
    //protected $items;

    /** @var array */
    protected $plugins;

    /**
     * **The Scope constructor accepts an array or any arrayable object.**
     *
     * @param array $items
     */
    public function __construct($items = [])
    {
        $this->items = is_array($items) ? $items : $this->getArrayableItems($items);
    }

    /**
     * **Call handler for plugins.**
     *
     * @param  string $method
     * @param  array  $parameters
     *
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if ($this->hasPlugin($method)) {

            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $method = $this->plugins[$method];

            /** @var Closure $method */
            if ($method instanceof Closure) {
                return call_user_func_array($method->bindTo($this, get_class($this)), $parameters);
            }

            return call_user_func_array($method, $parameters);
        }

        throw new BadMethodCallException("Plug-in {$method} does not exist.");
    }

    /**
     * **The count of items in the Scope.**
     *
     * @return int
     */
    public function count() : int
    {
        return count($this->items);
    }

    /**
     * **Forget a plugin if it exists.**
     *
     * @param string $pluginName The name of the plugin. May include '.' and '_'
     */
    public function forgetPlugin($pluginName)
    {
        if (array_key_exists($pluginName, $this->plugins)) {
            unset($this->plugins[$pluginName]);

            ksort($this->plugins);
        }
    }

    /**
     * **Checks if plugin has been registered.**
     *
     * @param  string $name
     *
     * @return bool
     */
    public function hasPlugin($name) : bool
    {
        return array_key_exists($name, (array) $this->plugins);
    }

    /**
     * **Returns all items as a json string.**
     *
     * @return string
     */
    public function jsonSerialize() : string
    {
        return json_encode($this->items);
    }

    /**
     * **Merge the scope with the provided arrayable items.**
     *
     * @param  mixed $items
     *
     * @return $this|array
     */
    public function merge($items) : array
    {
        return $this->items = array_merge($this->items, $this->getArrayableItems($items));
    }

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
    public function plugin($name, callable $plugin)
    {
        $this->plugins[$name] = $plugin;
    }

    /**
     * Put an item in storage by key.
     *
     * @param  mixed $key
     * @param  mixed $value
     *
     * @return void
     */
    public function set(string $key, $value)
    {
        $this->items[$key] = $value;
    }


    /**
     * **Get the collection of items as JSON.**
     *
     * @param  int $options
     *
     * @return string
     */
    public function toJson($options = 0) : string
    {
        return json_encode($this->toArray(), $options);
    }
}
