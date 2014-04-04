<?php

require_once 'included/dal.php';

echo json_encode((new CachedFeed())->getLikedPosts(filter_input(INPUT_GET, 'offset'), filter_input(INPUT_GET, 'limit')));