<?php
//ini_set('display_errors', '1');
//ini_set('display_startup_errors', '1');
//error_reporting(E_ALL);

function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

function checkMissionInput($class_ids,$deck_ids,$exo_ids,$starting_today,$deadline)
{
    return count($class_ids)==0 || count($deck_ids)==0 || count($exo_ids)==0 || validateDate($deadline, 'Y-m-d') || is_bool($starting_today);
}
function CheckProfRightOnClasses($mysqli,$user_id,$class_ids)
{//check if user has right on all those classes
    $flag=1;
    $sql = 'SELECT COUNT(*) as flag FROM user_class Where class_id = ? AND user_id='.$user_id.' AND role="prof"';
    $stmt=$mysqli->prepare($sql);
    foreach ($class_ids as $class_id){
      $stmt->bind_param("i", $class_id);
      $stmt->execute();
      $result = $stmt->get_result(); // get the mysqli result
      $row = $result->fetch_assoc();
      $flag=$flag*$row['flag'];
    }
    $stmt->close();
    return $flag;
}
function insertMissionsData($mysqli,$mission_id,$user_id,$class_ids,$deck_ids,$exo_ids,$starting_date,$deadline)
{
  $mission_id=(int)$mission_id;
  $user_id=$user_id;
  $class_ids=$class_ids;
  $deck_ids=$deck_ids;
  $exo_ids=$exo_ids;
  $starting_date=htmlspecialchars($starting_date);
  $deadline=htmlspecialchars($deadline);
  $feedback="";
  $sql = "INSERT INTO mission_class (mission_id,class_id) VALUES (?,?)";
  $stmt=$mysqli->prepare($sql);
  foreach ($class_ids as $class_id){
    $stmt->bind_param("ii", $mission_id,$class_id);
    $stmt->execute();
  }
  $stmt->close();

  $sql2 = 'INSERT INTO sub_missions (mission_id,deck_id,exo_id,quantity) VALUES (?,?,?,?)';
  $stmt_insert_Sub_mission=$mysqli->prepare($sql2);

  $sql3 = 'SELECT COUNT(*) as total_quantity,SUM(hasAudio=1) as audio_quantity, SUM(hasImage!=0 OR mot_trad!="") as image_quantity FROM cards
  LEFT JOIN card_deck ON card_deck.card_id=cards.card_id
  Where cards.active=1 AND card_deck.deck_id = ?';
  $stmt=$mysqli->prepare($sql3);

  //prepare statement for user_sub_mission insertion to get the score of all users impacted (from classes) (preparation to be userd later inside the forEachLoop over exo_ids,deck_ids).
  $sql4 = 'INSERT INTO sub_mission_user (sub_mission_id,user_id,success,score)
  SELECT ?,acti.user_id,IF(COUNT(*)>=?,1,0),COUNT(*) as scoreUser FROM
  (SELECT MAX(activiteGlobal.timeStmp) as LastRD,
          activiteGlobal.card_id as card_id,
          activiteGlobal.user_id as user_id
    FROM activiteGlobal
    LEFT JOIN cards ON cards.card_id=activiteGlobal.card_id
    LEFT JOIN user_class ON user_class.user_id=activiteGlobal.user_id
    LEFT JOIN card_deck ON card_deck.card_id=cards.card_id
    WHERE cards.active=1 AND correctness=1 AND user_class.class_id=? AND card_deck.deck_id=? AND activiteGlobal.exo_id=? AND activiteGlobal.timeStmp>?
    GROUP BY CONCAT(activiteGlobal.card_id,"-",activiteGlobal.user_id,"-",activiteGlobal.exo_id)) as acti
  WHERE 1
  GROUP BY acti.user_id
  ON DUPLICATE KEY UPDATE sub_mission_user.score=VALUES(score),sub_mission_user.success=VALUES(success)';
  $stmt_Sub_mission_user=$mysqli->prepare($sql4);

  foreach ($deck_ids as $deck_id){
    $deck_id=(int)$deck_id;
    $stmt->bind_param("i", $deck_id);
    $stmt->execute();
    $result = $stmt->get_result(); // get the mysqli result
    $row = $result->fetch_assoc();
    $total_quantity=$row['total_quantity'];
    $image_quantity=$row['image_quantity'];
    $audio_quantity=$row['audio_quantity'];
    $feedback.=' deck_id='.$deck_id;
    foreach ($exo_ids as $exo_id){
      $exo_id=(int)$exo_id;
      $feedback.=' exo_id='.$exo_id;
      //recupérer le nombre d'exo faissable en fonction de l'exo sur un deck:
        if($exo_id==6){$quantity=$audio_quantity;}
        else{$quantity=$image_quantity;}
        $stmt_insert_Sub_mission->bind_param("iiii", $mission_id,$deck_id,$exo_id,$quantity);
        $stmt_insert_Sub_mission->execute();
        $sub_mission_id=$stmt_insert_Sub_mission->insert_id;
        $sub_mission_id=(int)$sub_mission_id;
        $feedback.=' sub_mission_id='.$sub_mission_id.' insert for submission:'.$mission_id.'-'.$deck_id.'-'.$exo_id.'-'.$quantity;
        foreach ($class_ids as $class_id){
          $class_id=(int)$class_id;
          $feedback.=' class_id='.$class_id;
          $stmt_Sub_mission_user->bind_param("iiiiis",$sub_mission_id,$quantity,$class_id,$deck_id,$exo_id,$starting_date);
          $stmt_Sub_mission_user->execute();
        }
    }
  }
  $stmt->close();
  $stmt_insert_Sub_mission->close();
  $stmt_Sub_mission_user->close();
  return $feedback;
}

