<?php

require_once 'session.php';

$postId = $GET['postId'];

if ($postId == null) {
	$postId = '120696471425768_256930841135663';
}

$query = 'SELECT post_id,message,actor_id,permalink,like_info,share_info,comment_info,tagged_ids FROM stream WHERE post_id="' . $postId . '"';

$response = $fbSession->api(array(
	'method' => 'fql.query',
	'query' => $query
));

echo json_encode($response);

?>