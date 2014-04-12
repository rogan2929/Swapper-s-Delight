<?php

require_once 'include/data-access.php';

echo (new GraphApiClient())->deleteObject(filter_input(INPUT_GET, 'id'));