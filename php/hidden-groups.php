<?php

$uid = $_GET['uid'];

$connectionInfo = array("UID" => "rogan2929@lreuagtc6u", "pwd" => "Revelation19:11", "Database" => "swapperAGiJRLgvy", "LoginTimeout" => 30, "Encrypt" => 1);

$serverName = "tcp:lreuagtc6u.database.windows.net,1433";

$conn = sqlsrv_connect($serverName, $connectionInfo);

if ($conn === false) {
    die(print('Could not connect to database.'));
}

$sql = 'SELECT HiddenGroup FROM HiddenGroups WHERE UID=\'' . $uid . '\'';

echo $sql . "<br/>";

$result = sqlsrv_query($conn, $sql);

$hiddenGroups = '';

while ($row = sqlsrv_fetch_array($result)) {
    //$hiddenGroups .= $row['Group'] . ' ';
    echo $row['HiddenGroup'] . "<br/>";
}

echo $hiddenGroups;