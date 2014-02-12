<?php

namespace Lib\Utility;

class Logger
{
    /**
     * @var Logger $instance
     */
    private static $instance;
    private $enabled;
    private $file;
    private $fp;

    public static function init($baseDir)
    {
        self::$instance = new Logger($baseDir);
    }

    public static function log($text)
    {
        if (self::$instance === null) {
            throw new \Exception("Logging has not been instatiated");
        }

        self::$instance->write($text);
    }

    private function __construct($baseDir)
    {
        $configuration = Configuration::getInstance();

        $this->enabled = $configuration->get('log/enabled', false);
        $this->file = $configuration->get('log/file', false);
        $path = $configuration->get('log/path', $baseDir);

        if ($this->enabled && $this->file) {
            $this->fp = fopen($path . DIRECTORY_SEPARATOR . $this->file, 'a');
            register_shutdown_function(array($this, 'close'));
        }
    }

    public function write($message)
    {
        if ($this->enabled) {
            $time = @date('[d/M/Y:H:i:s]');
            if ($this->file) {
                fwrite($this->fp, "$time : $message" . PHP_EOL);
            } else {
                echo "$time : $message\n\r";
            }
        }
    }

    public function close()
    {
        fclose($this->fp);
    }
}