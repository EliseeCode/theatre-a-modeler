<?php
if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) {
  include "db.php";
  session_start();
  $action=$mysqli->escape_string(htmlspecialchars($_GET['action']));
  switch ($action){
    case 'getCurrentObjectifsByClass':
      $user_id=$_SESSION["user_id"];
      $class_id=$_GET["class_id"];
      $scores=array();
      $objectif=array();
      //get objectif correspondant à la classe indiqué
       $sql="SELECT class_objectif.day_num,class_objectif.objectif_id,class_objectif.class_id,class_objectif.quantity
       FROM class_objectif WHERE class_objectif.class_id=$class_id LIMIT 1";
      $result=$mysqli->query($sql);
      $objectif = $result->fetch_assoc();
      $result->free();
      // recupère le score actuel
      $sql="SELECT user_objectif.objectif_id,user_objectif.score,class_objectif.quantity,user_objectif.deadline
      FROM user_objectif
      LEFT JOIN class_objectif ON class_objectif.objectif_id=user_objectif.objectif_id
      WHERE class_objectif.class_id=$class_id AND user_objectif.user_id=$user_id AND user_objectif.deadline>=CURDATE() ORDER BY user_objectif.deadline DESC LIMIT 1";
      $result=$mysqli->query($sql);
      $scores = $result->fetch_assoc();
      // recupère le streak actuel
      $sql="SELECT user_streak.objectif_id,user_streak.value,user_streak.deadline
      FROM user_streak
      LEFT JOIN class_objectif ON class_objectif.objectif_id=user_streak.objectif_id
      WHERE class_objectif.class_id=$class_id AND user_streak.user_id=$user_id AND user_streak.deadline>=CURDATE() ORDER BY user_streak.deadline DESC LIMIT 1";
      $result=$mysqli->query($sql);
      $streak = $result->fetch_assoc();
      //récupérer le nombre d'objectif validé de facon consécutive:
      //dans user_objectif=>streak=streak last +1
      $result->free();
      $data = array ('status'=>'ok',"score"=>$scores,"objectif"=>$objectif,"streak"=>$streak);
      echo json_encode($data);
    break;

    case 'createUpdateObjectif':
      $user_id=$_SESSION["user_id"];
      $class_id=(int)$_GET['class_id'];
      $quantity=(int)$_GET['quantity'];
      $day_num=(int)$_GET['day_num'];//(0 monday,1tuesday...6 sunday)
      $today=date("Y-m-d");
      $statusMsg="";
      $sql="";
      //check if user has right on
      $result = $mysqli->query('SELECT user_class.role FROM user_class Where class_id = ' . $class_id.' AND user_id='.$user_id.' LIMIT 1');
      $row = $result->fetch_assoc();
      $role=$row["role"];
      $result->free();
      if($role=="prof" || $role=="perso")
      {
        $result = $mysqli->query('SELECT objectif_id FROM class_objectif Where class_id = ' . $class_id);
        $line_exist=$result->num_rows;
        $row = $result->fetch_assoc();
        $objectif_id=$row["objectif_id"];
        $result->free();
        if($line_exist==0)
        {
          $statusMsg.="there is not yet Obj.";
            $sql = "INSERT INTO class_objectif (class_id,quantity,day_num,creation_date,creator_id) "
                     . "VALUES (".$class_id.",".$quantity.",".$day_num.",'".$today."',".$user_id.")";
            $mysqli->query($sql);
        }
        else {
          $statusMsg.="already Obj.";
            $sql = "UPDATE class_objectif SET class_id=$class_id,quantity=$quantity,day_num=$day_num,creation_date='$today',creator_id=$user_id WHERE objectif_id=$objectif_id";
            $mysqli->query($sql);
            $sql2=" UPDATE user_objectif SET deadline=DATE_ADD(CURDATE(), INTERVAL (($day_num+7 -DAYOFWEEK(CURDATE()))%7) DAY) WHERE deadline>=CURDATE() AND objectif_id=$objectif_id";
            $mysqli->query($sql2);
            $sql3=" UPDATE user_streak SET deadline=DATE_ADD(CURDATE(), INTERVAL (($day_num+7 -DAYOFWEEK(CURDATE()))%7) DAY) WHERE deadline>=CURDATE() AND objectif_id=$objectif_id";
            $mysqli->query($sql3);
        }
        $statusMsg.="user_prof";
      }
      else{
        $statusMsg.="user_pas_prof";
      }

      $data = array ('status'=>$statusMsg,"sql"=>$sql);
      echo json_encode($data);
    break;
  }
  exit();
}
