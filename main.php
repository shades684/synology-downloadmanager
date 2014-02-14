#!/usr/syno/bin/php
<?php

use Lib\Download;
use Lib\Processable;
use Lib\Testing\Test;
use Lib\Utility\BigFileTools;
use Lib\Utility\Configuration;
use Lib\Utility\Logger;
use Lib\Utility\SplClassLoader;
use Lib\UpdateContext;

require_once('Lib/Utility/SplClassLoader.php');
$classLoader = new SplClassLoader('Lib', __DIR__);
$classLoader->register();

Configuration::init(__DIR__ . '/config.json');
Logger::init(__DIR__);
BigFileTools::init();

if ($argc > 1) {

    $param = implode(', ', array_map(function ($v, $k) {
        return $k . '=' . $v;
    }, $argv, array_keys($argv)));

    if (in_array('-test', $argv)) {
        $tests = new Test();
        $tests->run();
        exit(0);
    }
}

Logger::log("Starting download handling");

try {
    $context = new UpdateContext();
    $processed = array();
    $downloads = Download::getCompleted();

    foreach ($downloads as $download) {
        $processable = Processable::getByDownload($download);
        $processable->process($context);
    }

    $context->update();

} catch (\Exception $e) {
    Logger::log("Error : {$e->getMessage()}");
}

Logger::log("Finished download handling");