<?php

require_once 'graph/include.php';

$feed = new PostFactory();
$feed->setGid(filter_input(INPUT_GET, 'gid'));

echo json_encode($feed->getNewPosts(filter_input(INPUT_GET, 'refresh'), filter_input(INPUT_GET, 'offset'), filter_input(INPUT_GET, 'limit')));
