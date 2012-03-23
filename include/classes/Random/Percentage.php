<?php

namespace Random;

class Percentage implements Thing {

    private $_percentages;
    private $_result;

    public function __construct(Array $percentages) {
        foreach ($percentages as $currentPercentage) {
            $filteredPercentage = filter_var($currentPercentage,
                FILTER_VALIDATE_INT,
                array("options" => array("min_range" => 1)));
            if (empty($filteredPercentage)) {
                throw new Exception("Could not parse percentage!");
            }
            $this->_percentages[] = $filteredPercentage;
        }
    }

    public function __toString() {
        $result = $this->getResult();
        return implode(":", $this->_percentages) . "(no. $result)";
    }

    public function getResult() {
        if (is_null($this->_result)) {
            $this->roll();
        }
        return $this->_result;
    }

    public function roll() {
        $sumOfPercentages = array_sum($this->_percentages);
        $randomNumber = mt_rand(1, $sumOfPercentages);
        $result = 0;
        $currentSum = 0;
        do {
            $currentSum += $this->_percentages[$result];
            ++$result;
        } while ($currentSum < $randomNumber);
        return $this->_result = $result;
    }

}