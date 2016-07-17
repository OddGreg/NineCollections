<?php namespace Nine\Structure\Traits;

trait WithIterator
{
    public function current()
    {
        return current($this->items);
    }

    public function key()
    {
        return key($this->items);
    }

    public function next()
    {
        next($this->items);
    }

    public function rewind()
    {
        reset($this->items);
    }

    public function valid()
    {
        $key = $this->key();

        return isset($this->items[$key]);
    }
}
