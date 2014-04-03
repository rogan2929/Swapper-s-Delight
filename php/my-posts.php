<?php

require_once 'include/dal.php';

echo json_encode((new DataAccessLayer())->getMyPosts(filter_input(INPUT_GET, 'offset'), filter_input(INPUT_GET, 'limit')));