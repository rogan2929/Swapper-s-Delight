<?php

require_once 'stream_data.php';
require_once 'session.php';

$gid = $_GET['gid'];
$uid = $_GET['uid'];

$facebook = getFacebookSession();

if (http_response_code() != 401) {
    $posts = array();
    $stream = $_SESSION['stream'];
    
    // Look through the cached stream, match by uid => actor_id
    for ($i = 0; $i < count($stream); $i++) {
        if ($stream[$i]['actor_id'] == $uid) {
            $posts[] = $stream[$i];
        }
    }
    
    echo json_encode(getPostData($facebook, $posts));
}