<?php

require_once 'graph/include.php';

$postIds = json_decode(filter_input(INPUT_POST, 'postIds'));

echo json_encode((new PostFactory())->getRefreshedStreamData($postIds));