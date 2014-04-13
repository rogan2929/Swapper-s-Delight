<?php

require_once 'graph/data-access.php';

echo json_encode((new CachedFeed())->getMyPosts(filter_input(INPUT_GET, 'offset'), filter_input(INPUT_GET, 'limit')));