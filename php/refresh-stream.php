<?php

/**
 * refresh-stream.php endpoint.
 */

require_once 'graph/include.php';

(new PostFactory())->refreshStream(filter_input(INPUT_GET, 'gid'));