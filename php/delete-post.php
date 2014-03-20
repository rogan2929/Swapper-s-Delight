<?php

$facebook = new Facebook(array(
    'appId' => $_SESSION['appId'],
    'secret' => $_SESSION['appSecret'],
    'cookie' => true
        ));

$postId = $_GET['postId'];

// Delete the post with postId.
$facebook->api('/' . $postId, 'DELETE');