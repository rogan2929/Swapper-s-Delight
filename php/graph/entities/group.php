<?php

require_once 'graph-object.php';

/**
 * 
 * Graph API group object.
 * @author rogan2929
 *
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