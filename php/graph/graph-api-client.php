<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'facebook/facebook.php';

if (!session_id()) {
    session_start();
}

/**
 * A client to the Facebook Graph API.
 */
class GraphApiClient {

    /** Constants * */
    // Prod
    //const APP_ID = '1401018793479333';
    //const APP_SECRET = '603325411a953e21ccbc29d2c7d50e7e';
    // Test
    const APP_ID = '652991661414427';
    const APP_SECRET = 'b8447ce73d2dcfccde6e30931cfb0a90';

    private $facebook;
    private $appSecretProof;

    function __construct() {

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

        // Set the AppSecretProof, which is required for FB api calls.
        $this->appSecretProof = hash_hmac('sha256', $this->facebook->getAccessToken(), self::APP_SECRET);

        // Test the facebook object that was created successfully.
        $this->api('/me', 'GET');
    }

    /**
     * A wrapper for $facebook->api. Error handling is built in.
     * @return object
     */
    public function api(/* polymorphic */) {
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
            // https://developers.facebook.com/docs/graph-api/using-graph-api/#errors
            // Set a 400 response code and then exit with the FB exception message.
            http_response_code(400);

            die($ex->getMessage());
        }
    }

    /**
     * Returns the UID of the currently logged in user.
     * @return string
     */
    public function getMe() {
        return $this->api('/me')['id'];
    }

    /**
     * Delete a Facebook Object.
     * @param type $id
     */
    public function deleteObject($id) {
        $this->api('/' . $id, 'DELETE');
    }

    /**
     * Like a post.
     * @param type $postId
     * @param type $userLikes
     * @return type
     */
    public function likePost($postId, $userLikes) {
        if ($userLikes == true) {
            // Like the post.
            $this->api('/' . $postId . '/likes', 'POST', array('user_likes' => true));
        } else {
            // Delete the post's like.
            $this->api('/' . $postId . '/likes', 'DELETE');
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
     * Create a new post.
     */
    public function newPost() {
        
    }

    /**
     * Post a comment on a post.
     * @param type $postId
     * @param type $comment
     * @return type
     */
    public function postComment($postId, $comment) {
        // Post the comment and get the response
        $id = $this->api('/' . $postId . '/comments', 'POST', array('message' => $comment));

        // Get the comment and associated user data...
        $queries = array(
            'commentQuery' => 'SELECT fromid,text,text_tags,attachment,time,id FROM comment WHERE id=' . $id['id'],
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

}