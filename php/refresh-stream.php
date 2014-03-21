<?php

require_once 'stream_data.php';
require_once 'session.php';

$gid = $_GET['gid'];

$facebook = getFacebookSession();

// Refresh the stream for the group.
if (http_response_code() != 401) {
    fetchStream($facebook, $gid, 1);
}