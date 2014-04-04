<?php

require_once 'include/dal.php';

$gid = '120696471425768';

$feed = new CachedFeed();

echo var_dump($feed);

//echo json_encode($feed->getStream());