<?php

require_once 'dal.php';

$postIds = json_decode($_POST['postIds']);

echo var_dump($postIds);

//$dal = new DataAccessLayer();

//echo json_encode($dal->getRefreshedStreamData($postIds));