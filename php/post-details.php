<?php

require_once 'include/dal.php';

echo json_encode((new DataAccessLayer())->getPostDetails(filter_input(INPUT_GET, 'postId')));