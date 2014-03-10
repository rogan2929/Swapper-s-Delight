<?php

require_once 'session.php';
require_once 'include.php';

if (http_response_code() != 401) {
    $gid = $_GET['gid'];
    $uid = $_GET['uid'];

    // Define the initial window to search within.
    $windowSize = 3600 * 3;    // 6 Hour Periods
    $windowStart = time();
    $windowEnd = $windowStart - $windowSize;

    $batchSize = 5000;
    $batchRunCount = 100;

    // Create the constraints array.
    $likeConstraint = array(
        'field' => 'like_info.user_likes',
        'operator' => '=',
        'value' => '1'
    );

//    $queries = array();
    $posts = array();

    // Construct the FB batch request
    for ($i = 0; $i < $batchRunCount; $i++) {
        $constraints = array();

        $constraints[] = $likeConstraint;
        $constraints[] = array(// Window start constraint
            'field' => 'updated_time',
            'operator' => '<=',
            'value' => $windowStart
        );
        $constraints[] = array(// Window end constraint
            'field' => 'updated_time',
            'operator' => '>=',
            'value' => $windowEnd
        );
        
        $posts = array_merge($posts, streamQuery($fbSession, $gid, $constraints, $batchSize));

//        $queries[] = array(
//            'method' => 'POST',
//            'relative_url' => 'method/fql.multiquery?queries=' . json_encode(buildStreamQuery($gid, $constraints, $batchSize))
//        );

        $windowStart -= $windowSize;
        $windowEnd -= $windowSize;
    }

    // Call the batch query.
//    $response = $fbSession->api('/', 'POST', array(
//        'batch' => json_encode($queries),
//        'include_headers' => false
//    ));
//    
//    $posts = array();
//
//    // Sift through the results.
//    for ($i = 0; $i < count($response); $i++) {
//        $result = json_decode($response[$i]['body'], true);
//        $posts = array_merge($posts, processStreamQuery($result[0]['fql_result_set'], $result[1]['fql_result_set']));
//    }

    echo json_encode($posts);
}
