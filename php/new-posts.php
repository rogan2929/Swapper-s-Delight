<?php

//require_once 'session.php';
//require_once 'include.php';

require_once 'session.php';
require_once 'stream_data.php';

$gid = $_GET['gid'];
//$updatedTime = $_GET['updatedTime'];

// Fetch the stream for the group.
fetchStream($fbSession, $gid);

if (http_response_code() != 401) {
    //$constraints = array();
    $limit = 25;
    $offset = $_SESSION['pagingOffset'];
    
    $posts = array_slice($_SESSION['posts'], $offset, $limit);
    
    $_SESSION['pagingOffset'] = $offset + $limit;
    
    echo json_encode($posts);
}

//    if ($updatedTime) {
//        // Add to the constraints array.
//        $constraints[] = array(
//            'field' => 'updated_time',
//            'operator' => '<',
//            'value' => $updatedTime
//        );
//    }
//
//    // Begin querying the group's feed, trying for a larger limit if an insufficient number of results is returned.
//    for ($i = 1; $i <= 10; $i++) {
//        $posts = streamQuery($fbSession, $gid, $constraints, $i * 50);
//
//        if (count($posts) >= $limit) {
//            // Cap at 20 posts.
//            $posts = array_slice($posts, 0, $limit);
//            break;
//        }
//    }
//
//    // Return the result.
//    echo json_encode($posts);
//}
