<?php

/**
 * F9 (Formula Nine) Personal PHP Framework
 *
 * Copyright (c) 2010-2016, Greg Truesdell (<odd.greg@gmail.com>)
 * License: MIT (reference: https://opensource.org/licenses/MIT)
 *
 * Acknowledgements:
 *  - The code provided in this file (and in the Framework in general) may include
 * open sourced software licensed for the purpose, refactored code from related
 * packages, or snippets/methods found on sites throughout the internet.
 *  - All originator copyrights remain in force where applicable, as well as their
 *  licenses where obtainable.
 */

/**
 * Globally accessible convenience functions.
 *
 * @note    Please DO NOT USE THESE INDISCRIMINATELY!
 *       These functions (and those appended at the end)
 *       are intended mainly for views, testing and
 *       implementation hiding when temporarily useful.
 *
 * @package Nine
 * @version 0.4.2
 * @author  Greg Truesdell <odd.greg@gmail.com>
 */

use Nine\Collections\Collection;

if (PHP_VERSION_ID < 70000) {
    echo('Formula 9 requires PHP versions >= 7.0.0');
    exit(1);
}

// if this helpers file is included more than once, then calculate
// the global functions exposed and return a simple catalog.

if (defined('SUPPORT_HELPERS_LOADED')) {
    return TRUE;
}

define('SUPPORT_HELPERS_LOADED', TRUE);

if ( ! function_exists('collect')) {
    /**
     * Returns a collection containing the array values provided.
     *
     * @param array $array
     *
     * @return Collection
     */
    function collect(array $array)
    {
        return new Collection($array);
    }
}

if ( ! function_exists('is_not')) {

    function is_not($subject)
    {
        return ! $subject;
    }
}

if ( ! function_exists('throw_now')) {

    /**
     * @param $exception
     * @param $message
     */
    function throw_now($exception, $message)
    {
        throw new $exception($message);
    }
}

if ( ! function_exists('throw_if')) {
    /**
     * @param string  $exception
     * @param string  $message
     * @param boolean $if
     */
    function throw_if($if, $exception, $message)
    {
        if ($if) {
            throw new $exception($message);
        }
    }
}

if ( ! function_exists('throw_if_not')) {
    /**
     * @param string  $exception
     * @param string  $message
     * @param boolean $if
     */
    function throw_if_not($if, $exception, $message)
    {
        if ( ! $if) {
            throw new $exception($message);
        }
    }
}

if ( ! function_exists('w')) {

    /**
     * Converts a string of space or tab delimited words as an array.
     * Multiple whitespace between words is converted to a single space.
     *
     * ie:
     *      w('one two three') -> ['one','two','three']
     *      w('one:two',':') -> ['one','two']
     *
     *
     * @param string $words
     * @param string $delimiter
     *
     * @return array
     */
    function w($words, $delimiter = ' ') : array
    {
        return explode($delimiter, preg_replace('/\s+/', ' ', $words));
    }
}

if ( ! function_exists('tuples')) {

    /**
     * Converts an encoded string to an associative array.
     *
     * ie:
     *      tuples('one:1, two:2, three:3') -> ["one" => 1,"two" => 2,"three" => 3,]
     *
     * @param $encoded_string
     *
     * @return array
     */
    function tuples($encoded_string) : array
    {
        $array = w($encoded_string, ',');
        $result = [];

        foreach ($array as $tuple) {
            $ra = explode(':', $tuple);

            $key = trim($ra[0]);
            $value = trim($ra[1]);

            $result[$key] = is_numeric($value) ? (int) $value : $value;
        }

        return $result;
    }
}
