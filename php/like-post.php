<?php

require_once 'graph/data-access.php';

echo (new GraphApiClient())->likePost(filter_input(INPUT_POST, 'postId'), filter_input(INPUT_POST, 'userLikes'));