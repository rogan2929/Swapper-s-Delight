<?php

require_once 'session.php';
require_once 'queries.php';

if (http_response_code() != 401) {
    $gid = $_GET['gid'];
    $uid = $_GET['uid'];    
    
    // Define the initial window to search within.
    $windowSize = 3600 * 6;    // 6 Hour Periods
    $windowStart = time();
    $windowEnd = $windowStart - $windowSize;

    $batchSize = 5000;
    $batchRunCount = 50;
    $batchCount = 1;

    // Create the constraints array.
    $actorConstraint = array(
        'field' => 'actor_id',
        'operator' => '=',
        'value' => 'me()'
    );
    
    $posts = array();

    for ($i = 0; $i < $batchCount; $i++) {
        $queries = array();

        // Construct the FB batch request
        for ($j = 0; $j < $batchRunCount; $j++) {
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

        // Sift through the results.
        for ($i = 0; $i < count($response); $i++) {
            $result = json_decode($response[$i]['body'], true);
            $posts = array_merge($posts, processStreamQuery($result[0]['fql_result_set'], $result[1]['fql_result_set']));
        }
        
        sleep(3);
    }
}

echo json_encode($posts);