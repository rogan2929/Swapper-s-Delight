<?php

require_once 'include/data-access.php';

$postIds = json_decode(filter_input(INPUT_POST, 'postIds'));

echo json_encode((new CachedFeed())->getRefreshedStreamData($postIds));