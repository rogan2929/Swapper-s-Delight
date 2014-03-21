<?php

require_once 'dal.php';

echo json_encode((new DataAccessLayer())->getGroupInfo());