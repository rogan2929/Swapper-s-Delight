<?php

require_once 'graph/include.php';

$gid = '120696471425768';

$postFactory = new PostFactory();
$postFactory->setGid($gid);
$postFactory->fetchStream(true);

echo count($postFactory->getStream());