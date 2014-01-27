<?php

require_once 'session.php';

// First, retrieve marked group ids from database.
// For now, just use some static constants.
$selectedGroups = array('120696471425768', '1447216838830981', '575530119133790');
$queries = array();

// Construct a multi-query
foreach ($selectedGroups as $gid) {
    $queries[$gid] = ('SELECT gid,name,icon FROM group WHERE gid=' . $gid);
}

// Depending if $queries has more than one element, call either the FB
// FQL query API or the FB multiquery API.
if (count($queries) == 1) {
    $groups = $fbSession->api(array(
        'method' => 'fql.query',
        'query' => $queries
    ));
} else {
    $groups = $fbSession->api(array(
        'method' => 'fql.multiquery',
        'queries' => $queries
    ));
}

//echo json_encode($ret);
echo json_encode($groups);
?>