<?php

$message = filter_input(INPUT_POST, 'message');
$url = filter_input(INPUT_POST, 'url');
$line = filter_input(INPUT_POST, 'line');
$userAgent = $_SERVER['HTTP_USER_AGENT'];

$log = fopen($_SERVER['DOCUMENT_ROOT'] . '\jserror.txt', 'a');

if (!$log) {
    exit();
}

fwrite($log, date(DATE_RFC2822, $_SERVER['REQUEST_TIME']) . ': ' . $userAgent . ': ' . $message . ', ' . $url . ', ' . $line . PHP_EOL);

fclose($log);
