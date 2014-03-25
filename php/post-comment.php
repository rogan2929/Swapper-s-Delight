<?php

require_once 'dat.php';

$postId = $_POST['postId'];
$comment = $_POST['comment'];


echo $postId . "<br/>";
echo $comment . "<br/>";

$dal = new DataAccessLayer();

// Call the appropriate method in the newly instantiated DAL object.
echo json_encode($dal->postComment($postId, $comment));
