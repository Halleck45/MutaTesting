<?php

namespace Hal\MutaTesting\Runner\Adapter;

use Hal\MutaTesting\Runner\Process\ProcessManagerInterface;
use \Exception;

class AdapterFactory
{

    public function factory($name, $binary, $path, $options = array(), ProcessManagerInterface $processManager = null)
    {
        $name = strtolower($name);
        switch ($name) {
            case 'phpunit':
                return new PHPUnitAdapter($binary, $path, $options, $processManager);
            case 'atoum':
                return new AtoumAdapter($binary, $path, $options, $processManager);
            default:
                throw new Exception(sprintf('Unsupported adapter : "%s"', $name));
        }
    }

}
