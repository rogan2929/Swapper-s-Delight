<?php

require_once 'input/dal.php';

echo json_encode((new DataAccessLayer())->getLikedPosts(filter_input(INPUT_GET, 'offset'), filter_input(INPUT_GET, 'limit')));