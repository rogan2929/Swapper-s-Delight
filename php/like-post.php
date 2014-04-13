<?php

require_once 'graph/include.php';

echo (new GraphApiClient())->likePost(filter_input(INPUT_POST, 'postId'), filter_input(INPUT_POST, 'userLikes'));