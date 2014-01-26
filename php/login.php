<?php

require_once("facebook.php");

// Read the cookie created by the JS API
//$cookie = preg_replace("/^\"|\"$/i", "", $_COOKIE['fbs_' . FB_APP_ID]);
//parse_str($cookie, $data);

//var AppId = '1401018793479333'; // Prod
$appId = '652991661414427'; // Test

$appSecret = 'b8447ce73d2dcfccde6e30931cfb0a90';

// Startup the Facebook object
$fb = new Facebook(array(
    'appId'  => $appId,
    'secret' => $appSecret
));

// Say we are using the token from the JS
//$fb->setAccessToken($data['access_token']);

// It should work now
var_dump($fb->getUser());

?>