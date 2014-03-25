<?php

require_once 'dal.php';

$search = $_GET['search'];
$offset = $_GET['offset'];
$limit = $_GET['limit'];

$dal = new DataAccessLayer();

echo json_encode($dal->searchPosts($search, $offset, $limit));