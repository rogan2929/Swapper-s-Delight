<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'session.php';
require_once 'include.php';

$gid = $_GET['gid'];
$uid = $_GET['uid'];

/* * *
 * Using the Graph API, query a group's feed for post ids belonging to the given user.
 */
//function getGroupPostIdsByUid($fbSession, $gid, $uid, $windowSize, $until) {
//    $posts = array();
//    $feedQuery = '/' . $gid . '/feed?fields=id,from,updated_time&date_format=U&limit=5000';
//    $next = $feedQuery;
//    $oldest = time();
//
//    while ($oldest > $until) {
//        echo $next . "<br/>";
//        
//        $response = $fbSession->api($next);
//
//        for ($i = 0; $i < count($response['data']); $i++) {
//            if ($response['data'][$i]['from']['id'] == $uid) {
//                $postId = $response['data'][$i]['id'];
//                
//                if (!in_array($postId, $posts)) {
//                    echo $postId . "<br/>";
//                    $posts[] = $postId;
//                }
//            }
//        }
//        
//        $oldestPost = $response['data'][count($response['data']) - 1];
//        
//        // Get the next page and trim off the "https://graph.facebook.com"
//        //$next = substr($response['paging']['next'], strlen("https://graph.facebook.com"), strlen($response['paging']['next']) - strlen("https://graph.facebook.com"));
//        
//        $oldest -= $windowSize;
//        
//        $next = $feedQuery . '&until=' . $oldest . '&__paging_token=' . $oldestPost['id'];
//    }
//
//    return $posts;
//}

$startTime = time();
$endTime = time() - 3600;

$query = 'SELECT post_id FROM stream WHERE source_id = ' . $gid . ' AND updated_time <= ' . $startTime . ' AND updated_time >= ' . $endTime . ' LIMIT 100';

$response = $fbSession->api(array(
    'method' => 'fql.query',
    'query' => $query
        ));


echo count($response) . "<br/>";

echo getOptimalWindowSize($fbSession, $gid) / 3600 . "<br/>";

$constraints = array();

$constraints[] = array(
    'field' => 'actor_id',
    'operator' => '=',
    'value' => $uid
);

echo count(executeBatchQuery($fbSession, $gid, $constraints));

//echo count(getGroupPostsbyUid($fbSession, $gid, $uid));
