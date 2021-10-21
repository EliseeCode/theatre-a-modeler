<?php
/* Resend mail
 */
  include_once ("db.php");
 session_start();
include_once ("local_lang.php");
echo('in resendMail');
// Escape all $_POST variables to protect against SQL injections
$first_name = $mysqli->escape_string($_SESSION['first_name']);
$last_name = $mysqli->escape_string($_SESSION['last_name']);
$email = $mysqli->escape_string($_SESSION['email']);

$password = $mysqli->escape_string(password_hash($_POST['password'], PASSWORD_BCRYPT));
$hash=$mysqli->query("SELECT hash FROM users WHERE email='".$email."'")->fetch_object()->hash;

        $_SESSION['active'] = 0; //0 until user activates their account with verify.php
        $_SESSION['logged_in'] = false; // So we know the user has logged in
        $_SESSION['message'] =__("Un lien pour confirmer votre inscription vous a été envoyé à $email, Veuillez valider votre compte en cliquant sur le lien dans l'email!");

        // Send registration confirmation link (verify.php)

        //-----------------------------------------------
        //DECLARE LES VARIABLES
        //-----------------------------------------------

        // Send registration confirmation link (verify.php)

        $destinataire=$email;
        $email_expediteur='inscription@exolingo.com';
        $email_reply='no-reply@exolingo.com';
        $sujet='Validation du compte ( ExoLingo )';
        $message_texte=__('Bonjour ').$first_name.','.
             __('Veuillez suivre le lien suivant pour valider votre inscription : ').
             'https://'.$_SERVER['HTTP_HOST'].'/verify.php?email='.$email.'&hash='.$hash;



        $message_html=__('Bonjour ').$first_name.','."<br>".
             __('Merci pour votre intérêt !<br>Veuillez cliquer sur le lien suivant pour valider votre inscription :<br>').
             '<a href="https://'.$_SERVER['HTTP_HOST'].'/verify.php?email='.$email.'&hash='.$hash.'">http://'.$_SERVER['HTTP_HOST'].'/verify.php?email='.$email.'&hash='.$hash.'</a>';


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

        $result=mail($destinataire,$sujet,$message,$headers,"-f ".$email_expediteur) ;
         if (!$result)
         {
         $_SESSION['message'] =__("Le mail de confirmation n'a pas pu être envoyé. Merci de contacter l'administrateur du site.");
         echo "Le mail de conf n a pas plus envoyé";
         header("location: error.php");
         }
         else
         {
         echo __("Le mail de conf a été envoyé");
         header("location: message.php");
         }
