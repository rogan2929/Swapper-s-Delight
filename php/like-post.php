<?php

require_once 'include/dal.php';

echo (new GraphApiClient())->likePost(filter_input(INPUT_POST, 'postId'), filter_input(INPUT_POST, 'userLikes'));