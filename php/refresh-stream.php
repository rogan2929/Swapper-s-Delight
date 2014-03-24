<?php

require_once 'dal.php';

$dal = new DataAccessLayer();
$dal->setGid($_SESSION['gid']);

$dal->refreshStream();