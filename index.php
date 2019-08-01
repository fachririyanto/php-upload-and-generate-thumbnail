<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Simple upload and resize file with PHP - Fachri Riyanto</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,700&display=swap">
        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/style.css">
    </head>

    <body>
        <div class="site" id="page">
            <header class="M--navigation">
                <nav class="navigation">
                    <div class="container--fluid">
                        <div class="U--table -full-height">
                            <div class="table__cell -auto-width -vertical-align--middle">
                                <a href="https://fachririyanto.com" class="navigation__logo">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100">
                                        <g transform="translate(-315 -31)">
                                            <g transform="translate(284)">
                                                <rect width="100" height="100" transform="translate(31 31)"/>
                                            </g>
                                            <text transform="translate(365 107)" fill="#fff" font-size="72" font-family="LibreBaskerville-Bold, Libre Baskerville" font-weight="700">
                                                <tspan x="-24.804" y="0">F</tspan>
                                            </text>
                                        </g>
                                    </svg>
                                </a>
                            </div>
                            <div class="table__cell -vertical-align--middle U--text-right">
                                <a class="navigation__link" href="https://github.com/fachririyanto/php-upload-and-generate-thumbnail">
                                    Fork on Github
                                </a>
                            </div>
                        </div>
                    </div>
                </nav>
            </header>
            <section class="M--uploadzone module--container">
                <div class="container">
                    <form class="M--form form--upload" action="upload.php" method="POST" enctype="multipart/form-data">
                        <div class="form__field">
                            <label class="field__label">Upload Image</label>
                            <div class="field__component">
                                <div class="uploadzone">
                                    <span class="U--table -full-height">
                                        <span class="table__cell -vertical-align--middle">
                                            <span id="uploadzone-label" class="uploadzone__label">No file selected.</span>
                                        </span>
                                        <span class="table__cell -auto-width">
                                            <span class="uploadzone__button">
                                                Choose Image
                                            </span>
                                        </span>
                                    </span>
                                    <input id="uploadzone-inputfile" type="file" name="image-file" class="uploadzone__input U--overlay-layout">
                                </div>
                            </div>
                            <small class="field__description">
                                Allowed image type: PNG, JPG, JPEG. <br/>Max. size 1MB.
                            </small>
                        </div>
                        <div class="field__action">
                            <div class="U--table">
                                <div class="table__cell -vertical-align--middle">
                                    <?php
                                        if (isset($_GET['message'])) {
                                            switch ($_GET['message']) {
                                                case 'empty_image':
                                                    ?>
                                                    <div class="form__message -is-error">
                                                        Empty image.
                                                    </div>
                                                    <?php
                                                    break;
                                                case 'invalid_file_type':
                                                    ?>
                                                    <div class="form__message -is-error">
                                                        Invalid file type.
                                                    </div>
                                                    <?php
                                                    break;
                                                case 'invalid_file_size':
                                                    ?>
                                                    <div class="form__message -is-error">
                                                        Invalid file size.
                                                    </div>
                                                    <?php
                                                    break;
                                                case 'upload_failed':
                                                    ?>
                                                    <div class="form__message -is-error">
                                                        Upload failed.
                                                    </div>
                                                    <?php
                                                    break;
                                                case 'resize_failed':
                                                    ?>
                                                    <div class="form__message -is-error">
                                                        Resize failed.
                                                    </div>
                                                    <?php
                                                    break;
                                                case 'uploaded':
                                                    ?>
                                                    <div class="form__message -is-ok">
                                                        Your image is uploaded and generated.
                                                    </div>
                                                    <?php
                                                    break;
                                            }
                                        }
                                    ?>
                                </div>
                                <div class="table__cell -auto-width">
                                    <button type="submit" class="button">Upload &amp; Generate</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </section>

            <?php
            /**
             * Get image files.
             */
            $images = glob('upload/*');
            ?>

            <?php if (!empty($images)) : ?>
                <section class="M--resultzone module--container">
                    <div class="container">
                        <header class="block--header">
                            <div class="U--table">
                                <div class="table__cell -vertical-align--middle">
                                    <h2 class="header__title">Image Results</h2>
                                </div>
                                <div class="table__cell -auto-width -vertical-align--middle">
                                    <form action="clear.php" method="post">
                                        <button type="submit" class="button" name="button--clear">
                                            Clear Results
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </header>
                        <div class="list__posts">
                            <?php
                                /**
                                 * Showing images.
                                 */
                                foreach ($images as $index => $image) {
                                    // get image base info
                                    $imageinfo = pathinfo($image);

                                    // create image from path
                                    $imageObj = false;
                                    switch ($imageinfo['extension']) {
                                        case 'jpg':
                                        case 'jpeg':
                                            $imageObj = imagecreatefromjpeg($image);
                                            break;

                                        case 'png':
                                            $imageObj = imagecreatefrompng($image);
                                            break;
                                    }
                                    ?>
                                    <article class="post">
                                        <figure class="post__thumbnail">
                                            <img src="<?php echo $image; ?>" alt="<?php echo $image; ?>">
                                        </figure>
                                        <div class="post__detail">
                                            <h3 class="post__title">
                                                <?php echo $imageinfo['basename']; ?>
                                                <?php echo $imageinfo['filename'] == 'image' ? '(original size)' : ''; ?>
                                            </h3>
                                            <span class="post__meta">
                                                Resolution: <?php echo imagesx($imageObj) . 'x' . imagesy($imageObj) . ' Pixel'; ?><br/>
                                                Size: <?php echo round(filesize($image) / 1000); ?> KB
                                            </span>
                                            <a href="<?php echo $image; ?>" target="_blank" class="button post__permalink">
                                                Open image in new tab
                                            </a>
                                        </div>
                                    </article>
                                    <?php
                                }
                            ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>
        </div>

        <script>
            window.onload = function() {
                /**
                 * Add input file event.
                 */
                var inputfile = document.getElementById('uploadzone-inputfile');
                inputfile.addEventListener('change', function(event) {
                    var files = event.target.files;
                    if (files && files[0]) {
                        // validate file size
                        if (files[0].size > 1000000) {
                            alert('File size is more than 1MB.');
                            return;
                        }

                        // validate file type
                        var allowedType = ['image/jpeg', 'image/png', 'image/jpg'];
                        if (allowedType.indexOf(files[0].type) === -1) {
                            alert('File type is invalid.');
                            return;
                        }

                        // if success, change uploadzone label to file name
                        document.getElementById('uploadzone-label').innerHTML = files[0].name;
                    }
                });
            };
        </script>
    </body>
</html>