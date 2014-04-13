<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class User extends GraphObject {

    private $lastName;
    private $firstName;
    private $profileUrl;
    private $picSquare;

    public function getLastName() {
        return $this->lastName;
    }

    public function getFirstName() {
        return $this->firstName;
    }

    public function getProfileUrl() {
        return $this->profileUrl;
    }

    public function getPicSquare() {
        return $this->picSquare;
    }

    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }

    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }

    public function setProfileUrl($profileUrl) {
        $this->profileUrl = $profileUrl;
    }

    public function setPicSquare($picSquare) {
        $this->picSquare = $picSquare;
    }

}