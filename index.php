<?php

namespace Random;

require_once('include/base.inc.php');

session_start();

$sessionId = filter_input(INPUT_GET, 'hash');
if (!is_null($sessionId) && $sessionId != session_id()) {
    session_destroy();
    session_id($sessionId);
    session_start();
}

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS);
$ajax = filter_input(INPUT_GET, 'ajax', FILTER_VALIDATE_BOOLEAN);
$refreshPage = false;
$serverAddress = filter_var(isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : NULL, FILTER_DEFAULT);
$scriptAddress = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);
$linkToPage = "http://{$serverAddress}{$scriptAddress}?" . http_build_query(array(
            "hash" => session_id()
        ));

if (empty($_SESSION['results'])) {
    $_SESSION['results'] = array();
}

switch ($action) {
    case 'clear':
        session_unset();
        break;
    case 'roll':
        try {
            $inputDice = filter_input(INPUT_POST, 'dice',
                    FILTER_SANITIZE_SPECIAL_CHARS);
            if (is_null($inputDice)) {
                throw new Exception("Your input was not valid!");
            }
            
            $dice = new DiceCup($inputDice);
            $returnArray = array(
                'success' => true,
                'result' => $dice->roll(),
                'dice' => $dice->__toString(),
                'time' => time()
            );
            array_unshift($_SESSION['results'], $returnArray);
            if (count($_SESSION['results']) >= 100) {
                array_pop($_SESSION['results']);
            }
        } catch (Exception $e) {
            $returnArray = array(
                'success' => false,
                'error' => $e->getMessage(),
                'time' => time()
            );
        }

        if ($ajax) {
            echo json_encode($returnArray);
            break;
        }
    default:
        $sessionResults = $_SESSION['results'];
        if ($ajax) {
            $lastTime = filter_input(INPUT_POST, 'lastTime', FILTER_VALIDATE_INT);
            if ($sessionResults[0]['time'] > $lastTime) {
                echo json_encode($sessionResults);
            } else {
                header("HTTP/1.1 304 Not Modified");
            }
        } else {
            require('templates/showForm.tpl.php');
        }
}