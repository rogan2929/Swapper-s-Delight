<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'session.php';

$gid = $_GET['gid'];
$uid = $fbSession->getUser();

echo $uid;

$response = $fbSession->api('/' . $gid . '/feed?fields=id,from&limit=5000');



echo json_encode($response);