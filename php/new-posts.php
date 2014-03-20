<?php

//require_once 'session.php';
//require_once 'include.php';

require_once 'session.php';
require_once 'stream_data.php';

$gid = $_GET['gid'];
$refresh = $_GET['refresh'];

// Fetch the stream for the group.
fetchStream($fbSession, $gid, $refresh);

if (http_response_code() != 401) {
    if ($refresh == 1) {
        $_SESSION['pagingOffset'] = 0;
    }
    
    $limit = 25;
    $offset = $_SESSION['pagingOffset'];
    
    // Slice the array for processing.
    $posts = array_slice($_SESSION['stream'], $offset, $limit);
    
    $_SESSION['pagingOffset'] = $offset + $limit;    
    
    echo json_encode(getPostData($fbSession, $posts));
}