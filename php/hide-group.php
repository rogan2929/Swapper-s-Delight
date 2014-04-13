<?php

require_once 'graph/data-access.php';

echo (new GroupManager())->hideGroup(filter_input(INPUT_GET, 'gid'));