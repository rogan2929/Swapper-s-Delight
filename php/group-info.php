<?php

require_once 'graph/data-access.php';

echo json_encode((new GroupManager())->getGroupInfo());