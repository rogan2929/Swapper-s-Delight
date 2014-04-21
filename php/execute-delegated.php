<?php

require_once 'graph/include.php';

$className = filter_input(INPUT_POST, 'class');
$methodName = filter_input(INPUT_POST, 'method');
$echoResult = filter_input(INPUT_POST, 'echo');

if (!is_null($className)) {
    $result = (new $className())->$methodName();
}
else {
    $result = $methodName();
}

if ($echoResult) {
    echo $result;
}