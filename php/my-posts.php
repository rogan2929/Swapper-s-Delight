<?php

require_once 'dal.php';

$dal = new DataAccessLayer();

echo json_encode($dal->getMyPosts());