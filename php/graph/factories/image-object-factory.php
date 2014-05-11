<?php

require $_SERVER['DOCUMENT_ROOT'] . '\php\graph\entities\include.php';

/**
 * Factory for Image objects.
 */
class ImageObjectFactory {

    private $imageStream;

    /**
     * Constructor
     * @param array $imageStream
     */
    function __construct($imageStream) {
        $this->imageStream = $imageStream;
    }
    
    /**
     * Retrieve image objects for a single post.
     * @param type $post
     * @return array
     */
    public static function getSinglePostImageObjects($post) {
        return array();
    }

    /**
     * Get first image from graph response.
     * @param type $response
     */
    private static function getFirstImageFromGraphResponse($response) {
        $image = new Image();
        $image->setId($response->id);
        $image->setUrl($response->source);
        
        return $image;
    }
    
    public function getPostImageData($posts) {
        $requests = array();
        $images = array();

        for ($i = 0; $i < count($posts); $i++) {
            $post = $posts[$i];

            // Try to see if this post has a primary image.
            $image = $post->getFirstImage();

            if (!is_null($image)) {
                $requests[] = array(
                    'method' => 'GET',
                    'relative_url' => '/' . $image->getId() . '?fields=id,source'
                );
            }
        }

        // Execute the batch queries.
        $response = $this->graphApiClient->executeRequest('POST', '/', array(
            'batch' => json_encode($requests),
            'include_headers' => false
        ));

        for ($j = 0; $j < count($response); $j++) {
            $images[] = ImageObjectFactory::getFirstImageFromGraphResponse(json_decode($response[$j]->body));
        }

        return $images;
    }

    /**
     * Parse an FQL stream result and construct an array of Image entities.
     * @param type $post
     * @param type $smallImages
     * @return array
     */
    public function getImageObjectsFromFQL($post, $smallImages = true) {
        $images = array();

        if ($post['attachment'] && isset($post['attachment']['media'])) {
            // For posts with an image, look for associated image data.
            for ($i = 0; $i < count($post['attachment']); $i++) {
                if (isset($post['attachment']['media'][$i])) {
                    // Determine if this attachment is a photo or a link.
                    if ($post['attachment']['media'][$i]['type'] == 'photo' && $post['attachment']['media'][$i]['photo']) {
                        // Get image's unique Facebook Id
                        $fbid = $post['attachment']['media'][$i]['photo']['fbid'];

                        // Find the image url from the given Facebook ID
                        $images[] = $this->createImageObject($fbid, $smallImages);
                    }
                }
            }
        }

        return $images;
    }

    /**
     * Parse an FQL comment result and construct an array of Image entities.
     * @param type $comment
     * @return array
     */
    public function getImageObjectsFromFQLComment($comment) {
        $images = array();

        if ($comment['attachment'] && $comment['attachment']['type'] == 'photo' && $comment['attachment']['media'] && $comment['attachment']['media']['image']) {
            $image = new Image();
            $image->setUrl($comment['attachment']['media']['image']['src']);

            $images[] = $image;
        }

        return $images;
    }

    /**
     * Create an image object with the given ID.
     * @param type $fbid
     * @param type $smallImages
     * @return \Image
     */
    private function createImageObject($fbid, $smallImages = true) {
        $image = new Image();

        // Loop through the imageStream and try to find a match for the given FBID.
        for ($i = 0; $i < count($this->imageStream); $i++) {
            if ($fbid == $this->imageStream[$i]['object_id']) {
                // See if we are trying to retrieve a small image. (Usually last in the array.)
                if ($smallImages === true) {
                    $image->setUrl($this->getSmallImageUrl($this->imageStream[$i]['images']));
                } else {
                    //$imageUrl = $images[$i]['images'][$index]['source'];
                    $image->setUrl($this->getLargeImageUrl($this->imageStream[$i]['images']));
                }

                break;
            }
        }

        return $image;
    }

    /**
     * Finds the image source URL of the largest image in the FQL image stream.
     * @param array $image
     * @return string
     */
    private function getLargeImageUrl($image) {
        return $image[0]['source'];
    }

    /**
     * Finds the image source of the URL of the smallest image in the FQL image stream.
     * @param array $image
     * @return string
     */
    private function getSmallImageUrl($image) {
        // Grab the 'middle' image for a scaled version of the full size image.
        $index = intval(floor((count($image) / 2)));

        // Try to ensure a minimum width. If it is too small, then proceed to the next largest
        // image in the image collection. (0 being the largest).
        do {
            $imageSize = getimagesize($image[$index]['source']);
            $index--;

            if ($index < 0) {
                $index = 0;
                break;
            }
        } while ($imageSize[0] < 250 && $imageSize[1] < 150);

        return $image[$index]['source'];
    }

}
