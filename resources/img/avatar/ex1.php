<?php
$image = new Imagick('C:\wamp\www\vocabulaire\avatar\mende.jpg');

// Si 0 est fourni comme paramètre de hauteur ou de largeur,
// les proportions seront conservées
$image->thumbnailImage(100, 0);

echo $image;
?>
