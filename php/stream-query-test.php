<?php

require_once 'graph/include.php';

$gid = '120696471425768';

$graphApiClient = new GraphApiClient();

$response = $graphApiClient->executeRequest('GET', '/' . $gid . '/feed', array(
    'limit' => 10000
));

echo json_encode($response);
