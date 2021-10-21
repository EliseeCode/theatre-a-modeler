<?php
require 'db.php';
session_start();
include_once ("local_lang.php");
// Ensure that there is no request forgery going on, and that the user
  // sending us this connect request is the user that was supposed to.
  //echo $_GET["state"]."-".$_SESSION["state"];
  //if ($_GET["state"] != $_SESSION["state"]) {
  //  $_SESSION['message'] = 'Invalid state parameter';
  //      header("location: error.php");exit();
  //}

    $code=$_GET['code'];
    $client_id=$_ENV["GOOGLE_CLIENT_ID_LOGIN"];;
    $client_secret=$_ENV["GOOGLE_CLIENT_SECRET"];;
    //$redirect_uri="https://www.exolingo.com/loginGoogle.php";
    $redirect_uri="https://".$_SERVER['HTTP_HOST']."/loginGoogle.php";
    $urlToken="https://www.googleapis.com/oauth2/v4/token";
    //echange du code contre un token
                // Create map with request parameters
    $params = array ('code' => $code, 'grant_type' => "authorization_code",'client_id' => $client_id,'client_secret' => $client_secret,'redirect_uri' => $redirect_uri);
                // Build Http query using params
    $query = http_build_query ($params);
    $contextData = array (
                'method' => 'POST',
                'header' => "Connection: close\r\n".
                            "Content-Length: ".strlen($query)."\r\n",
                'content'=> $query );
                // Create context resource for our request
    $context = stream_context_create (array ( 'http' => $contextData ));
                // Read page rendered as result of your POST request
    $result =  file_get_contents (
                  $urlToken,  // page url
                  false,
                  $context);

    $response = json_decode($result, true);

    if(array_key_exists("error", $response)) {
      $_SESSION['message'] = $response["error_description"];
        header("location: loginPage.php");
        exit();
      }
    if(!array_key_exists("access_token", $response)){
      $_SESSION['message'] = "Pas de jeton";
      header("location: loginPage.php");
      exit();
    }
      //echange du token ccontre les info de l utilisateur
      $json = file_get_contents("https://www.googleapis.com/oauth2/v2/userinfo?access_token=".$response["access_token"]);
      $payload = json_decode($json,true);


      if (!$payload) {
        $_SESSION['message'] = __("l'API ne repond pas");
        header("location: loginPage.php");
        exit();
      }

      $googleID = $payload['id'];
      $email=$payload['email'];
      //echo $payload['name'];
      $email_verified=$payload['verified_email'];
      $pictureURL=$payload['picture'];
      $first_name=$payload['given_name'];
      $last_name=$payload['family_name'];

      if(isset($_SESSION['local_lang'])){
      $lang=$mysqli->escape_string($_SESSION['local_lang']);
      }
      else{$lang="en_US";}
      //echo $email."<br>".$googleID."<br>".$email_verified."<br>".$pictureURL."<br>".$first_name."<br>";
      if(!$email_verified){$_SESSION['message'] = __("Votre adresse mail Google n'a pas été vérifié.");
        header("location: loginPage.php");
        exit();
      }

      $result = $mysqli->query("SELECT * FROM users WHERE googleID='".$googleID."' LIMIT 1");
      $num_rows=$result->num_rows;
      if ( $num_rows == 0 )
        {//echo "<br>pas de GoogleID :".$googleID;
        $result->free();
        $result = $mysqli->query("SELECT * FROM users WHERE email='".$email."' LIMIT 1");
          if ( $result->num_rows == 0 )
              {//echo "<br>pas de mail:".$email;
               $type="register";//on va créer le compte
               $result->free();
              }
          else{
              $user = $result->fetch_assoc();
              $result->free();
              $type="connect";
              //il y a compte avec un mail
              $sql="UPDATE `users` SET googleID=".$googleID.", active=1 WHERE email='".$email."'";
              $mysqli->query($sql);
              }
          }
          else
          {
            $user = $result->fetch_assoc();
            $result->free();
            $type="connect";
          }

          //On choisit de se connecter ou de créer un compte
        if($type=="connect")
        {
          $_SESSION["active"]=1;
          $_SESSION["user_id"]=$user["user_id"];
          header("location: loginSession.php");
          exit();
        }


        if($type=="register")
        {
          //check if not exist yet (maybe it has been checked before)
          $result = $mysqli->query("SELECT COUNT(*) as num_rows FROM users WHERE email='".$email."'");
          $rows = $result->fetch_assoc();

          if ( $rows["num_rows"] != 0 ){//echo "il y a deja un compte";
            $result = $mysqli->query("SELECT * FROM users WHERE email='".$email."' LIMIT 1");
            $user = $result->fetch_assoc();
            $result->free();
            $_SESSION["active"]=1;
            $_SESSION["user_id"]=$user["user_id"];
            header("location: loginSession.php");
            exit();
          }
          else{
              $today=date("Y-m-d");
              $avatar_id=rand(0,719);
              $sql = "INSERT INTO users (first_name,googleID, last_name, email,type,active,lang,avatar_id) "
                  . "VALUES ('$first_name',".$googleID.",'$last_name','$email','',1,'$lang',".$avatar_id.")";
                  $mysqli->query($sql);
                  $user_id = $mysqli->insert_id;

                  $sql = "INSERT INTO `user_avatar`(`user_id`, `avatar_id`, `status`)
                  VALUES (".$user_id.",".$avatar_id.",'selected')";
                  $mysqli->query($sql);
                //Création de la classe perso de l'utilisateur
                  $sql = "INSERT INTO `classes`(`class_name`, `creator_id`, `promo`, `status`, `school_id`, `code`, `lang`)
                  VALUES ('Mon lexique',".$user_id.",'','perso',0,'','')";
                  $mysqli->query($sql);
                  $class_id = $mysqli->insert_id;
                  $sql = "INSERT INTO `user_class`(`user_id`, `class_id`, `position`, `role`)
                  VALUES (".$user_id.",".$class_id.",0,'perso')";
                  $mysqli->query($sql);
                  $sql = "INSERT INTO `user_class`(`user_id`, `class_id`, `position`, `role`)
                  SELECT ".$user_id.",class_id,0,'explore' FROM classes WHERE status='explore'";
                  $mysqli->query($sql);

                  $_SESSION["active"]=1;
                  $_SESSION["user_id"]=$user_id;
                  header("location: loginSession.php");
                  exit();
              }
      }
       else {
        //echo "<br>pas de Payload";
        $_SESSION['message'] = __("Votre identification a échoué.");
        header("location: loginPage.php");
        exit();
      }
?>
