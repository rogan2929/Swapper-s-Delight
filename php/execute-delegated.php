<?php

require_once 'graph/include.php';

$className = filter_input(INPUT_POST, 'class');
$methodName = filter_input(INPUT_POST, 'method');
$methodParams = filter_input(INPUT_POST, 'params');
$echoResult = filter_input(INPUT_POST, 'echo');

if (!is_null($className)) {
    $result = (new $className())->$methodName($methodParams);
}
else {
    $result = $methodName($methodParams);
}

if ($echoResult) {
    echo $result;
}