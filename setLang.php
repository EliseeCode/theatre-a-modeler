<?php
include_once ("db.php");
session_start();

$explodeURI=explode('/',$_SERVER['REQUEST_URI']);
$_SESSION['url']=end($explodeURI);
if(!isset($_SESSION['user_id'])){header("location:checkLoginCookie.php");exit();}

$user_id=(int)($_SESSION['user_id']);
if(isset($_GET['target_lang'])){
$lang_id=(int)($_GET['target_lang']);
$sql="DELETE FROM user_target_lang WHERE user_id=".$user_id." AND lang_id=".$lang_id;
$mysqli->query($sql);
$sql="INSERT INTO user_target_lang (user_id, lang_id) VALUE (".$user_id.",".$lang_id.")";
$mysqli->query($sql);
$sql="SELECT lang_id,lang_code2,lang_code2_2,lang_code3 FROM lang WHERE lang_id=".$lang_id;
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();
$_SESSION["target_lang_code3"]=$row["lang_code3"];
$_SESSION["target_lang_code2"]=$row["lang_code2"];
$_SESSION["target_lang_code2_2"]=$row["lang_code2_2"];
$_SESSION["target_lang"]=(int)$row["lang_id"];
}
header("location:decks.php");exit();
?>
