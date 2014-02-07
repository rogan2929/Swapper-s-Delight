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

//// Save or retrieve the accessToken.
//if (!isset($_SESSION['accessToken'])) {
//    $_SESSION['accessToken'] = $fbSession->getAccessToken();
//}
//else {
//    $fbSession->setAccessToken($_SESSION['accessToken']);
//}
//
//// Test the access token.
//try {
//    // Test the connectivity waters...
//    $me = $fbSession->api('/me');
//} catch (FacebookApiException $e) {
//    //echo $e->getType();
//    //echo $e->getMessage();
//    
//    // An error occurred, so refresh the access token.
//    $_SESSION['accessToken'] = $fbSession->getAccessToken();
//}

//// Test the access token.
//try {
//    // Test the connectivity waters...
//    $me = $fbSession->api('/me');
//} catch (FacebookApiException $e) {
//    //echo $e->getType();
//    //echo $e->getMessage();
//    
//    // An error occurred, so refresh the access token.    
//}
//
//// Save or retrieve the accessToken.
//if (!isset($_SESSION['accessToken'])) {
//    $_SESSION['accessToken'] = $fbSession->getAccessToken();
//}
//else {
//    $fbSession->setAccessToken($_SESSION['accessToken']);
//}