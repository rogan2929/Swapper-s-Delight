<?php

require_once 'session.php';
require_once 'queries.php';

$gid = $_GET['gid'];

// Get FB user id.
//$uid = $fbSession->getUser();
$uid = '1332932817';

// Allow everything younger than one month.
//$oldestAllowed = strtotime('-1 month');
// Define the initial window to search within.
$windowSize = 3600 * 24;    // 1 Day
$windowStart = time();
$windowEnd = $windowStart - $windowSize;

$batchSize = 500;
$batchRunCount = 30;

// Create the constraints array.
$actorConstraint = array(
    'field' => 'actor_id',
    'operator' => '=',
    'value' => $uid
);

$posts = array();

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
    
    $queries[] = array('method' => 'POST', 'relative_url' => 'method/fql.multiquery?queries=' . json_encode(buildStreamQuery($gid, $constraints, $batchSize)));
    
    
    
    //$response = streamQuery($fbSession, $gid, $constraints, $batchSize);
    
    //$posts = array_merge($posts, $response);

    $windowStart -= $windowSize;
    $windowEnd -= $windowSize;
}

//echo json_encode($queries);

$response = $fbSession->api('?batch=' . json_encode($queries), 'POST');
//$post_url = "https://graph.facebook.com/" . "?batch=" . json_encode($queries) . "&access_token=" . $fbSession->getAccessToken() . "&method=post";

//curl -k -F 'access_token=' . $fbSession->getAccessToken() -F 'batch=' . json_encode($queries) https://graph.facebook.com

echo json_encode($response);

//echo json_encode($response);

//echo json_encode($posts);