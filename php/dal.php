<?php

header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');

require 'facebook.php';

class DataAccessLayer {

    /** Constants * */
    // Prod
    //const APP_ID = '1401018793479333';
    //const APP_SECRET = '603325411a953e21ccbc29d2c7d50e7e';
    // Test
    const APP_ID = '652991661414427';
    const APP_SECRET = 'b8447ce73d2dcfccde6e30931cfb0a90';

    // Class members
    private $facebook;
    private $appSecretProof;
    private $gid;
    private $stream;
    private $sqlConnectionInfo;

    /*
     * Swd constructor.
     * 
     */

    function __construct() {
        if (!session_id()) {
            session_start();
        }

        $this->facebook = new Facebook(array(
            'appId' => self::APP_ID,
            'secret' => self::APP_SECRET,
            'cookie' => true
        ));

        // Look up an existing access token, if need be.
        if ($this->facebook->getAccessToken() === null) {
            $this->facebook->setAccessToken($_SESSION['accessToken']);
        } else {
            $_SESSION['accessToken'] = $this->facebook->getAccessToken();
        }

        $this->appSecretProof = hash_hmac('sha256', $this->facebook->getAccessToken(), self::APP_SECRET);

        // Retrieve the stream if it's there.
        if (isset($_SESSION['stream'])) {
            $this->stream = $_SESSION['stream'];
        }

        // Test the facebook object that was created.
        $this->api('/me', 'GET');

        $this->sqlConnectionInfo = array("UID" => "rogan2929@lreuagtc6u", "pwd" => "Revelation19:11", "Database" => "swapperAGiJRLgvy", "LoginTimeout" => 30, "Encrypt" => 1);
    }

    /** Getters and Setters * */

    /**
     * Get the currently loaded group's gid.
     * @return type
     */
    public function getGid() {
        return $this->gid;
    }

    /**
     * Set the currently loaded group's gid.
     * @param type $gid
     */
    public function setGid($gid) {
        $this->gid = $gid;
    }

    /** Methods * */
    // Group management functions.

    public function getGroupInfo() {
        $queries = array(
            'memberQuery' => 'SELECT gid,bookmark_order FROM group_member WHERE uid=me() ORDER BY bookmark_order',
            'groupQuery' => 'SELECT gid,name,icon FROM group WHERE gid IN (SELECT gid FROM #memberQuery)'
        );

        $response = $this->api(array(
            'method' => 'fql.multiquery',
            'queries' => $queries
        ));

        // Grab the results of the query and return it.
        return $response[1] ['fql_result_set'];
    }

    public function getHiddenGroups() {
        
    }

    public function hideGroup($gid) {
        
    }

    // Post operation functions.

    public function deletePost($postId) {
        $this->api('/' . $postId, 'DELETE');
    }

    public function likePost($postId, $userLikes) {
        if ($userLikes == true) {
            // Like the post.
            $this->api('/' . $postId . '/likes', 'POST', array('user_likes' => true));
        } else {
            // Delete the post's like.
            $this->api('/' . $postId . '/likes', 'DELETE');
        }

        // TODO: Update the cached FQL stream.

        return $userLikes;
    }

    public function newPost() {
        
    }

    public function postComment($postId, $comment) {        
        // Post the comment and get the response
        $id = $this->api('/' . $postId . '/comments', 'POST', array('message' => $comment));

        // Get the comment and associated user data...
        $queries = array(
            'commentQuery' => 'SELECT fromid,text,text_tags,attachment,time FROM comment WHERE id=' . $id['id'],
            'commentUserQuery' => 'SELECT uid,last_name,first_name,pic_square,profile_url FROM user WHERE uid IN (SELECT fromid FROM #commentQuery)'
        );

        // Query Facebook's servers for the necessary data.
        $response = $this->api(array(
            'method' => 'fql.multiquery',
            'queries' => $queries
        ));

        // Construct a return object.
        $newComment = $response[0]['fql_result_set'][0];
        $newComment['user'] = $response[1]['fql_result_set'][0];

        // Replace any line breaks with <br/>
        if ($newComment['text']) {
            $newComment['text'] = nl2br($newComment['text']);
        }

        return $newComment;
    }

