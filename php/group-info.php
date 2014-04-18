<?php

/**
 * group-info.php endpoint.
 */

require_once 'graph/include.php';

echo json_encode((new GroupFactory())->getGroupInfo());