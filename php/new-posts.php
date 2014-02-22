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

// Begin querying the group's feed, trying for a larger limit if an insufficient number of results is returned.
for ($i = 1; $i <= 10; $i++) {
    $posts = streamQuery($fbSession, $gid, $constraints, $i * 50);
    
    if (count($posts) >= 20) {
        $posts = array_slice($posts, 0, 20);
        break;
    }
}

//$posts = streamQuery($fbSession, $gid, $constraints, 20);
//
//// If no results were retrieved, try again with a large sample.
//if (count($posts) <= 20) {
//    $posts = streamQuery($fbSession, $gid, $constraints, 50);
//}
//
//// If no results were retrieved, try again with a large sample.
//if (count($posts) <= 20) {
//    $posts = streamQuery($fbSession, $gid, $constraints, 100);
//}

// Return the result.
echo json_encode($posts);
