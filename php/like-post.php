<?php

if (http_response_code() != 401) {
    $postId = $_POST['postId'];
    $userLikes = $_POST['userLikes'];
    $fbSession = $_SESSION['fbSession'];
    
    if ($userLikes == true) {
        // Like the post.
        $fbSession->api('/' . $postId . '/likes', 'POST', array('user_likes' => true));
    } 
    else {
        // Delete the post's like.
        $fbSession->api('/' . $postId . '/likes', 'DELETE');
    }
    
    // TODO: Update the cached FQL stream.
    
    echo $userLikes;
}