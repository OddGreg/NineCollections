<?php namespace Nine\Structure\Traits;

use Nine\Structure\Interfaces\StructureInterface;

trait WithStructureToArray
{
    public function toArray()
    {
        return array_map(
            static function ($value) {
                if ($value instanceof StructureInterface) {
                    $value = $value->toArray();
                }

                return $value;
            },
            $this->items
        );
    }
}
