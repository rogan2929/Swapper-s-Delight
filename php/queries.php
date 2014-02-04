<?php

require_once 'facebook.php';

/* * *
 * Reusable funciton that executes an FQL query against the given stream.
 */

function streamQuery($fbSession, $sourceId, $constraints, $updatedTime, $limit = 20) {
    $streamQuery = 'SELECT post_id,updated_time,message,attachment,comment_info FROM stream WHERE source_id=' . $sourceId;

    if ($updatedTime) {
        // Add to the constraints array.
        $constraints[] = array(
            'field' => 'updated_time',
            'operator' => '<',
            'value' => $updatedTime
        );
    }

    // Check for constraints.
    for ($i = 0; $i < count($constraints); $i++) {
        $constraint = $constraints[$i];
        $streamQuery .= ' AND ' . $constraint['field'] . ' ' . $constraint['operator'] . ' ' . $constraint['value'];
    }

    // Fetch 20 results, and sorted by creation time.
    $streamQuery .= ' ORDER BY updated_time DESC LIMIT ' . $limit;

    $queries = array(
        'streamQuery' => $streamQuery,
        'imageQuery' => 'SELECT object_id,images FROM photo WHERE object_id IN (SELECT attachment FROM #streamQuery)'
    );

    //echo $fbSession->getAccessToken();

//    try {
//        // Test the connectivity waters...
//        $me = $fbSession->api('/me');
//    } catch (FacebookApiException $e) {
//        echo $e->getType();
//        echo $e->getMessage();
//    }

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
        if ($post['attachment'] && $post['attachment']['media'] && $post['attachment']['media'][0] && $post['attachment']['media'][0]['photo']) {
            for ($j = 0; $j < count($images); $j++) {
                if ($post['attachment']['media'][0]['photo']['fbid'] == $images[$j]['object_id']) {
                    $post['image_url'][] = $images[$j]['images'][0]['source'];
                    $post['image_url'][] = $images[$j]['images'][0]['source'];
                    break;
                }
            }
        }

        // Add to the posts array.
        $posts[] = $post;
    }
    return $posts;
}
