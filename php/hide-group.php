<?php

require_once 'include/data-access.php';

echo (new GroupManager())->hideGroup(filter_input(INPUT_GET, 'gid'));