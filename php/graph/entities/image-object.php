<?php

require_once 'graph-object.php';

/**
 * 
 * Graph API image object entity.
 * @author rogan2929
 *
 */
class ImageObject extends GraphObject {

    private $id;
    private $url;
    private $width;
    private $height;
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
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

    public function jsonSerialize() {
        return get_object_vars($this);
    }
}