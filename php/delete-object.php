<?php

require_once 'include/dal.php';

echo (new GraphApiClient())->deleteObject(filter_input(INPUT_GET, 'id'));