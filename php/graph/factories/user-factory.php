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
    public static function getUserFromGraphResponse($response) {
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

}
