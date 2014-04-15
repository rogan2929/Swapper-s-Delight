<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'dal.php';

// Offloaded PostFactory functionality.

$dal = new PostFactory();
$dal->setGid(INPUT_GET, 'gid');

$windowSize = $dal->getOptimalWindowSize();

$stream = $dal->getFeedData($windowSize, $windowStart, 50, 1);
$windowStart = $windowStart - ($windowSize * 50 * 1);
$stream = array_merge($stream, $dal->getFeedData($windowSize * 2, $windowStart, 13, 1));
$windowStart = $windowStart - ($windowSize * 2 * 13 * 1);
$stream = array_merge($stream, $dal->getFeedData($windowSize * 3, $windowStart, 11, 1));
$windowStart = $windowStart - ($windowSize * 3 * 11 * 1);
$stream = array_merge($stream, $dal->getFeedData(3600 * 24 * 30, $windowStart, 1, 1));

echo json_encode($stream);