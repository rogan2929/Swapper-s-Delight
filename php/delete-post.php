<?php

require_once 'session.php';

$postId = $_GET['postId'];

$facebook = getFacebookSession();

// Delete the post with postId.
if (http_response_code() != 401) {
    $facebook->api('/' . $postId, 'DELETE');
}