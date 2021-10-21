<?php

function isimage(){
$type=$_FILES['fileToUpload']['type'];

$extensions=array('image/jpg','image/jpe','image/jpeg','image/jfif','image/png','image/bmp','image/dib','image/gif');
    if(in_array($type, $extensions))
  	{
  		$info = getimagesize($_FILES['fileToUpload']['tmp_name']);
  		if ($info === FALSE) {
  		return false;
  		}else{return true;}
  	}
    else
    {return false;}
}

function isaudio(){
$type=$_FILES['fileToUpload']['type'];

$extensions=array('audio/mp3','audio/wav','audio/webm','audio/ogg','audio/flac','audio/mpeg');
    if(in_array($type, $extensions))
	    {return true;}
    else
      {return false;}
}

echo "start Upload.";
$id=$_POST["id"];
$type=$_POST["type"];
echo "Here it is.";
//$path_parts[''];
$filename=$_FILES["fileToUpload"]["name"];
$tmp = explode('.', $filename);
$extension = end($tmp);
if($type=="deck_img"){$target_dir = "deck_img/";$target_file = $target_dir ."deck_".$id.".png";}
elseif($type=="card_img"){$target_dir = "card_img/";$target_file = $target_dir ."card_".$id.".png";}
elseif($type=="card_audio"){$target_dir = "card_audio/";$target_file = $target_dir ."card_".$id.".wav";}
elseif($type=="deck_audio"){$target_dir = "deck_audio/";$target_file = $target_dir ."deck_".$id.".wav";}
elseif($type=="deck_poster"){$target_dir = "deck_poster/";$target_file = $target_dir ."deck_".$id.".png";}
//$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
echo "Here it come.";
echo 'type:'.$_FILES['fileToUpload']['type'];
if($_FILES["fileToUpload"]["size"]<1024*1024*50)
{
  echo "Beware.";
  if(isimage()){echo "isImage";}else{echo "isNotImage";}
  if(isaudio()){echo "isAudio";}else{echo "isNotAudio";}
  if(isimage() || isaudio()){
    echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " will be be uploaded.";
      if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
          echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
      } else {
          echo "Sorry, there was an error uploading your file.";
      }
  }
}else
{echo "File too big.(max 10Mo)";}
?>
