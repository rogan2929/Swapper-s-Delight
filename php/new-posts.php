<?php

require_once 'session.php';
require_once 'queries.php';

// Retrieve group id that is being queried.
$gid = $_GET['gid'];
$updatedTime = $_GET['updatedTime'];

$constraints = array();

if ($updatedTime) {
    // Add to the constraints array.
    $constraints[] = array(
        'field' => 'updated_time',
        'operator' => '<',
        'value' => $updatedTime
    );
}

$posts = streamQuery($fbSession, $gid, $constraints, 20);

// If no results were retrieved, try again with a large sample.
if (count($posts) <= 5) {
    $posts = streamQuery($fbSession, $gid, $constraints, 50);    
}

// Return the result.
echo json_encode($posts);
