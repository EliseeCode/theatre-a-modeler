<?php
/* Registration process, inserts user info into the database
   and sends account confirmation email message
 */
include_once ("local_lang.php");
$_SESSION['logged_in'] = 0;
// Set session variables to be used on profile.php page
$_SESSION['type'] = "";
$_SESSION['email'] = strtolower($_POST['email']);
$_SESSION['first_name'] = $_POST['firstname'];
$_SESSION['last_name'] = $_POST['lastname'];

// Escape all $_POST variables to protect against SQL injections
$first_name = $mysqli->escape_string($_POST['firstname']);
$last_name = $mysqli->escape_string($_POST['lastname']);
$email = $mysqli->escape_string($_POST['email']);
$email=strtolower($email);
if(isset($_SESSION['local_lang'])){
$lang=$mysqli->escape_string($_SESSION['local_lang']);
}
else{$lang="en_US";}

$type="";

$password = $mysqli->escape_string(password_hash($_POST['password'], PASSWORD_BCRYPT));

$hash = $mysqli->escape_string( md5( rand(0,1000) ) );

// Check if user with that email already exists
$result = $mysqli->query("SELECT * FROM users WHERE email='$email'") or die($mysqli->error());

// We know user email exists if the rows returned are more than 0
if ( $result->num_rows > 0 ) {

    $_SESSION['message'] = __('Un utilisateur avec cette adresse email existe déjà !');
    header("location: message.php");
    exit();

}
else { // Email doesn't already exist in a database, proceed...

    // active is 0 by DEFAULT (no need to include it here)
    $today=date("Y-m-d");
    $avatar_id=rand(0,719);
    $sql = "INSERT INTO users (first_name, last_name, email, password, hash,type,lang,avatar_id) "
            . "VALUES ('$first_name','$last_name','$email','$password', '$hash','$type','$lang','$avatar_id')";

    // Add user to the database
    if ( $mysqli->query($sql) ){
      $user_id = $mysqli->insert_id;
      //Création de la classe perso de l'utilisateur
        $sql = "INSERT INTO `user_avatar`(`user_id`, `avatar_id`, `status`)
        VALUES (".$user_id.",".$avatar_id.",'selected')";
        $mysqli->query($sql);

        $sql = "INSERT INTO `classes`(`class_name`,`creator_id`, `niveau`, `promo`, `status`, `school_id`, `code`, `lang`)
        VALUES ('Mon lexique',".$user_id.",'','','perso',0,'','')";
        $mysqli->query($sql);
        $class_id = $mysqli->insert_id;
        $sql = "INSERT INTO `user_class`(`user_id`, `class_id`, `position`, `role`)
        VALUES (".$user_id.",".$class_id.",0,'perso')";
        $mysqli->query($sql);
        $sql = "INSERT INTO `user_class`(`user_id`, `class_id`, `position`, `role`)
        SELECT ".$user_id.",class_id,0,'explore' FROM classes WHERE status='explore'";
        $mysqli->query($sql);

        $_SESSION['active'] = 0; //0 until user activates their account with verify.php
        //$_SESSION['logged_in'] = true; // So we know the user has logged in
        $_SESSION['message'] =

                 __("Un lien pour confirmer votre inscription vous a été envoyé à $email, Veuillez valider votre compte en cliquant sur le lien dans l'email!");

     //-----------------------------------------------
     //DECLARE LES VARIABLES
     //-----------------------------------------------

	 // Send registration confirmation link (verify.php)

     $destinataire=$email;
     $email_expediteur='inscription@exolingo.com';
     $email_reply='no-reply@exolingo.com';
	   $sujet='Validation du compte ( ExoLingo )';
     $message_texte=__('Bonjour ').$first_name.','.
					__('Veuillez suivre le lien suivant pour valider votre inscription').' : '.
					'http://'.$_SERVER['HTTP_HOST'].'/verify.php?email='.$email.'&hash='.$hash;



     $message_html=__('Bonjour ').$first_name.','."<br>".
					__('Veuillez cliquer sur le lien suivant pour valider votre inscription').' :<br>'.
					'<a href="http://'.$_SERVER['HTTP_HOST'].'/verify.php?email='.$email.'&hash='.$hash.'">http://www.exolingo.com/verify.php?email='.$email.'&hash='.$hash.'</a>';


     //-----------------------------------------------
     //GENERE LA FRONTIERE DU MAIL ENTRE TEXTE ET HTML
     //-----------------------------------------------

     $frontiere = '-----=' . md5(uniqid(mt_rand()));

     //-----------------------------------------------
     //HEADERS DU MAIL
     //-----------------------------------------------

	 $headers = 'From: "ExoLingo" <'.$email_expediteur.'>'."\n";
     $headers .= 'Return-Path: <'.$email_reply.'>'."\n";
     $headers .= 'MIME-Version: 1.0'."\n";
     $headers .='List-Unsubscribe: <http://www.exolingo.com/profile.php?cmd=unsub>'."\n";
     $headers .= 'Content-Type: multipart/alternative; boundary="'.$frontiere.'"';

     //-----------------------------------------------
     //MESSAGE TEXTE
     //-----------------------------------------------
     $message = 'This is a multi-part message in MIME format.'."\n\n";

     $message .= '--'.$frontiere."\n";
     $message .= 'Content-Type: text/plain; charset="iso-8859-1"'."\n";
     $message .= 'Content-Transfer-Encoding: 8bit'."\n\n";
     $message .= $message_texte."\n\n";

     //-----------------------------------------------
     //MESSAGE HTML
     //-----------------------------------------------
     $message .= '--'.$frontiere."\n";
     $message .= 'Content-Type: text/html; charset="iso-8859-1"'."\n";
     $message .= 'Content-Transfer-Encoding: 8bit'."\n\n";
     $message .= $message_html."\n\n";

     //$message .= '--'.$frontiere."\n";

    $file = fopen("../log_register_mail.txt","ab");
    echo fwrite($file,"\n".$destinataire);
    fclose($file);

	 $result=mail($destinataire,$sujet,$message,$headers,"-f ".$email_expediteur) ;
			if (!$result)
			{
			$_SESSION['message'] =__("Le mail de confirmation n'a pas pu être envoyé. Merci de contacter l'administrateur du site.");
			echo "Le mail de conf n'a pas pu être envoyé";
      $file = fopen("../log_register_mail.txt","ab");
      echo fwrite($file,"-"."fail");
      fclose($file);
			header("location: message.php");
      exit();
			}
			else
			{
			echo __("Le mail de conf a été envoyé");
      $file = fopen("../log_register_mail.txt","ab");
      echo fwrite($file,"-"."ok");
      fclose($file);
			header("location: message.php");
      exit();
			}
	}

    else {
        $_SESSION['message'] = __("L'enregistrement a échoué!");
		echo "L'enregistrement à échouer";
        header("location: message.php");
        exit();
    }

}
