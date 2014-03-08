<?php

require_once 'session.php';
require_once 'queries.php';

if (http_response_code() != 401) {
    $gid = $_GET['gid'];
    $uid = $_GET['uid'];    // For some reason, calling $fbSession->getUser() kills the access token. So, we cheated.
    // Allow everything younger than one month.
    // Define the initial window to search within.
    $windowSize = 3600 * 48;    // 10 Hour Periods
    $windowStart = time();
    $windowEnd = $windowStart - $windowSize;

    $batchSize = 500;
    $batchRunCount = 1;

    // Create the constraints array.
    $actorConstraint = array(
        'field' => 'actor_id',
        'operator' => '=',
        'value' => 'me()'
    );

    $queries = array();

    // Construct the FB batch request
    for ($i = 0; $i < $batchRunCount; $i++) {
        $constraints = array();

        $constraints[] = $actorConstraint;

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
            'relative_url' => 'method/fql.multiquery?queries=' . json_encode(buildNewsFeedQuery($gid, $constraints, $batchSize))
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

    $posts = array();

    // Sift through the results.
    for ($i = 0; $i < count($response); $i++) {
        $result = json_decode($response[$i]['body'], true);
        $posts = array_merge($posts, processStreamQuery($result[0]['fql_result_set'], $result[1]['fql_result_set']));
    }

    echo json_encode($posts);
}

function buildNewsFeedQuery($targetId, $constraints, $limit = 20) {
    $streamQuery = 'SELECT post_id,updated_time,message,attachment,comment_info FROM stream WHERE filter_key in (SELECT filter_key FROM stream_filter WHERE uid=me() AND type="newsfeed") AND is_hidden=0 AND target_id=' . $targetId;

    // Check for constraints.
    for ($i = 0; $i < count($constraints); $i++) {
        $constraint = $constraints[$i];
        $streamQuery .= ' AND ' . $constraint['field'] . ' ' . $constraint['operator'] . ' ' . $constraint['value'];
    }

    // Fetch 20 results, and sorted by creation time.
    $streamQuery .= ' ORDER BY updated_time DESC LIMIT ' . $limit;

    $queries = array(
        'streamQuery' => $streamQuery,
        'imageQuery' => 'SELECT object_id,images FROM photo WHERE object_id IN (SELECT attachment FROM #streamQuery)'
    );

    return $queries;
}
