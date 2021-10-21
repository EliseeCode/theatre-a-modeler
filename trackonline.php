<?php
include "db.php";
if (!empty($_GET['user_id']) && !empty($_GET['date'])) {

$user_id = (int)$_GET['user_id'];
$sendingDate = htmlspecialchars($_GET['date']);
$openingDate= date("Y-m-d");
$subject = htmlspecialchars($_GET['subject']);
$sql='UPDATE emailTracker SET openingDate="'.$openingDate.'" WHERE user_id='.$user_id.' AND sendingDate="'.$sendingDate.'" AND sujet="'.$subject.'"';
//echo ($sql);
$mysqli->query($sql);

//Get the http URI to the image
$graphic_http = 'https://www.exolingo.com/img/pxblanc.png';

//Get the filesize of the image for headers
$filesize = filesize('img/pxblanc.png');

//Now actually output the image requested, while disregarding if the database was affected
header('Pragma: public');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private', false);
header('Content-Disposition: attachment; filename="img/pxblanc.png"');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . $filesize);
readfile($graphic_http);

//All done, get out!
exit;
}
?>
