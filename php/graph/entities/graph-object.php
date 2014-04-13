<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class GraphObject {

    protected $id;
    protected $createdTime;
    protected $updatedTime;

    public function getId() {
        return $this->id;
    }

    public function getCreatedTime() {
        return $this->createdTime;
    }

    public function getUpdatedTime() {
        return $this->updatedTime;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setCreatedTime($createdTime) {
        $this->createdTime = $createdTime;
    }

    public function setUpdatedTime($updatedTime) {
        $this->updatedTime = $updatedTime;
    }

}