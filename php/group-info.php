<?php

require_once 'include/dal.php';

echo json_encode((new GroupManager())->getGroupInfo());