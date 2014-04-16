<?php

require $_SERVER['DOCUMENT_ROOT'] . '\php\graph\graph-api-client.php';
require $_SERVER['DOCUMENT_ROOT'] . '\php\graph\entities\include.php';

class BaseFactory {
    protected $graphApiClient;
    
    function __construct() {
        $this->graphApiClient = new GraphApiClient();
    }
}