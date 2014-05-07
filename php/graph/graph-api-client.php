<?php

//require_once 'facebook/facebook.php';
//Facebook::$CURL_OPTS[CURLOPT_CAINFO] = getcwd() . '/fb_ca_chain_bundle.crt';

require $_SERVER['DOCUMENT_ROOT'] . '\vendor\autoload.php';


// Facebook PHP 4.0 SDK
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookRequestException;
use Facebook\FacebookJavaScriptLoginHelper;

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

    //private $facebook;
    private $session;
    private $appSecretProof;

    function __construct() {

        // Try to set the default application.
        FacebookSession::setDefaultApplication(static::APP_ID, static::APP_SECRET);

        //$helper = new FacebookJavaScriptLoginHelper();
        $helper = new FacebookCanvasLoginHelper();

        try {
            $this->session = $helper->getSession();
        } catch (FacebookRequestException $ex) {
            error_log($ex->getMessage());
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
        }
        
        // Generate appsecret_proof
        if ($session) {
            $this->appSecretProof = hash_hmac('sha256', $this->session->getToken(), static::APP_SECRET);
        }
    }
    
    /**
     * Create and execute a FacebookRequest object.
     * @param string $method
     * @param type $parameters
     * @return object
     */
    public function executeRequest($method, $path, $parameters = null) {
        try {
            if (!is_null($parameters) && is_array($parameters)) {
                $parameters[appsecret_proof] = $this->appSecretProof;
            }
            else {
                $parameters = array(
                    'appsecret_proof' => $this->appSecretProof
                );
            }
            
            $response = (new FacebookRequest($this->session, $method, $path, $parameters));
            return $response->execute()->getResponse();
        } catch (FacebookRequestException  $ex) {
            // Set a 400 response code and then exit with the FB exception message.
            http_response_code(400);

            error_log($ex->getCode() . ': ' . $ex->getMessage());

            die(json_encode(array(
                'code' => $ex->getCode(),
                'message' => $ex->getMessage()
            )));
        }
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
            if (isset($args[1]) && is_array($args[1]) && empty($args[2])) {
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

            error_log($ex->getCode() . ': ' . $ex->getMessage());

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
        //return $this->api('/me')['id'];
        return $this->executeRequest('GET', '/me');
    }

    /**
     * 
     * @param string $query
     * @param string $type
     */
    public function search($query, $type) {
        return $this->executeRequest('GET', '/search?q=' . $query . '?type=' . $type);
    }

}
