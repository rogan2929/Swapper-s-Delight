<?php

require_once 'graph/include.php';

echo json_encode((new CommentFactory())->postComment(filter_input(INPUT_POST, 'postId'), filter_input(INPUT_POST, 'comment')));
