<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'session.php';

$gid = $_GET['gid'];
$uid = $_GET['uid'];

function getMyPosts($fbSession, $gid, $uid, $until) {
    $posts = array();
    $oldest = time();

//    while ($oldest > $until) {
        $response = $fbSession->api('/' . $gid . '/feed?fields=id,from,updated_time&limit=5000');

        for ($i = 0; $i < count($response['data']); $i++) {
            if ($response['data'][$i]['from']['id'] == $uid) {
                $posts[] = $response['data'][$i]['id'];
            }
        }
        
        $oldest = $response['data'][count($response['data']) - 1]['updated_time'];
        
        echo json_encode($response['paging']);
        echo $oldest;
//    }

    return $posts;
}

// Look up to 15 days back.
$until = time() - 3600 * 24 * 15;

$posts = getMyPosts($fbSession, $gid, $uid, $until);

//echo json_encode($posts);
