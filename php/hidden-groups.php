<?php

require_once 'include/data-access.php';

echo (new GroupManager())->getHiddenGroups();