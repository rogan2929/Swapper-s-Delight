<?php

$fbSession = $_SESSION['fbSession'];

$postId = $_GET['postId'];

// Delete the post with postId.
$fbSession->api('/' . $postId, 'DELETE');