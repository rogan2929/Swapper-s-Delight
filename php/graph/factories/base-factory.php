<?php

require $_SERVER['DOCUMENT_ROOT'] . '\php\graph\graph-api-client.php';
require $_SERVER['DOCUMENT_ROOT'] . '\php\graph\entities\include.php';

/**
 * Base factory class.
 */
class BaseFactory {
    protected $graphApiClient;
    
    /**
     * Default constructor.
     */
    function __construct() {
        $this->graphApiClient = new GraphApiClient();
    }
}