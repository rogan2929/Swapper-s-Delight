<?php

require_once 'dat.php';

$postId = $_POST['postId'];
$comment = $_POST['comment'];

// Call the appropriate method in the newly instantiated DAL object.
echo json_encode((new DataAccessLayer())->postComment($postId, $comment));