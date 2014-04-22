<?php

/**
 * post-details.php endpoint.
 */

require_once 'graph/include.php';

error_log('post-details.php');

echo json_encode((new PostFactory())->getPostDetails(filter_input(INPUT_GET, 'postId')));