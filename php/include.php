<?php

/* * *
 * Build a 'streamQuery' FQL multi-query.
 */

function buildStreamQuery($gid, $constraints, $limit = 20) {
    $streamQuery = 'SELECT post_id,actor_id,updated_time,message,attachment,comment_info,created_time FROM stream WHERE source_id=' . $gid;

    // Check for constraints.
    for ($i = 0; $i < count($constraints); $i++) {
        $constraint = $constraints[$i];
        $streamQuery .= ' AND ' . $constraint['field'] . ' ' . $constraint['operator'] . ' ' . $constraint['value'];
    }

    // Fetch 20 results, and sorted by creation time.
    $streamQuery .= ' ORDER BY updated_time DESC LIMIT ' . $limit;

    $queries = array(
        'streamQuery' => $streamQuery
        //'imageQuery' => 'SELECT object_id,images FROM photo WHERE object_id IN (SELECT attachment FROM #streamQuery)',
        //'userQuery' => 'SELECT uid,last_name,first_name,pic_square,profile_url,pic FROM user WHERE uid IN (SELECT actor_id FROM #streamQuery)'
    );

    return $queries;
}

/* * *
 * Reusable, generic function that executes an FQL query against the given stream.
 */

function streamQuery($fbSession, $gid, $constraints, $limit = 20) {
    $queries = buildStreamQuery($gid, $constraints, $limit);

    $response = $fbSession->api(array(
        'method' => 'fql.multiquery',
        'queries' => $queries
    ));

    return processStreamQuery($response[0]['fql_result_set'], $response[1]['fql_result_set'], $response[2]['fql_result_set']);
}

/* * *
 * Take a response and construct post objects out of it.
 */

function processStreamQuery($stream, $images, $users) {
    $posts = array();

    for ($i = 0; $i < count($stream); $i++) {
        $post = $stream[$i];

        // Parse associated data from the query.
        $post['image_url'] = getImageUrlArray($post, $images, true);
        $post['link_data'] = getLinkData($post);
        $post['user'] = getUserData($post, $users);

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

/* * *
 * Function to parse FQL user data.
 */

function getUserData($post, $users) {
    $user = array();

    for ($i = 0; $i < count($users); $i++) {
        if ($post['actor_id'] == $users[$i]['uid']) {
            $user = $users[$i];
        }
    }

    return $user;
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
    //$index = intval(floor((count($image) / 2)));
    $index = 0;

    // Try to ensure a minimum width. If it is too small, then proceed to the next largest
    // image in the image collection. (0 being the largest).
//    do {
//        $imageSize = getimagesize($image[$index]['source']);
//        $index--;
//
//        if ($index < 0) {
//            $index = 0;
//            break;
//        }
//    } while ($imageSize[0] < 250 && $imageSize[1] < 150);

    return $image[$index]['source'];
}

/**
 * Determine the optimal window size to use in batch queries.
 */
function getOptimalWindowData($fbSession, $gid) {
    $startTime = time();
    $endTime = time() - 3600;

    $query = 'SELECT post_id FROM stream WHERE source_id = ' . $gid . ' AND updated_time <= ' . $startTime . ' AND updated_time >= ' . $endTime . ' LIMIT 100';
    
    $response = $fbSession->api(array(
        'method' => 'fql.query',
        'query' => $query
    ));

    $count = count($response);

    // These values were reached through trial and error.
    switch ($count) {
        case $count < 65:
            $windowSize = 4;
            $batchCount = 2;
            break;
        case $count >= 65 && $count < 85:
            $windowSize = 3.5;
            $batchCount = 2;
            break;
        case $count >= 85 && $count < 115:
            $windowSize = 3.5;
            $batchCount = 2;
            break;
        case $count >= 115 && $count < 150:
            $windowSize = 2.5;
            $batchCount = 2;
            break;
        case $count >= 150 && $count < 225:
            $windowSize = 2;
            $batchCount = 2;
            break;
        default:
            $windowSize = 1;
            $batchCount = 2;
            break;
    }

    return array('windowSize' => 3600 * $windowSize, 'batchCount' => $batchCount);
}

function executeBatchQuery($fbSession, $gid, $constraints = array()) {
    $windowData = getOptimalWindowData($fbSession, $gid);

    $windowSize = $windowData['windowSize'];
    $windowStart = time();
    $windowEnd = $windowStart - $windowSize;

    $batchSize = 5000;
    $batchRunCount = 50;

    $posts = array();

    for ($i = 0; $i < $windowData['batchCount']; $i++) {
        $queries = array();

        // Construct the FB batch request
        for ($j = 0; $j < $batchRunCount; $j++) {
            $queryContraints = $constraints;

            // Add start and end constraints.
            // Start Window Constraint
            $queryContraints[] = array(
                'field' => 'updated_time',
                'operator' => '<=',
                'value' => $windowStart
            );

            // End Window constraint
            $queryContraints[] = array(
                'field' => 'updated_time',
                'operator' => '>=',
                'value' => $windowEnd
            );

            $queries[] = array(
                'method' => 'POST',
                'relative_url' => 'method/fql.multiquery?queries=' . json_encode(buildStreamQuery($gid, $queryContraints, $batchSize))
            );

            $windowStart -= $windowSize;
            $windowEnd -= $windowSize;
        }
        
//        // Call the batch query.
        $response = $fbSession->api('/', 'POST', array(
            'batch' => json_encode($queries),
            'include_headers' => false
        ));

        // Sift through the results.
        for ($k = 0; $k < count($response); $k++) {
            $result = json_decode($response[$k]['body'], true);
            $posts = array_merge($posts, processStreamQuery($result[0]['fql_result_set'], $result[1]['fql_result_set'], $result[2]['fql_result_set']));
        }
    }

    return $posts;
}
