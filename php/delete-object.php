<?php

require_once 'graph/data-access.php';

echo (new GraphApiClient())->deleteObject(filter_input(INPUT_GET, 'id'));