<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ImageObject extends GraphObject {

    private $url;
    private $width;
    private $height;

    function __construct($id, $url) {
        $this->id = $id;
        $this->url = $url;
    }

    public function getUrl() {
        return $this->url;
    }

    public function getWidth() {
        return $this->width;
    }

    public function getHeight() {
        return $this->height;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    public function setWidth($width) {
        $this->width = $width;
    }

    public function setHeight($height) {
        $this->height = $height;
    }

}