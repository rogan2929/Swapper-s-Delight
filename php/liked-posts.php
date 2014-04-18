<?php

/**
 * liked-posts.php endpoint.
 */

require_once 'graph/include.php';

echo json_encode((new PostFactory())->getLikedPosts(filter_input(INPUT_GET, 'offset'), filter_input(INPUT_GET, 'limit')));