<?php

require_once 'graph-object.php';

/**
 * 
 * Graph API group object.
 * @author rogan2929
 *
 */
class Group extends GraphObject {

    private $gid;
    private $name;
    private $icon;

    function __construct($gid, $name, $icon) {
        $this->gid = $gid;
        $this->name = $name;
        $this->icon = $icon;
    }   
    
    public function jsonSerialize() {
       return get_object_vars($this);
    }
    
    public function getGid() {
        return $this->gid;
    }

    public function getName() {
        return $this->name;
    }

    public function getIcon() {
        return $this->icon;
    }

    public function setGid($gid) {
        $this->gid = $gid;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setIcon($icon) {
        $this->icon = $icon;
    }


}