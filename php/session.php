<?php

require_once ("facebook.php");

// Prod AppId and Secret
//$appId = '1401018793479333';

// Test AppId and Secret
$appId = '652991661414427';
$appSecret = 'b8447ce73d2dcfccde6e30931cfb0a90';

$cookie = preg_replace("/^\"|\"$/i", "", $_COOKIE['fbs_' . FB_APP_ID]);
parse_str($cookie, $data);

// Startup the Facebook object
$fbSession = new Facebook( array(
	'appId' => $appId,
	'secret' => $appSecret
));

$fbSession->setAccessToken($data['access_token']);

?>