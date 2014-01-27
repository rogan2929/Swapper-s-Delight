<?php

require_once 'session.php';

$postId = $_GET['postId'];

$queries = array(
	'detailsQuery' => 'SELECT post_id,message,actor_id,permalink,like_info,share_info,comment_info,tagged_ids FROM stream WHERE post_id="' . $postId . '"',
	'userQuery' => 'SELECT last_name,first_name,pic_square,profile_url FROM user WHERE uid IN (SELECT actor_id FROM #detailsQuery)',
	'commentsQuery' => 'SELECT fromid,text,text_tags,attachment FROM comment WHERE post_id IN (SELECT post_id FROM #detailsQuery)'
);

// Run the query.
$response = $fbSession->api(array(
	'method' => 'fql.multiquery',
	'queries' => $queries
));

// Construct a return object.
$postDetails = $response[0]['fql_result_set'][0];
$postDetails['user'] = $response[2]['fql_result_set'][0];
$postDetails['comments'] = $response[1]['fql_result_set'];

// Return the result.
//echo json_encode($postDetails);
echo json_encode($response);
?>