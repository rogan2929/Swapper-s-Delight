<?php

require_once 'graph-object.php';

/**
 * 
 * Graph API post object.
 * @author rogan2929
 *
 */
class Post extends GraphObject {

    protected $id;
    protected $message;
    protected $actor;
    protected $commentCount;
    protected $likeCount;
    protected $userLikes;
    protected $linkData;
    protected $type;
    protected $permalink;
    protected $comments;
    protected $imageObjects;

    public function getImageObjects() {
        return $this->imageObjects;
    }

    public function setImageObjects($imageObjects) {
        $this->imageObjects = $imageObjects;
    }


    public function getComments() {
        return $this->comments;
    }

    public function setComments($comments) {
        $this->comments = $comments;
    }

    public function getPermalink() {
        return $this->permalink;
    }

    public function setPermalink($permalink) {
        $this->permalink = $permalink;
    }
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

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

    public function setLinkData($linkData) {
        $this->linkData = $linkData;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function jsonSerialize() {
        return get_object_vars($this);
    }
}