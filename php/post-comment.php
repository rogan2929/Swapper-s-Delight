<?php

require_once 'session.php';

$postId = $_POST['postId'];
$comment = $_POST['comment'];

$response = array( 'postId' => $postId, 'comment' => $comment);

echo json_encode($response);

// Post the comment and get the response
//$response = $fbSession->api('/' . $postId, 'POST', array('message' => $comment));

// Get the comment and associated user data...
//'commentsQuery' => 'SELECT fromid,text,text_tags,attachment,time FROM comment WHERE post_id IN (SELECT post_id FROM #detailsQuery)',
//    'commentUserQuery' => 'SELECT uid,last_name,first_name,pic_square,profile_url FROM user WHERE uid IN (SELECT fromid FROM #commentsQuery)'
?>