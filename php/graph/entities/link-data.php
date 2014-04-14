<?php

/**
 * 
 * Link data entity.
 * @author rogan2929
 *
 */
class LinkData implements JsonSerializable {

    private $caption;
    private $href;
    private $name;
    private $description;
    private $src;

    public function getCaption() {
        return $this->caption;
    }

    public function getHref() {
        return $this->href;
    }

    public function getName() {
        return $this->name;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getSrc() {
        return $this->src;
    }

    public function setCaption($caption) {
        $this->caption = $caption;
    }

    public function setHref($href) {
        $this->href = $href;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setSrc($src) {
        $this->src = $src;
    }
    
    public function jsonSerialize() {
        return get_object_vars($this);
    }
}