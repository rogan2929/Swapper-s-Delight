<?php

require_once 'dal.php';

$gid = $_GET['gid'];
$refresh = $_GET['refresh'];
$offset = $_GET['offset'];

$dat = new DataAccessLayer();
$dat->setGid($gid);

//echo json_encode($dat->getNewPosts($refresh, $offset));

//require_once 'stream_data.php';
//require_once 'session.php';
//
//$gid = $_GET['gid'];
//$refresh = $_GET['refresh'];
//
//$facebook = getFacebookSession();
//
//if (http_response_code() != 401) {
//    // Fetch the stream for the group.
//    fetchStream($facebook, $gid, $refresh);
//
//    // Check to see if an offset has been provided. (In the case of a refresh or view change.)
//    if (isset($_GET['offset'])) {
//        $offset = $_GET['offset'];
//    } else {
//        $offset = $_SESSION['pagingOffset'];
//    }
//
//    $limit = 25;
//
//    // Slice the array for processing.
//    $posts = array_slice($_SESSION['stream'], $offset, $limit);
//
//    $_SESSION['pagingOffset'] = $offset + $limit;
//
//    echo json_encode(getPostData($facebook, $posts));
//}