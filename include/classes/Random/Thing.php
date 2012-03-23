<?php

namespace Random;

interface Thing {

    public function __toString();

    public function roll();

    public function getResult();
}