function updateUserMissionsScoreByClass($mysqli,$class_id,$deck_ids,$exo_ids,$starting_date)
{
  //get all missions user has
  $sql = "SELECT sub_missions.sub_mission_id,
                  sub_missions.deck_id,
                  sub_missions.exo_id,
                  sub_missions.quantity,
                   FROM sub_missions
                   LEFT JOIN mission_class ON mission_class.mission_id=sub_missions.mission_id
                   WHERE mission_class.class_id=".$class_id;
  $stmt_sub_mission=$mysqli->prepare($sql);

  $sql = 'SELECT COUNT(*) as total_quantity,SUM(hasAudio=1) as audio_quantity, SUM(hasImage!=0 OR mot_trad!="") as image_quantity FROM cards Where active=1 AND deck_id = ?';
  $stmt_quantity=$mysqli->prepare($sql3);

  //prepare statement for user_sub_mission insertion to get the score of all users impacted (from classes) (preparation to be userd later inside the forEachLoop over exo_ids,deck_ids).
  $sql = 'INSERT INTO sub_mission_user (sub_mission_id,user_id,success,score)
  SELECT ?,acti.user_id,0,COUNT(*) as scoreUser FROM
  (SELECT MAX(activiteGlobal.timeStmp) as LastRD,
          activiteGlobal.card_id as card_id,
          activiteGlobal.user_id as user_id
    FROM activiteGlobal
    LEFT JOIN cards ON cards.card_id=activiteGlobal.card_id
    LEFT JOIN user_class ON user_class.user_id=activiteGlobal.user_id
    WHERE correctness=1 AND user_class.class_id=? AND cards.deck_id=? AND activiteGlobal.exo_id=? AND activiteGlobal.timeStmp>?
    GROUP BY CONCAT(activiteGlobal.card_id,"-",activiteGlobal.user_id,"-",activiteGlobal.exo_id)) as acti
  WHERE 1
  GROUP BY acti.user_id
  ON DUPLICATE KEY UPDATE sub_mission_user.score=sub_mission_user.score';
  $stmt_sub_mission_user=$mysqli->prepare($sql);

  foreach ($deck_ids as $deck_id){

    $stmt->bind_param("i", $deck_id);
    $stmt->execute();
    $result = $stmt->get_result(); // get the mysqli result
    $row = $result->fetch_assoc();
    $total_quantity=$row['total_quantity'];
    $image_quantity=$row['image_quantity'];
    $audio_quantity=$row['audio_quantity'];

    foreach ($exo_ids as $exo_id){
      //recupérer le nombre d'exo faissable en fonction de l'exo sur un deck:
        if($exo_id==6){$quantity=$audio_quantity;}
        else{$quantity=$image_quantity;}
        $stmt_sub_mission->bind_param("iiii", $mission_id,$deck_id,$exo_id,$quantity);
        $stmt_sub_mission->execute();
        $sub_mission_id=$stmt_sub_mission->insert_id;

        foreach ($class_ids as $class_id){
          $stmt_sub_mission_user->bind_param("iiiis", $sub_mission_id,$class_id,$deck_id,$exo_id,$starting_date);
          $stmt_sub_mission_user->execute();
        }
    }
  }
  $stmt->close();
  $stmt_insert_Sub_mission->close();
  $stmt_sub_mission_user->close();
}

