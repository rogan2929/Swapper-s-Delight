<?php

require 'base-factory.php';
require 'user-factory.php';
require 'image-object-factory.php';

/*
 * Factory for comment objects.
 */
class CommentFactory extends BaseFactory {
    
    /**
     * Parse an FQL result and construct an array of Comment entities.
     * @param type $commentStream
     */
    public function getCommentsFromFQLResultSet($commentStream, $userStream, $imageStream) {
        $comments = array();
        $usrFactory = new UserFactory();
        $imgFactory = new ImageObjectFactory($imageStream);
        
        error_log(json_encode($imageStream));
        
        // Begin parsing comment data.
        
        for ($i = 0; $i < count($commentStream); $i++) {
            $comment = new Comment();
            
            if (!is_null($commentStream[$i]['text'])) {
                $comment->setMessage(nl2br($commentStream[$i]['text']));
            }
            
            $comment->setCreatedTime($commentStream[$i]['time']);
            
            // For each comment, attach user data to it.
            for ($j = 0; $j < count($userStream); $j++) {
                $user = $userStream[$j];

                // See if the comment is from the user.
                if ($commentStream[$i]['fromid'] == $user['uid']) {
                    $comment->setActor($usrFactory->createUser($user));
                    break;
                }
            }
            
            // For each comment, look for associated image attachment.
            $comment->setImageObjects($imgFactory->getImageObjectsFromFQLComment($commentStream[$i], false));
            
            // Add the comment to the array.
            $comments[] = $comment;
        }
        
        return $comments;
    }
}