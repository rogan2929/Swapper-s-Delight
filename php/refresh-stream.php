<?php

require_once 'include/dal.php';

echo (new CachedFeed())->refreshStream(filter_input(INPUT_GET, 'gid'));