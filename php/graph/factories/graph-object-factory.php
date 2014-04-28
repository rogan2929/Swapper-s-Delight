<?php

require $_SERVER['DOCUMENT_ROOT'] . '\php\graph\graph-api-client.php';
require $_SERVER['DOCUMENT_ROOT'] . '\php\graph\entities\include.php';

/**
 * Base factory class.
 */
class GraphObjectFactory {

    protected $graphApiClient;
    
    
    // FQL Query Strings
    const DETAILS_QUERY = 'SELECT post_id,message,actor_id,permalink,like_info,share_info,comment_info,tagged_ids,attachment,created_time,updated_time FROM stream ';
    const STREAM_QUERY = 'SELECT post_id,actor_id,updated_time,message,attachment,comment_info,created_time,like_info FROM stream ';
    const USER_QUERY = 'SELECT uid,last_name,first_name,pic_square,profile_url,pic FROM user ';
    const COMMENT_QUERY = 'SELECT fromid,text,text_tags,attachment,time,id FROM comment ';
    const IMAGE_QUERY = 'SELECT object_id,images FROM photo ';

    /**
     * Default constructor.
     */
    function __construct() {
        $this->graphApiClient = new GraphApiClient();
    }

    /**
     * Delete a Facebook Object.
     * @param type $id
     */
    public function deleteObject($id) {
        return $this->graphApiClient->api('/' . $id, 'DELETE');
    }

    /**
     * Like a Facebook object.
     * @param string $id
     */
    public function likeObject($id) {
        return $this->graphApiClient->api('/' . $id . '/likes', 'POST', array('user_likes' => true));
    }

    /**
     * Unlike a Facebook object.
     * @param type $id
     */
    public function unLikeObject($id) {
        return $this->graphApiClient->api('/' . $id . '/likes', 'DELETE');
    }

}
