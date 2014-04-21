<?php

if (!session_id()) {
    session_start();
}

require_once 'graph/include.php';

// GET Methods

$className = filter_input(INPUT_GET, 'class');
$methodName = filter_input(INPUT_GET, 'method');
$_SESSION['accessToken'] = filter_input(INPUT_GET, 'accessToken');
$echoResult = filter_input(INPUT_GET, 'echo');

error_log(var_dump($_GET));

if (!is_null($className)) {
    $result = (new $className())->$methodName();
}
else {
    $result = $methodName();
}

if ($echoResult) {
    echo $result;
}