<?php

// Header required by IE.
header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');

require_once ("facebook.php");

/* * *
 * Initialize a Facebook + PHP session.
 */

function initSession() {

    // Prod AppId and Secret
    //$appId = '1401018793479333';
    //$appSecret = '603325411a953e21ccbc29d2c7d50e7e';
    // Test AppId and Secret
    $appId = '652991661414427';
    $appSecret = 'b8447ce73d2dcfccde6e30931cfb0a90';

    // Create the Facebook object
    $facebook = new Facebook(array(
        'appId' => $appId,
        'secret' => $appSecret,
        'cookie' => true
    ));

    session_start();

    // Save session variables.
    $_SESSION['appId'] = $appId;
    $_SESSION['appSecret'] = $appSecret;
    $_SESSION['accessToken'] = $facebook->getAccessToken();

    // Ensure the session is valid.
    testFacebookSession($facebook);

    return $facebook;
}

/* * *
 * Retrieve the Facebook session object.
 */

function getFacebookSession() {    
    // Create a Facebook object.
    $facebook = new Facebook(array(
        'appId' => $_SESSION['appId'],
        'secret' => $_SESSION['appSecret']
    ));
    
    // Get the access token that was set earlier.
    $facebook->setAccessToken($_SESSION['accessToken']);
    
    echo $facebook->getAccessToken();

    // Ensure the session is valid.
    testFacebookSession($facebook);

    return $facebook;
}

/* * *
 * Test the Facebook session object.
 */

function testFacebookSession($facebook) {
    // Test the access token.
    try {
        $facebook->api('/me');
    } catch (FacebookApiException $e) {
        // If the user is logged out, you can have a 
        // user ID even though the access token is invalid.
        // In this case, we'll get an exception, so we'll
        // just ask the user to login again here.

        $loginUrl = $facebook->getLoginUrl();
        echo $e->getMessage();
        http_response_code(401);
    }
}
