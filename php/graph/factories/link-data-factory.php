<?php

require_once '../entities/include.php';

/**
 * A factory to create LinkData objects.
 */
class LinkDataFactory {
    
    /**
     * With the given data, constrcut a LinkData object.
     * @param type $post
     * @return LinkData
     */
    function getLinkDataFromFQLResultSet($post) {
        $linkData = new LinkData();

        // See if the attachment type is of type 'link'.
        if ($post['attachment'] && $post['attachment']['media'] && $post['attachment']['media'][0] && $post['attachment']['media'][0]['type'] == 'link') {
            $linkData->setCaption($post['attachment']['caption']);
            $linkData->setDescription($post['attachment']['description']);
            $linkData->setHref($post['attachment']['media'][0]['href']);
            $linkData->setName($post['attachment']['name']);
            $linkData->setSrc($post['attachment']['media'][0]['src']);
        }

        return $linkData;
    }
}