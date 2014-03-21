<?php

require_once 'dal.php';

$postId = $_GET['postId'];

(new DataAccessLayer())->deletePost($postId);