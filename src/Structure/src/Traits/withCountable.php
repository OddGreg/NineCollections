<?php namespace Nine\Structure\Traits;

trait withCountable /* implements Countable */
{
    public function count()
    {
        return count($this->items);
    }
}
