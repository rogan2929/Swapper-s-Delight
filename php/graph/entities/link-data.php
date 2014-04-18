<?php

/**
 * Link data entity. Loosely corresponds to FQL comment and post attachment data.
 */
class LinkData implements JsonSerializable {

    private $caption;
    private $href;
    private $name;
    private $description;
    private $src;

    /**
     * Gets the caption for the link.
     * @return string
     */
    public function getCaption() {
        return $this->caption;
    }

    /**
     * Gets the href for the link.
     * @return string
     */
    public function getHref() {
        return $this->href;
    }

    /**
     * Gets the name of the link. This is usually the remote page's title.
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Gets the description of the link.
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Gets the image source of the link.
     * @return string
     */
    public function getSrc() {
        return $this->src;
    }

    /**
     * Sets the caption for the link.
     * @param string $caption
     */
    public function setCaption($caption) {
        $this->caption = $caption;
    }

    /**
     * Sets the href for the link.
     * @param string $href
     */
    public function setHref($href) {
        $this->href = $href;
    }

    /**
     * Sets the name of the link object.
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Sets the description of the link object.
     * @param string $description
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * Sets the image source URL of the link data object.
     * @param string $src
     */
    public function setSrc($src) {
        $this->src = $src;
    }
    
    /**
     * Implementation of JsonSerializable
     * @return array
     */
    public function jsonSerialize() {
        return get_object_vars($this);
    }
}