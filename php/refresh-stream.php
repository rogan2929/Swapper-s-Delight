<?php

require_once 'graph/include.php';

echo (new CachedFeed())->refreshStream(filter_input(INPUT_GET, 'gid'));