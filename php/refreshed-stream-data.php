<?php

require_once 'include/dal.php';

$postIds = json_decode(filter_input(INPUT_POST, 'postIds'));

echo json_encode((new DataAccessLayer())->getRefreshedStreamData($postIds));