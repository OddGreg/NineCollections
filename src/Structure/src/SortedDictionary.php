<?php namespace Nine\Structure;

class SortedDictionary extends Dictionary
{
    public function withValue($key, $value)
    {
        /** @noinspection PhpParamsInspection */
        return $this->sortChanged(
            parent::withValue($key, $value)
        );
    }

    public function withValues(array $values)
    {
        /** @noinspection PhpParamsInspection */
        return $this->sortChanged(
            parent::withValues($values)
        );
    }

    public function withoutValue($key)
    {
        /** @noinspection PhpParamsInspection */
        return $this->sortChanged(
            parent::withoutValue($key)
        );
    }

    /**
     * Sorts values, respecting keys.
     *
     * @return void
     */
    protected function sortValues()
    {
        asort($this->items);
    }

    /**
     * Sorts the dictionary if it is not the same.
     *
     * @param SortedDictionary $copy
     *
     * @return SortedDictionary
     */
    private function sortChanged(SortedDictionary $copy)
    {
        if ($copy !== $this) {
            $copy->sortValues();
        }

        return $copy;
    }
}
