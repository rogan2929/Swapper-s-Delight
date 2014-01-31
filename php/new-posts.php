<?php

require_once 'session.php';

// Retrieve group id that is being queried.
$gid = $_GET['gid'];
$createdTime = $_GET['createdTime'];

// Base query
$streamQuery = 'SELECT post_id,updated_time,message,attachment,comment_info FROM stream WHERE source_id=' . $gid;

// For FQL pagination (query by posts with created_time less than created_time of last query's oldest post.)
if ($createdTime) {
	$streamQuery .= ' AND created_time < ' . $createdTime;
}

// Fetch 30 results, and sorted by creation time.
$streamQuery .= ' ORDER BY updated_time DESC LIMIT 20';

$queries = array(
	'streamQuery' => $streamQuery,
	'imageQuery' => 'SELECT object_id,images FROM photo WHERE object_id IN (SELECT attachment FROM #streamQuery)'
);

$response = $fbSession->api(array(
	'method' => 'fql.multiquery',
	'queries' => $queries
));

$posts = array();

$stream = $response[0]['fql_result_set'];
$images = $response[1]['fql_result_set'];

for ($i = 0; $i < count($stream); $i++) {
	$post = $stream[$i];
	
	$post['image_url'] = null;
	
	// For posts with an image, look for associate image data.
	if ($post['attachment'] && $post['attachment']['media'] &&
		$post['attachment']['media'][0] && $post['attachment']['media'][0]['photo']) {
			for ($j = 0; $j < count($images); $j++) {
				if ($post['attachment']['media'][0]['photo']['fbid'] == $images[$j]['object_id']) {
					$post['image_url'][] = $images[$j]['images'][2]['source'];
					$post['image_url'][] = $images[$j]['images'][5]['source'];
					break;
				}
			}
	}
		
	// Add to the posts array.
	$posts[] = $post;
}

// Return the result.
echo json_encode($posts);
?>
