<?php

require_once 'graph-object.php';

/**
 * Graph API user entity. Loosely corresponds to the FQL user table.
 */
class User extends GraphObject {

    private $uid;
    private $lastName;
    private $firstName;
    private $profileUrl;
    private $picSquare;
    private $picFull;
    
    /**
     * Gets source URL of the large picture of the user.
     * @return string
     */
    public function getPicFull() {
        return $this->picFull;
    }

    /**
     * Sets the source URL of the large picture of the user.
     * @param string $picFull
     */
    public function setPicFull($picFull) {
        $this->picFull = $picFull;
    }
    
    /**
     * Gets the user's unique ID.
     * @return string
     */
    public function getUid() {
        return $this->uid;
    }

    /**
     * Sets the user's unqiue ID.
     * @param string $uid
     */
    public function setUid($uid) {
        $this->uid = $uid;
    }
    
    /**
     * Gets the user's last name.
     * @return string
     */
    public function getLastName() {
        return $this->lastName;
    }

    /**
     * Gets the user's first name.
     * @return string
     */
    public function getFirstName() {
        return $this->firstName;
    }
    
    /**
     * Gets the user's full name as a function of first and last.
     * @return string
     */
    public function getFullName() {
        return $this->firstName . ' ' . $this->lastName;
    }

    /**
     * Gets the user's Facebook profile URL.
     * @return string
     */
    public function getProfileUrl() {
        return $this->profileUrl;
    }

    /**
     * Gets the source URL of the square version of the user's profile image.
     * @return string
     */
    public function getPicSquare() {
        return $this->picSquare;
    }

    /**
     * Sets the last name of the user.
     * @param string $lastName
     */
    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }

    /**
     * Sets the first name of the user.
     * @param string $firstName
     */
    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }

    /**
     * Sets the user's Facebook profile URL.
     * @param string $profileUrl
     */
    public function setProfileUrl($profileUrl) {
        $this->profileUrl = $profileUrl;
    }

    /**
     * Gets the source URL of the square version of the user's profile image.
     * @param string $picSquare
     */
    public function setPicSquare($picSquare) {
        $this->picSquare = $picSquare;
    }

    /**
     * Implementation of JsonSerializable
     * @return array
     */
    public function jsonSerialize() {
        return get_object_vars($this);
    }
}
