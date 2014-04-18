<?php

/**
 * hide-group.php endpoint.
 */

require_once 'graph/include.php';

echo (new GroupFactory())->hideGroup(filter_input(INPUT_GET, 'gid'));