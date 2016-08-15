<?php namespace Nine\Collections;

/**
 * @package Nine Collections
 * @version 0.4.2
 * @author  Greg Truesdell <odd.greg@gmail.com>
 */

use Nine\Traits\WithItemArrayAccess;
use Nine\Traits\WithItemImportExport;

/**
 * **Paths provides a simple interface for handling paths in the F9 framework.**
 */
class Paths implements PathsInterface, \ArrayAccess
{
    // file and type import and export methods
    use WithItemImportExport;
    // standard set of array access methods
    use WithItemArrayAccess;

    /**
     * Paths constructor.
     *
     * @param array $data
     *
     * @throws \LogicException
     */
    public function __construct(array $data = [])
    {
        // verify/update paths
        foreach ($data as $key => $path)
            /** @noinspection AlterInForeachInspection */
            $data[$key] = $this->normalizePath($path);

        $this->items = $data;
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @param array $import
     *
     * @return $this|Paths
     * @throws \LogicException
     */
    public function merge($import) : Paths
    {
        # set normalize paths
        array_map(
            function ($key, $path) use (&$import) {
                $this->offsetSet($key, $this->normalizePath($path));
            },
            array_keys($import), array_values($import)
        );

        return $this;
    }

    /**
     * Adds a new path to the collection.
     *
     * @param string $key
     * @param string $path
     *
     * @return Paths|static
     * @throws \LogicException
     */
    public function set(string $key, $path) : Paths
    {
        $this->items[$key] = $path = $this->normalizePath($path);

        return $this;
    }

    /**
     * @param $path
     *
     * @return string
     * @throws \LogicException
     */
    private function normalizePath($path) : string
    {
        $orig = $path;

        return ! realpath($path) ? $orig : rtrim(realpath($path), '/') . '/';
    }

}
