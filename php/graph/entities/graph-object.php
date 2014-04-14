<?php

/**
 * 
 * Base Graph API object entity.
 * @author rogan2929
 *
 */
abstract class GraphObject implements JsonSerializable {

    protected $createdTime;
    protected $updatedTime;

    public function getCreatedTime() {
        return $this->createdTime;
    }

    public function getUpdatedTime() {
        return $this->updatedTime;
    }

    public function setCreatedTime($createdTime) {
        $this->createdTime = $createdTime;
    }

    public function setUpdatedTime($updatedTime) {
        $this->updatedTime = $updatedTime;
    }
}