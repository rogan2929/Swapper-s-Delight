<?php

require_once 'session.php';
require_once 'queries.php';

$postId = $_GET['postId'];

$queries = array(
    'detailsQuery' => 'SELECT post_id,message,actor_id,permalink,like_info,share_info,comment_info,tagged_ids,attachment FROM stream WHERE post_id="' . $postId . '"',
    'imageQuery' => 'SELECT object_id,images FROM photo WHERE object_id IN (SELECT attachment FROM #detailsQuery)',
    'userQuery' => 'SELECT last_name,first_name,pic_square,profile_url FROM user WHERE uid IN (SELECT actor_id FROM #detailsQuery)',
    'commentsQuery' => 'SELECT fromid,text,text_tags,attachment,time FROM comment WHERE post_id IN (SELECT post_id FROM #detailsQuery)',
    'commentUserQuery' => 'SELECT uid,last_name,first_name,pic_square,profile_url FROM user WHERE uid IN (SELECT fromid FROM #commentsQuery)'
);

// Run the query.
$response = $fbSession->api(array(
    'method' => 'fql.multiquery',
    'queries' => $queries
        ));

// Construct a return object.
$post = $response[0]['fql_result_set'][0];
$post['comments'] = $response[1]['fql_result_set'];
$images = $response[2]['fql_result_set'];
$post['user'] = $response[3]['fql_result_set'][0];

if ($post['message']) {
    // Replace new line characters with <br/>
    $post['message'] = nl2br($post['message']);
}

$post['image_url'] = getImageUrlArray($post, $images);

//$commentUserData = array();
//
//// For each comment, attach user data to it.
//for ($i = 0; $i < count($post['comments']); $i++) {
//    // Replace any line breaks with <br/>
//    if ($post['comments'][$i]['text']) {
//        $post['comments'][$i]['text'] = nl2br($post['comments'][$i]['text']);
//    }
//    
//    for ($j = 0; $j < count($response[4]['fql_result_set']); $j++) {
//        $userDataObject = $response[4]['fql_result_set'][$j];
//
//        // See if the comment is from the user.
//        if ($post['comments'][$i]['fromid'] == $userDataObject['uid']) {
//            $post['comments'][$i]['user'] = $userDataObject;
//            break;
//        }
//    }
//}
//
//// Query action links for the given post. (FQL's action_links column always returns null. Suspect a bug.)
//$actions = $fbSession->api('/' . $postId . '?fields=actions');
//
//$post['action_links'] = $actions['actions'];
//
//// Return the result.
echo json_encode($post);