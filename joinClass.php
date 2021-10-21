<?php
include_once ("db.php");
session_start();
include_once ("local_lang.php");
$explodeURI=explode('/',$_SERVER['REQUEST_URI']);
$_SESSION['url']=end($explodeURI);
if(!isset($_SESSION['user_id'])){header("location:checkLoginCookie.php");exit();}
$_SESSION['url']="";
$user_id=$_SESSION['user_id'];

if(!isset($_GET["code"])){
  $_SESSION['message'] = __("Code d'activation non indiqué");
  header("location: error.php");exit();}
$code=$_GET["code"];

$sql="SELECT class_id FROM `classes` WHERE code='".$code."'";
$result = $mysqli->query($sql);
$row=$result->fetch_assoc();
$class_id=$row["class_id"];
if(!$class_id)
{$_SESSION['message'] = __("Code d'activation inconnu");
header("location: error.php");exit();}
else
{
  $sql="SELECT COUNT(*) AS flag FROM `user_class` WHERE class_id=".$class_id." AND user_id=".$user_id;
  $result = $mysqli->query($sql);
  $row=$result->fetch_assoc();
  $flag=$row["flag"];
  if(!$flag){
    $sql = "INSERT INTO user_class (user_id,class_id,position) "
            . "VALUES (".$user_id.",".$class_id.",0)";
    $mysqli->query($sql);

    $sql="SELECT lang_id FROM `classes` WHERE class_id=".$class_id;
    $result = $mysqli->query($sql);
    $row = $result->fetch_assoc();
    $lang_id=$row["lang_id"];
    $result->free();
    $sql="DELETE FROM user_target_lang WHERE user_id=".$user_id." AND lang_id=".$lang_id;
    $mysqli->query($sql);
    $sql="INSERT INTO user_target_lang (user_id, lang_id) VALUE (".$user_id.",".$lang_id.")";
    $mysqli->query($sql);

    header("location: decks.php?categorie=myClass&class_id=".$class_id);exit();

  }else{
    $_SESSION['message'] = __("Vous êtes déjà dans la classe");
    header("location: error.php");exit();
  }

}



//$sql = "INSERT INTO deck_class (deck_id,class_id,visible) "      . "VALUES (".$deck_id.",".$class_id.",0)";
//$mysqli->query($sql);
