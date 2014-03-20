<?php

require_once 'session.php';
require_once 'include.php';

/* * *
 * Fetch the FQL stream table for the given group id.
 */

function fetchStream($gid) {
    if (http_response_code() != 401) {
        // On certain conditions, execute a new batch query to fetch the stream.
        // 1. Last updated time > 5 minutes.
        // 2. A new group was selected.
        // 3. Stream has not been fetched yet.
        if (!isset($_SESSION['posts']) || $_SESSION['lastUpdateTime'] < time() - 300 || $_SESSION['gid'] !== $gid) {
            $_SESSION['posts'] = executeBatchQuery($fbSession, $gid);
            $_SESSION['lastUpdateTime'] = time();
            $_SESSION['gid'] = $gid;
            $_SESSION['pagingOffset'] = 0;
        }
    }
}
