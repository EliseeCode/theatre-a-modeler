<?php
echo "test";
$usmap = './original.svg';
$im = new Imagick();
$svg = file_get_contents($usmap);
//echo $svg;
/*loop to color each state as needed, something like*/
// $idColorArray = array(
//      "AL" => "339966"
//     ,"AK" => "0099FF"
//     ...
//     ,"WI" => "FF4B00"
//     ,"WY" => "A3609B"
// );

// foreach($idColorArray as $state => $color){
// //Where $color is a RRGGBB hex value
//     $svg = preg_replace(
//          '/id="'.$state.'" style="fill:#([0-9a-f]{6})/'
//         , 'id="'.$state.'" style="fill:#'.$color
//         , $svg
//     );
// }
$svg = preg_replace(
         '/inkscape:label="eye1"[ ]*\n[ ]*style="display:inline"/'
        , 'inkscape:label="eye1" style="display:none"'
        , $svg
    );
$svg = preg_replace(
             '/inkscape:label="body1"[ ]*\n[ ]*style="display:inline"/'
            , 'inkscape:label="body1" style="display:none"'
            , $svg
        );
$svg = preg_replace(
             '/style="stop-color:#edff00;stop-opacity:1"/'
            , 'style="stop-color:#ff0000;stop-opacity:1"'
            , $svg
        );
$svg = preg_replace(
             '/style="stop-color:#0024ff;stop-opacity:1"/'
            , 'style="stop-color:#0000ff;stop-opacity:1"'
            , $svg
        );
  
echo $svg;

$im->readImageBlob($svg);
echo "2";
/*png settings*/
$im->setImageFormat("png24");
echo "3";
$im->resizeImage(720, 445, imagick::FILTER_LANCZOS, 1);  /*Optional, if you need to resize*/
echo "4";
/*jpeg*/
// $im->setImageFormat("jpeg");
// $im->adaptiveResizeImage(720, 445); /*Optional, if you need to resize*/

$flag=$im->writeImage('C:\wamp\www\vocabulaire\avatar/original3.png');/*(or .jpg)*/
if($flag){echo 'ok';}
  echo "5";
$im->clear();
echo "6";
$im->destroy();
?>
