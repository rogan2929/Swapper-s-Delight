<?php

require 'base-factory.php';
require 'image-object-factory.php';
require 'link-data-factory.php';
require 'comment-factory.php';
require 'user-factory.php';

if (!session_id()) {
    session_start();
}

/**
 * A factory for retrieving posts from a group's stream.
 */
class PostFactory extends BaseFactory {

    private $gid;
    private $stream;

    // FQL Query Strings
    const DETAILS_QUERY = 'SELECT post_id,message,actor_id,permalink,like_info,share_info,comment_info,tagged_ids,attachment,created_time,updated_time FROM stream ';
    const STREAM_QUERY = 'SELECT post_id,actor_id,updated_time,message,attachment,comment_info,created_time,like_info FROM stream ';
    const USER_QUERY = 'SELECT uid,last_name,first_name,pic_square,profile_url,pic FROM user ';
    const COMMENT_QUERY = 'SELECT fromid,text,text_tags,attachment,time,id FROM comment ';
    const IMAGE_QUERY = 'SELECT object_id,images FROM photo ';

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct();

        // Retrieve the stream if it's there.
        if (isset($_SESSION['stream'])) {
            $this->stream = $_SESSION['stream'];
        } else {
            $this->stream = null;
        }
    }

    /**
     * Create a new post.
     */
    public function newPost() {
        
    }

    /**
     * Like a post.
     * @param string $postId
     * @param bool $userLikes
     * @return bool
     */
    public function likePost($postId, $userLikes) {
        if ($userLikes == true) {
            // Like the post.
            $this->graphApiClient->likeObject($postId);
        } else {
            // Unlike the post.
            $this->graphApiClient->unLikeObject($postId);
        }

//        // Update the cached post stream.
//        for ($i = 0; $i < count($this->stream); $i++) {
//            if ($this->stream[$i]['post_id'] == $postId) {
//                $this->stream[$i]['user_likes'] = (int) $userLikes;
//            }
//        }
//
//        // Save the updated stream.
//        $_SESSION['stream'] = $this->stream;

        return $userLikes;
    }

    /**
     * Set the currently loaded group's gid.
     * @param string $gid
     */
    public function setGid($gid) {
        $this->gid = $gid;
    }

    /**
     * Method for performing a fully query of the FQL stream table. 
     * Designed to be called from an external file so execution can be
     * performed asynchronously through fopen or popen.
     * @return string
     */
    public function fetchStreamFullAsync($args) {
        $this->graphApiClient->setAccessToken($args['accessToken']);
        $this->setGid($args['gid']);
        
        $windowStart = time();
        $windowSize = $this->getOptimalWindowSize();

        $stream = $this->getFeedData($windowSize, $windowStart, 50, 1);
        $windowStart = $windowStart - ($windowSize * 50 * 1);
        $stream = array_merge($stream, $this->getFeedData($windowSize * 2, $windowStart, 13, 1));
        $windowStart = $windowStart - ($windowSize * 2 * 13 * 1);
        $stream = array_merge($stream, $this->getFeedData($windowSize * 3, $windowStart, 11, 1));
        $windowStart = $windowStart - ($windowSize * 3 * 11 * 1);
        $stream = array_merge($stream, $this->getFeedData(3600 * 24 * 30, $windowStart, 1, 1));

        return serialize($stream);
    }
    
    private function fetchStreamFullAsyncCallback() {
        
    }

    /**
     * Gets the cached post stream.
     * @return array
     */
    public function getStream() {
        return $this->stream;
    }

    /**
     * Retrieve posts that are liked by the current user.
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getLikedPosts($offset, $limit) {
        $posts = array();

        $this->waitForFetchStreamCompletion();

        // Look through the cached stream for liked posts.
        for ($i = 0; $i < count($this->stream); $i++) {
            if ($this->stream[$i]->getUserLikes() == 1) {
                $posts[] = $this->stream[$i];
            }
        }

        return $this->getPostData($posts, $offset, $limit);
    }

    /**
     * Retrieves posts that were created by the current user.
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getMyPosts($offset, $limit) {
        $uid = $this->graphApiClient->getMe();
        $posts = array();

        $this->waitForFetchStreamCompletion();

        // Look through the cached stream, match by uid => actor_id
        for ($i = 0; $i < count($this->stream); $i++) {
            if ($this->stream[$i]->getActor()->getUid() == $uid) {
                $posts[] = $this->stream[$i];
            }
        }

        return $this->getPostData($posts, $offset, $limit);
    }

    /**
     * Retrieves posts newly created in the specified group. Optionally can trigger a refresh of the stream cache.
     * @param bool $refresh
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getNewPosts($refresh, $offset, $limit) {
        // Get a new stream if necessary.
        if ($refresh == true) {
            $this->fetchStream(true);
        }

        return $this->getPostData($this->stream, $offset, $limit);
    }

    /**
     * Retrieves additional data for the given post and populates a /Post entity object.
     * @param type $postId
     * @return /Post
     */
    public function getPostDetails($postId) {
        error_log('getPostDetails');
        
        $queries = array(
            'detailsQuery' => PostFactory::DETAILS_QUERY . 'WHERE post_id="' . $postId . '"',
            'imageQuery' => PostFactory::IMAGE_QUERY . 'WHERE object_id IN (SELECT attachment FROM #detailsQuery)',
            'userQuery' => PostFactory::USER_QUERY . 'WHERE uid IN (SELECT actor_id FROM #detailsQuery)',
            'commentsQuery' => PostFactory::COMMENT_QUERY . 'WHERE post_id IN (SELECT post_id FROM #detailsQuery) ORDER BY time ASC',
            'commentUserQuery' => PostFactory::USER_QUERY . 'WHERE uid IN (SELECT fromid FROM #commentsQuery)',
            'commentImageQuery' => PostFactory::IMAGE_QUERY . 'WHERE object_id IN (SELECT attachment FROM #commentsQuery)'
        );

        // Run the query.
        $response = $this->graphApiClient->api(array(
            'method' => 'fql.multiquery',
            'queries' => $queries
        ));

        // Check to ensure that post data was actually returned.
        // This is done by checking for post_id in the fql_result_set.
        if (!isset($response[0]['fql_result_set'][0]['post_id'])) {
            $e = new Exception('Sorry, but this post couldn\'t be loaded. It may have been deleted.');
            echo $e->getMessage();
            throw $e;
        }

        try {
            $raw = $response[0]['fql_result_set'][0];

            // Collect the returned data.
            $post = new Post();

            // post_id,message,actor_id,permalink,like_info,share_info,comment_info,tagged_ids,attachment,created_time
            $post->setId($raw['post_id']);
            $post->setMessage($raw['message']);
            $post->setPermalink($raw['permalink']);
            $post->setUserLikes($raw['like_info']['user_likes']);
            $post->setUpdatedTime($raw['updated_time']);
            $post->setCreatedTime($raw['created_time']);
            $post->setActor((new UserFactory())->createUser($response[3]['fql_result_set'][0]));
        } catch (Exception $ex) {
            echo 'Sorry, but this post couldn\'t be loaded. It may have been deleted.';
            http_response_code(500);
        }

        if (strlen($post->getMessage()) > 0) {
            // Replace new line characters with <br/>
            $post->setMessage(nl2br($post->getMessage()));
        }

        // Extract image data for the post.
        $imgFactory = new ImageObjectFactory($response[2]['fql_result_set']);
        $post->setImageObjects($imgFactory->getImageObjectsFromFQL($raw, false));

        // Extract link data.
        $post->setLinkData((new LinkDataFactory())->getLinkDataFromFQL($raw));

        // Determine type of post.
        $post->setType($this->getPostType($post));

        // Parse comment data and set it.
        $post->setComments((new CommentFactory())->getCommentsFromFQL($response[1]['fql_result_set'], $response[5]['fql_result_set'], $response[4]['fql_result_set']));

        return $post;
    }

    /**
     * Retrieves refreshed post data for the given post ids.
     * @param array $postIds
     */
    public function getRefreshedStreamData($postIds) {
        $posts = array();

        for ($i = 0; $i < count($postIds); $i++) {
            for ($j = 0; $j < count($this->stream); $j++) {
                if ($postIds[$i] == $this->stream[$j]->getId()) {
                    $posts[] = $this->stream[$j];
                }
            }
        }

        // Return the updated stream data.
        return $posts;
    }

    /**
     * Forces an update of the cached post stream.
     * @return boolean
     */
    public function refreshStream($gid) {
        $this->gid = $gid;

        // Fetch the new stream.
        $this->fetchStream(false);

        return var_dump($this->stream);
    }

    /**
     * Search the post cache for posts that match the search string. Search is based on message and poster's name.
     * @param type $search
     * @param type $offset
     * @param type $limit
     * @return type
     */
    public function searchPosts($search, $offset, $limit) {
        $posts = array();

        $this->waitForFetchStreamCompletion();

        // Look through the cached stream for posts whose message or user matches the search term.
        for ($i = 0; $i < count($this->stream); $i++) {
            if (stripos($this->stream[$i]->getMessage(), $search) !== false || stripos($this->stream[$i]->getActor()->getFullName(), $search) !== false) {
                $posts[] = $this->stream[$i];
            }
        }

        return $this->getPostData($posts, $offset, $limit);
    }

    /**
     * Builds a locally cached version of the FQL stream table.
     * @param bool $prefetchOnly
     */
    private function fetchStream($prefetchOnly) {
        if ($prefetchOnly) {
            // Only retrieve a small subset of the full stream, in order for data to be displayed more quickly to the user.
            $windowStart = time();
            $windowSize = $this->getOptimalWindowSize();

            // Perform a two step query of varying window sizes, and then merge the result.
            $this->stream = array_merge(
                    $this->getFeedData($windowSize, $windowStart, 14, 1), 
                    $this->getFeedData(3600 * 24 * 30, $windowStart - ($windowSize * 14 * 1), 1, 1)
            );
        } else {
            $_SESSION['refreshing'] = true;

//            // Offload full query of the stream onto a simulated background thread by calling fopen.
//            //$childProc = fopen('https://' . filter_input(INPUT_SERVER, 'HTTP_HOST') . '/php/execute-delegated.php?class=PostFactory&method=fetchStreamFullAsync&echo=1&accessToken' . $this->graphApiClient->getAccessToken(), 'r');
//            $url = 'http://' . filter_input(INPUT_SERVER, 'HTTP_HOST') . '/php/execute-delegated.php';
//
//            // Get response from child (if any) as soon at it's ready:
//            //$this->stream = json_decode(stream_get_contents($childProc));
//            
//            $args = array(
//                'gid' => $this->gid,
//                'accessToken' => $this->graphApiClient->getAccessToken()
//            );
//            
//            $postFields = array(
//                'class' => 'PostFactory',
//                'method' => 'fetchStreamFullAsync',
//                'args' => json_encode($args)
//            );
//            
//            $ch = curl_init();
//            curl_setopt($ch, CURLOPT_URL, $url);
//            curl_setopt($ch, CURLOPT_POST, true);
//            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
//            curl_setopt($ch,CURLOPT_RETURNTRANSFER, false);
//            curl_setopt($ch, CURLOPT_HEADER, 0);
//            
//            $result = curl_exec($ch);
//            
//            if (!$result) {
//                error_log(curl_errno($ch));
//                error_log(curl_error($ch));
//            }
//            
//            curl_close($ch);
//            $this->stream = unserialize($result);
            
            $args = array(
                'gid' => $this->gid,
                'accessToken' => $this->graphApiClient->getAccessToken()
            );
            
            $this->stream = unserialize($this->fetchStreamFullAsync($args));

            $_SESSION['refreshing'] = false;
        }

        $_SESSION['stream'] = $this->stream;
    }

    /**
     * Determine the optimal window size to use in batch queries.
     */
    private function getOptimalWindowSize() {
        $startTime = time();
        $endTime = time() - 3600;

        // Make the call and count the number of responses.
        $count = count($this->graphApiClient->api('/' . $this->gid . '/feed?fields=id&since=' . $endTime . '&until=' . $startTime . ' LIMIT 100'));

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

    /**
     * Retrieve additional data for the posts in the provided array.
     * @param array $posts
     * @param int $offset
     * @param int $limit
     * @return array
     */
    private function getPostData($posts, $offset, $limit) {
        $queries = array();
        $result = array();

        if (!isset($limit)) {
            $limit = 50;        // Max batch size.
        }

        // Slice and dice the array.
        $page = array_slice($posts, $offset, $limit);

        // Build a multiquery for each post in the provided array.
        // If count($page) > 50, then it has to be broken up, since the maximum batch size is only 50.
        for ($i = 0; $i < count($page); $i++) {
            $queries[] = array(
                'method' => 'POST',
                'relative_url' => 'method/fql.multiquery?queries=' . json_encode(array(
                    'streamQuery' => PostFactory::STREAM_QUERY . 'WHERE post_id="' . $page[$i]->getId() . '"',
                    'imageQuery' => PostFactory::IMAGE_QUERY . 'WHERE object_id IN (SELECT attachment FROM #streamQuery)',
                    'userQuery' => PostFactory::USER_QUERY . 'WHERE uid IN (SELECT actor_id FROM #streamQuery)'
                ))
            );
        }

        $processed = 0;

        // Execute the batch queries in chunks of 50.
        while ($processed < count($page)) {
            $response = $this->graphApiClient->api('/', 'POST', array(
                'batch' => json_encode(array_slice($queries, $processed, 50)),
                'include_headers' => false
            ));

            // Sift through the results.
            for ($i = 0; $i < count($response); $i++) {
                $body = json_decode($response[$i]['body'], true);
                $result = array_merge($result, $this->processStreamQuery($body[0]['fql_result_set'], $body[1]['fql_result_set'], $body[2]['fql_result_set']));
            }

            $processed += count($response);
        }

        // If there are no posts to load, then insert an terminating post.
        if ($offset + $limit >= count($posts)) {
            $result[] = array('id' => 'terminator');
        }

        return $result;
    }

    /**
     * Take a response and construct \Post objects out of it.
     * @param array $stream
     * @param array $images
     * @param array $users
     * @return array
     */
    private function processStreamQuery($stream, $images, $users) {
        $posts = array();
        $imgFactory = new ImageObjectFactory($images);
        $lnkFactory = new LinkDataFactory();
        $usrFactory = new UserFactory($users);

        for ($i = 0; $i < count($stream); $i++) {
            $post = new Post();

            //post_id,actor_id,updated_time,message,attachment,comment_info,created_time
            $post->setId($stream[$i]['post_id']);
            $post->setUpdatedTime($stream[$i]['updated_time']);
            $post->setCreatedTime($stream[$i]['created_time']);
            $post->setCommentCount($stream[$i]['comment_info']['comment_count']);
            $post->setMessage($stream[$i]['message']);

            // Parse associated data from the query.
            $post->setImageObjects($imgFactory->getImageObjectsFromFQL($stream[$i], false));
            $post->setLinkData($lnkFactory->getLinkDataFromFQL($stream[$i]));
            $post->setActor($usrFactory->getUserFromFQLResultSet($stream[$i]));

            // Determine which kind of post this is.
            $post->setType($this->getPostType($post));

            // Replace any line breaks with <br/>
            if (strlen($post->getMessage()) > 0) {
                $post->setMessage(nl2br($post->getMessage()));
            }

            // Add to the posts array.
            $posts[] = $post;
        }

        return $posts;
    }

    /**
     * Determine the post's type.
     * @param Post $post
     * @return string
     */
    private function getPostType($post) {
        $postType = 'unknown';

        // The logic below should catch everything. If it does, then we have for some reason picked up a post with no visible content.
        if (count($post->getImageObjects()) > 0) {
            $postType = 'image';       // Image Post
        } else if (strlen($post->getMessage()) > 0) {
            $postType = 'text';        // Assume text post, but this might change to link.
        }

        if (strlen($post->getMessage()) == 0 && !is_null($post->getLinkData())) {
            $postType = 'link';        // Link post.
        }

        if (strlen($post->getMessage()) > 0 && !is_null($post->getLinkData())) {
            $postType = 'textlink';    // Link + Text post.
        }

        return $postType;
    }

    /**
     * Post a comment.
     * @param type $postId
     * @param type $comment
     * @return Comment
     */
    public function postComment($postId, $comment) {
        // Post the comment and get the response
        $id = $this->graphApiClient->api('/' . $postId . '/comments', 'POST', array('message' => $comment));

        // Get the comment and associated user data...
        $queries = array(
            'commentQuery' => PostFactory::COMMENT_QUERY . 'WHERE id=' . $id['id'],
            'commentUserQuery' => PostFactory::USER_QUERY . 'WHERE uid IN (SELECT fromid FROM #commentQuery)'
        );

        // Query Facebook's servers for the necessary data.
        $response = $this->graphApiClient->api(array(
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

        // Create an entity object.
        $comment = new Comment();
        $comment->setId($newComment['id']);
        $comment->setMessage($newComment['text']);
        $comment->setCreatedTime($newComment['time']);

        $comment->setActor((new UserFactory())->createUser($newComment['user']));

        // For each comment, look for associated image attachment.
        //$comment->setImageObjects($imgFactory->getImageObjectsFromFQLComment($newComment, false));
        $comment->setImageObjects(array());

        return $comment;
    }

    /**
     * Execute a batch request against the selected group's feed.
     * @param int $windowSize
     * @param int $windowStart
     * @param int $batchSize
     * @param int $iterations
     * @return array
     */
    private function getFeedData($windowSize, $windowStart, $batchSize, $iterations = 1) {
        $windowEnd = $windowStart - $windowSize;

        $stream = array();
        $users = array();
        $posts = array();

        // Pull the feed for stream data.
        for ($i = 0; $i < $iterations; $i++) {
            $queries = array();

            for ($j = 0; $j < $batchSize; $j++) {
                $query = array(
                    'streamQuery' => PostFactory::STREAM_QUERY . 'WHERE source_id=' . $this->gid . ' AND updated_time <= ' . $windowStart . ' AND updated_time >= ' . $windowEnd . ' LIMIT 5000',
                    'userQuery' => PostFactory::USER_QUERY . 'WHERE uid IN (SELECT actor_id FROM #streamQuery)'
                );

                $windowStart -= $windowSize;
                $windowEnd -= $windowSize;

                $queries[] = array(
                    'method' => 'POST',
                    'relative_url' => 'method/fql.multiquery?queries=' . json_encode($query)
                );
            }

            // Execute a batch query.
            $response = $this->graphApiClient->api('/', 'POST', array(
                'batch' => json_encode($queries),
                'include_headers' => false
            ));

            // Parse the response.
            for ($k = 0; $k < count($response); $k++) {
                $body = json_decode($response[$k]['body'], true);

                $stream = array_merge($stream, $body[0]['fql_result_set']);
                $users = array_merge($users, $body[1]['fql_result_set']);
            }
        }

        // Create the user factory.
        $usrFactory = new UserFactory($users);

        // Clean up the response a little bit for our own purposes.
        for ($i = 0; $i < count($stream); $i++) {
            // Create a new post object and add it to the posts array.
            $post = new Post();

            // post_id,message,actor_id,like_info,comment_info FROM stream
            $post->setId($stream[$i]['post_id']);
            $post->setMessage($stream[$i]['message']);
            $post->setCommentCount($stream[$i]['comment_info']['comment_count']);
            $post->setUserLikes((int) $stream[$i]['like_info']['user_likes']);
            $post->setActor($usrFactory->getUserFromFQLResultSet($stream[$i]));

            $posts[] = $post;
        }

        return $posts;
    }

    /**
     * Forcibly pause the thread in order for fetchStream to complete.
     */
    private function waitForFetchStreamCompletion() {
        while ($_SESSION['refreshing'] == true) {
            sleep(3);
        }
    }

}
