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
            
            error_log(json_encode($commentStream[$i]));
            
            // For each comment, look for associated image attachment.
            $comment->setImageObjects($imgFactory->getImageObjectsFromFQLComment($commentStream[$i], false));
            
            // Add the comment to the array.
            $comments[] = $comment;
        }
        
//        for ($i = 0; $i < count($post['comments']); $i++) {
//            // Replace any line breaks with <br/>
//            if ($post['comments'][$i]['text']) {
//                $post['comments'][$i]['text'] = nl2br($post['comments'][$i]['text']);
//            }
//
//            // Set image urls.
//            $post['comments'][$i]['image_url'] = array();
//
//            if ($post['comments'][$i]['attachment'] && $post['comments'][$i]['attachment']['media']) {
//                //echo var_dump($post['comments'][$i]['attachment']['media']['image']) . "<br/>";
//                $post['comments'][$i]['image_url'][] = $post['comments'][$i]['attachment']['media']['image']['src'];
//            }
//
//            unset($post['comments'][$i]['attachment']);
//
//            // For each comment, attach user data to it.
//            for ($j = 0; $j < count($response[4]['fql_result_set']); $j++) {
//                $userDataObject = $response[4]['fql_result_set'][$j];
//
//                // See if the comment is from the user.
//                if ($post['comments'][$i]['fromid'] == $userDataObject['uid']) {
//                    $post['comments'][$i]['user'] = $userDataObject;
//                    break;
//                }
//            }
//        }
        
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
            'commentUserQuery' => PostFactory::USER_QUERY . 'WHERE uid IN (SELECT fromid FROM #commentQuery)'
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
        
        // Create an entity object.
        $comment = new Comment();
        $comment->setId($newComment['id']);
        $comment->setMessage($newComment['text']);
        $comment->setCreatedTime($newComment['time']);
        
        $comment->setActor((new UserFactory())->createUser($comment['user']));

        return $comment;
    }
}