<?php

require_once 'graph-object.php';

/**
 * 
 * Graph API image object entity.
 * @author rogan2929
 *
 */
class Image extends GraphObject {

    private $id;
    private $url;
    private $width;
    private $height;
    
    /**
     * Gets the unique ID of the image.
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Sets the unique ID of the image.
     * @param string $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Gets the source URL of the image.
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Gets the width of the image.
     * @return int
     */
    public function getWidth() {
        return $this->width;
    }

    /**
     * Gets the height of the image.
     * @return int
     */
    public function getHeight() {
        return $this->height;
    }

    /**
     * Sets the source URL of the image.
     * @param string $url
     */
    public function setUrl($url) {
        $this->url = $url;
    }

    /**
     * Sets the width of the image.
     * @param int $width
     */
    public function setWidth($width) {
        $this->width = $width;
    }

    /**
     * Sets the height of the image.
     * @param int $height
     */
    public function setHeight($height) {
        $this->height = $height;
    }

    /**
     * Implementation of JsonSerializable.
     * @return array
     */
    public function jsonSerialize() {
        return get_object_vars($this);
    }
}