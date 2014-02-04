<?php

require_once 'session.php';

// First, retrieve marked group ids from database.
// For now, just use some static constants.
$selectedGroups = array(
    '120696471425768',
    '1447216838830981',
    '575530119133790'
);

// Retrieve all groups.
$response = $fbSession->api('/me/groups?fields=id,name,icon');
$groups = $response['data'];

// Iterate through returned groups and determine if they have been marked or not.
for ($i = 0; $i < count($groups); $i++) {
    $marked = false;
    
    for ($j = 0; $j < count($selectedGroups); $j++) {
        if ($selectedGroups[$j] == $groups[$i]['id']) {
            $marked = true;
            break;
        }
    }
    
    // Insert additional field indicating if the group has been marked as a "BST" group.
    $groups[$i]['marked'] = $marked;
}

echo json_encode($groups);