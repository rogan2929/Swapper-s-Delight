<?php

/**
 * Base Graph API object entity. Class is abstract, so all child classes must
 * implement JsonSerializable.
 */
abstract class GraphObject implements JsonSerializable {

    protected $createdTime;
    protected $updatedTime;

    /**
     * Gets creation time of the GraphObject as a UNIX timestamp.
     * @return int
     */
    public function getCreatedTime() {
        return $this->createdTime;
    }

    /**
     * Gets updated time of the GraphObject as a UNIX timestamp.
     * @return int
     */
    public function getUpdatedTime() {
        return $this->updatedTime;
    }

    /**
     * Sets creation time.
     * @param int $createdTime
     */
    public function setCreatedTime($createdTime) {
        $this->createdTime = $createdTime;
    }

    /**
     * Sets updated time.
     * @param int $updatedTime
     */
    public function setUpdatedTime($updatedTime) {
        $this->updatedTime = $updatedTime;
    }
}