<?php namespace Nine\Structure\Traits;

use Nine\Structure\Interfaces\StructureInterface;

trait WithIsSimilar
{
    public function isSimilar(StructureInterface $target)
    {
        return $target instanceof self;
    }
}
