<?php

/**
 * update-comment.php endpoint.
 */

require_once 'graph/include.php';

echo (new CommentFactory())->updateComment(filter_input(INPUT_POST, 'id'), filter_input(INPUT_POST, 'message'));