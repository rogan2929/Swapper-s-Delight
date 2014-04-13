<?php

require_once 'graph/data-access.php';

echo (new CachedFeed())->refreshStream(filter_input(INPUT_GET, 'gid'));