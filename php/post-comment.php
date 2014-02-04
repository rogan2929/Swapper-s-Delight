<?php

require_once 'session.php';

$postId = $_POST['postId'];
$comment = $_POST['comment'];

echo $postId;

//echo json_encode($_POST);

//$response = array('postId' => $postId, 'comment' => $comment);
//echo json_encode($response);

// Post the comment and get the response
//$id = $fbSession->api('/' . $postId, 'POST', array('message' => $comment));
//
//$uid = $fbSession->GetUser();
//
//// Get the comment and associated user data...
//$queries = array(
//    'commentsQuery' => 'SELECT fromid,text,text_tags,attachment,time FROM comment WHERE post_id="' . $id . '"',
//    'commentUserQuery' => 'SELECT uid,last_name,first_name,pic_square,profile_url FROM user WHERE uid=' . $uid
//);
//
//// Query Facebook's servers for the necessary data.
//$response = $fbSession->api(array(
//    'method' => 'fql.multiquery',
//    'queries' => $queries
//        ));
//
//// Construct a return object.
//$newComment = $response[0]['fql_result_set'][0];
//$newComment['user'] = $response[1]['fql_result_set'][0];
//
//echo json_encode($newComment);