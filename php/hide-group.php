<?php

$uid = $_GET['uid'];
$gid = $_GET['gid'];

$connectionInfo = array("UID" => "rogan2929@lreuagtc6u", "pwd" => "Revelation19:11", "Database" => "swapperAGiJRLgvy", "LoginTimeout" => 30, "Encrypt" => 1);

$serverName = "tcp:lreuagtc6u.database.windows.net,1433";

$conn = sqlsrv_connect($serverName, $connectionInfo);

if ($conn === false) {
    die(print('Could not connect to database.'));
}

$sql = 'INSERT INTO HiddenGroups (UID, HiddenGroup) VALUES (\'' . $uid . '\', \'' . $gid . '\')';

// Execute the query.
$result = sqlsrv_query($conn, $sql);
