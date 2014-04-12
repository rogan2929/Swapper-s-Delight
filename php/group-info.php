<?php

require_once 'include/data-access.php';

echo json_encode((new GroupManager())->getGroupInfo());