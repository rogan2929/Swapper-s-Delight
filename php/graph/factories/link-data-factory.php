<?php

require 'graph-object-factory.php';

/**
 * A factory for LinkData objects.
 */
class LinkDataFactory extends GraphObjectFactory {

    /**
     * Parse a Graph response and construct a LinkData entity.
     * @param type $response
     * @return \LinkData
     */
    public function getLinkDataFromGraphResponse($response) {
        $linkData = null;

        if (isset($response->link)) {
            $linkData = new LinkData();
            $linkData->setCaption($response->caption);
            $linkData->setDescription($response->description);
            $linkData->setName($response->name);
            $linkData->setSrc($response->picture);
            $linkData->setHref($response->link);
        }

        return $linkData;
    }

}
