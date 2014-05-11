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
            $linkData->setHref($response->link);

            if (isset($response->caption)) {
                $linkData->setCaption($response->caption);
            }
            if (isset($response->description)) {
                $linkData->setDescription($response->description);
            }
            if (isset($response->name)) {
                $linkData->setName($response->name);
            }
            if (isset($response->picture)) {
                $linkData->setSrc($response->picture);
            }
        }

        return $linkData;
    }

}
