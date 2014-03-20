<?php

require_once 'session.php';
require_once 'stream_data.php';

$gid = $_GET['gid'];

if (http_response_code() != 401) {
    $posts = array();
    $stream = $_SESSION['stream'];
    
    // Look through the cached stream, look for liked posts.
    for ($i = 0; $i < count($stream); $i++) {
        if ($stream[$i]['like_info']['user_likes'] == 1) {
            $posts[] = $stream[$i];
        }
    }
    
    echo json_encode(getPostData($fbSession, $posts));
}