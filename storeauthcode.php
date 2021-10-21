<?php
require 'db.php';
session_start();
include_once ("local_lang.php");
@require_once '../../vendor/autoload.php';
// Get $id_token via HTTPS POST.
echo "test";

$id_token=htmlspecialchars($_GET['idtoken']);
$type=htmlspecialchars($_GET['type']);
//$type="toto";
$CLIENT_ID=$_ENV["GOOGLE_CLIENT_ID_LOGIN"];
$client = new Google_Client(['client_id' => $CLIENT_ID]);  // Specify the CLIENT_ID of the app that accesses the backend

$payload = $client->verifyIdToken($id_token);

//print_r($payload);
//Test s'il y a un jeton
if ($payload) {
  echo "<br>il y a un payload";

  $googleID = $payload['sub'];
  $email=$payload['email'];
  //echo $payload['name'];
  $email_verified=$payload['email_verified'];
  $pictureURL=$payload['picture'];
  $first_name=$payload['given_name'];
  $last_name=$payload['family_name'];

  if(!$email_verified){$_SESSION['message'] = __("Votre adresse mail Google n'a pas été vérifié.");
    header("location: error.php");exit();
    echo 'goError :'.$_SESSION['message'];
  }

  //if($type=="connect"){
    $result = $mysqli->query("SELECT * FROM users WHERE googleID='".$googleID."'");
    $num_rows=$result->num_rows;
    echo "<br>numRows :".$num_rows;
    if ( $num_rows == 0 )
    {echo "<br>pas de GoogleID :".$googleID;
    $result->free();

    $result = $mysqli->query("SELECT * FROM users WHERE email='".$email."'");
      if ( $result->num_rows == 0 )
        {echo "<br>pas de mail:".$email;
         $type="register";
         $result->free();
         //$_SESSION['message'] = "Il n'y a pas de compte associé à cette adresse mail.";
         //header("location: error.php");exit();
         //echo 'goError :'.$_SESSION['message'];

        }
      else{
        while($rows = $result->fetch_assoc())
        {
          $user=$rows;
        }
        $result->free();
        $type="connect";
        echo "<br>il y a un mail:".$email;
        $sql="UPDATE `users` SET googleID=".$googleID." WHERE email='".$email."'";
        $mysqli->query($sql);
        }
    }
    else
    {
      while($rows = $result->fetch_assoc())
      {
        $user=$rows;
      }
      $result->free();
      $type="connect";
    }
  //}

  if($type=="connect")
  {
    echo "<br>";
    print_r($user);
    echo "<br>type connect2";
  //echo "<script src='js/cookiesManager.js'></script>";
   //echo "<script>createCookie('login','".$email."',365);";
   //echo "createCookie('hash','".$user['password']."',365);</script>";

    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['type'] = $user['type'];
    $_SESSION['classe'] = $user['classe'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['last_name'] = $user['last_name'];
    $_SESSION['active'] = $user['active'];
    // This is how we'll know the user is logged in
    $_SESSION['logged_in'] = true;

  if(isset($_SESSION['url'])){$url=$_SESSION['url'];}else{$url='decks.php';}
  header("location: ".$url);exit();
  echo 'goUrl :'.$url;
  }


  if($type=="register")
  {
    echo "<br>type register";
    //check if not exist yet
    $result = $mysqli->query("SELECT COUNT(*) as num_rows FROM users WHERE email='".$email."'");
    $rows = $result->fetch_assoc();

    if ( $rows["num_rows"] != 0 ){echo "il y a deja un compte";
      $result = $mysqli->query("SELECT * FROM users WHERE email='".$email."'");
      while($user = $result->fetch_assoc())
      {}
      $result->free();
      $_SESSION['user_id'] = $user['user_id'];
      $_SESSION['type'] = $user['type'];
      $_SESSION['classe'] = $user['classe'];
      $_SESSION['email'] = $user['email'];
      $_SESSION['first_name'] = $user['first_name'];
      $_SESSION['last_name'] = $user['last_name'];
      $_SESSION['active'] = $user['active'];
      // This is how we'll know the user is logged in
      $_SESSION['logged_in'] = true;
    }
    else{
        $today=date("Y-m-d");
        $sql = "INSERT INTO users (first_name,googleID, last_name, email,type,active,jourInscription) "
            . "VALUES ('$first_name',".$googleID.",'$last_name','$email','eleve',1,'$today')";
            $mysqli->query($sql);
            $user_id = $mysqli->insert_id;

            $_SESSION['user_id'] = $user_id;
            $_SESSION['school'] = "";
            $_SESSION['type'] = "eleve";
            $_SESSION['email'] = $email;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['last_name'] = $last_name;
            $_SESSION['active'] = 1;
            // This is how we'll know the user is logged in
            $_SESSION['logged_in'] = true;
  }
  if(isset($_SESSION['url'])){$url=$_SESSION['url'];}else{$url='decks.php';}
  header("location: ".$url);exit();
  echo 'go :'.$url;
}
 else {
   echo "<br>pas de Payload";
  $_SESSION['message'] = __("Votre identification a échoué.");
  header("location: error.php");exit();
  echo 'goError :'.$_SESSION['message'];
}
}
echo "<br>fin";
$_SESSION['message'] = __("Un problème est survenu, contactez M Elisée.");
echo 'goError :'.$_SESSION['message'];
  header("location: error.php");exit();

?>
