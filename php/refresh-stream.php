<?php

require_once 'include/data-access.php';

echo (new CachedFeed())->refreshStream(filter_input(INPUT_GET, 'gid'));