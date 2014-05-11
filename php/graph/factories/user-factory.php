<?php

require 'graph-object-factory.php';

/*
 * Factory for user objects.
 */

class UserFactory extends GraphObjectFactory {

    /**
     * Create a user object from the provided graph response.
     * @param type $response
     * @return \User
     */
    public function getUserFromGraphResponse($response) {
        // Create user object.
        $user = new User();
        $user->setUid($response->id);

        // Get full name.
        if (isset($response->name)) {
            $names = preg_split("/[\s,]+/", $response->name);

            $user->setFirstName($names[0]);
            $user->setLastName($names[1]);
        }
        else {
            $user->setFirstName($response->first_name);
            $user->setLastName($response->last_name);
        }

        if (isset($response->picture)) {
            $user->setPicSquare($response->picture->data->url);
            $user->setPicFull($response->picture->data->url);
        }

        if (isset($response->link)) {
            $user->setProfileUrl($response->link);
        }

        return $user;
    }
    
    /**
     * Get user data for the array of posts.
     * @param type $posts
     * @return type
     */
    public function getPostUserData($posts) {
        $requests = array();
        $users = array();

        // Generate requests for additional data.
        for ($i = 0; $i < count($posts); $i++) {
            $actor = $posts[$i]->getActor();

            $requests[] = array(
                'method' => 'GET',
                'relative_url' => '/' . $actor->getUid() . '?fields=first_name,last_name,link,picture'
            );
        }

        // Execute the batch queries
        $response = $this->graphApiClient->executeRequest('POST', '/', array(
            'batch' => json_encode($requests),
            'include_headers' => false
        ));

        // Parse the user request responses.
        for ($j = 0; $j < count($response); $j++) {
            $users[] = $this->getUserFromGraphResponse(json_decode($response[$j]->body));
        }

        return $users;
    }
    
    /**
     * Get user data for a single post.
     * @param Post $post
     * @return User
     */
    public function getSinglePostUserData($post) {
        $actor = $post->getActor();
        
        $response = $this->graphApiClient->executeRequest('GET', '/' . $actor->getUid() . '?fields=first_name,last_name,link,picture');
        
        error_log(json_encode($response));
        
        $user = $this->getUserFromGraphResponse($response);
        
        return $user;
    }

}
