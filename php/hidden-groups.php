<?php

/**
 * hidden-groups.php endpoint.
 */

require_once 'graph/include.php';

echo (new GroupFactory())->getHiddenGroups();