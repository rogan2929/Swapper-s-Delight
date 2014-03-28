<?php

require_once 'dal.php';

$id = $_GET['id'];

(new DataAccessLayer())->deleteObject($id);