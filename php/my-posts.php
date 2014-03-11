<?php

require_once 'session.php';
require_once 'include.php';

if (http_response_code() != 401) {
    $gid = $_GET['gid'];
    $uid = $_GET['uid'];
    
    // Create the constraints array.
    $constraints = array();

    $constraints[] = array(
        'field' => 'actor_id',
        'operator' => '=',
        'value' => $uid
    );

    echo json_encode(executeBatchQuery($fbSession, $gid, $constraints));
}