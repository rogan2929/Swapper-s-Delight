<?php

require_once 'dal.php';

$dal = new DataAccessLayer();

echo $dal->getHiddenGroups();