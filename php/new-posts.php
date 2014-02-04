<?php

require_once 'session.php';
require_once 'queries.php';

// Retrieve group id that is being queried.
$gid = $_GET['gid'];
$updatedTime = $_GET['updatedTime'];

var_dump($fbSession);

$posts = streamQuery($fbSession, $gid, array(), $updatedTime, 20);

// Return the result.
echo json_encode($posts);