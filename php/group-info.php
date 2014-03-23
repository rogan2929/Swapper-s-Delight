<?php

require_once 'dal.php';

$dal = $_SESSION['dal'];

// Call the appropriate method in the newly instantiated DAL object.
echo json_encode($dal->getGroupInfo());