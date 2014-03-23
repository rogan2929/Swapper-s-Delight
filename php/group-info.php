<?php

require_once 'dal.php';

if (!session_id()) {
    session_start();
}

// Set up the data access layer object and save it.
$_SESSION['dal'] = new DataAccessLayer();

$dal = $_SESSION['dal'];

// Call the appropriate method in the newly instantiated DAL object.
echo json_encode($dal->getGroupInfo());