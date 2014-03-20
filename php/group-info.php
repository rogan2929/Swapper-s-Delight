<?php

$fbSession = $_SESSION['fbSession'];

if (http_response_code() != 401) {
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

    echo json_encode($groups);
}
