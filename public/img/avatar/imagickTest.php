<?PHP
$svgEye = file_get_contents('./eye1.svg');
$svgBody = file_get_contents('./body1.svg');
$svg = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
    <svg width="800" height="800" viewport="0 0 800 800" version="1.1" xmlns="http://www.w3.org/2000/svg">
      '.$svgBody.$svgEye.'
        <defs>
            <clipPath id="myClip">
                <circle cx="30" cy="30" r="20"></circle>
                <circle cx="70" cy="70" r="20"></circle>
            </clipPath>
        </defs>

        <rect x="10" y="10" width="100" height="100" clip-path="url(#myClip)"></rect>

    </svg>';

    $image = new \Imagick();

    $image->readImageBlob($svg);
    $image->setImageFormat("jpg");
    header("Content-Type: image/jpg");
    echo $image->getImageBlob();
    ?>
