<?php

require_once 'dal.php';

$gid = $_GET['gid'];
$refresh = $_GET['refresh'];
$offset = $_GET['offset'];
$limit = $_GET['limit'];

$dal = new DataAccessLayer();
$dal->setGid($gid);

// Call the appropriate method in the newly instantiated DAL object.
echo json_encode($dal->getNewPosts($refresh, $offset, $limit));