<?php

require_once 'session.php';

if (http_response_code() != 401) {
    $postId = $_POST['postId'];
    $userLikes = $_POST['userLikes'];

    if ($userLikes == $true) {
        // Like the post and get the response.
        $fbSession->api('/' . $postId . '/likes', 'POST', array('user_likes' => true));
    } 
    else {
        $fbSession->api('/' . $postId . '/likes', 'DELETE');
        echo 'Deleting...';
    }
    
    echo $userLikes;
}