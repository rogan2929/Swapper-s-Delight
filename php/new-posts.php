<?php

/**
 * new-posts.php endpoint.
 */

require_once 'graph/include.php';

$postFactory = new PostFactory();
$postFactory->setGid(filter_input(INPUT_GET, 'gid'));

echo json_encode($postFactory->getNewPosts(filter_input(INPUT_GET, 'refresh'), filter_input(INPUT_GET, 'offset'), filter_input(INPUT_GET, 'limit')));
