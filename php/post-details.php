<?php

require_once 'stream_data.php';

$facebook = new Facebook(array(
    'appId' => $_SESSION['appId'],
    'secret' => $_SESSION['appSecret'],
    'cookie' => true
        ));

if (http_response_code() != 401) {
    $postId = $_GET['postId'];

    $queries = array(
        'detailsQuery' => 'SELECT post_id,message,actor_id,permalink,like_info,share_info,comment_info,tagged_ids,attachment,created_time FROM stream WHERE post_id="' . $postId . '"',
        'imageQuery' => 'SELECT object_id,images FROM photo WHERE object_id IN (SELECT attachment FROM #detailsQuery)',
        'userQuery' => 'SELECT last_name,first_name,pic,profile_url FROM user WHERE uid IN (SELECT actor_id FROM #detailsQuery)',
        'commentsQuery' => 'SELECT fromid,text,text_tags,attachment,time FROM comment WHERE post_id IN (SELECT post_id FROM #detailsQuery) ORDER BY time ASC',
        'commentUserQuery' => 'SELECT uid,last_name,first_name,pic_square,profile_url FROM user WHERE uid IN (SELECT fromid FROM #commentsQuery)'
    );

    // Run the query.
    $response = $facebook->api(array(
        'method' => 'fql.multiquery',
        'queries' => $queries
    ));

    // Begin parsing the returned data.
    $post = $response[0]['fql_result_set'][0];
    $post['comments'] = $response[1]['fql_result_set'];
    $images = $response[2]['fql_result_set'];
    $post['user'] = $response[3]['fql_result_set'][0];

    if (strlen($post['message']) > 0) {
        // Replace new line characters with <br/>
        $post['message'] = nl2br($post['message']);
    }

    // Extract image data for the post.
    $post['image_url'] = getImageUrlArray($post, $images, false);

    // Extract link data.
    $post['link_data'] = getLinkData($post);

    // Determine type of post.
    $post['post_type'] = getPostType($post);

    // Erase attachment data (to make the object smaller), since this has already been parse.
    unset($post['attachment']);

    $commentUserData = array();

    // Begin parsing comment data.
    for ($i = 0; $i < count($post['comments']); $i++) {
        // Replace any line breaks with <br/>
        if ($post['comments'][$i]['text']) {
            $post['comments'][$i]['text'] = nl2br($post['comments'][$i]['text']);
        }

        // For each comment, attach user data to it.
        for ($j = 0; $j < count($response[4]['fql_result_set']); $j++) {
            $userDataObject = $response[4]['fql_result_set'][$j];

            // See if the comment is from the user.
            if ($post['comments'][$i]['fromid'] == $userDataObject['uid']) {
                $post['comments'][$i]['user'] = $userDataObject;
                break;
            }
        }
    }

    // Query action links for the given post. (FQL's action_links column always returns null. Suspect a bug.)
    $actions = $facebook->api('/' . $postId . '?fields=actions');

    $post['action_links'] = $actions['actions'];

    // Return the result.
    echo json_encode($post);
}