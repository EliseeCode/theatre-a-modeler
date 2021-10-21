<?php
/* User login process, checks if user exists and password is correct */
require 'db.php';
session_start();
include_once ("local_lang.php");
// Escape email to protect against SQL injections
$email = $mysqli->escape_string($_GET['login']);
//$rememberMe = (int)($_POST['rememberMe']);
$hash = $mysqli->escape_string($_GET['hash']);

$result = $mysqli->query("SELECT * FROM users WHERE email='$email'");

if ( $result->num_rows == 0 ){ // User doesn't exist
    $_SESSION['message'] = __("Aucun utilisateur inscrit avec cette adresse email !");
    header("location: loginPage.php");
    exit();
}
else { // User exists
    $user = $result->fetch_assoc();
    if ( $hash == $user['password'] && $user['active']==1) {
      //mise en place de la session et connexion
      $_SESSION["active"]=1;
      $_SESSION["user_id"]=$user["user_id"];
      header("location: loginSession.php");
      exit();
    }
    else {
		if($user['active']==0)
		{
			$_SESSION['user_id'] = $user['user_id'];
			$_SESSION['email'] = $user['email'];
			$_SESSION['first_name'] = $user['first_name'];
			$_SESSION['last_name'] = $user['last_name'];
			$_SESSION['message'] = __("Votre compte n'a pas été validé. Veuillez vérifier vos mails. Si vous n'avez pas reçu de mail, <a href='resendMail.php'>Cliquez ici</a>");}
		else
		{$_SESSION['message'] = __("Vous avez taper un mauvais mot de passe ! Essayez encore.");}
      header("location: loginPage.php");
      exit();
    }
    echo "fin";
}
