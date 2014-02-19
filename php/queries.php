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

/***
 * For posts with an image, look for associated image data.
 */
function getImageUrlArray($post, $images, $thumbnails = true) {
    $imageUrls = array();

    if ($post['attachment'] && $post['attachment']['media']) {
        // For posts with an image, look for associated image data.
        for ($i = 0; $i < count($post['attachment']); $i++) {
            if ($post['attachment']['media'][$i] && $post['attachment']['media'][$i]['photo']) {
                // Get image's unique Facebook Id
                $fbid = $post['attachment']['media'][$i]['photo']['fbid'];
                
                // Find the image url from the given Facebook ID
                $imageUrls[] = getImageUrlFromFbId($fbid, $images, $thumbnails);
            }
        }
    }
    
    return $imageUrls;
}

function getImageUrlFromFbId($fbid, $images, $thumbnails = true) {
    $imageUrl = null;
    
    for ($i = 0; $i < count($images); $i++) {
        if ($fbid == $images[$i]['object_id']) {
            $index = 0;
            
            // See if we are trying to retrieve a small image. (Usually last in the array.)
            if ($thumbnails) {
                $index = count($images[$i]['images']) - 1;
            }
            
            $imageUrl = $images[$i]['images'][$index]['source'];
            break;
        }
    }
    
    return $imageUrl;
}
