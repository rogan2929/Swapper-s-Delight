<?php

require_once 'dal.php';

$postId = $_POST['postId'];
$userLikes = $_POST['userLikes'];

// Call the appropriate method in the newly instantiated DAL object.
echo ((new DataAccessLayer())->likePost($postId, $userLikes));