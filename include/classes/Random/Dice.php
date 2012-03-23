<?php

namespace Random;

class Dice implements Thing {

    private $_sides;
    private $_result;

    public function __construct($sides = 6) {
        $filteredSides = filter_var($sides, FILTER_VALIDATE_INT,
            array("options" => array(
                "min_range" => 2,
                "max_range" => 10000,
            )));
        if (empty($filteredSides)) {
            throw new Exception("A dice may have between 2 and 10,000 sides!");
        }
        $this->_sides = $sides;
    }

    public function getSides() {
        return $this->_sides;
    }

    public function __toString() {
        $result = $this->getResult();
        return "d{$this->_sides}({$result})";
    }

    public function roll() {
        $this->_result = mt_rand(1, $this->_sides);
        return $this->getResult();
    }

    public function getResult() {
        if (is_null($this->_result)) {
            $this->roll();
        }
        return $this->_result;
    }

}