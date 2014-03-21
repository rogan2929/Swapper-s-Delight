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
    private $facebookState;
    private $lastFacebookApiException;
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

        $this->appSecretProof = hash_hmac('sha256', $this->facebook->getAccessToken(), $this->APP_SECRET);

        // Test the facebook object that was created.
        $this->api('/me', 'GET');

        $this->sqlConnectionInfo = array("UID" => "rogan2929@lreuagtc6u", "pwd" => "Revelation19:11", "Database" => "swapperAGiJRLgvy", "LoginTimeout" => 30, "Encrypt" => 1);
    }

    /** Getters and Setters * */

    /**
     * Get the most recent FacebookApiException.
     * @return type
     */
    public function getFacebookErrorMessage() {
        return $this->$lastFacebookApiException->getMessage();
    }

    /**
     * Get the Facebook state.
     * @return type
     */
    public function getFacebookState() {
        return $this->facebookState;
    }

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

    public function getNewPosts() {
        
    }

    public function getPostDetails($postId) {
        
    }

    public function refreshStream() {
        
    }

    public function searchPosts($search) {
        
    }

    /** Private Methods * */
    private function api($path, $method, $params = array()) {
        
        // Insert appsecret_proof into each API call.
        $params['appsecret_proof'] = $this->appSecretProof;
        
        echo var_dump($params);

        try {
            // Call the facebook->api function.
            $this->facebook->api($path, $method, $params);
            //call_user_func_array(array($this->facebook, 'api'), array($path));
        } catch (FacebookApiException $ex) {
            $this->lastFacebookApiException = $ex;

            echo json_encode($ex->getResult());
//            echo $ex->getMessage();
            // Selectively decide how to handle the error, based on returned code.
            // https://developers.facebook.com/docs/graph-api/using-graph-api/#errors
            switch ($ex->getCode()) {
                case 'OAuthException':              // Invalid Session
                    //http_response_code(401);
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
        $_SESSION['gid'] = $gid;
    }

}