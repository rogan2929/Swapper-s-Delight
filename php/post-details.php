<?php

require_once 'graph/include.php';

echo json_encode((new CachedFeed())->getPostDetails(filter_input(INPUT_GET, 'postId')));