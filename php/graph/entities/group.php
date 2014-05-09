<?php

require_once 'graph-object.php';

/**
 * Graph API group entity. Loosely corresponds to the FQL group table.
 */
class Group extends GraphObject {

    private $gid;
    private $name;
    private $icon;

    /**
     * Constructor.
     * @param type $gid
     * @param type $name
     * @param type $icon
     */
    function __construct($gid, $name, $icon) {
        $this->gid = $gid;
        $this->name = $name;
        $this->icon = $icon;
    }

    /**
     * Implemention of JsonSerializable
     * @return array
     */
    public function jsonSerialize() {
        return get_object_vars($this);
    }

    /**
     * Gets the group's unique ID.
     * @return string
     */
    public function getGid() {
        return $this->gid;
    }

    /**
     * Gets the group's name.
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Gets the group's icon.
     * @return string
     */
    public function getIcon() {
        return $this->icon;
    }

    /**
     * Sets the group's unique ID.
     * @param string $gid
     */
    public function setGid($gid) {
        $this->gid = $gid;
    }

    /**
     * Sets the group's name.
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Sets the group's icon.
     * @param string $icon
     */
    public function setIcon($icon) {
        $this->icon = $icon;
    }

}
