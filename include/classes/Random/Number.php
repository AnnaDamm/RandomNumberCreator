<?php

namespace Random;

class Number implements Thing {

    private $_min;
    private $_max;
    private $_result;

    public function __construct($min, $max) {
        $filterMin = filter_var($min, FILTER_VALIDATE_INT);
        $filterMax = filter_var($max, FILTER_VALIDATE_INT,
            array("options" => array("min_range" => $filterMin + 1)));
        if (empty($filterMin) || empty($filterMax)) {
            throw new Exception("Could not parse random string!");
        }
        $this->_min = $filterMin;
        $this->_max = $filterMax;
    }

    public function __toString() {
        $result = $this->getResult();
        return "{$this->_min}-{$this->_max}({$result})";
    }

    public function roll() {
        return $this->_result = mt_rand($this->_min, $this->_max);
    }

    public function getResult() {
        if (is_null($this->_result)) {
            $this->roll();
        }
        return $this->_result;
    }

    public function getMin() {
        return $this->_min;
    }

    public function getMax() {
        return $this->_max;
    }

}