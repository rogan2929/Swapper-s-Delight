<?php

require_once 'graph/include.php';

$gid = '120696471425768';

$postFactory = new PostFactory();

//echo var_dump($feed);

echo json_encode($postFactory->getStream());
