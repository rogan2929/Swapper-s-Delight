<?php

require_once 'session.php';

$postId = $_POST['postId'];
$comment = $_POST['comment'];

if ($postId == null) {
    $postId = 'blah';
}

if ($comment == null) {
    $comment = 'blah2';
}

$response = array('postId' => $postId, 'comment' => $comment);

//echo json_encode($response);
echo json_encode($response);

// Post the comment and get the response
//$response = $fbSession->api('/' . $postId, 'POST', array('message' => $comment));

//$uid = $fbSession->GetUser();
//
//// Get the comment and associated user data...
//$queries = array(
//    'commentsQuery' => 'SELECT fromid,text,text_tags,attachment,time FROM comment WHERE post_id="' . $response . '"',
//    'commentUserQuery' => 'SELECT uid,last_name,first_name,pic_square,profile_url FROM user WHERE uid=' . $uid
//);
//
//$response = $fbSession->api(array(
//    'method' => 'fql.multiquery',
//    'queries' => $queries
//        ));
?>