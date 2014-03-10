<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'session.php';

$gid = $_GET['gid'];
$uid = $_GET['uid'];

$posts = array();

$response = $fbSession->api('/' . $gid . '/feed?fields=id,from&limit=5000');

for ($i = 0; $i < count($response['data']); $i++) {
    if ($response['data'][$i]['from']['id'] == $uid) {
        $posts[] = $response['data'][$i]['id'];
    }
}

echo json_encode($posts);