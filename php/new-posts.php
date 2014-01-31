<?php

require_once 'session.php';
require_once 'queries.php';

// Retrieve group id that is being queried.
$gid = $_GET['gid'];
$updatedTime = $_GET['updatedTime'];

$constraints = array();

// For FQL pagination (query by posts with created_time less than created_time of last query's oldest post.)
if ($updatedTime) {
	// Add to the constraints array.
	$constraints[] = array(
		'field' => 'updated_time',
		'operator' => '<',
		'value' => $updatedTime
	);
}

echo $gid;
echo $updatedTime;
echo json_encode($constraints);

//$posts = streamQuery($gid, 20, $constraints);

// Return the result.
//echo json_encode($posts);
?>
