<?php

require_once 'graph/include.php';

$gid = '120696471425768';

$postFactory = new PostFactory();
$postFactory->refreshStream($gid);

echo count($postFactory->getStream());