<?php

class Controller
{

    private $isAjax = false;
    private $template;

    public function __construct() {
        $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS);
        $this->isAjax = filter_input(INPUT_GET, 'ajax', FILTER_VALIDATE_BOOLEAN);

        $callFunction = 'defaultAction';
        if (!is_null($action) && is_callable(array($this, $action . 'Action'))) {
            $callFunction = $action . 'Action';
        }

        $templateName = $this->isAjax ? "ajax/" . $callFunction: $callFunction;
        $this->template = new Template($templateName);

        $this->$callFunction();

        echo $this->template;
    }

    private function clearAction() {
        session_unset();
    }

    private function rollAction() {
        try {
            $inputDice = filter_input(INPUT_POST, 'dice',
                FILTER_SANITIZE_SPECIAL_CHARS);
            if (is_null($inputDice)) {
                throw new Exception("Your input was not valid!");
            }

            $dice = new Random\DiceCup($inputDice);
            $returnArray = array(
                'success' => true,
                'result' => $dice->roll(),
                'dice' => $dice->__toString(),
                'time' => time()
            );
            array_unshift($_SESSION['results'], $returnArray);
            if (count($_SESSION['results']) >= Configuration::get('maxResultsPerSession')) {
                array_pop($_SESSION['results']);
            }
        } catch (Exception $e) {
            $returnArray = array(
                'success' => false,
                'error' => $e->getMessage(),
                'time' => time()
            );
        }

        $this->template->returnArray = $returnArray;
    }

    private function defaultAction() {
        $sessionResults = $_SESSION['results'];

        if ($this->isAjax) {
            $lastTime = filter_input(INPUT_POST, 'lastTime', FILTER_VALIDATE_INT);
            if ($sessionResults[0]['time'] <= $lastTime) {
                header("HTTP/1.1 304 Not Modified");
                return;
            }
        } else {
            $this->template->refreshPage    = false;
            $this->template->serverAddress  = filter_var(isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : NULL, FILTER_DEFAULT);
            $this->template->scriptAddress  = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);
            $this->template->linkToPage     = "http://{$this->template->serverAddress}{$this->template->scriptAddress}?" .
                http_build_query(array(
                    "hash" => session_id()
                ));
        }
        $this->template->returnArray = $sessionResults;
    }
}
