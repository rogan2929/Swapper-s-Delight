<?php

require_once 'session.php';
require_once 'include.php';

if (http_response_code() != 401) {
    $gid = $_GET['gid'];
    $uid = $_GET['uid'];

    echo json_encode(getGroupPostsbyUid($fbSession, $gid, $uid));
    //echo count(getGroupPostsbyUid($fbSession, $gid, $uid));
}