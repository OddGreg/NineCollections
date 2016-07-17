<?php namespace Nine\Collections;

use Nine\Traits\WithItemExport;

/**
 * @package Nine
 * @version 0.4.2
 * @author  Greg Truesdell <odd.greg@gmail.com>
 */
class ExportableClass
{
    use WithItemExport;

    /**
     * @var array
     */
    private $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function toJson()
    {
        return $this->exportJSON();
    }

}
