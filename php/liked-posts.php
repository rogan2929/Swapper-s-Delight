<?php

require_once 'session.php';
require_once 'include.php';

if (http_response_code() != 401) {
    $gid = $_GET['gid'];
    $uid = $_GET['uid'];
    
    // Create the constraints array.
    $constraints = array();

    $constraints[] = array(
        'field' => 'like_info.user_likes',
        'operator' => '=',
        'value' => '1'
    );

    echo json_encode(executeBatchQuery($fbSession, $gid, $constraints));
}
