<?php namespace Nine\src\Traits;

/**
 * @package Nine Loader
 * @version 0.5.0
 * @author  Greg Truesdell <odd.greg@gmail.com>
 */

trait WithItemsToArray
{
    protected $items;

    /**
     * **Get the collection of items as a PHP array.**
     *
     * @return array
     */
    public function toArray() : array
    {
        return array_map(function ($value)
        {
            return is_object($value) && method_exists($value, 'toArray')
                ? $value->toArray()
                : $value;

        }, $this->items);
    }
}
