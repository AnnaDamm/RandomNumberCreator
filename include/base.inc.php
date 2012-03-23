<?php


spl_autoload_register(function($className) {
    require_once dirname(__FILE__) . '/classes/' . str_replace('\\', '/', $className) . '.php';
});