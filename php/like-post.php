<?php

require_once 'dal.php';

$postId = $_POST['postId'];
$userLikes = $_POST['userLikes'];

$dal = $_SESSION['dal'];

// Call the appropriate method in the newly instantiated DAL object.
echo ($dal->likePost($postId, $userLikes));