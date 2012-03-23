<?php
class Template
{
    private $fileName;
    private $variables = array();


    public function __construct($actionName) {
        if (!file_exists('templates/' . $actionName . '.tpl.php')) {
            throw new Exception("There is no template for action {$actionName}");
        }
        $this->fileName = 'templates/' . $actionName . '.tpl.php';
    }

    public function __set($name, $value) {
        $this->variables[$name] = $value;
    }

    public function __get($name) {
        if (isset($this->variables[$name])) {
            return $this->variables[$name];
        }
        return false;
    }

    public function __unset($name) {
        if (isset($this->variables[$name])) {
            unset($this->variables[$name]);
        }
    }

    public function __isset($name) {
        return isset($this->variables[$name]);
    }

    public function __toString() {
        return $this->getContents();
    }

    public function getContents() {
        extract($this->variables);
        if (ob_get_level() > 0) {
            ob_flush();
        }
        ob_start();
        include $this->fileName;
        return ob_get_clean();
    }
}
