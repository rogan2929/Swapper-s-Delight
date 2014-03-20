<?php

function fetchStream($gid, $refresh = 0) {
    echo 'TEST';
    
    $facebook = new Facebook(array(
        'appId' => $_SESSION['appId'],
        'secret' => $_SESSION['appSecret'],
        'cookie' => true
    ));

    echo json_encode($facebook);
    
    if (http_response_code() != 401) {
        // Wait for other threads to finish updating the cached FQL stream.
        waitForFetchStreamCompletion();

        // Refresh the FQL stream.
        if ($refresh == 1) {
            $_SESSION['refreshing'] = true;
            $_SESSION['stream'] = queryStream($facebook, $gid);
            $_SESSION['gid'] = $gid;
            $_SESSION['refreshing'] = false;
        }
    }
}

/* * *
 * Forcibly pause the thread in order for fetchStream to complete.
 */

function waitForFetchStreamCompletion() {
    while ($_SESSION['refreshing'] == true) {
        sleep(3);
    }
}

/* * *
 * Retrieve additional data for the posts in the provided array.
 */

function getPostData($posts, $limit) {
    $facebook = new Facebook(array(
        'appId' => $_SESSION['appId'],
        'secret' => $_SESSION['appSecret'],
        'cookie' => true
    ));

    $queries = array();
    $result = array();

    if (!isset($limit)) {
        $limit = count($posts);
    }

    // Build a multiquery for each post in the provided array.
    for ($i = 0; $i < count($posts) && $i < $limit; $i++) {
        $queries[] = array(
            'method' => 'POST',
            'relative_url' => 'method/fql.multiquery?queries=' . json_encode(array(
                'streamQuery' => 'SELECT post_id,actor_id,updated_time,message,attachment,comment_info,created_time FROM stream WHERE post_id="' . $posts[$i]['post_id'] . '"',
                'imageQuery' => 'SELECT object_id,images FROM photo WHERE object_id IN (SELECT attachment FROM #streamQuery)',
                'userQuery' => 'SELECT uid,last_name,first_name,pic_square,profile_url,pic FROM user WHERE uid IN (SELECT actor_id FROM #streamQuery)'
            ))
        );
    }

    // Execute a batch query.
    $response = $facebook->api('/', 'POST', array(
        'batch' => json_encode($queries),
        'include_headers' => false
    ));

    // Sift through the results.
    for ($i = 0; $i < count($response); $i++) {
        $body = json_decode($response[$i]['body'], true);
        $result = array_merge($result, processStreamQuery($body[0]['fql_result_set'], $body[1]['fql_result_set'], $body[2]['fql_result_set']));
    }

    return $result;
}

/* * *
 * Query the FQL stream table for some basic data that will be cached.
 */

function queryStream($facebook, $gid) {
    $windowData = getOptimalWindowData($facebook, $gid);

    $windowSize = $windowData['windowSize'];
    $windowStart = time();
    $windowEnd = $windowStart - $windowSize;

    $stream = array();

    for ($i = 0; $i < $windowData['batchCount']; $i++) {
        $queries = array();

        // Construct the FB batch request
        for ($j = 0; $j < 50; $j++) {
            $query = 'SELECT post_id,actor_id,message,like_info FROM stream WHERE source_id=' . $gid . ' AND updated_time <= ' . $windowStart . ' AND updated_time >= ' . $windowEnd . ' LIMIT 5000';

            $queries[] = array(
                'method' => 'GET',
                'relative_url' => 'method/fql.query?query=' . urlencode($query)
            );

            $windowStart -= $windowSize;
            $windowEnd -= $windowSize;
        }

        // Call the batch query.
        $response = $facebook->api('/', 'POST', array(
            'batch' => json_encode($queries),
            'include_headers' => false
        ));

        for ($j = 0; $j < count($response); $j++) {
            $stream = array_merge($stream, json_decode($response[$j]['body'], true));
        }
    }

    return $stream;
}

/**
 * Determine the optimal window size to use in batch queries.
 */
function getOptimalWindowData($facebook, $gid) {
    $startTime = time();
    $endTime = time() - 3600;

    $query = 'SELECT post_id FROM stream WHERE source_id = ' . $gid . ' AND updated_time <= ' . $startTime . ' AND updated_time >= ' . $endTime . ' LIMIT 100';

    $response = $facebook->api(array(
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
    $index = intval(floor((count($image) / 2)));

    // Try to ensure a minimum width. If it is too small, then proceed to the next largest
    // image in the image collection. (0 being the largest).
    do {
        $imageSize = getimagesize($image[$index]['source']);
        $index--;

        if ($index < 0) {
            $index = 0;
            break;
        }
    } while ($imageSize[0] < 250 && $imageSize[1] < 150);

    return $image[$index]['source'];
}
