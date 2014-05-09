<?php

header('Content-Type: application/json');

require_once 'graph/include.php';

//$gid = '120696471425768';
$gid = '409783902455116';

$postFactory = new PostFactory();
$postFactory->setGid($gid);
$postFactory->fetchPosts(true);

echo json_encode($postFactory->getStream());