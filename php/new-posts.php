<?php

require_once 'session.php';
require_once 'queries.php';

// Retrieve group id that is being queried.
$gid = $_GET['gid'];
$updatedTime = $_GET['updatedTime'];

$posts = streamQuery($fbSession, $gid, null, $updatedTime, 20);

// Return the result.
echo json_encode($posts);