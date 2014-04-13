<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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