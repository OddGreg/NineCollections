<?php namespace Nine\Collections\Interfaces;

/**
 * CreatesWithData.php
 *
 * @project Collections
 * @created 2016-12-25 5:41 PM
 * @author  Greg Truesdell <odd.greg@gmail.com>
 */

interface CreatesWithData
{
    /**
     *
     * @param string $folder
     *
     * @return static
     */
    public static function createFromFolder($folder);

    /**
     * @param string $json - filename or JSON string
     *
     * @return static
     */
    public static function createFromJson($json);

    /**
     * @param $yaml
     *
     * @return static
     */
    public static function createFromYaml($yaml);
}
