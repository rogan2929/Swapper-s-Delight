<?php

require_once 'dal.php';

$dal = new DataAccessLayer();

// If the gid was supplied, then use it.
if (isset($_GET['gid'])) {
    $dal->setGid($_GET['gid']);
}

echo $dal->refreshStream();