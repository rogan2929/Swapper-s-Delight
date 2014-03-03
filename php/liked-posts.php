<?php

require_once 'session.php';
require_once 'queries.php';

if (http_response_code() != 401) {
    $gid = $_GET['gid'];
    $uid = $_GET['uid'];    // For some reason, calling $fbSession->getUser() kills the access token. So, we cheated.

    // Allow everything younger than one month.
    // Define the initial window to search within.
    $windowSize = 3600 * 12;    // 1 Day
    $windowStart = time();
    $windowEnd = $windowStart - $windowSize;

    $batchSize = 1500;
    $batchRunCount = 50;

    // Create the constraints array.
    $likeConstraint = array(
        'field' => 'like_info.user_likes',
        'operator' => '=',
        'value' => '1'
    );

    $queries = array();

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

        $queries[] = array(
            'method' => 'POST',
            'relative_url' => 'method/fql.multiquery?queries=' . json_encode(buildStreamQuery($gid, $constraints, $batchSize))
        );

        $windowStart -= $windowSize;
        $windowEnd -= $windowSize;
    }

    // Call the batch query.
    $response = $fbSession->api('/', 'POST', array(
        'batch' => json_encode($queries),
        'include_headers' => false
    ));

    $posts = array();

// Sift through the results.
    for ($i = 0; $i < count($response); $i++) {
        $result = json_decode($response[$i]['body'], true);
        $posts = array_merge($posts, processStreamQuery($result[0]['fql_result_set'], $result[1]['fql_result_set']));
    }

    echo json_encode($posts);
}
