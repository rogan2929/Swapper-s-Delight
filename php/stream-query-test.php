<?php

require_once 'graph/data-access.php';

$gid = '120696471425768';

$feed = new CachedFeed();

//echo var_dump($feed);

echo json_encode($feed->getStream());
