<?php

$uid = $_GET['uid'];

$connectionInfo = array("UID" => "rogan2929@lreuagtc6u", "pwd" => "Revelation19:11", "Database" => "swapperAGiJRLgvy", "LoginTimeout" => 30, "Encrypt" => 1);

$serverName = "tcp:lreuagtc6u.database.windows.net,1433";

$conn = sqlsrv_connect($serverName, $connectionInfo);

$sql = 'SELECT Group FROM HiddenGroups WHERE UID="' . $uid . '"';

$groups = sqlsrv_query($conn, $query);

echo var_dump($groups);