<?php

require_once 'session.php';
require_once 'include.php';

if (http_response_code() != 401) {
    $gid = $_GET['gid'];
    $search = $_GET['search'];

    // Create the constraints array.
    $constraints = array();

    // Reference the strpos() and upper() FQL functions. (There is no 'like' operator.)
    $constraints[] = array(
        'field' => 'strpos(upper(message), upper("' . $search . '"))',
        'operator' => '>=',
        'value' => '0'
    );

    echo json_encode(executeBatchQuery($fbSession, $gid, $constraints));
}