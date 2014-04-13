<?php

require_once 'graph/data-access.php';

$feed = new CachedFeed();
$feed->setGid(filter_input(INPUT_GET, 'gid'));

echo json_encode($feed->getNewPosts(filter_input(INPUT_GET, 'refresh'), filter_input(INPUT_GET, 'offset'), filter_input(INPUT_GET, 'limit')));
