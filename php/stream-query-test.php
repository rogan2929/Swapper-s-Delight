<?php

require_once 'dal.php';

$gid = '120696471425768';

$dal = new DataAccessLayer();

echo json_encode($dal->getStream());