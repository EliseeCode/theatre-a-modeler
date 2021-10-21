<?php
require 'db.php';
session_start();
include_once ("local_lang.php");
if(isset($_SESSION["user_id"]) && isset($_SESSION["active"]))
{
    $user_id=(int)$_SESSION["user_id"];
    $result = $mysqli->query("SELECT * FROM users WHERE user_id=".$user_id);
    if ( $result->num_rows == 0 ){ // User doesn't exist
        $_SESSION['message'] = __("Aucun utilisateur inscrit avec cette adresse email !");
        header("location: loginPage.php");
        exit();
    }
    else if($_SESSION["active"]==1){// User exists
      $user = $result->fetch_assoc();
      
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['type'] = $user['type'];
        $_SESSION['classe'] = $user['classe'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['active'] = $user['active'];
        $_SESSION['nbreCoins'] = $user['nbreCoins'];
        error_log("nbreCoin:".$user['nbreCoins'], 3, "/home/elisee/Desktop/log.txt");
        $_SESSION['local_lang'] = $user['lang'];
        $_SESSION['avatar_id'] = $user['avatar_id'];
        $_SESSION['ruby'] = $user['ruby'];
        // This is how we'll know the user is logged in
        $_SESSION['logged_in'] = true;
        //Parametre par default, réécrit par les possesseur de licences.
        $_SESSION["boltNoLimit"]=false;
        $_SESSION["premiumDeckAccess"]=false;
        $_SESSION["premiumCourseAccess"]=false;
        $_SESSION["classNoLimit"]=false;
        $_SESSION["premiumAvatar"]=false;
        $_SESSION["SuperSchoolManager"]=false;
        //check for user licenced
        $now=date("Y-m-d");
        $sql="SELECT licences.licence_type,user_licence.licence_role FROM user_licence LEFT JOIN licences on licences.licence_id=user_licence.licence_id WHERE user_licence.user_id=".$user['user_id']." AND licences.active=1";
        //echo $sql;
        $result = $mysqli->query($sql);
        while($licence = $result->fetch_assoc())
        {
          if($licence["licence_role"]=="SuperStudent"){
            $_SESSION["boltNoLimit"]=true;
            $_SESSION["premiumDeckAccess"]=true;
            $_SESSION["premiumCourseAccess"]=true;
            $_SESSION["premiumAvatar"]=true;
          }
          if($licence["licence_role"]=="SuperTeacher"){
            $_SESSION["premiumDeckAccess"]=true;
            $_SESSION["premiumAvatar"]=true;
          }
          if($licence["licence_role"]=="SuperSchoolManager"){
            $_SESSION["SuperSchoolManager"]=true;
            $_SESSION["premiumAvatar"]=true;
          }
        }
        
        //getLanguage
        $sql="SELECT lang.lang_id,lang.lang_code2,lang.lang_code2_2,lang.lang_code3,lang.lang_name FROM user_target_lang LEFT join lang ON user_target_lang.lang_id=lang.lang_id WHERE user_target_lang.user_id=".$user_id." ORDER BY user_target_lang.changed_time DESC LIMIT 1";
        $result = $mysqli->query($sql);
        $flag =$result->num_rows;
          if($flag==1){
            $row = $result->fetch_assoc();
            $_SESSION["target_lang_code3"]=htmlspecialchars($row["lang_code3"]);
            $_SESSION["target_lang_code2"]=htmlspecialchars($row["lang_code2"]);
            $_SESSION["target_lang_code2_2"]=htmlspecialchars($row["lang_code2_2"]);
            $_SESSION["target_lang_name"]=htmlspecialchars($row["lang_name"]);
            $_SESSION["target_lang"]=(int)$row["lang_id"];
          }
          else{
            header("location:lang.php");
            exit();
          }
        //redirection vers la page précédemment souhaité
       if(isset($_SESSION['url'])){$url=$_SESSION['url'];if($url==""){$url='decks.php';}}else{$url='decks.php';}
       
        header("location: ".$url);
        exit();
      }
}
echo 'pas de session';
?>
