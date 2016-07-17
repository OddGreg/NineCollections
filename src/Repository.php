<?php namespace Nine\Collections;

use Nine\Collections\Exceptions\RepositoryAppendNotPossibleException;
use Nine\Collections\Exceptions\RepositoryPrependNotPossibleException;
use Nine\Collections\Interfaces\RepositoryInterface;
use Nine\Library\Lib;

/**
 * @package Nine Collections
 * @version 0.4.2
 */
class Repository implements RepositoryInterface, \ArrayAccess
{
    /** @var array */
    protected $items = [];

    /** @var Lib */
    protected $lib;

    /**
     * Create a new configuration repository.
     *
     * @param  array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
        $this->lib = new Lib();
    }

    /**
     * Get all of the configuration items for the application.
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Push a value onto an array configuration value.
     *
     * @param  string $key
     * @param  mixed  $value
     *
     * @throws RepositoryAppendNotPossibleException
     */
    public function append($key, $value)
    {
        $array = $this->get($key);

        if (is_array($array)) {
            $array[] = $value;
            $this->set($key, $array);

            return;
        }

        throw new RepositoryAppendNotPossibleException(
            'Append requires that the target be an array. ' . gettype($array) . ' given.');
    }

    /**
     * Get the specified configuration value.
     *
     * @param  string $key
     * @param  mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = NULL)
    {
        return $this->lib->array_get($this->items, $key, $default);
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param  string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return $this->lib->array_has($this->items, $key);
    }

    /**
     * Determine if the given configuration option exists.
     *
     * @param  string $key
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * Get a configuration option.
     *
     * @param  string $key
     *
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set a configuration option.
     *
     * @param  string $key
     * @param  mixed  $value
     *
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Unset a configuration option.
     *
     * @param  string $key
     *
     * @return void
     */
    public function offsetUnset($key)
    {
        $this->set($key, NULL);
    }

    /**
     * Prepend a value onto an array value.
     *
     * @param  string $key
     * @param  mixed  $value
     *
     * @return void
     * @throws \Nine\Collections\Exceptions\RepositoryPrependNotPossibleException
     */
    public function prepend($key, $value)
    {
        $array = $this->get($key);

        if (is_array($array)) {
            array_unshift($array, $value);
            $this->set($key, $array);

            return;
        }

        throw new RepositoryPrependNotPossibleException(
            'Prepend requires that the target be an array. ' . gettype($array) . ' given.');
    }

    /**
     * Set a given configuration value.
     *
     * @param  array|string $key
     * @param  mixed        $value
     *
     * @return void
     */
    public function set($key, $value = NULL)
    {
        if (is_array($key)) {
            foreach ($key as $innerKey => $innerValue) {
                $this->lib->array_set($this->items, $innerKey, $innerValue);
            }
        }
        else {
            $this->lib->array_set($this->items, $key, $value);
        }
    }

}
