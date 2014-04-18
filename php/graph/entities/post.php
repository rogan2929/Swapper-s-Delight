<?php

require_once 'graph-object.php';

/**
 * Graph API post entity. Loosely corresponds to the FQL stream table.
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

    /**
     * Gets the post's associated image objects.
     * @return array
     */
    public function getImageObjects() {
        return $this->imageObjects;
    }

    /**
     * Sets the post's associated image objects.
     * @param array $imageObjects
     */
    public function setImageObjects($imageObjects) {
        $this->imageObjects = $imageObjects;
    }

    /**
     * Gets the post's associated comment objects.
     * @return array
     */
    public function getComments() {
        return $this->comments;
    }

    /**
     * Sets the post's associated comment objects.
     * @param array $comments
     */
    public function setComments($comments) {
        $this->comments = $comments;
    }

    /**
     * Gets the post's permalink.
     * @return string
     */
    public function getPermalink() {
        return $this->permalink;
    }

    /**
     * Sets the post's permalink.
     * @param string $permalink
     */
    public function setPermalink($permalink) {
        $this->permalink = $permalink;
    }
    
    /**
     * Gets the post's unique ID.
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Gets the post's unique ID.
     * @param string $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Gets the post's message.
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * Gets the post's User object.
     * @return /User
     */
    public function getActor() {
        return $this->actor;
    }

    /**
     * Gets the comment count of the post.
     * @return int
     */
    public function getCommentCount() {
        return $this->commentCount;
    }

    /**
     * Gets the like count of the post.
     * @return int
     */
    public function getLikeCount() {
        return $this->likeCount;
    }

    /**
     * Gets whether or not the currently logged in user has 'liked' the post.
     * @return bool
     */
    public function getUserLikes() {
        return $this->userLikes;
    }

    /**
     * Gets associated LinkData object.
     * @return /LinkData
     */
    public function getLinkData() {
        return $this->linkData;
    }

    /**
     * Gets the post's type: image, text, link, or textlink.
     * This value is used client side to determine rendering of the post object.
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Sets the post's message.
     * @param string $message
     */
    public function setMessage($message) {
        $this->message = $message;
    }

    /**
     * Sets the User object for the post.
     * @param /User $actor
     */
    public function setActor($actor) {
        $this->actor = $actor;
    }

    /**
     * Sets the comment count of the post.
     * @param int $commentCount
     */
    public function setCommentCount($commentCount) {
        $this->commentCount = $commentCount;
    }

    /**
     * Sets the like count of the post.
     * @param int $likeCount
     */
    public function setLikeCount($likeCount) {
        $this->likeCount = $likeCount;
    }

    /**
     * Sets whether or not the currently logged in user has 'liked' the post.
     * @param bool $userLikes
     */
    public function setUserLikes($userLikes) {
        $this->userLikes = $userLikes;
    }

    /**
     * Set the LinkData object of the post.
     * @param /LinkData $linkData
     */
    public function setLinkData($linkData) {
        $this->linkData = $linkData;
    }

    /**
     * Gets the post's type: image, text, link, or textlink.
     * This value is used client side to determine rendering of the post object.
     * @param string $type
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * Implementaiton of JsonSerializable
     * @return array
     */
    public function jsonSerialize() {
        return get_object_vars($this);
    }
}