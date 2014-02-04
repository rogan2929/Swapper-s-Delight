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

var_dump($fbSession);

$posts = streamQuery($fbSession, $gid, $constraints, $updatedTime, 20);

echo json_encode($posts);