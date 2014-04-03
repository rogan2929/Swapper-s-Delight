<?php

require_once 'include/dal.php';

echo (new DataAccessLayer())->deleteObject(filter_input(INPUT_GET, 'id'));