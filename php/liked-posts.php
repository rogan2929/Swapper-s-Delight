<?php

require_once 'dal.php';

$offset = $_GET['offset'];
$limit = $_GET['limit'];

$dal = new DataAccessLayer();

echo json_encode($dal->getLikedPosts($offset, $limit));