<?php

require_once 'session.php';
require_once 'stream_data.php';

$gid = $_GET['gid'];
$refresh = $_GET['refresh'];

// Fetch the stream for the group.
fetchStream($fbSession, $gid, $refresh);

if (http_response_code() != 401) {
    $posts = array();
    $stream = $_SESSION;
    
    // Look through the cached stream, look for liked posts.
    for ($i = 0; $i < count($stream); $i++) {
        if ($stream[$i]['like_info']['user_likes'] == 1) {
            $posts[] = $stream[$i];
        }
    }
    
    echo json_encode(getPostData($fbSession, $posts));
}