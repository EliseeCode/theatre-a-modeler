    <?php
    // $usmap = './original.svg';
    // $im = new Imagick();
    // $svg = file_get_contents($usmap);
function cleanSVG($svg)
{
  $svg = preg_replace(
           '/<\?xml version="1\.0" encoding="UTF-8" standalone="no"\?>/'
          , ''
          , $svg
      );
  $svg = preg_replace(
           '/<!-- Created with Inkscape \(http:\/\/www\.inkscape\.org\/\) -->/'
          , ''
          , $svg
      );
  return $svg;
}

              //
//$colorList1=["FDE005","FB2F86","C3F000","5FA0FE"];//clair
$colorList1=["FDF025","FB4F96","E3F020","7FC0FE"];//clair
$colorList2=["F47621","AB0163","3BBF00","8079EB"];//fonce
$colorList3=["FDE005","FB2F86","C3F000","5FA0FE"];//complementaire
$colorList4=["FDE005","FB2F86","C3F000","5FA0FE"];//complementaire
$svgEye=array();
$svgBody=array();
$svgBouche=array();
$svgOreille=array();
$svgBras = cleanSVG(file_get_contents('./avatar/bras1.svg'));
$svgShadow = cleanSVG(file_get_contents('./avatar/shadow.svg'));

foreach ([1,2,3,4,5] as $body_k) {
$svgBody[$body_k] = cleanSVG(file_get_contents('./avatar/body'.$body_k.'.svg'));
}
foreach ([1,2,3,4] as $bouche_k) {
$svgBouche[$bouche_k] = cleanSVG(file_get_contents('./avatar/bouche'.$bouche_k.'.svg'));
}
foreach ([1,2,3] as $eye_k) {
$svgEye[$eye_k] = cleanSVG(file_get_contents('./avatar/eye'.$eye_k.'.svg'));
}
foreach ([1,2,3] as $oreille_k) {
$svgOreille[$oreille_k] = cleanSVG(file_get_contents('./avatar/oreille'.$oreille_k.'.svg'));
}
$im = new \Imagick();
foreach ([1,2,3] as $oreille_k) {
  foreach ([1,2,3] as $eye_k) {
    foreach ([1,2,3,4] as $bouche_k) {
      foreach ([1,2,3,4,5] as $body_k) {
        foreach ([0,1,2,3] as $color_k) {
// foreach ([1,2,3] as $oreille_k) {
//   foreach ([1,2,3] as $eye_k) {
//     foreach ([1,2,3,4] as $bouche_k) {
//       foreach ([1,2,3,4,5] as $body_k) {
//         foreach ([0,1,2,3] as $color_k) {

$color1=$colorList1[$color_k];
$color2=$colorList2[$color_k];
$color3=$colorList3[rand(0,3)];
$color4=$colorList4[rand(0,3)];
$color5=$colorList4[rand(0,3)];
$ThissvgOreille=$svgOreille[$oreille_k];
$ThissvgBody=$svgBody[$body_k];
$ThissvgEye=$svgEye[$eye_k];
$ThissvgBouche=$svgBouche[$bouche_k];

$svg='<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
     width="750px" height="850px" viewBox="0 0 750 950">'
     .$svgShadow
     .$ThissvgOreille
     .$svgBras
     .$ThissvgBody
     .$ThissvgEye
     .$ThissvgBouche
     ."</svg>";
     //tatouage
     $svg = preg_replace(
              '/01fbff/'
             , $color4
             , $svg
         );
     //eyeColor
     $svg = preg_replace(
              '/ffff00/'
             , $color4
             , $svg
         );
      //eyeColor2
     $svg = preg_replace(
              '/00ff00/'
             , $color5
             , $svg
         );
     $svg = preg_replace(
              '/ff1744/'
             , $color1
             , $svg
         );
     $svg = preg_replace(
              '/ff8c96/'
             , $color1
             , $svg
         );
     $svg = preg_replace(
              '/8cffa3/'
             , $color2
             , $svg
         );
    //oreille1
     $svg = preg_replace(
              '/00ffff/'
             , $color2
             , $svg
         );
    //oreille
     $svg = preg_replace(
              '/ff0000/'
             , $color1
             , $svg
         );
echo $svg;

    //echo $svg;

    $im->setBackgroundColor(new ImagickPixel('transparent'));
    $im->readImageBlob($svg);
    $im->setImageFormat("png24");

    $im->resizeImage(200, 200, imagick::FILTER_LANCZOS, 1);  /*Optional, if you need to resize*/

    $im->writeImage('C:\wamp\www\vocabulaire\avatar\avatar\result\avatar'.$oreille_k.$eye_k.$bouche_k.$body_k.$color_k.'.png');/*(or .jpg)*/
    $im->clear();
  }
}
}
}
}
    ?>
