<?php

require 'base-factory.php';

/**
 * A factory for LinkData objects.
 */
class LinkDataFactory extends BaseFactory {
    
    /**
     * Parse an FQL stream result and construct a LinkData entity.
     * @param type $post
     * @return /LinkData
     */
    function getLinkDataFromFQL($post) {
        $linkData = null;

        // Verify that the attachment type is of type 'link'.
        if ($post['attachment'] && $post['attachment']['media'] && $post['attachment']['media'][0] && $post['attachment']['media'][0]['type'] == 'link') {
            $linkData = new LinkData();
            $linkData->setCaption($post['attachment']['caption']);
            $linkData->setDescription($post['attachment']['description']);
            $linkData->setHref($post['attachment']['media'][0]['href']);
            $linkData->setName($post['attachment']['name']);
            $linkData->setSrc($post['attachment']['media'][0]['src']);
        }

        return $linkData;
    }
}