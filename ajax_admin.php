<?php

if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest' ) {exit();}
include "db.php";
session_start();
include_once ("local_lang.php");
if(!isset($_SESSION["user_id"])){exit();}
$user_id=$_SESSION["user_id"];

//Check if SuperAdmin
$sql="SELECT
COUNT(*) as flag
FROM licences
LEFT JOIN user_licence ON user_licence.licence_id=licences.licence_id
WHERE user_licence.user_id=".$user_id." AND licences.licence_type='superAdmin'";
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();
if($row["flag"]==0){exit();}

$current_tps=time();
$action=$mysqli->escape_string(htmlspecialchars($_GET['action']));

switch ($action){
case 'takeControl':
$user_id=(int)($_GET['user_id']);
$_SESSION["user_id"]=$user_id;
echo json_encode("ok");
break;
case 'getUsers':
$sql="SELECT
users.user_id,
users.first_name,
users.last_name,
users.lang,
users.email,
users.type as role,
DATE(users.jourInscription) as jourInscription,
users.notification
FROM users
WHERE users.active=1";
$myUsers=array();
$result = $mysqli->query($sql);
while ($row = $result->fetch_assoc()) {
$myUsers[]=$row;
$user_id2rk[$row["user_id"]]=sizeof($myUsers)-1;
}

$result->free();
//classe
$sql="SELECT
users.user_id,
lang.lang_name,
lang.lang_code2,
classes.class_id,
classes.class_name,
classes.promo,
classes.status,
classes.code,
user_class.role,
classes.timestp
FROM users
LEFT JOIN user_class ON user_class.user_id=users.user_id
LEFT JOIN classes ON user_class.class_id=classes.class_id
LEFT JOIN lang ON lang.lang_id=classes.lang_id
WHERE users.active=1";
$result = $mysqli->query($sql);
while ($row = $result->fetch_assoc()) {
  if(isset($myUsers[$user_id2rk[$row["user_id"]]])){
    if(!isset($myUsers[$user_id2rk[$row["user_id"]]]["classes"]))
    {$myUsers[$user_id2rk[$row["user_id"]]]["classes"]=array();}
    array_push($myUsers[$user_id2rk[$row["user_id"]]]["classes"],$row);
  }
}
$result->free();

$sql="SELECT
users.user_id,
licences.licence_id,
licences.licence_type,
licences.lang_id,
licences.date_ini,
licences.active,
licences.date_fin
FROM users
LEFT JOIN user_licence ON user_licence.user_id=users.user_id
LEFT JOIN licences ON licences.licence_id=user_licence.licence_id
WHERE users.active=1";
$result = $mysqli->query($sql);
while ($row = $result->fetch_assoc()) {
  if(isset($myUsers[$user_id2rk[$row["user_id"]]])){
    if(!isset($myUsers[$user_id2rk[$row["user_id"]]]["licences"]))
    {$myUsers[$user_id2rk[$row["user_id"]]]["licences"]=array();}
    array_push($myUsers[$user_id2rk[$row["user_id"]]]["licences"],$row);
  }
}
$result->free();

echo json_encode(array("data"=>$myUsers));
break;
case 'addSentenceAuto':
$deck_id=(int)($_GET['deck_id']);
$myCards=array();
$sql="SELECT cards.mot,cards.card_id,lang.lang_id,lang.lang_code3 FROM cards LEFT JOIN lang ON lang.lang_id=cards.lang_id WHERE cards.deck_id=".$deck_id;
$result = $mysqli->query($sql);
while ($row = $result->fetch_assoc()) {
        array_push($myCards,$row);
    }
$result->close();
foreach($myCards as $myCard)
{
  $mot=$myCard["mot"];
  $card_id=$myCard["card_id"];
  $lang_id=$myCard["lang_id"];
  $phrases=array();
  $sql="SELECT tatoeba_sent.phrase FROM tatoeba_sent LEFT JOIN lang ON lang.lang_code3=tatoeba_sent.lang WHERE tatoeba_sent.phrase REGEXP '[[:<:]]".$mot."[[:>:]]' AND lang.lang_id='".$lang_id."' LIMIT 5";
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
          array_push($phrases,$row);
      }
  $result->close();
  $sql = "INSERT INTO card_sentence (sentence,card_id) "
              . "VALUES (?,?)";
  $stmt=$mysqli->prepare($sql);
  foreach($phrases as $phrase)
  {
    $phrase = str_replace($mot, "*".$mot."*", $phrase['phrase']);
    $stmt->bind_param("si", $phrase, $card_id);
    $stmt->execute();
    $stmt->close();
  }
}
echo json_encode(array("cards"=>$myCards,"phrases"=>$phrases,"lang_id"=>$lang_id));

break;
case 'addLicence':
$user_id=(int)($_GET['user_id']);
$licence_type=htmlspecialchars($_GET['licence_type']);
$licence_type=$mysqli->real_escape_string($licence_type);
$nbreJour=(int)($_GET['nbreJour']);
$lang_id=(int)($_GET['lang_id']);
$sql="INSERT INTO licences (licence_type, date_fin,lang_id) VALUE (?,DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL ? DAY),?)";
$statement=$mysqli->prepare($sql);
$statement->bind_param("sii",$licence_type,$nbreJour,$lang_id);
$statement->execute();
$licence_id=$mysqli->insert_id;
$sql="INSERT INTO user_licence (licence_id, user_id) VALUE (?,?)";
$statement=$mysqli->prepare($sql);
$statement->bind_param("ii",$licence_id,$user_id);
$statement->execute();
echo json_encode(array("licence_id"=>$licence_id));
break;
case 'disableLicence':
$licence_id=(int)($_GET['licence_id']);
$sql="UPDATE licences SET active=0 WHERE licence_id=".$licence_id;
$mysqli->query($sql);
echo json_encode("done");
break;
case 'setTrad':
  $user_id=(int)($_SESSION['user_id']);
  $lang_id=(int)($_GET['lang_id']);
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
