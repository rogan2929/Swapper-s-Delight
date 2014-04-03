<?php

require_once 'include/dal.php';

$dal = new DataAccessLayer();
$dal->setGid(filter_input(INPUT_GET, 'gid'));

echo json_encode($dal->getNewPosts(filter_input(INPUT_GET, 'refresh'), filter_input(INPUT_GET, 'offset'), filter_input(INPUT_GET, 'limit')));
