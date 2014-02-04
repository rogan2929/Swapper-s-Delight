<?php

require 'session.php';
require 'queries.php';

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

$posts = streamQuery($fbSession, $gid, $constraints, $updatedTime, 20);

echo json_encode($posts);