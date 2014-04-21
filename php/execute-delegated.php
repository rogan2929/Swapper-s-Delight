<?php

if (!session_id()) {
    session_start();
}

echo var_dump($_GET);

require_once 'graph/include.php';

// GET Methods

$className = filter_input(INPUT_GET, 'class');
$methodName = filter_input(INPUT_GET, 'method');
$_SESSION['accessToken'] = filter_input(INPUT_GET, 'accessToken');
$echoResult = filter_input(INPUT_GET, 'echo');

if (!is_null($className)) {
    $result = (new $className())->$methodName();
}
else {
    $result = $methodName();
}

if ($echoResult) {
    echo $result;
}