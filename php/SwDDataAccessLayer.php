<?php

require 'facebook.php';

class SwDDataAccessLayer {

    /** Constants **/
    
    // Prod
    //const APP_ID = '1401018793479333';
    //const APP_SECRET = '603325411a953e21ccbc29d2c7d50e7e';
    
    // Test
    const APP_ID = '652991661414427';
    const APP_SECRET = 'b8447ce73d2dcfccde6e30931cfb0a90';

    // Class members
    private $facebook;
    private $gid;
    private $stream;
    
    private $sqlConnectionInfo;

    /* 
     * Swd constructor.
     * 
     */

    function __construct($gid) {
        $this->$facebook = new Facebook(array(
            'appId' => self::APP_ID,
            'secret' => self::APP_SECRET,
            'cookie' => true
        ));

        // Look up an existing access token, if there is one.
        if (!isset($_SESSION['accessToken'])) {
            $_SESSION['accessToken'] = $this->facebook->getAccessToken();
        } else {
            $this->facebook->setAccessToken($_SESSION['accessToken']);
        }
        
        $this->gid = $gid;
        
        // See if a new stream needs to be fetched.
        if (!isset($_SESSION['gid']) || $_SESSION['gid'] !== $gid || !isset($_SESSION['stream'])) {
            // Fetch the new stream.
            $this->fetchStream();
        }
        
        $this->sqlConnectionInfo = array("UID" => "rogan2929@lreuagtc6u", "pwd" => "Revelation19:11", "Database" => "swapperAGiJRLgvy", "LoginTimeout" => 30, "Encrypt" => 1);
    }

    /** Methods **/
    
    // Group management functions.

    public function getGroupInfo() {
        
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
    
    /** Private Methods **/
    
    private function fetchStream() {
        // Save $gid to session for later use.
        $_SESSION['gid'] = $gid;
    }
}