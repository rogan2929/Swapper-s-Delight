<?php

require_once 'dal.php';

$postId = $_POST['postId'];
$comment = $_POST['comment'];

$dal = new DataAccessLayer();

// Call the appropriate method in the newly instantiated DAL object.
echo json_encode($dal->postComment($postId, $comment));
