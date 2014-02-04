<?php

//require_once 'session.php';
require_once 'queries.php';

$appId = '652991661414427';
$appSecret = 'b8447ce73d2dcfccde6e30931cfb0a90';

$cookie = preg_replace("/^\"|\"$/i", "", $_COOKIE['fbs_' . $appId]);
parse_str($cookie, $data);

// Startup the Facebook object
$fbSession = new Facebook(array(
    'appId' => $appId,
    'secret' => $appSecret
        ));

$fbSession->setAccessToken($data['access_token']);

$gid = $_GET['gid'];
$updatedTime = $_GET['updatedTime'];

// Get FB user id.
$uid = $fbSession->getUser();

$constraints = array();

// Create the constraints array.
$constraints[] = array(
    'field' => 'actor_id',
    'operator' => '=',
    'value' => $uid
);

$posts = streamQuery($fbSession, $gid, $constraints, $updatedTime, 20);

echo json_encode($posts);