<?php namespace Nine;

/**
 * F9 (Formula 9) Personal PHP Framework
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

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Model;
use LogicException;
use Nine\Collections\Collection;
use Nine\Collections\Paths;
use Nine\Collections\Scope;
use Nine\Containers\Forge;
use Nine\Library\Lib;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Silex\Application;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

if (PHP_VERSION_ID < 70000) {
    echo('Formula 9 requires PHP versions >= 7.0.0');
    exit(1);
}



// if this helpers file is included more than once, then calculate
// the global functions exposed and return a simple catalog.

if (defined('HELPERS_LOADED')) {
    return true;
}

define('HELPERS_LOADED', TRUE);

if ( ! function_exists('app')) {
    /**
     * @param string $alias
     *
     * @return Application|mixed
     */
    function app($alias = NULL)
    {
        static $app;
        $app = $app ?: Forge::find('app');

        return $alias ? Forge::find($alias) : $app;
    }
}

if ( ! function_exists('array_accept')) {
    /**
     * Get all of the given array except for a specified array of items.
     *
     * @param  array|string $keys
     * @param  array        $array
     *
     * @return array
     */
    function array_except($array, $keys)
    {
        return array_diff_key($array, array_flip((array) $keys));
    }
}

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

if ( ! function_exists('config')) {
    /**
     * config(search_key, default)
     *
     * relies on `forge()`.
     *
     * @param null $search_key
     * @param null $default
     *
     * @return mixed
     */
    function config($search_key = NULL, $default = NULL)
    {
        return $search_key
            ? forge('config')->get($search_key, $default)
            : forge('config');
    }
}

if ( ! function_exists('applog')) {

    /**
     * Write an entry into a specific context log.
     *
     * Note that the written filename is "<local/logs/>$context.log".
     *
     * @param string $message
     * @param string $context
     */
    function applog($message, $context = 'info')
    {
        /** @var LoggerInterface $logger */
        static $logger;

        try {
            // try getting the framework logger
            $logger = $logger ?: forge('logger');

            // write the message
            $logger->log($context, $message);

        } catch (\InvalidArgumentException $e) {
            throw new \LogicException('applog(): no logger is available.');
        }

    }
}

if ( ! function_exists('dlog')) {
    /**
     * @param        $message
     * @param string $priority
     */
    function dlog($message, $priority = 'info')
    {
        if (env('DEBUG') and isset($app['logger'])) {
            app('logger')->log($priority, $message);
        }
    }
}

if ( ! function_exists('e')) {
    /**
     * Escape HTML entities in a string.
     *
     * @param  string $value
     *
     * @return string
     */
    function e($value)
    {
        return htmlentities($value, ENT_QUOTES, 'UTF-8', FALSE);
    }
}

