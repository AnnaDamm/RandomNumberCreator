<?php

namespace Random;

class Coin implements Thing {

    private $_result;

    public function __toString() {
        $result = $this->getResult();
        return "&copy;(" . (($result) ? "Heads" : "Tails") . ")";
    }

    public function getResult() {
        if (is_null($this->_result)) {
            $this->roll();
        }
        return $this->_result;
    }

    public function roll() {
        $this->_result = (mt_rand(1, 2) == 1);
    }

}
