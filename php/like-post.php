<?php

require_once 'session.php';

if (http_response_code() != 401) {
    $postId = $_POST['postId'];
    $userLikes = $_POST['userLikes'];

// Like the post and get the response.
    $userLikes = $fbSession->api('/' . $postId . '/likes', 'POST', array('user_likes' => $userLikes));

    echo $userLikes;
}