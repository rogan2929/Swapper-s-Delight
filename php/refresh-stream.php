<?php

require_once 'include/dal.php';

echo (new DataAccessLayer())->refreshStream(filter_input(INPUT_GET, 'gid'));