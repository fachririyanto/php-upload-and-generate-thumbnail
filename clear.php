<?php
/**
 * Validate action.
 */
if (isset($_POST['button--clear'])) {
    // get files
    $files = glob('upload/*');

    // delete files
    if (!empty($files)) {
        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    // return to demo
    header('location: index.php?clear=true');
    exit;
} else {
    die('Cheating?');
}