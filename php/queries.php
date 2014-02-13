<?php

/* * *
 * Build a 'streamQuery' FQL multi-query.
 */

function buildStreamQuery($sourceId, $constraints, $limit = 20) {
    $streamQuery = 'SELECT post_id,updated_time,message,attachment,comment_info FROM stream WHERE source_id=' . $sourceId;

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

    return $queries;
}

/* * *
 * Reusable, generic function that executes an FQL query against the given stream.
 */

function streamQuery($fbSession, $sourceId, $constraints, $limit = 20) {
    $queries = buildStreamQuery($sourceId, $constraints, $limit);

    $response = $fbSession->api(array(
        'method' => 'fql.multiquery',
        'queries' => $queries
    ));

    return processStreamQuery($response[0]['fql_result_set'], $response[1]['fql_result_set']);
}

/* * *
 * Take a response and construct post objects out of it.
 */

function processStreamQuery($stream, $images) {
    $posts = array();

    for ($i = 0; $i < count($stream); $i++) {
        $post = $stream[$i];

        $post['image_url'] = getImageUrlArray($post, $images);

        // Replace any line breaks with <br/>
        if ($post['message']) {
            $post['message'] = str_replace('\n', '<br/>', $post['message']);
        }

        // Add to the posts array.
        $posts[] = $post;
    }

    return $posts;
}

function getImageUrlArray($post, $images) {
    // For posts with an image, look for associated image data.
//        if ($post['attachment'] && $post['attachment']['media'] && $post['attachment']['media'][0] && $post['attachment']['media'][0]['photo']) {
//            for ($k = 0; $k < count($images); $k++) {
//                if ($post['attachment']['media'][0]['photo']['fbid'] == $images[$k]['object_id']) {
//                    $post['image_url'][] = $images[$k]['images'][0]['source'];
//                    //$post['image_url'][] = $images[$j]['images'][0]['source'];
//                    break;
//                }
//            }
//        }
    
    $imageUrls = array();

    if ($post['attachment'] && $post['attachment']['media']) {
        // For posts with an image, look for associated image data.
        for ($i = 0; $i < count($post['attachment']); $i++) {
            if ($post['attachment']['media'][$i] && $post['attachment']['media'][$i]['photo']) {
                // Get image's unique Facebook Id
                $fbid = $post['attachment']['media'][$i]['photo']['fbid'];
                
                echo 'POST_ID: ' . $post['post_id'] . ', FBID: ' . $fbid . '<br/><br/>';
                
                // Find the image url from the given Facebook ID
                $post['image_url'][] = getImageUrlFromFbId($fbid, $images);
            }
        }
    }
    
    return $imageUrls;
}

function getImageUrlFromFbId($fbid, $images) {
    $imageUrl = null;
    
    for ($i = 0; $i < count($images); $i++) {
        if ($fbid == $images[$i]['object_id']) {
            $imageUrl = $images[$i]['images'][0]['source'];
            break;
        }
    }
    
    return $imageUrl;
}
