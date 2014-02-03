<?php

require_once 'session.php';

// First, retrieve marked group ids from database.
// For now, just use some static constants.
$selectedGroups = array(
    '120696471425768',
    '1447216838830981',
    '575530119133790'
);

// Retrieve all groups.
$response = $fbSession.api('/me/groups');

echo json_encode($response);

//// Iterate through returned groups and determine if they have been marked or not.
//for ($i = 0; $i < count($response); $i++) {
//    $marked = false;
//    
//    for ($j = 0; $j < count($selectedGroups); $j++) {
//        if ($selectedGroups[$j] == $response[$i]['id']) {
//            $marked = true;
//            break;
//        }
//    }
//    
//    // Insert additional field indicating if the group has been marked as a "BST" group.
//    $response[$i]['marked'] = $marked;
//}

//$queries = array();
//
//// Construct a multi-query
//foreach ($selectedGroups as $gid) {
//    $queries[$gid] = ('SELECT gid,name,icon FROM group WHERE gid=' . $gid);
//}
//
//// Make an FQL call.
//$response = $fbSession->api(array(
//    'method' => 'fql.multiquery',
//    'queries' => $queries
//        ));
//
//$groups = array();
//
//for ($i = 0; $i < count($response); $i++) {
//    $groups[$i] = $response[$i]['fql_result_set'][0];
//}

// Pass the data on to the client.
//echo json_encode($groups);
?>