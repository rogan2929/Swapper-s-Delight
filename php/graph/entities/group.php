<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Group extends GraphObject {

    private $name;
    private $icon;

    function __construct($gid, $name, $icon) {
        $this->id = $gid;
        $this->name = $name;
        $this->icon = $icon;
    }

}