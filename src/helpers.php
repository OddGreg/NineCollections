<?php

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
use Nine\Library\Lib;

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

if ( ! function_exists('collect') && ! class_exists('\Illuminate\Support\Collection')) {
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

if ( ! function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed $value
     *
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
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

if ( ! function_exists('data_get')) {
    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param  mixed        $target
     * @param  string|array $key
     * @param  mixed        $default
     *
     * @return mixed
     */
    function data_get($target, $key, $default = NULL)
    {
        if (is_null($key)) {
            return $target;
        }

        $key = is_array($key) ? $key : explode('.', $key);

        while (($segment = array_shift($key)) !== NULL) {
            if ($segment === '*') {
                if ($target instanceof Collection) {
                    $target = $target->all();
                }
                elseif ( ! is_array($target)) {
                    return value($default);
                }

                $result = Lib::array_pluck($target, $key);

                /** @noinspection TypeUnsafeArraySearchInspection */
                return in_array('*', $key) ? Lib::array_collapse($result) : $result;
            }

            if (Lib::array_accessible($target) && Lib::array_exists($target, $segment)) {
                $target = $target[$segment];
            }
            elseif (is_object($target) && isset($target->{$segment})) {
                $target = $target->{$segment};
            }
            else {
                return value($default);
            }
        }

        return $target;
    }
}
