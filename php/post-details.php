<?php

require_once 'session.php';

$postId = $_GET['postId'];

$queries = array(
	'detailsQuery' => 'SELECT post_id,message,actor_id,permalink,like_info,share_info,comment_info,tagged_ids FROM stream WHERE post_id="' . $postId . '"',
	'userQuery' => 'SELECT last_name,first_name,pic_square,profile_url FROM user WHERE uid IN (SELECT actor_id FROM #detailsQuery)',
	'commentsQuery' => 'SELECT fromid,text,text_tags,attachment FROM comment WHERE post_id IN (SELECT post_id FROM #detailsQuery)',
	'commentUserQuery' => 'SELECT uid,last_name,first_name,pic_square,profile_url FROM user WHERE uid IN (SELECT fromid FROM #commentsQuery)'
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

$commentUserData = array();

// For each comment, attach user data to it.
for ($i = 0; $i < count($postDetails['comments']); $i++) {
	for ($j = 0; $j < count($response[3]['fql_result_set']); $j++) {
		$userDataObject = $response[3]['fql_result_set'][$j];
		
		// See if the comment is from the user.
		if ($postDetails['comments'][$i]['fromid'] == $userDataObject['uid']) {
			$postDetails['comments'][$i]['user'] = $userDataObject;
			break;
		}
	}
}

// Query action links for the given post. (FQL's action_links column always returns null. Suspect a bug.)
$response = $fbSession->api('/"' . $postId . '""?fields=actions');

$postDetails['action_links'] = $response['actions'];

// Return the result.
echo json_encode($postDetails);
?>