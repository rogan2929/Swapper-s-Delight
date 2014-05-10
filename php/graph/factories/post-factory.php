<?php

require 'graph-object-factory.php';
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
class PostFactory extends GraphObjectFactory {

    private $gid;
    private $stream;

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
     * Override of GraphObjectFactory::likeObject
     * @param type $id
     * @return bool
     */
    public function likeObject($id) {
        return parent::likeObject($id);
    }

    /**
     * Override of GraphObjectFactory::unLikeObject
     * @param type $id
     * @return bool
     */
    public function unLikeObject($id) {
        return parent::unLikeObject($id);
    }

    /**
     * Set the currently loaded group's gid.
     * @param string $gid
     */
    public function setGid($gid) {
        $this->gid = $gid;
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
//        $queries = array(
//            'detailsQuery' => GraphObjectFactory::DETAILS_QUERY . 'WHERE post_id="' . $postId . '"',
//            'imageQuery' => GraphObjectFactory::IMAGE_QUERY . 'WHERE object_id IN (SELECT attachment FROM #detailsQuery)',
//            'userQuery' => GraphObjectFactory::USER_QUERY . 'WHERE uid IN (SELECT actor_id FROM #detailsQuery)',
//            'commentsQuery' => GraphObjectFactory::COMMENT_QUERY . 'WHERE post_id IN (SELECT post_id FROM #detailsQuery) ORDER BY time ASC',
//            'commentUserQuery' => GraphObjectFactory::USER_QUERY . 'WHERE uid IN (SELECT fromid FROM #commentsQuery)',
//            'commentImageQuery' => GraphObjectFactory::IMAGE_QUERY . 'WHERE object_id IN (SELECT attachment FROM #commentsQuery)'
//        );
//
//        // Run the query.
//        $response = $this->graphApiClient->api(array(
//            'method' => 'fql.multiquery',
//            'queries' => $queries
//        ));
//
//        // Check to ensure that post data was actually returned.
//        // This is done by checking for post_id in the fql_result_set.
//        if (!isset($response[0]['fql_result_set'][0]['post_id'])) {
//            $e = new Exception('Sorry, but this post couldn\'t be loaded. It may have been deleted.');
//            echo $e->getMessage();
//            throw $e;
//        }
//
//        try {
//            $raw = $response[0]['fql_result_set'][0];
//
//            // Collect the returned data.
//            $post = new Post();
//
//            // post_id,message,actor_id,permalink,like_info,share_info,comment_info,tagged_ids,attachment,created_time
//            $post->setId($raw['post_id']);
//            $post->setMessage($raw['message']);
//            $post->setPermalink($raw['permalink']);
//            $post->setUserLikes($raw['like_info']['user_likes']);
//            $post->setUpdatedTime($raw['updated_time']);
//            $post->setCreatedTime($raw['created_time']);
//            $post->setActor((new UserFactory())->createUser($response[3]['fql_result_set'][0]));
//        } catch (Exception $ex) {
//            echo 'Sorry, but this post couldn\'t be loaded. It may have been deleted.';
//            http_response_code(500);
//        }
//
//        if (strlen($post->getMessage()) > 0) {
//            // Replace new line characters with <br/>
//            $post->setMessage(nl2br($post->getMessage()));
//        }
//
//        // Extract image data for the post.
//        $imgFactory = new ImageObjectFactory($response[2]['fql_result_set']);
//        $post->setImageObjects($imgFactory->getImageObjectsFromFQL($raw, false));
//
//        // Extract link data.
//        $post->setLinkData((new LinkDataFactory())->getLinkDataFromFQL($raw));
//
//        // Determine type of post.
//        $post->setType($this->getPostType($post));
//
//        // Parse comment data and set it.
//        $post->setComments((new CommentFactory())->getCommentsFromFQL($response[1]['fql_result_set'], $response[5]['fql_result_set'], $response[4]['fql_result_set']));
//
//        return $post;
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

        return count($this->stream);
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
    public function fetchStream($prefetchOnly) {
        if ($prefetchOnly) {
            // Only retrieve a small subset of the full stream, in order for data to be displayed more quickly to the user.
            $windowStart = time();
            $windowSize = $this->getOptimalWindowSize();

            // To speed up the initial load, only grab a maximum of 200 posts during deep query.
            $postLimit = 200;

            // Perform a two step query of varying window sizes, and then merge the result.
            $stream = $this->getStreamData($windowSize, $windowStart, 14, 1, $postLimit);

            // Only continue with another batch if $postLimit has not been reached.
            if (count($stream) <= $postLimit) {
                $windowStart = $windowStart - ($windowSize * 14 * 1);
                $stream = array_merge($stream, $this->getStreamData(3600 * 24 * 30, $windowStart, 1, 1));
            }

            $this->stream = $stream;
        } else {
            $windowStart = time();
            $windowSize = $this->getOptimalWindowSize();

            $stream = $this->getStreamData($windowSize, $windowStart, 50, 1);
            $windowStart = $windowStart - ($windowSize * 50 * 1);
            $stream = array_merge($stream, $this->getStreamData($windowSize * 2, $windowStart, 13, 1));
            $windowStart = $windowStart - ($windowSize * 2 * 13 * 1);
            $stream = array_merge($stream, $this->getStreamData($windowSize * 3, $windowStart, 11, 1));
            $windowStart = $windowStart - ($windowSize * 3 * 11 * 1);
            $stream = array_merge($stream, $this->getStreamData(3600 * 24 * 30, $windowStart, 1, 1));

            $this->stream = $stream;
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
        //$count = count($this->graphApiClient->executeRequest('GET', '/' . $this->gid . '/feed?fields=id&since=' . $endTime . '&until=' . $startTime . ' LIMIT 100'));
        $count = count($this->graphApiClient->executeRequest('GET', '/' . $this->gid . '/feed', array(
                    'since' => $endTime,
                    'until' => $startTime,
                    'limit' => 100
        )));

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
        if (!isset($limit) || $limit > 50) {
            $limit = 50;        // Max batch size.
        }

        // What needs to be captured from the Graph API?
        // Fully flushed image data. (Multiple images, small / large srcs.) This may have to be retrieved using FQL.
        // User profile picture, url.
        $pagedPosts = array_slice($posts, $offset, $limit);

        // Get Post User Data
        $users = $this->getPostUserData($pagedPosts);
        $images = $this->getPostImageData($pagedPosts);

        for ($i = 0; $i < count($pagedPosts); $i++) {
            $post = $pagedPosts[$i];

            // Set actor.
            $post->setActor($users[$i]);
            
            $image = $post->getFirstImage();
            
            // Set tile image by looking for its associated post.
            for ($j = 0; $j < count($images); $j++) {
                if ($image->getId() == $images[$j]->getId()) {
                    $post->setFirstImage($images[$j]);
                    break;
                }
            }           
            
            // Get post type.
            $post->setType($this->getPostType($post));
        }

        // If there are no posts to load, then insert an terminating post.
        if ($offset + $limit >= count($posts)) {
            $pagedPosts[] = array('id' => 'terminator');
        }

        return $pagedPosts;
    }

    private function getPostImageData($posts) {
        $requests = array();
        $images = array();

        for ($i = 0; $i < count($posts); $i++) {
            $post = $posts[$i];
            
            // Try to see if this post has a primary image.
            $image = $post->getFirstImage();

            if (!is_null($image)) {
                $requests[] = array(
                    'method' => 'GET',
                    'relative_url' => '/' . $image->getId() . '?fields=id,source'
                );
            }
        }
        
        // Execute the batch queries.
        $response = $this->graphApiClient->executeRequest('POST', '/', array(
            'batch' => json_encode($requests),
            'include_headers' => false
        ));
        
        for ($j = 0; $j < count($response); $j++) {
            $images[] = ImageObjectFactory::getFirstImageFromGraphResponse(json_decode($response[$j]->body));
        }
        
        return $images;
    }

    /**
     * Get user data for the array of posts.
     * @param type $posts
     * @return type
     */
    private function getPostUserData($posts) {
        $requests = array();
        $users = array();

        // Generate requests for additional data.
        for ($i = 0; $i < count($posts); $i++) {
            $actor = $posts[$i]->getActor();

            $requests[] = array(
                'method' => 'GET',
                'relative_url' => '/' . $actor->getUid() . '?fields=first_name,last_name,link,picture'
            );
        }

        // Execute the batch queries
        $response = $this->graphApiClient->executeRequest('POST', '/', array(
            'batch' => json_encode($requests),
            'include_headers' => false
        ));

        // Parse the user request responses.
        for ($j = 0; $j < count($response); $j++) {
            $users[] = UserFactory::getUserFromGraphResponse(json_decode($response[$j]->body));
        }

        return $users;
    }

    /**
     * Determine the post's type.
     * @param Post $post
     * @return string
     */
    private function getPostType($post) {
        $postType = 'unknown';

        // The logic below should catch everything. If it does, then we have for some reason picked up a post with no visible content.
        if (!is_null($post->getFirstImage())) {
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
     * Manages batch requests targeted toward the selected group's feed.
     * @param int $windowSize
     * @param int $windowStart
     * @param int $batchSize
     * @param int $iterations
     * @return array
     */
    private function getStreamData($windowSize, $windowStart, $batchSize, $iterations = 1, $postLimit = null) {
        $windowEnd = $windowStart - $windowSize;

        $stream = array();
        $posts = array();

        // Pull the feed for stream data.
        for ($i = 0; $i < $iterations; $i++) {
            // If a post limit has been set and has been reached, then immediately return what we have.
            if (!is_null($postLimit) && count($posts) > $postLimit) {
                return $posts;
            }

            $requests = array();

            // Create the batch query.
            for ($j = 0; $j < $batchSize; $j++) {
                $requests[] = array(
                    'method' => 'GET',
                    'relative_url' => '/' . $this->gid . '/feed?fields=id,from,message,created_time,updated_time,picture,object_id,actions,link,comments.limit(1).summary(true)&since=' . $windowEnd . '&until=' . $windowStart . '&limit=5000&date_format=U'
                );

                $windowStart -= $windowSize;
                $windowEnd -= $windowSize;
            }

            // Execute the batch query.
            $response = $this->graphApiClient->executeRequest('POST', '/', array(
                'batch' => json_encode($requests),
                'include_headers' => false
            ));

            // Gather up post data.
            for ($k = 0; $k < count($response); $k++) {
                $body = json_decode($response[$k]->body);
                $stream = array_merge($stream, $body->data);
            }
        }

        // Parse the post data.
        for ($i = 0; $i < count($stream); $i++) {
            $post = new Post();

            // Actor
            $post->setActor(UserFactory::getUserFromGraphResponse($stream[$i]->from));

            // ID
            $post->setId($stream[$i]->id);

            // Message
            if ($stream[$i]->message) {
                $post->setMessage($stream[$i]->message);
            }

            // Comment count.
            if (isset($stream[$i]->comments)) {
                $post->setCommentCount($stream[$i]->comments->summary->total_count);
            } else {
                $post->setCommentCount(0);
            }

            // Permalink
            $post->setPermalink($stream[$i]->actions[0]->link);

            // Creation / updated time.
            $post->setCreatedTime($stream[$i]->created_time);
            $post->setUpdatedTime($stream[$i]->updated_time);

            // Grab any basic image data first attached image, if there is one. Src lookup will be done later.
            if (isset($stream[$i]->object_id)) {
                $image = new Image();
                $image->setId($stream[$i]->object_id);
                $post->setFirstImage($image);
            }

            $posts[] = $post;
        }

        return $posts;
    }

}
