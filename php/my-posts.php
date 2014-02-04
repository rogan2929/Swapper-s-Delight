<?php

require_once 'session.php';
require_once 'queries.php';

$gid = $_GET['gid'];
$updatedTime = $_GET['updatedTime'];

// Get FB user id.
$uid = $fbSession->GetUser();

$constraints = array();

// Create the constraints array.
$constraints[] = array(
    'field' => 'actor_id',
    'operator' => '=',
    'value' => $uid
);

if ($updatedTime) {
    // Add to the constraints array.
    $constraints[] = array(
        'field' => 'updated_time',
        'operator' => '<',
        'value' => $updatedTime
    );
}

$posts = streamQuery($fbSession, $gid, $constraints, 20);

echo json_encode($posts);