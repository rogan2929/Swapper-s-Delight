<?php

require_once 'session.php';

$gid = $_GET['gid'];
$uid = $_GET['uid'];    // For some reason, calling $fbSession->getUser() kills the access token. So, we cheated.

