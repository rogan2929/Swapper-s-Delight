<?php

require_once 'graph/include.php';

echo (new GraphApiClient())->deleteObject(filter_input(INPUT_GET, 'id'));