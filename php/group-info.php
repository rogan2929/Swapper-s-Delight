<?php

require_once 'dal.php';

// Call the appropriate method in the newly instantiated DAL object.
echo json_encode((new DataAccessLayer())->getGroupInfo());