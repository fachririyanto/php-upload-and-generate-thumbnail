<?php
/**
 * Image class.
 * @version 0.0.1
 * @author Fachri Riyanto
 */
class Image {

    /**
     * Generate image thumbnails.
     * @param string $imagepath
     * @param array $sizes (
     *    @param array (
     *       @param string name
     *       @param int width
     *       @param int height
     *    )
     * )
     * @return bool
     * @since 0.0.1
     */
    function generateThumbnail($imagepath, $sizes = array()) {
        // validate file exists
        if (!file_exists($imagepath)) return false;

        // if sizes not defined
        if (empty($sizes)) return false;

        // get image base info
        $imagename = basename($imagepath);
        $imageext  = strtolower(pathinfo($imagepath, PATHINFO_EXTENSION));

        // create image from path
        $image = false;
        switch ($imageext) {
            case 'jpg':
            case 'jpeg':
                $image = imagecreatefromjpeg($imagepath);
                break;

            case 'png':
                $image = imagecreatefrompng($imagepath);
                break;
        }
        if (!$image) return false;

        // get original image resolution
        $imageResolution = array(
            'width'  => imagesx($image),
            'height' => imagesy($image)
        );

        // create resized image by sizes
        foreach ($sizes as $index => $size) {
            // if dont have size
            if (!isset($size['width']) && !isset($size['height'])) continue;
            if (!$size['width'] && !$size['height']) continue;

            // define resized image object
            $imagewidth  = 0;
            $imageheight = 0;

            // if size have bot width and height
            if ($size['width'] && $size['height']) {
                $imagewidth  = $size['width'];
                $imageheight = $size['height'];

                // validate if resized size is larger than original size
                if ($imagewidth > $imageResolution['width'] || $imageheight > $imageResolution['height']) {
                    $imagewidth  = $imageResolution['width'];
                    $imageheight = $imageResolution['height'];
                }
            } else {
                // if size dont have width
                if (!$size['width']) {
                    if ($size['height'] > $imageResolution['height']) {
                        // if resized size larger than original size
                        // use original size
                        $imageheight = $imageResolution['height'];
                        $imagewidth  = $imageResolution['width'];
                    } else {
                        $imageheight = $size['height'];
                        $imagewidth  = $size['height'] / $imageResolution['height'] * $imageResolution['width'];
                    }
                }

                // if size dont have height
                elseif (!$size['height']) {
                    if ($size['width'] > $imageResolution['width']) {
                        // if resized size larger than original size
                        // use original size
                        $imagewidth  = $imageResolution['width'];
                        $imageheight = $imageResolution['height'];
                    } else {
                        $imagewidth  = $size['width'];
                        $imageheight = $size['width'] / $imageResolution['width'] * $imageResolution['height'];
                    }
                }
            }

            // create thumbnail
            $thumbnail = imagecreatetruecolor($imagewidth, $imageheight);
            imagecopyresampled(
                $thumbnail,
                $image,
                0,
                0,
                0,
                0,
                $imagewidth,
                $imageheight,
                $imageResolution['width'],
                $imageResolution['height']
            );

            // move file to upload file
            $newImageName = 'upload/image-' . $size['name'] . '.' . $imageext;
            switch ($imageext) {
                case 'jpg':
                case 'jpeg':
                    imagejpeg($thumbnail, $newImageName, 72);
                    break;
    
                case 'png':
                    imagepng($thumbnail, $newImageName, 9);
                    break;
            }
        }
        return true;
    }
}