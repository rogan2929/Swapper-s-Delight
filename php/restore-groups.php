<?php

/**
 * restore-groups.php endpoint.
 */

require_once 'graph/include.php';

echo (new GroupFactory())->restoreGroups();