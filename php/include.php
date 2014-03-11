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

        $post['image_url'] = getImageUrlArray($post, $images, true);
        $post['link_data'] = getLinkData($post);

        // Erase any attachment data to save on object size.
        // This has already been parsed out.
        unset($post['attachment']);

        $post['post_type'] = getPostType($post);

        // Determine which kind of post this is.
        // Replace any line breaks with <br/>
        if (strlen($post['message']) > 0) {
            $post['message'] = nl2br($post['message']);
        }

        // Add to the posts array.
        $posts[] = $post;
    }

    return $posts;
}

/* * *
 * For posts with an image, look for associated image data.
 */

function getImageUrlArray($post, $images, $thumbnails = true) {
    $imageUrls = array();

    if ($post['attachment'] && $post['attachment']['media']) {
        // For posts with an image, look for associated image data.
        for ($i = 0; $i < count($post['attachment']); $i++) {
            if ($post['attachment']['media'][$i]) {
                // Determine if this attachment is a photo or a link.
                if ($post['attachment']['media'][$i]['type'] == 'photo' && $post['attachment']['media'][$i]['photo']) {
                    // Get image's unique Facebook Id
                    $fbid = $post['attachment']['media'][$i]['photo']['fbid'];

                    // Find the image url from the given Facebook ID
                    $imageUrls[] = getImageUrlFromFbId($fbid, $images, $thumbnails);
                }
            }
        }
    }

    return $imageUrls;
}

/* * *
 * Function to parse FQL attachment data for links.
 */

function getLinkData($post) {
    $linkData = array();

    // Loop through media attachments, looking for type 'link'.
    if ($post['attachment'] && $post['attachment']['media'] && $post['attachment']['media'][0] &&
            $post['attachment']['media'][0]['type'] == 'link') {
        $linkData = $post['attachment'];
    }

    return $linkData;
}

function getImageUrlFromFbId($fbid, $images, $thumbnails = true) {
    $imageUrl = null;

    for ($i = 0; $i < count($images); $i++) {
        if ($fbid == $images[$i]['object_id']) {
            // See if we are trying to retrieve a small image. (Usually last in the array.)
            if ($thumbnails) {
                $imageUrl = getSmallImageUrl($images[$i]['images']);
            } else {
                //$imageUrl = $images[$i]['images'][$index]['source'];
                $imageUrl = getLargeImageUrl($images[$i]['images']);
            }


            break;
        }
    }

    return $imageUrl;
}

/* * *
 * Determines the post type:
 *  1. Image Posts (text and non-text, doesn't matter.) ('image')
 *  2. Text Only Posts ('text')
 *  3. Link Only Posts ('link')
 *  4. Link + Text Posts ('textlink')
 */

function getPostType($post) {
    $postType = 'unknown';

    // The logic below should catch everything.
    if (count($post['image_url']) > 0) {
        $postType = 'image';       // Image Post
    } else if (strlen($post['message']) > 0) {
        $postType = 'text';        // Assume text post, but this might change to link.
    }

    if (strlen($post['message']) == 0 && count($post['link_data']) > 0) {
        $postType = 'link';        // Link post.
    }

    if (strlen($post['message']) > 0 && count($post['link_data']) > 0) {
        $postType = 'textlink';    // Link + Text post.
    }

    return $postType;
}

/* * *
 * In an array, find the largest Facebook image.
 */

function getLargeImageUrl($image) {
    return $image[0]['source'];
}

/* * *
 * In an array, find the smallest Facebook image.
 */

function getSmallImageUrl($image) {
    // Grab the 'middle' image for a scaled version of the full size image.
    $index = intval(floor((count($image) / 2)));
    return $image[$index]['source'];
}

/**
 * Determine the optimal window size to use in batch queries.
 */
function getOptimalWindowSize($fbSession, $sourceId) {
    // Various window sizes to try.
    $multiples = array(
        0.5, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5, 6, 8, 10, 12, 14, 16, 18, 20, 22, 24
    );
    $index = 0;
    $max = 0;

    // Try each window size, and determine which one yields the highest number of results.
    for ($i = 0; $i < count($multiples); $i++) {
        $startTime = time();
        $endTime = $startTime - 3600 * $multiples[$i];

        $query = 'SELECT post_id FROM stream WHERE source_id = ' . $sourceId . ' AND updated_time <= ' . $startTime . ' AND updated_time >= ' . $endTime . ' LIMIT 100';

        $response = $fbSession->api(array(
            'method' => 'fql.query',
            'query' => $query
        ));
        
        $count = count($response['data']);
        
        if ($count > $max) {
            $max = $count;
            $index = $i;
        }
    }

    return $multiples[$index] * 3600;
}

function getGroupPostsbyUid($fbSession, $sourceId, $uid) {
    // Define the initial window to search within.
    $windowSize = getOptimalWindowSize($fbSession, $sourceId);
    $windowStart = time();
    $windowEnd = $windowStart - $windowSize;
    
    echo $windowSize;

//    $batchSize = 5000;
//    $batchRunCount = 50;
//
//    // Create the constraints array.
//    $actorConstraint = array(
//        'field' => 'actor_id',
//        'operator' => '=',
//        'value' => 'me()'
//    );
//
//    $queries = array();
//    //$posts = array();
//
//    // Construct the FB batch request
//    for ($i = 0; $i < $batchRunCount; $i++) {
//        $constraints = array();
//
//        $constraints[] = $actorConstraint;
//
//        $constraints[] = array(// Window start constraint
//            'field' => 'updated_time',
//            'operator' => '<=',
//            'value' => $windowStart
//        );
//
//        $constraints[] = array(// Window end constraint
//            'field' => 'updated_time',
//            'operator' => '>=',
//            'value' => $windowEnd
//        );
//
//        $queries[] = array(
//            'method' => 'POST',
//            'relative_url' => 'method/fql.multiquery?queries=' . json_encode(buildStreamQuery($gid, $constraints, $batchSize))
//        );
//        
//        //$posts = array_merge($posts, streamQuery($fbSession, $gid, $constraints, $batchSize));
//
//        $windowStart -= $windowSize;
//        $windowEnd -= $windowSize;
//    }
//
//    // Call the batch query.
//    $response = $fbSession->api('/', 'POST', array(
//        'batch' => json_encode($queries),
//        'include_headers' => false
//    ));
//
//    $posts = array();
//
//    // Sift through the results.
//    for ($i = 0; $i < count($response); $i++) {
//        $result = json_decode($response[$i]['body'], true);
//        $posts = array_merge($posts, processStreamQuery($result[0]['fql_result_set'], $result[1]['fql_result_set']));
//    }
//
//    return $posts;
}