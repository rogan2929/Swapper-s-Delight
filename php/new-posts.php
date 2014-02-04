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

// Retrieve group id that is being queried.
$gid = $_GET['gid'];
$updatedTime = $_GET['updatedTime'];

$posts = streamQuery($fbSession, $gid, array(), $updatedTime, 20);

// Return the result.
echo json_encode($posts);