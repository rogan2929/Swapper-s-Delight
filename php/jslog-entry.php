<?php

$message = filter_input(INPUT_POST, 'message');

$log = fopen($_SERVER['DOCUMENT_ROOT'] . '\jslog-entry.txt', 'a');

if (!$log) {
    exit();
}

fwrite($log, $_SERVER['REQUEST_TIME'] . ': ' . $message . PHP_EOL);

fclose($log);
