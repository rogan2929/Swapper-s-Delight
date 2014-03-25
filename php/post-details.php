<?php

require_once 'dal.php';

$postId = $_GET['postId'];

$dal = new DataAccessLayer();

echo json_encode($dal->getPostDetails($postId));