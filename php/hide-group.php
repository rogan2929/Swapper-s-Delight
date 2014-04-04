<?php

require_once 'include/dal.php';

echo (new GroupManager())->hideGroup(filter_input(INPUT_GET, 'gid'));