<?php

require_once 'stream_data.php';

$gid = $_GET['gid'];
$refresh = $_GET['refresh'];

// Fetch the stream for the group.
fetchStream($gid, $refresh);

if (http_response_code() != 401) {
    // Check to see if an offset has been provided. (In the case of a refresh or view change.)
    if (isset($_GET['offset'])) {
        $offset = $_GET['offset'];
    } else {
        $offset = $_SESSION['pagingOffset'];
    }

    $limit = 25;

    // Slice the array for processing.
    $posts = array_slice($_SESSION['stream'], $offset, $limit);

    $_SESSION['pagingOffset'] = $offset + $limit;

    echo json_encode(getPostData($posts));
}