<?php

require_once 'include/dal.php';

echo json_encode((new CachedFeed())->getPostDetails(filter_input(INPUT_GET, 'postId')));