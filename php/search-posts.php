<?php

require_once 'session.php';
require_once 'include.php';

if (http_response_code() != 401) {
    $gid = $_GET['gid'];
    $search = $_GET['search'];
    
    echo $search;
}