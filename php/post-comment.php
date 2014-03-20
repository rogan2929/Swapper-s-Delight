<?php

require 'facebook.php';

$facebook = new Facebook(array(
    'appId' => $_SESSION['appId'],
    'secret' => $_SESSION['appSecret'],
    'cookie' => true
        ));

if (http_response_code() != 401) {
    $postId = $_POST['postId'];
    $comment = $_POST['comment'];

    // Post the comment and get the response
    $id = $facebook->api('/' . $postId . '/comments', 'POST', array('message' => $comment));

    // Get the comment and associated user data...
    $queries = array(
        'commentQuery' => 'SELECT fromid,text,text_tags,attachment,time FROM comment WHERE id=' . $id['id'],
        'commentUserQuery' => 'SELECT uid,last_name,first_name,pic_square,profile_url FROM user WHERE uid IN (SELECT fromid FROM #commentQuery)'
    );

    // Query Facebook's servers for the necessary data.
    $response = $facebook->api(array(
        'method' => 'fql.multiquery',
        'queries' => $queries
    ));

    // Construct a return object.
    $newComment = $response[0]['fql_result_set'][0];
    $newComment['user'] = $response[1]['fql_result_set'][0];

    // Replace any line breaks with <br/>
    if ($newComment['text']) {
        $newComment['text'] = nl2br($newComment['text']);
    }

    echo json_encode($newComment);
}