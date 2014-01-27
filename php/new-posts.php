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
					//$post['image_url'] = $images[$j][4]['source'];
					echo json_encode($images[$j][0][4]);
					//echo json_encode($post);
					break;
				}
			}
	}
}

/*
                   // For posts with an image, look for associate image data.
                    if (posts[i].attachment && posts[i].attachment.media
                            && posts[i].attachment.media[0] && posts[i].attachment.media[0].photo) {
                        for (j = 0; j < imageQuery.length; j++) {
                            // See if attachment media has a match for object_id.
                            if (posts[i].attachment.media[0].photo.fbid === imageQuery[j].object_id) {
                                posts[i]['image_url'] = imageQuery[j].images[4].source;
                                break;
                            }
                        }
                    }
                }*/

/*
for ($i = 0; $i < count($response); $i++) {
	$posts[$i] = $response[$i]['fql_result_set'][0];
}

echo json_encode($posts);*/
?>
