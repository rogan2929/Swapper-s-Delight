<?php

require_once 'include/dal.php';

echo json_encode((new GraphApiClient())->postComment(filter_input(INPUT_POST, 'postId'), filter_input(INPUT_POST, 'comment')));
