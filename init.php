<?php

spl_autoload_register(function($class) {
            if (false !== strpos($class, 'Hal\\MutaTesting')) {
                $filename = __DIR__ . '/src/' . str_replace('\\', '/', $class) . '.php';
                if (file_exists($filename)) {
                    require_once(__DIR__ . '/src/' . str_replace('\\', '/', $class) . '.php');
                    return true;
                }
            }
        }, true, false);

require_once 'vendor/autoload.php';
$dispatcher = new Symfony\Component\EventDispatcher\EventDispatcher();
$app = new Hal\MutaTesting\Console\MutaTestingApplication();
$app->setDispatcher($dispatcher);
$app->run();