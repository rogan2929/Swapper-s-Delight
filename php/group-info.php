<?php

include_once 'session.php';

// First, retrieve marked group ids from database.
$selectedGroups = array('120696471425768', '1447216838830981', '575530119133790');
$query = array();

// Construct a multi-query
foreach ($selectedGroups as $gid) {
    $query[$gid] = ('SELECT gid,name,icon FROM group WHERE gid=' . $gid);
}

$ret = $fbSession->api('/fql?q=' . json_encode($query));

//echo json_encode($ret);
echo json_encode($query);

?>