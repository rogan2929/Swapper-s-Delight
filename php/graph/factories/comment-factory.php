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
    
    /**
     * Post a comment on a post.
     * @param type $postId
     * @param type $comment
     * @return type
     */
    public function postComment($postId, $comment) {
        // Post the comment and get the response
        $id = $this->graphApiClient->api('/' . $postId . '/comments', 'POST', array('message' => $comment));

        // Get the comment and associated user data...
        $queries = array(
            'commentQuery' => PostFactory::COMMENT_QUERY . 'WHERE id=' . $id['id'],
            'commentUserQuery' => PostFactory::USER_QUERY . 'WHERE uid IN (SELECT fromid FROM #commentQuery)',
            'commentImageQuery' => PostFactory::IMAGE_QUERY . 'WHERE object_id IN (SELECT attachment FROM #commentQuery)'
        );

        // Query Facebook's servers for the necessary data.
        $response = $this->graphApiClient->api(array(
            'method' => 'fql.multiquery',
            'queries' => $queries
        ));

        // Construct a return object.
        $newComment = $response[0]['fql_result_set'][0];
        $newComment['user'] = $response[1]['fql_result_set'][0];

        // Replace any line breaks with <br/>
        if ($newComment['text']) {
            $newComment['text'] = nl2br($newComment['text']);
        }
        
        // Image factory.
        $imgFactory = new ImageObjectFactory($response[2]['fql_result_set']);
        
        // Create an entity object.
        $comment = new Comment();
        $comment->setId($newComment['id']);
        $comment->setMessage($newComment['text']);
        $comment->setCreatedTime($newComment['time']);
        
        $comment->setActor((new UserFactory())->createUser($newComment['user']));
        
        // For each comment, look for associated image attachment.
        //$comment->setImageObjects($imgFactory->getImageObjectsFromFQLComment($newComment, false));
        $comment->setImageObjects(array());

        return $comment;
    }
}