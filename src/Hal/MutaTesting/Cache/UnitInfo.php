<?php

namespace Hal\MutaTesting\Cache;

use Hal\MutaTesting\Test\UnitInterface;

class UnitInfo
{

    private $file;
    private $map = array();

    public function __construct() {
        $this->file = sys_get_temp_dir().'/muta-cache.php';
        if(!file_exists($this->file)) {
            $this->flush();
        }

        $this->refresh();
    }

    public function refresh() {
        $this->map = require $this->file;
    }

    public function flush() {
        $content = "<?php return "
        . var_export($this->map, true)
        . ';';
        file_put_contents($this->file, $content);
    }

    public function get(UnitInterface $unit) {
        return $this->has($unit) ? $this->map[md5($unit->getFile())] : null;
    }

    public function has(UnitInterface $unit) {
        if(!isset($this->map[md5($unit->getFile())])) {
            return false;
        }

        $dMap = \DateTime::createFromFormat('U', filemtime($this->file));
        $dUnit = \DateTime::createFromFormat('U', filemtime($unit->getFile()));
        return $dUnit < $dMap;

    }

    public function persist(UnitInterface $unit) {
        $this->map[md5($unit->getFile())] = $unit;
    }

}
