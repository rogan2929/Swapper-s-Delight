<?php

require_once("facebook.php");

//var AppId = '1401018793479333'; // Prod
$appId = '652991661414427'; // Test

$appSecret = 'b8447ce73d2dcfccde6e30931cfb0a90';

// Startup the Facebook object
$fb = new Facebook(array(
    'appId'  => $appId,
    'secret' => $appSecret
));

?>