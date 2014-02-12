<?php
/**
 * Created by PhpStorm.
 * User: Robbert Beesems
 * Date: 7-2-14
 * Time: 16:45
 */

namespace Lib\Web;

/**
 * Utility class for handling and creating URLs.
 */
class URL
{
    /**
     * Construct object.
     *
     * @param string $url Base URL.
     */
    public function __construct($url)
    {
        if(FALSE === ($this->elements = parse_url($url)))
            throw new \InvalidArgumentException("'$url' is not a valid URL");
    }

    /**
     * @return string
     */
    public function toString()
    {
        return self::unparseURL($this->elements);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Overwrites a specific part of the contained URL, by replacing or merging parameters.
     *
     * @param string $section
     * @param array $parameters
     * @param bool $merge
     */
    private function setParameters($section, array $parameters, $merge = false)
    {
        if($merge) {
            parse_str($this->elements[$section], $params);
            $parameters = $params + $parameters;
        }
        $this->elements[$section] = http_build_query($parameters);
    }

    /**
     * Determines whether a string looks like an absolute URL.
     *
     * @param $input String to be checked
     * @return bool
     */
    public static function isAbsoluteURL($input)
    {
        return (bool)preg_match('#^([a-z]+:)?//.+/#', $input);
    }

    /**
     * Returns a URL with adapted GET parameters.
     *
     * @param string $url
     * @param array $parameters
     * @return string
     */
    public static function mergeGetParameters($url, array $parameters)
    {
        $url = new URL($url);
        $url->setParameters('query', $parameters, true);
        return $url->toString();
    }

    /**
     * Returns a URL with adapted fragment parameters.
     *
     * @param string $url
     * @param array $parameters
     * @return string
     */
    public static function mergeFragmentParameters($url, array $parameters)
    {
        $url = new URL($url);
        $url->setParameters('fragment', $parameters, true);
        return $url->toString();
    }

    /**
     * Creates a URL string from an array in the format returned by parse_url.
     *
     * Based on PHP.net comments section, so possibly unreliable.
     *
     * @link http://www.php.net/parse_url#106731
     * @param array $parsed_url
     * @return string
     */
    public static function unparseURL(array $parsed_url)
    {
        $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }

    private $elements;
}