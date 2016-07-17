<?php namespace Nine\Structure\Traits;

trait WithStructure /* implements StructureInterface */
{
    use WithArrayAccess;
    use WithStructureToArray;
    use WithIsSimilar;
    use withCountable;
    use WithIterator;
    use WithSerializable;
    use WithJsonSerializable;
    use WithAssertValid;

    /**
     * @var array
     */
    protected $items = [];

    public function __construct(array $items = [])
    {
        $this->assertValid($items);
        $this->items = $items;
    }
}
