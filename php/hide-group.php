<?php

require_once 'include/dal.php';

echo (new DataAccessLayer())->hideGroup(filter_input(INPUT_GET, 'gid'));