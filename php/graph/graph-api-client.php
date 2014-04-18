<?php

require_once 'facebook/facebook.php';

Facebook::$CURL_OPTS[CURLOPT_CAINFO] = getcwd() . '/fb_ca_chain_bundle.crt';

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

            die(json_encode(array(
                'code' => $ex->getCode(),
                'message' => $ex->getMessage()
            )));
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

}
