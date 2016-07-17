<?php namespace Nine\Structure\Traits;

trait WithJsonSerializable /* implements JsonSerializable */
{
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @see \Nine\Structure\Traits\WithStructureToArray
     */
    abstract public function toArray();
}