if ( ! function_exists('elapsed_time_since_request')) {
    /**
     * @param bool $raw
     *
     * @return string
     */
    function elapsed_time_since_request($raw = FALSE)
    {
        return ! $raw
            ? sprintf('%8.1f ms', (microtime(TRUE) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000)
            : (microtime(TRUE) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000;
    }
}

if ( ! function_exists('env')) {
    /**
     * Gets the value of an environment variable. Supports boolean, empty and null.
     *
     * @param  string $key
     * @param  mixed  $default
     *
     * @return mixed
     */
    function env($key, $default = NULL)
    {
        $value = getenv($key);

        if ($value === FALSE) {
            return value($default);
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return TRUE;

            case 'false':
            case '(false)':
                return FALSE;

            case 'empty':
            case '(empty)':
                return '';

            case 'null':
            case '(null)':
                return NULL;
        }

        if (strlen($value) > 1 && Lib::starts_with('"', $value) && Lib::ends_with('"', $value)) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}

if ( ! function_exists('events')) {

    function events()
    {
        // object cache
        static $events = NULL;

        return $events ?: $events = forge('dispatcher');
    }
}

if ( ! function_exists('flash')) {

    /**
     * Retrieve or store a flash value.
     *
     * get: flash(
     *
     * @param string      $type
     * @param string|null $message
     *
     * @return array
     */
    function flash($type = NULL, $message = NULL)
    {
        $flashes = app('flashbag');

        if ( ! $type) {
            return $flashes;
        }

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $message ? $flashes->add($type, $message) : $flashes->get($type);
    }
}

if ( ! function_exists('forge')) {
    /**
     * @param $alias
     *
     * @return Forge|stdClass|callable
     */
    function forge($alias = NULL)
    {
        static $forge;
        $forge = $forge ?: Forge::getInstance();

        return NULL === $alias ? $forge : $forge::find($alias);
    }
}
else {
    throw new LogicException('Cannot continue: Forge not found.');
}

/**
 * Kernel
 */
if ( ! function_exists('kernel')) {

    function kernel($property = NULL)
    {
        // object cache
        static $kernel = NULL;
        $kernel = $kernel ?: $kernel = forge('kernel');

        return NULL === $property ? $kernel : $kernel->{$property}();
    }
}

if ( ! function_exists('database_path')) {

    /**
     * Returns the current database path.
     *
     * Note that this function mirrors the function of the same name
     * in the standalone Artisan application - giving Artisan the
     * ability to refer to the same directory is required.
     *
     * @param string|null $path
     *
     * @return string
     */
    function database_path($path = NULL)
    {
        return $path ? DATABASE . $path : DATABASE;
    }
}

if ( ! function_exists('pad_left')) {

    /**
     * Left-pad a string
     *
     * @param string $str
     * @param int    $length
     * @param string $space
     *
     * @return string
     */
    function pad_left($str, $length = 0, $space = ' ')
    {
        return str_pad($str, $length, $space, STR_PAD_LEFT);
    }
}

if ( ! function_exists('pad_right')) {

    /**
     * Left-pad a string
     *
     * @param string $str
     * @param int    $length
     * @param string $space
     *
     * @return string
     */
    function pad_right($str, $length = 0, $space = ' ')
    {
        return str_pad($str, $length, $space, STR_PAD_RIGHT);
    }
}

if ( ! function_exists('memoize')) {
    /**
     * Cache repeated function results.
     *
     * @param $lambda - the function whose results we cache.
     *
     * @return Closure
     */
    function memoize($lambda)
    {
        return function () use ($lambda) {
            # results cache
            static $results = [];

            # collect arguments and serialize the key
            $args = func_get_args();
            $key = serialize($args);

            # if the key result is not cached then cache it
            if (empty($results[$key])) {
                $results[$key] = call_user_func_array($lambda, $args);
            }

            return $results[$key];
        };
    }
}

if ( ! function_exists('model_id')) {

    /**
     * returns the ID of a model or the value of the argument.
     *
     * @param Model|integer|callable $model
     *
     * @return integer
     */
    function model_id($model)
    {
        return $model instanceof Model ? $model->{'id'} : value($model);
    }
}

if ( ! function_exists('is_not')) {

    function is_not($subject)
    {
        return ! $subject;
    }
}

if ( ! function_exists('partial')) {
    /**
     * Curry a function.
     *
     * @param $lambda - the function to curry.
     * @param $arg    - the first or only argument
     *
     * @return Closure
     */
    function partial($lambda, $arg)
    {
        $func_args = func_get_args();
        $args = array_slice($func_args, 1);

        return function () use ($lambda, $args) {
            $full_args = array_merge($args, func_get_args());

            return call_user_func_array($lambda, $full_args);
        };
    }
}

if ( ! function_exists('path')) {
    /**
     * @param null $key
     * @param null $default
     *
     * @return mixed|Paths
     */
    function path($key = NULL, $default = NULL)
    {
        return $key
            ? forge('paths')->get($key, $default)
            : forge('paths');
    }
}

if ( ! function_exists('redirect')) {

    /**
     * @param     $url
     * @param int $status
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    function redirect($url, $status = 302)
    {
        static $app;
        $app = $app ?: Forge::find('app');

        return $app->redirect($url, $status);
    }
}

if ( ! function_exists('request')) {

    /**
     * @param null   $uri
     * @param string $method
     * @param array  $parameters
     * @param array  $cookies
     * @param array  $files
     * @param array  $server
     * @param null   $content
     *
     * @return Request
     */
    function request($uri = NULL, $method = 'GET', array $parameters = [], array $cookies = [], array $files = [], array $server = [], $content = NULL)
    {
        $request = NULL === $uri
            // no uri provided, so either get the current request or create a new one
            ? forge()->has('request') ? forge('request') : Request::createFromGlobals()
            // uri provided, so create a new request
            : Request::create($uri, $method, $parameters, $cookies, $files, $server, $content);

        return $request;
    }
}

if ( ! function_exists('decorated_request')) {

    /**
     * @param string|null $uri
     * @param string      $method
     * @param array       $parameters
     * @param array       $cookies
     * @param array       $files
     * @param array       $server
     * @param null        $content
     *
     * @return Request
     */
    function decorated_request($uri = NULL, $method = 'GET', array $parameters = [], array $cookies = [], array $files = [], array $server = [], $content = NULL)
    {
        // the new request.
        $request = Request::create($uri, $method, $parameters, $cookies, $files, $server, $content);

        // handle the request as a sub-request, which populates $request->attributes if found.
        try {
            app()->handle($request, HttpKernelInterface::SUB_REQUEST, FALSE);
        } catch (Exception $e) {
            // the route could not be found, so flush the headers nad pretend nothing happened.
            flush();
        }

        // return the new request whether successfully decorated or not.
        return $request;
    }
}

if ( ! function_exists('response')) {
    /**
     * Returns a new response or the Response object.
     *
     * @param string $content
     * @param int    $status
     * @param array  $headers
     *
     * @return Response
     */
    function response($content = '', $status = 200, array $headers = [])
    {
        return Response::create($content, $status, $headers);
    }
}

if ( ! function_exists('scope')) {

    /**
     * Returns a reference to the global scope used primarily by views.
     *
     * @return Scope
     */
    function scope()
    {
        static $gs;
        $gs = $gs ?: Forge::find('global.scope');

        return $gs;
    }
}

if ( ! function_exists('share')) {

    /**
     * Merges data with the global scope, used by Views.
     *
     * @param $data
     */
    function share($data)
    {
        static $gs;
        $gs = $gs ?: scope();

        $gs->merge($data);
    }
}

if ( ! function_exists('value')) {
    /**
     *  Returns value of a variable. Resolves closures.
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

if ( ! function_exists('view')) {

    /**
     * @param string $template
     * @param array  $symbols
     *
     * @return string|null
     */
    function view($template, array $symbols = [])
    {
        // handle possible blade templates
        if (app()->has('blade.view') and app('blade.view')->hasView($template)) {
            return app('blade.view')->render($template, $symbols);
        }


        // handle possible twig templates
        if (app()->has('twig')) {

            // get the twig finder function
            $twig_has = app('twig.finder');

            if ( ! Lib::ends_with('.twig', $template)) {
                // TWIG is all that is left, so transform to <view>.html if necessary
                $twig_template = (FALSE === strpos($template, '.html')) ? "$template.html" : $template;
                $template = (FALSE === strpos($twig_template, '.twig')) ? "$twig_template.twig" : $twig_template;
            }

            // try locating the
            /** @var callable $twig_has */
            if ($twig_has($template)) {
                return app('twig')->render($template, $symbols);
            }
        }

        // there is no other renderer to use
        throw_now(RuntimeException::class, "Cannot determine suitable rendering engine for view `$template`.");

        // nothing to return
        return NULL;
    }
}

if ( ! function_exists('throw_now')) {

    /**
     * @param $exception
     * @param $message
     *
     * @return null
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

if ( ! function_exists('stopwatch')) {

    function stopwatch($event_name = NULL)
    {
        return $event_name ? app('stop.watch')->{'getEvent'}($event_name) : app('stop.watch');
    }
}

if ( ! function_exists('tail')) {
    // blatantly stolen from Ionuț G. Stan on stack overflow
    function tail($filename)
    {
        $line = '';

        $f = fopen(realpath($filename), 'r');
        $cursor = -1;

        fseek($f, $cursor, SEEK_END);
        $char = fgetc($f);

        /**
         * Trim trailing newline chars of the file
         */
        while ($char === "\n" || $char === "\r") {
            fseek($f, $cursor--, SEEK_END);
            $char = fgetc($f);
        }

        /**
         * Read until the start of file or first newline char
         */
        while ($char !== FALSE && $char !== "\n" && $char !== "\r") {
            /**
             * Prepend the new char
             */
            $line = $char . $line;
            fseek($f, $cursor--, SEEK_END);
            $char = fgetc($f);
        }

        return $line;
    }
}

if ( ! function_exists('dd')) {

    /**
     * Override Illuminate dd()
     *
     * @param null $value
     * @param int  $depth
     */
    function dd($value = NULL, $depth = 8)
    {
        ddump($value, $depth);
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
    function w($words, $delimiter = ' ')
    {
        return explode($delimiter, preg_replace('/\s+/', ' ', $words));
    }
}

if ( ! function_exists('ww')) {

    /**
     * Converts an encoded string to an associative array.
     *
     * ie:
     *      ww('one:1, two:2, three:3') -> ["one" => 1,"two" => 2,"three" => 3,]
     *
     * @param $tuples
     *
     * @return array
     */
    function ww($tuples)
    {
        $array = w($tuples, ',');
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
