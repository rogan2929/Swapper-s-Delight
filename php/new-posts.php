<?php

require_once 'dal.php';

$gid = $_GET['gid'];
$refresh = $_GET['refresh'];
$offset = $_GET['offset'];
$limit = $_GET['limit'];

$dat = new DataAccessLayer();
$dat->setGid($gid);

// Call the appropriate method in the newly instantiated DAL object.
echo json_encode($dat->getNewPosts($refresh, $offset, $limit));