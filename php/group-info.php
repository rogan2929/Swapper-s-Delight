<?php

require_once 'session.php';

// First, retrieve marked group ids from database.
// For now, just use some static constants.
//$selectedGroups = array(
//    '120696471425768',
//    '1447216838830981',
//    '575530119133790'
//);

$queries = array(
    'memberQuery' => 'SELECT gid,bookmark_order FROM group_member WHERE uid=me() ORDER BY bookmark_order',
    'groupQuery' => 'SELECT gid,name,icon FROM group WHERE gid IN (SELECT gid FROM #memberQuery)'
);

$response = $fbSession->api(array(
    'method' => 'fql.multiquery',
    'queries' => $queries
        ));

// Grab the results of the query.
$groups = $response[1]['fql_result_set'];

for ($i = 0; $i < count($groups); $i++) {
    $groups[$i]['marked'] = true;
}

// Retrieve all groups.
//$response = $fbSession->api('/me/groups?fields=id,name,icon');
//$groups = $response['data'];
// Iterate through returned groups and determine if they have been marked or not.
//for ($i = 0; $i < count($groups); $i++) {
//    $marked = true;
//    
////    for ($j = 0; $j < count($selectedGroups); $j++) {
////        if ($selectedGroups[$j] == $groups[$i]['id']) {
////            $marked = true;
////            break;
////        }
////    }
//    
//    // Insert additional field indicating if the group has been marked as a "BST" group.
//    $groups[$i]['marked'] = $marked;
//}

echo json_encode($groups);
