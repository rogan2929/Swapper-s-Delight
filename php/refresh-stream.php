<?php

require_once 'stream_data.php';

$gid = $_GET['gid'];

// Refresh the stream for the group.
fetchStream($gid, 1);