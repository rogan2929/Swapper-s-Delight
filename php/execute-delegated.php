<?php

require_once 'graph/include.php';

$className = filter_input(INPUT_GET, 'class');
$methodName = filter_input(INPUT_GET, 'method');
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