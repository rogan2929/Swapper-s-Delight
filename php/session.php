<?php

// Header required by IE.
header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');

require ("facebook.php");

// Prod AppId and Secret
//$appId = '1401018793479333';
//$appSecret = '603325411a953e21ccbc29d2c7d50e7e';
// Test AppId and Secret
$appId = '652991661414427';
$appSecret = 'b8447ce73d2dcfccde6e30931cfb0a90';

// Start up the Facebook object
$facebook = new Facebook(array(
    'appId' => $appId,
    'secret' => $appSecret,
    'cookie' => true
        ));

session_start();

// Save session variables.
$_SESSION['appId'] = $facebook->getAppId();
$_SESSION['appSecret'] = $facebook->getAppSecret();

// Test the access token.
try {
    $userProfile = $facebook->api('/me', 'GET');
} catch (FacebookApiException $e) {
    // If the user is logged out, you can have a 
    // user ID even though the access token is invalid.
    // In this case, we'll get an exception, so we'll
    // just ask the user to login again here.

    $loginUrl = $facebook->getLoginUrl();
    http_response_code(401);
}