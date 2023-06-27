<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../db.class.php';

class TestKernel {
    public function getContainer() {
        return new TestContainer();
    }

    public static function boot() {
        global $kernel;

        $kernel = new static();
        mysqli_report(MYSQLI_REPORT_OFF);
    }
}

class TestContainer {
    private $services = array();
    /**
     * @throws Exception
     */
    public function get($service) {
        if ($service === 'lokalise.adapter.database') {
            $this->services[$service] = new MeekroDB();
        }

        if (isset($this->services[$service]))
            return $this->services[$service];

        throw new Exception("Service $service not found");
    }
}