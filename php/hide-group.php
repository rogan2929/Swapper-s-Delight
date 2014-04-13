<?php

require_once 'graph/include.php';

echo (new GroupManager())->hideGroup(filter_input(INPUT_GET, 'gid'));