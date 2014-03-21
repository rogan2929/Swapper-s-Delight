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
     * Set the currently loaded group's gid. Automatically fetches the stream when changed.
     * @param type $gid
     */
    public function setGid($gid) {
        // See if a new stream needs to be fetched.
        if (!isset($_SESSION['gid']) || $_SESSION['gid'] !== $gid || !isset($_SESSION['stream'])) {
            $this->gid = $gid;

            // Fetch the new stream.
            $this->fetchStream();
        }
        
        $this->stream = $_SESSION['stream'];
        
        echo json_encode($this->stream);
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
        
    }

    public function likePost($postId) {
        
    }

    public function newPost() {
        
    }

    public function postComment($postId, $comment) {
        
    }

    // Stream operation functions.

    public function getLikedPosts() {
        
    }

    public function getMyPosts() {
        
    }

    public function getNewPosts($refresh, $offset) {
        
    }

    public function getPostDetails($postId) {
        
    }

    public function refreshStream() {
        
    }

    public function searchPosts($search) {
        
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
            echo json_encode($ex->getResult());

            // Selectively decide how to handle the error, based on returned code.
            // https://developers.facebook.com/docs/graph-api/using-graph-api/#errors
            switch ($ex->getCode()) {
                case 'OAuthException':              // Invalid Session
                    http_response_code(401);
                    break;
                case '4':                           // Too many API calls.
                    break;
                case '17':                          // Too many user API calls.
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
    }

    /**
     * Determine the optimal window size to use in batch queries.
     */
    private function getOptimalWindowData() {
        $startTime = time();
        $endTime = time() - 3600;

        $query = 'SELECT post_id FROM stream WHERE source_id = ' . $this->gid . ' AND updated_time <= ' . $startTime . ' AND updated_time >= ' . $endTime . ' LIMIT 100';

        $response = $this->api(array(
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

    /*
     * Query the FQL stream table for some basic data that will be cached.
     */

    private function queryStream() {
        $windowData = $this->getOptimalWindowData();

        $windowSize = $windowData['windowSize'];
        $windowStart = time();
        $windowEnd = $windowStart - $windowSize;

        $stream = array();

        for ($i = 0; $i < $windowData['batchCount']; $i++) {
            $queries = array();

            // Construct the FB batch request
            for ($j = 0; $j < 50; $j++) {
                $query = 'SELECT post_id,actor_id,message,like_info FROM stream WHERE source_id=' . $this->gid . ' AND updated_time <= ' . $windowStart . ' AND updated_time >= ' . $windowEnd . ' LIMIT 5000';

                $queries[] = array(
                    'method' => 'GET',
                    'relative_url' => 'method/fql.query?query=' . urlencode($query)
                );

                $windowStart -= $windowSize;
                $windowEnd -= $windowSize;
            }

            // Call the batch query.
            $response = $this->api('/', 'POST', array(
                'batch' => json_encode($queries),
                'include_headers' => false
            ));

            for ($j = 0; $j < count($response); $j++) {
                $stream = array_merge($stream, json_decode($response[$j]['body'], true));
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
