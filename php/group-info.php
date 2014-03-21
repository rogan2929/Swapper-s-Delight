<?php

require 'dal.php';

$dal = new DataAccessLayer();

//require 'facebook.php';
//
//$APP_ID = '652991661414427';
//$APP_SECRET = 'b8447ce73d2dcfccde6e30931cfb0a90';
//
////$dal = new DataAccessLayer();
//
//$facebook = new Facebook(array(
//            'appId' => $APP_ID,
//            'secret' => $APP_SECRET,
//            'cookie' => true
//        ));
//
//echo var_dump($facebook);
//
//echo $facebook->getAccessToken();

// Create a DAL object and retrieve the group info.
//echo json_encode((new DataAccessLayer())->getGroupInfo());