<?php

require_once 'dal.php';

$postIds = json_decode($_POST['postIds']);

$dal = new DataAccessLayer();

echo json_encode($dal->getRefreshedStreamData($postIds));