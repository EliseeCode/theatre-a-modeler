<?php

if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest' ) {exit();}
include "db.php";
session_start();
include_once ("local_lang.php");
if(!isset($_SESSION["user_id"])){exit();}
$current_tps=time();
$action=$mysqli->escape_string(htmlspecialchars($_GET['action']));

switch ($action){
case 'setTrad':
  $user_id=(int)($_SESSION['user_id']);
  $lang_id=(int)($_GET['lang_id']);

  $sql="SELECT
  COUNT(*) as flag
  FROM licences
  LEFT JOIN user_licence ON user_licence.licence_id=licences.licence_id
  WHERE user_licence.user_id=".$user_id." AND licences.licence_type='trans_interface' AND licences.lang_id=".$lang_id;
  $result = $mysqli->query($sql);
  $row = $result->fetch_assoc();
  if($row["flag"]==0){exit();}

  $sentence_id=(int)($_GET['sentence_id']);
  $tradLine=htmlspecialchars($_GET['tradLine']);
  $tradLine=$mysqli->real_escape_string($tradLine);
  $status=htmlspecialchars($_GET['status']);
  $status=$mysqli->real_escape_string($status);
  $sql="DELETE FROM interfaceTrad WHERE sentence_id=".$sentence_id." AND lang_id=".$lang_id;
  $mysqli->query($sql);
  if($tradLine!=""){
  $sql="INSERT INTO interfaceTrad (sentence_id, lang_id,traduction,user_id,status) VALUE (?,?,?,?,?)";
  $statement=$mysqli->prepare($sql);
  $statement->bind_param("iisis",$sentence_id,$lang_id,$tradLine,$user_id,$status);
  $statement->execute();
  }
  echo json_encode("done");
break;
}
