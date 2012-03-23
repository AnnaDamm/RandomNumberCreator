<?php
require_once('include/base.inc.php');

session_start();

error_reporting(E_ALL);
ini_set('display_errors', '1');

$sessionId = filter_input(INPUT_GET, 'hash');
if (!is_null($sessionId) && $sessionId != session_id()) {
    session_destroy();
    session_id($sessionId);
    session_start();
}

if (empty($_SESSION['results'])) {
    $_SESSION['results'] = array();
}

new Controller();