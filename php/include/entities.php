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

class LinkData {

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
}

class Post extends GraphObject {

    protected $message;
    protected $actor;
    protected $commentCount;
    protected $likeCount;
    protected $userLikes;
    protected $imageData;
    protected $linkData;
    protected $type;

    public function getMessage() {
        return $this->message;
    }

    public function getActor() {
        return $this->actor;
    }

    public function getCommentCount() {
        return $this->commentCount;
    }

    public function getLikeCount() {
        return $this->likeCount;
    }

    public function getUserLikes() {
        return $this->userLikes;
    }

    public function getImageData() {
        return $this->imageData;
    }

    public function getLinkData() {
        return $this->linkData;
    }

    public function getType() {
        return $this->type;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function setActor($actor) {
        $this->actor = $actor;
    }

    public function setCommentCount($commentCount) {
        $this->commentCount = $commentCount;
    }

    public function setLikeCount($likeCount) {
        $this->likeCount = $likeCount;
    }

    public function setUserLikes($userLikes) {
        $this->userLikes = $userLikes;
    }

    public function setImageData($imageData) {
        $this->imageData = $imageData;
    }

    public function setLinkData($linkData) {
        $this->linkData = $linkData;
    }

    public function setType($type) {
        $this->type = $type;
    }

}

class Comment extends Post {
    
}

class Group extends GraphObject {

    private $name;
    private $icon;

}
