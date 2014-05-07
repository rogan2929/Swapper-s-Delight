<?php

/**
 * fb-search.php endpoint.
 */

require_once 'graph/include.php';

echo json_encode((new GraphApiClient())->search(filter_input(INPUT_GET, 'search'), filter_input(INPUT_GET, 'type')));