<?php
include_once ("db.php");
session_start();
$action=htmlspecialchars($_GET['action']);
switch ($action){
case 'setTokenEleve':
$ssid=session_id();
$user_id=$_SESSION["user_id"];
$token=uniqid($user_id.'_');
$current_tps=time();
$token_expire=$current_tps+10;
$sql="UPDATE session SET token='".$token."',token_expire=".$token_expire." WHERE sess_id='".$ssid."'";
$mysqli->query($sql);
//echo $sql;
header('Location: quizEleve/'.$token);
break;
case 'setTokenProf':
$ssid=session_id();
$deck_id=(int)$_GET["deck_id"];
$user_id=$_SESSION["user_id"];
$token=uniqid($user_id.'_');
$current_tps=time();
$token_expire=$current_tps+10;
$sql="UPDATE session SET token='".$token."',token_expire=".$token_expire." WHERE sess_id='".$ssid."'";
$mysqli->query($sql);
//echo $sql;
header('Location: quizProf/'.$token.'?deck_id='.$deck_id);
break;
}
?>
