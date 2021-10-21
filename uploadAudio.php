<?php
session_start();
include "./db.php";

$save_folder = dirname(__FILE__) . "/card_audio";
if(! file_exists($save_folder)) {
  if(! mkdir($save_folder)) {
    die("failed to create save folder $save_folder");
  }
 }

$id=$_GET["id"];
$key = 'filename';
$tmp_name = $_FILES["audiofile"]["tmp_name"];
//$upload_name = $_FILES["audiofile"]["name"];
$upload_name = "card_".$id.".wav";
$type = $_FILES["audiofile"]["type"];
$filename = "$save_folder/$upload_name";
$saved = 0;
if(($type == 'audio/x-wav' || $type == 'application/octet-stream') && preg_match('/^[a-zA-Z0-9_\-]+\.wav$/', $upload_name) ) {
  $saved = move_uploaded_file($tmp_name, $filename) ? 1 : 0;
 }
if($saved){
	$mysqli->query("UPDATE cards SET hasAudio=1 WHERE card_id=".$id);
}
print ($saved ? "Saved ".$filename : 'Not saved');

exit;?>
