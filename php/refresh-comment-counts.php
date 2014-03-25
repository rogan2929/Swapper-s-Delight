<?php

require_once 'dal.php';

$postIds = $_POST['postIds'];

$dal = new DataAccessLayer();

echo json_encode($dal->refreshCommentCounts($postIds));