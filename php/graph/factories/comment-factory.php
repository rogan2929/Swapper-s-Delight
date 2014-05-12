<?php

require 'graph-object-factory.php';
require 'user-factory.php';
require 'image-object-factory.php';

/*
 * Factory for Comment objects.
 */

class CommentFactory extends GraphObjectFactory {

    /**
     * Retrieve comments for a single post.
     * @param Post $post
     * @return array
     */
    public function getSinglePostComments($post) {
        $response = $this->graphApiClient->executeRequest('GET', '/' . $post->getId() . '/comments', array(
            'date_format' => 'U',
            'fields' => 'message,created_time,from,attachment'
        ));
        
        $comments = array();
        
        for ($i = 0; $i < count($response->data); $i++) {
            $comment = new Comment();
            $respComment = $response->data[$i];

            $comment->setId($respComment->id);
            $comment->setCreatedTime($respComment->created_time);
            
            $userFactory = new UserFactory();
            $comment->setActor($userFactory->getUserFromGraphResponse($respComment->from));
            $user = $userFactory->getSinglePostUserData($comment);
            
            $comment->setActor($user);
            
            if (isset($respComment->message)) {
                $comment->setMessage($respComment->message);
            }
            
            $comments[] = $comment;
        }

        return $comments;
    }

    /**
     * Parse an FQL result and construct an array of Comment entities.
     * @param type $commentStream
     */
    public function getCommentsFromFQL($commentStream, $userStream, $imageStream) {
        $comments = array();
        $usrFactory = new UserFactory();
        $imgFactory = new ImageObjectFactory($imageStream);

        // Begin parsing comment data.

        for ($i = 0; $i < count($commentStream); $i++) {
            $comment = new Comment();

            $comment->setId($commentStream[$i]['id']);

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
            $comment->setImageObjects($imgFactory->getImageObjectsFromFQLComment($commentStream[$i]));

            // Add the comment to the array.
            $comments[] = $comment;
        }

        return $comments;
    }

    /**
     * Post a comment.
     * @param type $postId
     * @param type $comment
     * @return Comment
     */
    public function postComment($postId, $comment) {
        // Post the comment and get the response
        //$id = $this->graphApiClient->api('/' . $postId . '/comments', 'POST', array('message' => $comment));
        $id = $this->graphApiClient->executeRequest('POST', '/' . $postId . '/comments', array(
            'message' => $comment
        ));

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

        $comment->setActor((new UserFactory())->createUser($newComment['user']));

        // For each comment, look for associated image attachment.
        //$comment->setImageObjects($imgFactory->getImageObjectsFromFQLComment($newComment, false));
        $comment->setImageObjects(array());

        return $comment;
    }

    /**
     * Update a comment.
     * @param string $id
     * @param string $message
     */
    public function updateComment($id, $message) {
        $this->graphApiClient->executeRequest('POST', '/' . $id, array(
            'message' => $message,
        ));

        return nl2br($message);
    }

}
