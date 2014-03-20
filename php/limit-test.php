<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'include.php';
require_once 'session.php';

for ($i = 0; $i < 1000; $i++) {
    $response = $fbSession->api('/120696471425768/feed');
    echo json_encode($response) . '<br/>';
}