<?php
include "db.php";
session_start();

$current_tps=time();
$action=htmlspecialchars($_GET['action']);


switch ($action){

case 'duplicClasse':
//$deck_id=(int)$_GET['deck_id'];
//$mysqli->query("UPDATE decks SET visible=0, lastChange=".$current_tps."  WHERE deck_id=".$deck_id);
//$data = array ('status'=>'ok');
echo json_encode($data);
break;
}
