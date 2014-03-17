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


echo count($response) . "<br/>";

echo json_encode(getOptimalWindowSize($fbSession, $gid));

$constraints = array();

$constraints[] = array(
    'field' => 'actor_id',
    'operator' => '=',
    'value' => $uid
);

echo count(executeBatchQuery($fbSession, $gid, $constraints));

//echo count(getGroupPostsbyUid($fbSession, $gid, $uid));