if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
  include "db.php";
  session_start();

  $action=$mysqli->escape_string(htmlspecialchars($_GET['action']));
  switch ($action){
    case 'deleteMission':
    $user_id=(int)$_SESSION["user_id"];
    $mission_id=(int)$_GET["mission_id"];
    $sql="UPDATE missions SET active=0,delete_date=NOW() WHERE mission_id=".$mission_id." AND creator_id=".$user_id;
    $mysqli->query($sql);
    echo json_encode("ok");
    break;
    case 'getCurrentMissionByClass':
      $user_id=(int)$_SESSION["user_id"];
      $class_id=(int)$_GET["class_id"];
      $scores_mission=array();
      $scores_sub_mission=array();
      $missions=array();

      //get missions correspondant à la classe indiqué via les decks
       $sql="SELECT  distinct sub_missions.sub_mission_id,
                     missions.mission_id,
                     sub_missions.deck_id,
                     decks.hasImage,
                     decks.deck_name,
                     exos.name as exo_name,
                     sub_missions.exo_id,
                     sub_missions.quantity,
                     mission_class.class_id,
                     missions.deadline,
                     missions.creator_id,
                     users.first_name as creator_name,
                     missions.starting_date
       FROM missions
       LEFT JOIN mission_class ON mission_class.mission_id=missions.mission_id
       JOIN sub_missions ON sub_missions.mission_id=missions.mission_id
       LEFT JOIN decks ON decks.deck_id=sub_missions.deck_id
       LEFT JOIN exos ON exos.exo_id=sub_missions.exo_id
       LEFT JOIN users ON users.user_id=missions.creator_id
       WHERE mission_class.class_id=".$class_id." AND missions.deadline>=CURDATE() AND missions.active=1 ORDER BY missions.deadline DESC";

      $result=$mysqli->query($sql);
      while($row=$result->fetch_assoc())
      {array_push($missions,$row);}
      $result->free();

      // recupère le score des sub_missions
      $sql="SELECT distinct sub_mission_user.sub_mission_id,sub_mission_user.score,sub_mission_user.success,sub_missions.quantity
      FROM sub_mission_user
      LEFT JOIN sub_missions ON sub_missions.sub_mission_id=sub_mission_user.sub_mission_id
      LEFT JOIN missions ON missions.mission_id=sub_missions.mission_id
      WHERE sub_mission_user.user_id=".$user_id." ORDER BY missions.deadline DESC";
      $result=$mysqli->query($sql);
      while($row=$result->fetch_assoc())
      {array_push($scores_sub_mission,$row);}
      $result->free();
      // // recupère le score des missions
      $sql="SELECT distinct mission_user.mission_id,mission_user.success,mission_user.completed_date
      FROM mission_user
      LEFT JOIN missions ON missions.mission_id=mission_user.mission_id
      WHERE missions.active=1 AND mission_user.user_id=".$user_id." ORDER BY missions.deadline DESC";
      $result=$mysqli->query($sql);
      while($row=$result->fetch_assoc())
      {array_push($scores_mission,$row);}
      $result->free();
      // TODO://check if success registered?
      $data = array ('status'=>'ok',"score_mission"=>$scores_mission,"score_sub_mission"=>$scores_sub_mission,"mission"=>$missions);
      echo json_encode($data);

    break;
    case 'getCurrentMissionByClassProf':
      $user_id=(int)$_SESSION["user_id"];
      $class_id=(int)$_GET["class_id"];
      $missions=array();

      //get missions correspondant à la classe indiqué via les decks
       $sql="SELECT  distinct sub_missions.sub_mission_id,
                     missions.mission_id,
                     sub_missions.deck_id,
                     decks.hasImage,
                     decks.deck_name,
                     exos.name as exo_name,
                     sub_missions.exo_id,
                     sub_missions.quantity,
                     mission_class.class_id,
                     missions.deadline,
                     missions.creator_id,
                     users.first_name as creator_name,
                     nbre_sub_mission_T.nbre_sub_mission,
                     missions.starting_date
       FROM missions
       LEFT JOIN mission_class ON mission_class.mission_id=missions.mission_id
       JOIN sub_missions ON sub_missions.mission_id=missions.mission_id
       LEFT JOIN (SELECT COUNT(*) as nbre_sub_mission,mission_id FROM sub_missions GROUP BY mission_id) as nbre_sub_mission_T ON nbre_sub_mission_T.mission_id=missions.mission_id
       LEFT JOIN decks ON decks.deck_id=sub_missions.deck_id
       LEFT JOIN exos ON exos.exo_id=sub_missions.exo_id
       LEFT JOIN users ON users.user_id=missions.creator_id
       WHERE mission_class.class_id=".$class_id." AND missions.active=1 ORDER BY missions.deadline DESC";

      $result=$mysqli->query($sql);
      while($row=$result->fetch_assoc())
      {array_push($missions,$row);}
      $result->free();

      $data = array ('status'=>'ok',"mission"=>$missions);
      echo json_encode($data);

    break;

    case 'createMission':
      $user_id=(int)$_SESSION["user_id"];
      $mission_id=0;
      if(isset($_POST['starting_today']) && $_POST['starting_today']=="true"){ $starting_today=1;}else{$starting_today=0;}

      $CheckedInputFlag=checkMissionInput($_POST['class_ids'],$_POST['deck_ids'],$_POST['exo_ids'],$starting_today,$_POST['deadline']);
      if(!$CheckedInputFlag){
        $data = array ('status'=>"error","msg_error"=>"problems with inputs".$starting_today);
        echo json_encode($data);
        exit();
      }

      $class_ids=$_POST['class_ids'];
      $deck_ids=$_POST['deck_ids'];
      $exo_ids=$_POST['exo_ids'];
      $today=date("Y-m-d");
      if($starting_today==1){
        $starting_date=$today;
      }else {
        $starting_date="0000-00-00";
      }
      $deadline=$_POST['deadline'];


      $statusMsg="";

      if(!CheckProfRightOnClasses($mysqli,$user_id,$class_ids)){
        $data = array ('status'=>"error","msg_error"=>"Vous n'avez pas les droits sur au moins une classe classes");
        echo json_encode($data);
      };

      //create a mission, and sub_missions
      $num_sub_missions=count($deck_ids)*count($exo_ids);
      $sql = "INSERT INTO missions (deadline,creator_id,starting_date,num_sub_missions) "
               . "VALUES ('".$deadline."',".$user_id.",'".$starting_date."',".$num_sub_missions.")";
      $mysqli->query($sql);
      $mission_id=$mysqli->insert_id;
      $statusMsg=$sql;
      //insert data in DB and update students score
      $feedback=insertMissionsData($mysqli,$mission_id,$user_id,$class_ids,$deck_ids,$exo_ids,$starting_date,$deadline);

      $data = array ('mission_id'=>$mission_id,'status'=>$feedback,"sql"=>$sql);
      echo json_encode($data);
    break;
    case 'updateMission':
      // $mission_id=(int)$_GET["mission_id"];
      // $user_id=(int)$_SESSION["user_id"];
      // $deck_ids=$_GET['deck_ids'];
      // $class_ids=$_GET['class_ids'];
      // $exo_ids=$_GET['exo_ids'];
      // if(validateDate($_GET['deadline'], 'Y-m-d')){
      //   $deadline=$_GET['deadline'];
      // }
      // if(validateDate($_GET['starting_today'], 'Y-m-d')){
      //   $starting_date=$_GET['starting_date'];
      // }
      // $today=date("Y-m-d");
      //
      // $statusMsg="";
      // //check if user has right on all those classes
      // $flag=1;
      // $sql = 'SELECT COUNT(*) as flag FROM user_class Where class_id = ? AND user_id='.$user_id.' AND role="prof"';
      // $stmt=$mysqli->prepare($sql);
      // foreach ($class_ids as $class_id){
      //   $stmt->bind_param("i", $class_id);
      //   $stmt->execute();
      //   $result = $stmt->get_result(); // get the mysqli result
      //   $row = $result->fetch_assoc();
      //   $flag=$flag*$row['flag'];
      // }
      // $stmt->close();
      // if($flag==0 || count($class_ids)==0){
      //   echo json_encode("probleme d'appartenance à l'une des classes.");
      //   exit();
      // }
      //
      // //create a mission, and sub_missions
      //
      // $sql = "UPDATE missions SET deadline='".$deadline."',starting_date='".$starting_date."'";
      // $mysqli->query($sql);
      // $sql = "DELETE FROM mission_class WHERE mission_id=".$mission_id;
      // $mysqli->query($sql);
      // $sql = "INSERT INTO mission_class (mission_id,class_id) VALUES (?,?)";
      // $stmt=$mysqli->prepare($sql);
      // foreach ($class_ids as $class_id){
      //   $stmt->bind_param("ii", $mission_id,$class_id);
      //   $stmt->execute();
      // }
      // $stmt->close();
      //
      // //Check if deck+user is still there=>remove if not.
      // $deck2remove=array(-1);
      // $exo2remove=array(-1);
      // $sql="SELECT sub_missions.deck_id,sub_missions.exo_id FROM sub_missions Where mission_id=".$mission_id;
      // $result = $mysqli->query($sql);
      // while($row = $result->fetch_assoc())
      // {
      //   if(in_array($row["deck_id"],$deck_ids)){
      //     array_push($deck2remove,$row["deck_id"]);
      //   };
      //   if(in_array($row["exo_id"],$exo_ids)){
      //     array_push($exo2remove,$row["exo_id"]);
      //   };
      // }
      // $result->free();
      // $sql = "DELETE FROM sub_missions WHERE mission_id=".$mission_id." AND (deck_id=? OR exo_id=?)";
      // $mysqli->query($sql);
      //
      // foreach ($deck_ids as $deck_id){
      //   $sql = 'SELECT COUNT(*) as total_quantity,SUM(hasAudio=1) as audio_quantity, SUM(hasImage!=0 OR mot_trad!="") as image_quantity FROM cards Where active=1 AND deck_id = ?';
      //   $stmt=$mysqli->prepare($sql);
      //   $stmt->bind_param("i", $deck_id);
      //   $stmt->execute();
      //   $result = $stmt->get_result(); // get the mysqli result
      //   $row = $result->fetch_assoc();
      //   $total_quantity=$row['total_quantity'];
      //   $image_quantity=$row['image_quantity'];
      //   $audio_quantity=$row['audio_quantity'];
      //   $stmt->close();
      //
      //   $sql = 'INSERT INTO sub_missions (mission_id,deck_id,exo_id,quantity) VALUES (?,?,?,?)';
      //   $stmt_insertSubMission=$mysqli->prepare($sql);
      //   foreach ($exo_ids as $exo_id){
      //     //recupérer le nombre d'exo faissable en fonction de l'exo sur un deck:
      //       if($exo_id==9){$quantity=$audio_quantity;}
      //       else{$quantity=$image_quantity;}
      //
      //     $stmt_insertSubMission->bind_param("iiii", $mission_id,$deck_id,$exo_id,$quantity);
      //     $stmt_insertSubMission->execute();
      //   }
      //   $stmt_insertSubMission->close();
      // }
      //
      //
      // $data = array ('status'=>$statusMsg,"sql"=>$sql);
      // echo json_encode($data);
     break;
  }
  exit();
}
