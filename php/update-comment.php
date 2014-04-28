<?php

/**
 * update-comment.php endpoint.
 */

require_once 'graph/include.php';

echo json_encode((new CommentFactory())->updateComment(filter_input(INPUT_POST, 'id'), filter_input(INPUT_POST, 'message')));