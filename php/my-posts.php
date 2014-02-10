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
    
    
    
    //$response = streamQuery($fbSession, $gid, $constraints, $batchSize);
    
    //$posts = array_merge($posts, $response);

    $windowStart -= $windowSize;
    $windowEnd -= $windowSize;
}

echo json_encode($posts);