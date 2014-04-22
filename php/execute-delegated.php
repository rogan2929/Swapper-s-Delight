<?php

if (!session_id()) {
    session_start();
}

require_once 'graph/include.php';

// GET Methods

$className = filter_input(INPUT_POST, 'class');
$methodName = filter_input(INPUT_POST, 'method');
$args = json_decode(filter_input(INPUT_POST, 'args'));

if (!is_null($className)) {
    $result = (new $className())->$methodName($args);
} 
else {
    $result = $methodName();
}

echo $result;