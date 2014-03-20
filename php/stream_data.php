<?php

/* * *
 * Fetch the FQL stream table for the given group id.
 */

function fetchStream($fbSession, $gid, $refresh = 0) {
    if (http_response_code() != 401) {
        $posts = array();

        // On certain conditions, execute a new batch query to fetch the stream.
        // 1. Last updated time > 5 minutes.
        // 2. A new group was selected.
        // 3. Stream has not been fetched yet.
        if (!isset($_SESSION['stream']) || $_SESSION['lastUpdateTime'] < time() - 300 || $_SESSION['gid'] !== $gid || $refresh === 1) {
            //$_SESSION['posts'] = executeBatchQuery($fbSession, $gid);
            //$streamQuery = 'SELECT post_id,actor_id,message FROM stream WHERE source_id=' . $gid;

            $_SESSION['stream'] = queryStream($fbSession, $gid);
            $_SESSION['lastUpdateTime'] = time();
            $_SESSION['gid'] = $gid;
            $_SESSION['pagingOffset'] = 0;
        }
    }
}

function getPostData($stream, $offset, $length) {
    
}

function queryStream($fbSession, $gid) {
    $windowData = getOptimalWindowData($fbSession, $gid);

    $windowSize = $windowData['windowSize'];
    $windowStart = time();
    $windowEnd = $windowStart - $windowSize;

    for ($i = 0; $i < $windowData['batchCount']; $i++) {
        $queries = array();

        // Construct the FB batch request
        for ($j = 0; $j < 50; $j++) {
            $query = 'SELECT post_id,actor_id,message FROM stream WHERE source_id=' . $gid . ' AND updated_time <= ' . $windowStart . ' AND updated_time >= ' . $windowEnd . ' LIMIT 5000';

            $queries[] = array(
                'method' => 'GET',
                'relative_url' => 'method/fql.query?query=' . urlencode($query)
            );

            $windowStart -= $windowSize;
            $windowEnd -= $windowSize;
        }

        // Call the batch query.
        $response = $fbSession->api('/', 'POST', array(
            'batch' => json_encode($queries),
            'include_headers' => false
        ));
        
        echo json_encode($response);
    }

    return $response['body']['data'];
}

/**
 * Determine the optimal window size to use in batch queries.
 */
function getOptimalWindowData($fbSession, $gid) {
    $startTime = time();
    $endTime = time() - 3600;

    $query = 'SELECT post_id FROM stream WHERE source_id = ' . $gid . ' AND updated_time <= ' . $startTime . ' AND updated_time >= ' . $endTime . ' LIMIT 100';

    $response = $fbSession->api(array(
        'method' => 'fql.query',
        'query' => $query
    ));

    $count = count($response);

    // These values were reached through trial and error.
    switch ($count) {
        case $count < 65:
            $windowSize = 4;
            $batchCount = 2;
            break;
        case $count >= 65 && $count < 85:
            $windowSize = 3.5;
            $batchCount = 2;
            break;
        case $count >= 85 && $count < 115:
            $windowSize = 3.5;
            $batchCount = 2;
            break;
        case $count >= 115 && $count < 150:
            $windowSize = 2.5;
            $batchCount = 2;
            break;
        case $count >= 150 && $count < 225:
            $windowSize = 2;
            $batchCount = 2;
            break;
        default:
            $windowSize = 1;
            $batchCount = 2;
            break;
    }

    return array('windowSize' => 3600 * $windowSize, 'batchCount' => $batchCount);
}
