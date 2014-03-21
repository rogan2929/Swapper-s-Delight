<?php

require 'dal.php';

// Create a DAL object and retrieve the group info.
echo json_encode((new DataAccessLayer())->getGroupInfo());