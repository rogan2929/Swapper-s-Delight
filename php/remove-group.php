<?php

/**
 * restore-groups.php endpoint.
 */

require_once 'graph/include.php';

echo (new GroupFactory())->removeGroup(filter_input(INPUT_GET, 'id'));