    // Stream operation functions.

    public function getLikedPosts($offset, $limit) {
        $posts = array();

        // Look through the cached stream for liked posts.
        for ($i = 0; $i < count($this->stream); $i++) {
            if ($this->stream[$i]['user_likes'] == 1) {
                $posts[] = $this->stream[$i];
            }
        }

        return $this->getPostData(array_slice($posts, $offset, $limit));
    }

    public function getMe() {
        return $this->api('/me')['id'];
    }

    public function getMyPosts($offset, $limit) {
        $uid = $this->api('/me')['id'];
        $posts = array();

        // Look through the cached stream, match by uid => actor_id
        for ($i = 0; $i < count($this->stream); $i++) {
            if ($this->stream[$i]['actor_id'] == $uid) {
                $posts[] = $this->stream[$i];
            }
        }

        return $this->getPostData(array_slice($posts, $offset, $limit));
    }

    public function getNewPosts($refresh, $offset, $limit) {
        // Get a new stream if necessary.
        if ($refresh == 1) {
            $this->fetchStream();
        }

        return $this->getPostData(array_slice($this->stream, $offset, $limit));
    }

    public function getPostDetails($postId) {
        $queries = array(
            'detailsQuery' => 'SELECT post_id,message,actor_id,permalink,like_info,share_info,comment_info,tagged_ids,attachment,created_time FROM stream WHERE post_id="' . $postId . '"',
            'imageQuery' => 'SELECT object_id,images FROM photo WHERE object_id IN (SELECT attachment FROM #detailsQuery)',
            'userQuery' => 'SELECT last_name,first_name,pic,profile_url FROM user WHERE uid IN (SELECT actor_id FROM #detailsQuery)',
            'commentsQuery' => 'SELECT fromid,text,text_tags,attachment,time FROM comment WHERE post_id IN (SELECT post_id FROM #detailsQuery) ORDER BY time ASC',
            'commentUserQuery' => 'SELECT uid,last_name,first_name,pic_square,profile_url FROM user WHERE uid IN (SELECT fromid FROM #commentsQuery)'
        );

        // Run the query.
        $response = $this->api(array(
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
        $post['image_url'] = $this->getImageUrlArray($post, $images, false);

        // Extract link data.
        $post['link_data'] = $this->getLinkData($post);

        // Determine type of post.
        $post['post_type'] = $this->getPostType($post);

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
//        $actions = $this->api('/' . $postId . '?fields=actions');
//
//        $post['action_links'] = $actions['actions'];

        return $post;
    }

    public function refreshStream() {
        if (isset($_SESSION['gid'])) {
            $this->setGid($_SESSION['gid']);

            // Fetch the new stream.
            $this->fetchStream();
        }
    }

    public function searchPosts($search, $offset, $limit) {
        $posts = array();

        // Look through the cached stream for liked posts.
        for ($i = 0; $i < count($this->stream); $i++) {
            if (stripos($this->stream[$i]['message'], $search) !== false) {
                $posts[] = $this->stream[$i];
            }
        }

        return $this->getPostData(array_slice($posts, $offset, $limit));
    }

    /** Private Methods * */
    private function api(/* polymorphic */) {
        $args = func_get_args();

        if (is_array($args[0])) {
            // Array was passed as an argument.
            $args[0]['appsecret_proof'] = $this->appSecretProof;
        } else {
            // Array was not passed as an argument.
            if (is_array($args[1]) && empty($args[2])) {
                $args[2] = $args[1];
                $args[1] = 'GET';
            }

            // Insert appsecret_proof into each API call.
            $args[2]['appsecret_proof'] = $this->appSecretProof;
        }

        try {
            // Call the facebook->api function.
            return call_user_func_array(array($this->facebook, 'api'), $args);
        } catch (FacebookApiException $ex) {
            // Selectively decide how to handle the error, based on returned code.
            // https://developers.facebook.com/docs/graph-api/using-graph-api/#errors
            switch ($ex->getCode()) {
                case '190':
                    http_response_code(401);
                    echo json_encode(array('message' => 'Sorry, but your session is no longer valid - automatically taking you back to the main page.'));
                    break;
                default:
                    http_response_code(500);
                    echo json_encode($ex->getResult());
                    break;
            }
        }
    }

    private function fetchStream() {
        // Save $gid to session for later use.
        $_SESSION['gid'] = $this->gid;

        // Wait for other threads to finish updating the cached FQL stream.
        $this->waitForFetchStreamCompletion();

        // Refresh the FQL stream.
        $_SESSION['refreshing'] = true;
        $_SESSION['stream'] = $this->queryStream();
        $_SESSION['refreshing'] = false;

        $this->stream = $_SESSION['stream'];
    }

    /**
     * Determine the optimal window size to use in batch queries.
     */
    private function getOptimalWindowSize() {
        $startTime = time();
        $endTime = time() - 3600;

        $request = '/' . $this->gid . '/feed?fields=id&since=' . $endTime . '&until=' . $startTime . ' LIMIT 100';

        $response = $this->api($request);

        $count = count($response);

        // These values were reached through trial and error.
        switch ($count) {
            case $count < 65:
                $windowSize = 4;
                break;
            case $count >= 65 && $count < 85:
                $windowSize = 3.5;
                break;
            case $count >= 85 && $count < 115:
                $windowSize = 3.5;
                break;
            case $count >= 115 && $count < 150:
                $windowSize = 2.5;
                break;
            case $count >= 150 && $count < 225:
                $windowSize = 2;
                break;
            default:
                $windowSize = 1;
                break;
        }

        return 3600 * $windowSize;
    }

    /*
     * Retrieve additional data for the posts in the provided array.
     */

    private function getPostData($posts, $limit) {
        $queries = array();
        $result = array();

        if (!isset($limit)) {
            $limit = 50;        // Max batch size.
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
        $response = $this->api('/', 'POST', array(
            'batch' => json_encode($queries),
            'include_headers' => false
        ));

        // Sift through the results.
        for ($i = 0; $i < count($response); $i++) {
            $body = json_decode($response[$i]['body'], true);
            $result = array_merge($result, $this->processStreamQuery($body[0]['fql_result_set'], $body[1]['fql_result_set'], $body[2]['fql_result_set']));
        }
        
        // If there are no posts to load, then insert an terminating post.
        if (count($posts) < $limit) {
            $result[] = array('post_id' => 'terminator');
        }

        return $result;
    }

    /*
     * Take a response and construct post objects out of it.
     */

    private function processStreamQuery($stream, $images, $users) {
        $posts = array();

        for ($i = 0; $i < count($stream); $i++) {
            $post = $stream[$i];

            // Parse associated data from the query.
            $post['image_url'] = $this->getImageUrlArray($post, $images, true);
            $post['link_data'] = $this->getLinkData($post);
            $post['user'] = $this->getUserData($post, $users);

            // Erase any attachment data to save on object size.
            // This has already been parsed out.
            unset($post['attachment']);

            $post['post_type'] = $this->getPostType($post);

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

    /*
     * For posts with an image, look for associated image data.
     */

    private function getImageUrlArray($post, $images, $thumbnails = true) {
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
                        $imageUrls[] = $this->getImageUrlFromFbId($fbid, $images, $thumbnails);
                    }
                }
            }
        }

        return $imageUrls;
    }

    /*     * *
     * Function to parse FQL attachment data for links.
     */

    private function getLinkData($post) {
        $linkData = array();

        // Loop through media attachments, looking for type 'link'.
        if ($post['attachment'] && $post['attachment']['media'] && $post['attachment']['media'][0] &&
                $post['attachment']['media'][0]['type'] == 'link') {
            $linkData = $post['attachment'];
        }

        return $linkData;
    }

    /*
     * Function to parse FQL user data.
     */

    private function getUserData($post, $users) {
        $user = array();

        for ($i = 0; $i < count($users); $i++) {
            if ($post['actor_id'] == $users[$i]['uid']) {
                $user = $users[$i];
            }
        }

        return $user;
    }

    private function getImageUrlFromFbId($fbid, $images, $thumbnails = true) {
        $imageUrl = null;

        for ($i = 0; $i < count($images); $i++) {
            if ($fbid == $images[$i]['object_id']) {
                // See if we are trying to retrieve a small image. (Usually last in the array.)
                if ($thumbnails) {
                    $imageUrl = $this->getSmallImageUrl($images[$i]['images']);
                } else {
                    //$imageUrl = $images[$i]['images'][$index]['source'];
                    $imageUrl = $this->getLargeImageUrl($images[$i]['images']);
                }


                break;
            }
        }

        return $imageUrl;
    }

    /*
     * Determines the post type:
     *  1. Image Posts (text and non-text, doesn't matter.) ('image')
     *  2. Text Only Posts ('text')
     *  3. Link Only Posts ('link')
     *  4. Link + Text Posts ('textlink')
     */

    private function getPostType($post) {
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

    /*     * *
     * In an array, find the largest Facebook image.
     */

    private function getLargeImageUrl($image) {
        return $image[0]['source'];
    }

    /*     * *
     * In an array, find the smallest Facebook image.
     */

    private function getSmallImageUrl($image) {
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

    /*
     * Query the FQL stream table for some basic data that will be cached.
     */

    private function queryStream() {
        $windowSize = $this->getOptimalWindowSize();
        $windowStart = time();
        $windowEnd = $windowStart - $windowSize;

        $stream = array();

        $uid = $this->getMe();

        // Pull the feed for stream data.
        for ($i = 0; $i < 2; $i++) {
            $queries = array();

            for ($j = 0; $j < 50; $j++) {
                $query = '/' . $this->gid . '/feed?fields=id,message,from,likes&limit=5000&since=' . $windowEnd . '&until=' . $windowStart;

                $windowStart -= $windowSize;
                $windowEnd -= $windowSize;

                $queries[] = array(
                    'method' => 'GET',
                    'relative_url' => $query
                );
            }

            // Execute a batch query.
            $response = $this->api('/', 'POST', array(
                'batch' => json_encode($queries),
                'include_headers' => false
            ));

            for ($k = 0; $k < count($response); $k++) {
                $body = json_decode($response[$k]['body'], true);
                $stream = array_merge($stream, $body['data']);
            }
        }

        // Shape the stream to make it look like it came from an FQL query.
        // id => post_id
        // from['id'] => actor_id
        // likes['data'] => user_likes

        for ($i = 0; $i < count($stream); $i++) {
            $stream[$i]['post_id'] = $stream[$i]['id'];
            unset($stream[$i]['id']);

            $stream[$i]['actor_id'] = $stream[$i]['from']['id'];
            unset($stream[$i]['from']);

            $stream[$i]['user_likes'] = 0;

            if (isset($stream[$i]['likes']) && isset($stream[$i]['likes']['data'])) {
                for ($j = 0; $j < count($stream[$i]['likes']['data']); $j++) {
                    $likeInfo = $stream[$i]['likes']['data'][$j];

                    if ($likeInfo['id'] == $uid) {
                        $stream[$i]['user_likes'] = 1;
                    }
                }

                unset($stream[$i]['likes']);
            }
        }

        return $stream;
    }

    /*
     * Forcibly pause the thread in order for fetchStream to complete.
     */

    private function waitForFetchStreamCompletion() {
        while ($_SESSION['refreshing'] == true) {
            sleep(3);
        }
    }

}
