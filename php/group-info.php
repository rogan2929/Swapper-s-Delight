<?php

require_once 'dal.php';

$dal = $_SESSION['dal'];

echo var_dump($dal);

// Call the appropriate method in the newly instantiated DAL object.
echo json_encode($dal->getGroupInfo());