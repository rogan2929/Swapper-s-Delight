<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'session.php';

$gid = $_GET['gid'];
$uid = $_GET['uid'];

/***
 * Using the Graph API, query a group's feed for post ids belonging to the given user.
 */
function getGroupPostIdsByUid($fbSession, $gid, $uid, $windowSize, $until) {
    $posts = array();
    $feedQuery = '/' . $gid . '/feed?fields=id,from,updated_time&date_format=U&limit=500';
    $next = $feedQuery;
    $oldest = time();

    while ($oldest > $until) {
        echo $next . "<br/>";
        
        $response = $fbSession->api($next);

        for ($i = 0; $i < count($response['data']); $i++) {
            if ($response['data'][$i]['from']['id'] == $uid) {
                $postId = $response['data'][$i]['id'];
                
                if (!in_array($postId, $posts)) {
                    $posts[] = $postId;
                }
            }
        }
        
        $oldestPost = $response['data'][count($response['data']) - 1];
        
        // Get the next page and trim off the "https://graph.facebook.com"
        //$next = substr($response['paging']['next'], strlen("https://graph.facebook.com"), strlen($response['paging']['next']) - strlen("https://graph.facebook.com"));
        
        $oldest -= $windowSize;
        
        $next = $feedQuery . '&until=' . $oldest . '&__paging_token=' . $oldestPost['id'];
    }

    return $posts;
}

// Look up to 15 days back.
$windowSize = 3600 * 2;
$windowCount = 30;
$until = time() - $windowSize * $windowCount;

$posts = getGroupPostIdsByUid($fbSession, $gid, $uid, $windowSize, $until);

echo count($posts);
