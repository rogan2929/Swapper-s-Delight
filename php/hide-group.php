<?php

require_once 'dal.php';

$dal = new DataAccessLayer();

$dal->hideGroup($_GET['gid']);