<?php

class Configuration
{
    private static $instance;
    private $constants;

    public function __construct($configFile = 'config.ini')
    {
        if (!file_exists($configFile)) {
            throw new Exception("Config file {$configFile} does not exist!");
        }
        $this->constants = parse_ini_file($configFile);
    }

    public function __get($name) {
        if (isset($this->constants[$name])) {
            return $this->constants[$name];
        }
    }

    public static function get($name) {
        return self::getInstance()->$name;
    }

    public function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
