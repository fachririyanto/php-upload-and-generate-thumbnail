<?php
/**
 * Get image from input.
 */
$image = isset($_FILES['image-file']) ? $_FILES['image-file'] : '';

/**
 * Data validation.
 */
if (empty($image) || empty($image['name'])) {
    header('location: index.php?message=empty_image');
    exit;
}

// check file type
$allowedTypes = array('image/png', 'image/jpg', 'image/jpeg');
if (!in_array($image['type'], $allowedTypes)) {
    header('location: index.php?message=invalid_file_type');
    exit;
}

// check file size
if ($image['size'] > 1000000) {
    header('location: index.php?message=invalid_file_size');
    exit;
}

/**
 * Create upload dir if it doesn't exist.
 */
if (!file_exists('upload')) {
    mkdir('upload', 0777, true);
}

/**
 * Define image data.
 */
$imageBaseName = basename($image['name']);
$imageFileType = strtolower(pathinfo($imageBaseName, PATHINFO_EXTENSION));
$imageNames    = array(
    'original' => 'image.' . $imageFileType,
    'mobile'   => 'image-mobile.' . $imageFileType,
    'tablet'   => 'image-tablet.' . $imageFileType,
    'desktop'  => 'image-desktop' . $imageFileType
);

/**
 * Remove file if exists.
 */
$files = glob('upload/*');
foreach ($files as $index => $file) {
    if (file_exists($file)) {
        unlink($file);
    }
}

/**
 * Upload data to server.
 */
$isUploaded = move_uploaded_file($image['tmp_name'], 'upload/' . $imageNames['original']);
if ($isUploaded) {
    /**
     * Resize image.
     */
    require_once('class/image.php');
    $Image = new Image();
    $isResized = $Image->generateThumbnail('upload/' . $imageNames['original'], array(
        array(
            'name'   => 'phone',
            'width'  => 480,
            'height' => false // false meaning is auto resize by image ratio
        ),
        array(
            'name'   => 'tablet',
            'width'  => 768,
            'height' => false
        ),
        array(
            'name'   => 'desktop',
            'width'  => 1024,
            'height' => false
        )
    ));

    // return to form page
    if ($isResized) {
        header('location: index.php?message=uploaded');
    } else {
        header('location: index.php?message=resize_failed');
    }
} else {
    header('location: index.php?message=upload_failed');
}
exit;