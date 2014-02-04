<?php

require_once ("facebook.php");

// Prod AppId and Secret
//$appId = '1401018793479333';
// Test AppId and Secret
$appId = '652991661414427';
$appSecret = 'b8447ce73d2dcfccde6e30931cfb0a90';

if (!isset($_REQUEST['signed_request'])) {
    echo 'BLAH';
    
    // Start up the Facebook object
    $fbSession = new Facebook(array(
        'appId' => $appId,
        'secret' => $appSecret,
        'cookie' => true
    ));
}

try {
    // Test the connectivity waters...
    $me = $fbSession->api('/me');
} catch (FacebookApiException $e) {
//    $loginUrl = $fbSession->getLoginUrl(array('scope' => 'user_groups,user_likes'));
//    echo '<div class="login-div">Please <a href="' . $loginUrl . '">login.</a></div>';
    //echo $e->getType();
    //echo $e->getMessage();
}

/*
  if ($fbSession->getUser()) {

  // We have a user ID, so probably a logged in user.
  // If not, we'll get an exception, which we handle below.
  try {
  // Test the connectivity waters...
  $me = $fbSession->api('/me');
  } catch(FacebookApiException $e) {
  // If the user is logged out, you can have a
  // user ID even though the access token is invalid.
  // In this case, we'll get an exception, so we'll
  // just ask the user to login again here.
  $loginUrl = $fbSession->getLoginUrl(array('scope' => 'user_groups,user_likes'));
  echo '<div class="login-div">Please <a href="' . $loginUrl . '">login.</a></div>';
  //header('Location: ' . $loginUrl);
  error_log($e->getType());
  error_log($e->getMessage());
  }
  } else {

  // No user, print a link for the user to login
  // We'll use the current URL as the redirect_uri, so we don't
  // need to specify it here.
  $loginUrl = $fbSession->getLoginUrl(array('scope' => 'user_groups,user_likes'));
  echo '<div class="login-div">Please <a href="' . $loginUrl . '">login.</a></div>';
  //header('Location: ' . $loginUrl);

  } */