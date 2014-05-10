<?php

/**
 * add-group.php endpoint.
 */

require_once 'graph/include.php';

echo (new GroupFactory())->addGroup(filter_input(INPUT_GET, 'gid'));