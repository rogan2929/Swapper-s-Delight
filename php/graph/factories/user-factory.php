<?php

/*
 * Factory for user objects.
 */
class UserFactory {
    
    private $userStream;
    
    function __construct($userStream = null) {
        $this->userStream = $userStream;
    }

    /**
     * Create a user object from FQL provided user data.
     * @param type $user
     * @return \User
     */
    public function createUser($user) {
        $userObject = new User();
        $userObject->setUid($user['uid']);
        $userObject->setLastName($user['last_name']);
        $userObject->setFirstName($user['first_name']);
        $userObject->setPicSquare($user['pic_square']);
        $userObject->setProfileUrl($user['profile_url']);
        
        return $userObject;
    }
    
    /**
     * Parse the userStream and look for the user associated with the given post.
     * @param type $post
     * @return \User
     */
    public function getUserFromFQLResultSet($post) {
        $user = null;
        
        for ($i = 0; $i < count($this->userStream); $i++) {
            if ($post['actor_id'] == $this->userStream[$i]['uid']) {
                $user = $this->createUser($this->userStream[$i]);
                error_log(json_encode($this->userStream[$i]));
                break;
            }
        }

        return $user;
    }
}
