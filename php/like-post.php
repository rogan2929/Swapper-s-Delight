<?php

require_once 'session.php';

if (http_response_code() != 401) {
    $postId = $_POST['postId'];
    $userLikes = $_POST['userLikes'];

    echo ($userLikes === true);
    
    if ($userLikes === true) {
        // Like the post.
        $fbSession->api('/' . $postId . '/likes', 'POST', array('user_likes' => true));
    } 
    else {
        // Delete the post's like.
        $fbSession->api('/' . $postId . '/likes', 'DELETE');
    }
    
    echo $userLikes;
}