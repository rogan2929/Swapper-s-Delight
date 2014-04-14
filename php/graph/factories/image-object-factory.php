<?php

require_once 'entities/include.php';

/**
 * Factory for ImageData creation.
 */
class ImageObjectFactory {
    
    private $imageStream;
    
    function __construct($imageStream) {
        $this->imageStream = $imageStream;
    }

    /**
     * With the given post and image array, construct an ImageData object.
     * 
     * @param type $post
     * @param type $smallImages
     * @return ImageData
     */
    public function getImageDataFromFQLResultSet($post, $smallImages = true) {
        $imageUrls = array();

        if ($post['attachment'] && $post['attachment']['media']) {
            // For posts with an image, look for associated image data.
            for ($i = 0; $i < count($post['attachment']); $i++) {
                if ($post['attachment']['media'][$i]) {
                    // Determine if this attachment is a photo or a link.
                    if ($post['attachment']['media'][$i]['type'] == 'photo' && $post['attachment']['media'][$i]['photo']) {
                        // Get image's unique Facebook Id
                        $fbid = $post['attachment']['media'][$i]['photo']['fbid'];

                        // Find the image url from the given Facebook ID
                        $imageUrls[] = $this->getImageDataFromFbId($fbid, $thumbnails);
                    }
                }
            }
        }

        return $imageUrls;
    }
    
    private function getImageDataFromFbId($fbid, $thumbnails = true) {
        $imageUrl = new ImageData();

        for ($i = 0; $i < count($this->imageStream); $i++) {
            if ($fbid == $this->imageStream[$i]['object_id']) {
                // See if we are trying to retrieve a small image. (Usually last in the array.)
                if ($thumbnails) {
                    $imageUrl = $this->getSmallImageUrl($this->imageStream[$i]['images']);
                } else {
                    //$imageUrl = $images[$i]['images'][$index]['source'];
                    $imageUrl = $this->getLargeImageUrl($this->imageStream[$i]['images']);
                }

                break;
            }
        }

        return $imageUrl;
    }
    
        private function getLargeImageUrl($image) {
        return $image[0]['source'];
    }

    /*     * *
     * In an array, find the smallest Facebook image.
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