<?php

require_once 'session.php';

$postId = $_POST['postId'];
$comment = $_POST['comment'];

// Post the comment and get the response
$id = $fbSession->api('/' . $postId . '/comments', 'POST', array('message' => $comment));

// Get the comment and associated user data...
$queries = array(
    'commentQuery' => 'SELECT fromid,text,text_tags,attachment,time FROM comment WHERE id=' . $id['id'],
    'commentUserQuery' => 'SELECT uid,last_name,first_name,pic_square,profile_url FROM user WHERE uid IN (SELECT fromid FROM #commentQuery)'
);

// Query Facebook's servers for the necessary data.
$response = $fbSession->api(array(
    'method' => 'fql.multiquery',
    'queries' => $queries
        ));

// Construct a return object.
$newComment = $response[0]['fql_result_set'][0];
$newComment['user'] = $response[1]['fql_result_set'][0];

echo json_encode($newComment);