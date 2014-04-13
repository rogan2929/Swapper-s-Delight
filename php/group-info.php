<?php

require_once 'graph/include.php';

echo json_encode((new GroupManager())->getGroupInfo());