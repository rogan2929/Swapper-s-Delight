<?php

require_once ("facebook.php");

// Prod AppId and Secret
//$appId = '1401018793479333';
// Test AppId and Secret
$appId = '652991661414427';
$appSecret = 'b8447ce73d2dcfccde6e30931cfb0a90';

// Start up the Facebook object
$fbSession = new Facebook(array(
    'appId' => $appId,
    'secret' => $appSecret,
    'cookie' => true
        ));

// Test the access token.
try {
    $userProfile = $fbSession->api('/me','GET');
} 
catch(FacebookApiException $e) {
    // If the user is logged out, you can have a 
    // user ID even though the access token is invalid.
    // In this case, we'll get an exception, so we'll
    // just ask the user to login again here.
    
    $loginUrl = $fbSession->getLoginUrl();
    http_response_code(401);
    echo 'Sorry, your session has expired. Please refresh the page to log back in.';
}