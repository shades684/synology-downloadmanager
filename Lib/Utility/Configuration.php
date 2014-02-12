<?php
/**
 * Created by PhpStorm.
 * User: Robbert Beesems
 * Date: 7-2-14
 * Time: 11:49
 */

namespace Lib\Utility;

/**
 * Wrapper around JSON configuration files used in various places in the system.
 */
class Configuration
{
    private static $instance;

    public static function init($config)
    {
        self::$instance = new Configuration($config);
    }

    /**
     * @param null $config
     * @return Configuration
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            throw new \Exception("Configuration has not been instatiated");
        }

        return self::$instance;
    }

    /**
     * Constructs a configuration from either a prepared object, a JSON string, or filename.
     *
     * @param mixed $config A prepared object, a JSON string, or filename.
     * @throws \InvalidArgumentException If the config could not be parsed.
     */
    private function __construct($config = null)
    {
        if (!isset($config))
            return;
        elseif ($config instanceof Configuration)
            $this->config = $config->config;
        elseif (is_object($config) || is_array($config))
            $this->config = $config;
        elseif (file_exists($config)) {
            $file = file_get_contents($config);
            $this->config = json_decode($file);
            if (!isset($this->config))
                throw new \InvalidArgumentException("Could not decode file $config");
        } else {
            $this->config = json_decode($config);
            if (!isset($this->config))
                throw new \InvalidArgumentException("Could not decode file $config");
        }
    }

    /**
     * Retrieves a config element. Allows slash-separated paths into objects and arrays. By default returns the root elements.
     *
     * @param string|array|null $path Optional path to a sub-element, separated by slashes.
     * @param mixed $default The value to be returned if the element does not exist.
     * @return mixed The value of the config element.
     */
    public function get($path = null, $default = null)
    {
        if (!$path)
            return $this->config;
        $elements = is_array($path) ? $path : explode('/', $path);
        $config = $this->config;
        while ($element = array_shift($elements)) {
            if (isset($config->$element))
                $config = $config->$element;
            elseif (is_array($config) && isset($config[$element]))
                $config = $config[$element];
            else
                return $default;
        }
        return $config ? : $default;
    }

    private $config;
}
