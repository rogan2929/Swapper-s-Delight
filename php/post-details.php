<?php

require_once 'session.php';

$postId = $GET['postId'];

if ($postId == null) {
	$postId = '120696471425768_256930841135663';
}

$queries = array(
	'detailsQuery' => 'SELECT post_id,message,actor_id,permalink,like_info,share_info,comment_info,tagged_ids FROM stream WHERE post_id="' . $postId . '"',
	'userQuery' => 'SELECT last_name,first_name,pic_square,profile_url FROM user WHERE uid IN (SELECT actor_id FROM #detailsQuery)'
);

// Run the query.
$response = $fbSession->api(array(
	'method' => 'fql.multiquery',
	'queries' => $queries
));

// Return the result.
echo json_encode($response);
?>