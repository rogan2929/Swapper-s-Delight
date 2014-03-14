<?php

$uid = $_GET['uid'];

$connectionInfo = array("UID" => "rogan2929@lreuagtc6u", "pwd" => "Revelation19:11", "Database" => "swapperAGiJRLgvy", "LoginTimeout" => 30, "Encrypt" => 1);

$serverName = "tcp:lreuagtc6u.database.windows.net,1433";

$conn = sqlsrv_connect($serverName, $connectionInfo);

if ($conn === false) {
    die(print('Could not connect to database.'));
}

$sql = 'SELECT * FROM HiddenGroups';

$result = sqlsrv_query($conn, $query);

$hiddenGroups = '';

while ($row = sqlsrv_fetch_array($result)) {
    echo json_encode($row);
}