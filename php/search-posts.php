<?php

/**
 * search-posts.php endpoint.
 */

require_once 'graph/include.php';

echo json_encode((new PostFactory())->searchPosts(filter_input(INPUT_GET, 'search'), filter_input(INPUT_GET, 'offset'), filter_input(INPUT_GET, 'limit')));