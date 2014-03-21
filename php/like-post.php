<?php

require_once 'session.php';

$postId = $_POST['postId'];
$userLikes = $_POST['userLikes'];

$facebook = getFacebookSession();

if (http_response_code() != 401) {

    if ($userLikes == true) {
        // Like the post.
        $facebook->api('/' . $postId . '/likes', 'POST', array('user_likes' => true));
    } else {
        // Delete the post's like.
        $facebook->api('/' . $postId . '/likes', 'DELETE');
    }

    // TODO: Update the cached FQL stream.

    echo $userLikes;
}