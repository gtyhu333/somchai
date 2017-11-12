<?php
/*
 * PHP function to resize an image maintaining aspect ratio
 * http://salman-w.blogspot.com/2008/10/resize-images-using-phpgd-library.html
 *
 * Creates a resized (e.g. thumbnail, small, medium, large)
 * version of an image file and saves it as another file
 */
define('THUMBNAIL_IMAGE_MAX_WIDTH', 500);
define('THUMBNAIL_IMAGE_MAX_HEIGHT', 500);

function resizeImage($source_image_path, $ext, $prefix = null)
{
    list(
        $source_image_width, $source_image_height, $source_image_type
    ) = getimagesize($source_image_path);

    switch ($source_image_type) {
        case IMAGETYPE_GIF:
            $source_gd_image = imagecreatefromgif($source_image_path);
            break;
        case IMAGETYPE_JPEG:
            $source_gd_image = imagecreatefromjpeg($source_image_path);
            break;
        case IMAGETYPE_PNG:
            $source_gd_image = imagecreatefrompng($source_image_path);
            break;
    }

    if ($source_gd_image === false) {
        throw new Exception("Cannot process this image type", 1);
    }

    $source_aspect_ratio = $source_image_width / $source_image_height;
    $thumbnail_aspect_ratio = THUMBNAIL_IMAGE_MAX_WIDTH / THUMBNAIL_IMAGE_MAX_HEIGHT;

    if ($source_image_width <= THUMBNAIL_IMAGE_MAX_WIDTH && $source_image_height <= THUMBNAIL_IMAGE_MAX_HEIGHT) {
        // If the source image is smaller than the target dimensions, then we won't reize it.
        $thumbnail_image_width = $source_image_width;
        $thumbnail_image_height = $source_image_height;
    } elseif ($thumbnail_aspect_ratio > $source_aspect_ratio) {
        // If the target aspect ratio is bigger than the source's aspect ratio,
        // we will resize the source image's height to match the target aspect ratio.
        $thumbnail_image_width = (int) (THUMBNAIL_IMAGE_MAX_HEIGHT * $source_aspect_ratio);
        $thumbnail_image_height = THUMBNAIL_IMAGE_MAX_HEIGHT;
    } else {
        // If the target aspect ratio is bigger than the source's aspect ratio,
        // we will resize the source image's width to match the target aspect ratio.
        $thumbnail_image_width = THUMBNAIL_IMAGE_MAX_WIDTH;
        $thumbnail_image_height = (int) (THUMBNAIL_IMAGE_MAX_WIDTH / $source_aspect_ratio);
    }

    $thumbnail_gd_image = imagecreatetruecolor(
        $thumbnail_image_width, $thumbnail_image_height
    );

    imagecopyresampled(
        $thumbnail_gd_image, $source_gd_image, 
        0, 0, 0, 0, 
        $thumbnail_image_width, $thumbnail_image_height, 
        $source_image_width, $source_image_height
    );

    $filename = $prefix ? uniqid($prefix, true) : uniqid(rand(), true);
    $filename = str_replace('.', '-', $filename);

    $newfilepath = "images/{$filename}.{$ext}";

    imagejpeg($thumbnail_gd_image, $newfilepath, 100);
    imagedestroy($source_gd_image);
    imagedestroy($thumbnail_gd_image);

    return '/' . $newfilepath;
}
