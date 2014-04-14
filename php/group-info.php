<?php

try {
	require_once 'graph/include.php';
}
catch (Exception $e) {
	echo $e->getMessage();
}

//$groupFactory = new GroupFactory();

//echo json_encode((new GroupFactory())->getGroupInfo());