<?php

require_once 'session.php';

// First, retrieve marked group ids from database.
// For now, just use some static constants.
$selectedGroups = array(
	'120696471425768',
	'1447216838830981',
	'575530119133790'
);
$queries = array();

// Construct a multi-query
foreach ($selectedGroups as $gid) {
	$queries[$gid] = ('SELECT gid,name,icon FROM group WHERE gid=' . $gid);
}

// Make an FQL call.
$response = $fbSession->api(array(
	'method' => 'fql.multiquery',
	'queries' => $queries
));

$groups = array();

for ($i = 0; $i < count($response); $i++) {
	$groups[$i] = $response[$i]['fql_result_set'][0];
}

// Pass the data on to the client.
echo json_encode($groups);
?>