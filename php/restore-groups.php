<?php

$uid = $_GET['uid'];

$connectionInfo = array("UID" => "rogan2929@lreuagtc6u", "pwd" => "Revelation19:11", "Database" => "swapperAGiJRLgvy", "LoginTimeout" => 30, "Encrypt" => 1);

$serverName = "tcp:lreuagtc6u.database.windows.net,1433";

$conn = sqlsrv_connect($serverName, $connectionInfo);

if ($conn === false) {
    die(print('Could not connect to database.'));
}

$sql = 'DELETE FROM HiddenGroups WHERE UID=\'' . $uid . '\'';

// Execute the query.
$result = sqlsrv_query($conn, $sql);