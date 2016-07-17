<?php namespace Nine\Structure\Traits;

trait WithSerializable /* implements Serializable */
{
    use WithAssertValid;

    public function serialize()
    {
        return serialize($this->items);
    }

    public function unserialize($values)
    {
        $values = unserialize($values);
        $this->assertValid($values);
        $this->items = $values;
    }
}
