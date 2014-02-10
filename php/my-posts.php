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

//echo json_encode($response);

for ($i = 0; $i < count($response); $i++) {
    echo json_encode($response[$i]);
    echo '<br/><br/>';
}

//echo json_encode($posts);