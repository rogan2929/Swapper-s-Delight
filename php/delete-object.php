<?php

/**
 * delete-object.php endpoint.
 */

require_once 'graph/include.php';

echo (new GraphObjectFactory())->deleteObject(filter_input(INPUT_GET, 'id'));