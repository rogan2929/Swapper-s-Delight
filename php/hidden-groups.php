<?php

require_once 'graph/data-access.php';

echo (new GroupManager())->getHiddenGroups();