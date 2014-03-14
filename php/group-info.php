<?php

require_once 'session.php';

if (http_response_code() != 401) {
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
    $groups = $response[1] ['fql_result_set'];

    // Get group member count for each group.
    for ($i = 0; $i < count($groups); $i++) {
        $members = $fbSession->api(array(
            'method' => 'fql.query',
            'query' => 'SELECT uid FROM group_member WHERE gid=' . $groups[$i]['gid']
        ));
        
        $groups[$i]['size'] = count($members);
    }

    echo json_encode($groups);
}
