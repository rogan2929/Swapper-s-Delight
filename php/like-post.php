<?php

require 'facebook.php';

$facebook = new Facebook(array(
    'appId' => $_SESSION['appId'],
    'secret' => $_SESSION['appSecret'],
    'cookie' => true
        ));

if (http_response_code() != 401) {
    $postId = $_POST['postId'];
    $userLikes = $_POST['userLikes'];
    
    if ($userLikes == true) {
        // Like the post.
        $facebook->api('/' . $postId . '/likes', 'POST', array('user_likes' => true));
    } 
    else {
        // Delete the post's like.
        $facebook->api('/' . $postId . '/likes', 'DELETE');
    }
    
    // TODO: Update the cached FQL stream.
    
    echo $userLikes;
}