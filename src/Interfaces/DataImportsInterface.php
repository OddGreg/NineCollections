<?php namespace Nine\Collections\Interfaces;

/**
 * DataImportsInterface.php
 *
 * @project Collections
 * @created 2016-12-25 5:39 PM
 * @author  Greg Truesdell <odd.greg@gmail.com>
 */

interface DataImportsInterface
{
    /**
     * @param array $import
     */
    public function importArray(Array $import);

    /**
     * @param string $file
     */
    public function importFile($file);

    /**
     * Imports (merges) config files found in the specified directory.
     *
     * @param string $basePath
     * @param string $mask
     *
     * @return static
     */
    public function importFolder($basePath, $mask = '*.php');
}
