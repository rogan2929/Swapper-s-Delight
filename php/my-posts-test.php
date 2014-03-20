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

$startTime = time();
$endTime = time() - 3600;

$query = 'SELECT post_id FROM stream WHERE source_id = ' . $gid . ' AND updated_time <= ' . $startTime . ' AND updated_time >= ' . $endTime . ' LIMIT 100';

$response = $fbSession->api(array(
    'method' => 'fql.query',
    'query' => $query
        ));

$windowData = getOptimalWindowData($fbSession, $gid);

$windowSize = $windowData['windowSize'];
$windowStart = time();
$windowEnd = $windowStart - $windowSize;

$posts = array();
$myPosts = array();

// Find owned posts.
for ($i = 0; $i < 50; $i++) {
    $request = '/' . $gid . '/feed?fields=id,from&since=' . $windowStart . '&until=' . $windowEnd . '&limit=5000&date_format=U';
    
    $response = $fbSession->api($request);
    
    echo json_encode($response);
    
    //$posts = array_merge($posts, $response);
    
    $windowStart = $windowEnd;
    $windowEnd -= $windowSize;
}

//for ($i = 0; $i < count($posts); $i++) {
//    $post = $posts[$i];
//    
//    if ($post['from']['id'] === $uid) {
//        $myPosts[] = $post['id'];
//    }
//}
//
//echo count($myPosts);

//
//$constraints = array();
//
//$constraints[] = array(
//    'field' => 'actor_id',
//    'operator' => '=',
//    'value' => $uid
//);
//
//$posts = executeBatchQuery($fbSession, $gid, $constraints);
//
//echo count($response) . "<br/>";
//echo count($posts);

//echo count(getGroupPostsbyUid($fbSession, $gid, $uid));
