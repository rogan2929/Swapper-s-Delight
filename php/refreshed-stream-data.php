<?php

require_once 'graph/data-access.php';

$postIds = json_decode(filter_input(INPUT_POST, 'postIds'));

echo json_encode((new CachedFeed())->getRefreshedStreamData($postIds));