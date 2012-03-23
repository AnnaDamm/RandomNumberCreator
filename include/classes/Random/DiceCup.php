<?php

namespace Random;

class DiceCup {

    private $_dice;
    private $_plus;
    private $_randomClass;

    public function __construct($dice = null) {
        $this->_dice = array();
        $this->_plus = 0;
        if (!is_null($dice)) {
            $this->setDice($dice);
        }
    }

    public function setDice($dice) {
        return $this->_parseDice($dice);
    }

    private function _parseDice($dice) {
        $this->_dice = array();
        $this->_plus = 0;
        $this->_randomClass = null;

        if (preg_match_all("/(\d+)-(\d+)/", $dice, $matchArray)) {
            $matchCount = count($matchArray[0]);
            for ($matchCounter = 0; $matchCounter < $matchCount; ++$matchCounter) {
                $dice = preg_replace("/{$matchArray[0][$matchCounter]}/", "",
                        $dice);
                $this->addDie(new Number($matchArray[1][$matchCounter], $matchArray[2][$matchCounter]));
            }
        } else if (preg_match_all("/(flip|coin)/i", $dice, $matchArray)) {
            $matchCount = count($matchArray[0]);
            for ($matchCounter = 0; $matchCounter < $matchCount; ++$matchCounter) {
                $dice = preg_replace("/{$matchArray[0][$matchCounter]}/", "",
                        $dice);
                $this->addDie(new Coin());
            }
        } else if (preg_match_all("/(\d+):(\d+)" . str_repeat("(?::(\d+))?", 8) . "/i",
                        $dice, $matchArray)) {
            $matchCount = count($matchArray[0]);
            for ($matchCounter = 0; $matchCounter < $matchCount; ++$matchCounter) {
                $dice = preg_replace("/{$matchArray[0][$matchCounter]}/", "",
                        $dice);
                $percentages = array();
                $percentageCounter = 1;
                while (!empty($matchArray[$percentageCounter][$matchCounter])) {
                    $percentages[] = $matchArray[$percentageCounter][$matchCounter];
                    ++$percentageCounter;
                }
                $this->addDie(new Percentage($percentages));
            }
        } else {
            preg_match_all("/(\d*)[wd](\d*)/i", $dice, $matchArray);
            $matchCount = count($matchArray[0]);
            for ($matchCounter = 0; $matchCounter < $matchCount; ++$matchCounter) {
                $dice = preg_replace("/{$matchArray[0][$matchCounter]}/", "",
                        $dice);
                if (!empty($matchArray[0][$matchCounter])) {
                    if (empty($matchArray[1][$matchCounter])) {
                        $number = 1;
                    } else {
                        $number = $matchArray[1][$matchCounter];
                    }
                    if (empty($matchArray[2][$matchCounter])) {
                        $sides = 6;
                    } else {
                        $sides = $matchArray[2][$matchCounter];
                    }
                    for ($numberCounter = 0; $numberCounter < $number; ++$numberCounter) {
                        $this->addDie(new Dice($sides));
                    }
                }
            }
        }

        preg_match_all("/[+-]\d+/", $dice, $matchArray);
        $matchCount = count($matchArray[0]);
        for ($matchCounter = 0; $matchCounter < $matchCount; ++$matchCounter) {
            $dice = preg_replace("/{$matchArray[0][$matchCounter]}/", "", $dice);
            $this->addPlus($matchArray[0][$matchCounter]);
        }

        if ((empty($this->_dice) && empty($this->_plus)) || strlen(trim($dice)) > 0) {
            throw new Exception("Could not parse dice!");
        }



        return $this->_dice;
    }

    public function addDie(Thing $die) {
        if (is_null($this->_randomClass)) {
            $this->_randomClass = get_class($die);
        } else if (get_class($die) != $this->_randomClass) {
            throw new Exception("Cannot add a different die to the set!");
        }

        if (count($this->_dice, COUNT_RECURSIVE) > 1000) {
            throw new Exception("Don't use more than 1000 dice!");
        }

        if (get_class($die) == 'Dice') {
            $this->_dice[$die->getSides()][] = $die;
        } else {
            $this->_dice[] = $die;
        }

        return $die;
    }

    public function addPlus($number) {
        return $this->_plus += $number;
    }

    public function getPlus() {
        return (($this->_plus >= 0) ? "+" : "") . $this->_plus;
    }

    public function getDice() {
        ksort($this->_dice);
        return $this->_dice;
    }

    public function roll() {
        $number = $this->_plus;
        $number += $this->_rollArray($this->_dice);
        return $number;
    }

    private function _rollArray(Array $rollArray) {
        $result = 0;
        foreach ($rollArray as $currentObject) {
            if (is_array($currentObject)) {
                $result += $this->_rollArray($currentObject);
            } else {
                $result += $currentObject->roll();
            }
        }
        return $result;
    }

    public function __toString() {
        $returnString = "";
        $dice = $this->getDice();

        if ($this->_randomClass == 'Dice') {
            foreach ($dice as $currentSide => $currentDice) {
                $results = array();
                foreach ($currentDice as $currentDie) {
                    $results[] = $currentDie->getResult();
                }
                $returnString .= count($currentDice) . "d" . $currentSide;
                $returnString .= "(" . implode(", ", $results) . ") ";
            }
        } else {
            $returnString .= implode(", ", $dice);
        }

        if ($this->_plus != 0) {
            $returnString .= $this->getPlus();
        }
        return $returnString;
    }

}