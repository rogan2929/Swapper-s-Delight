<?php

header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');

//require 'dal.php';
require 'facebook.php';

$APP_ID = '652991661414427';
$APP_SECRET = 'b8447ce73d2dcfccde6e30931cfb0a90';

//$dal = new DataAccessLayer();

$facebook = new Facebook(array(
            'appId' => $APP_ID,
            'secret' => $APP_SECRET,
            'cookie' => true
        ));

echo var_dump($facebook);

// Create a DAL object and retrieve the group info.
//echo json_encode((new DataAccessLayer())->getGroupInfo());