<?php

require_once 'session.php';

// Retrieve group id that is being queried.
$gid = $_GET['gid'];
$createdTime = $_GET['createdTime'];

if ($gid == null) {
	$gid = '120696471425768';
}

// Base query
$streamQuery = 'SELECT post_id,created_time,message,attachment,comment_info FROM stream WHERE source_id=' . $gid;

// For FQL pagination (query by posts with created_time less than created_time of last query's oldest post.)
if ($createdTime) {
	$streamQuery .= ' AND created_time < ' . $createdTime;
}

// Fetch 30 results, and sorted by creation time.
$streamQuery .= ' ORDER BY created_time DESC LIMIT 30';

$queries = array(
	'streamQuery' => $streamQuery,
	'imageQuery' => 'SELECT object_id,images FROM photo WHERE object_id IN (SELECT attachment FROM #streamQuery)'
);

$response = $fbSession->api(array(
	'method' => 'fql.multiquery',
	'queries' => $queries
));

echo json_encode($response);
?>
