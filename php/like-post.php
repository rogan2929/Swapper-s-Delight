<?php

/**
 * like-post.php endpoint.
 */
require_once 'graph/include.php';

$postFactory = new PostFactory();
$postId = filter_input(INPUT_POST, 'postId');

if (filter_input(INPUT_POST, 'userLikes') == true) {
    // Like the post.
    echo $postFactory->likeObject($postId);
} else {
    // Unlike the post.
    echo $postFactory->unLikeObject($postId);
}
        