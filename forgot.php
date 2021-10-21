<?php
/* Reset your password form, sends reset.php password link */
include_once ("db.php");
session_start();
include_once ("local_lang.php");

// Check if form submitted with method="post"
if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
    $email = str_replace(" ", "", $_POST['email']);
    $result = $mysqli->query("SELECT * FROM users WHERE email='$email'");

    if ( $result->num_rows == 0 ) // User doesn't exist
    {
        $_SESSION['message'] = __("Aucun utilisateur inscrit avec cette adresse email");
        //echo "error";
        header("location: error.php");
    }
    else { // User exists (num_rows != 0)

        $user = $result->fetch_assoc(); // $user becomes array with user data

        $email = $user['email'];
        $hash = $user['hash'];
        $first_name = $user['first_name'];

        // Session message to display on success.php
        $_SESSION['message'] = __("<p>Veuillez consulter votre boîte mail  <span>").$email.__("</span> pour confirmer la réinitialisation de votre mot de passe!</p>");


        //echo "success";
        // Send registration confirmation link (verify.php)

        //-----------------------------------------------
        //DECLARE LES VARIABLES
        //-----------------------------------------------

        // Send registration confirmation link (verify.php)

        $destinataire=$email;
        $email_expediteur='inscription@exolingo.com';
        $email_reply='no-reply@exolingo.com';
        $sujet=__('Reinitialisation du mot de passe ( ExoLingo )');
        $message_texte=__('Bonjour ').$first_name.','.
        __('Vous avez demander la réinitialisation de votre mot de passe ! Cliquez sur le lien suivant pour réinitialiser votre mot de passe').':'.
        'https://exolingo.com/reset.php?email='.$email.'&hash='.$hash;



        $message_html=__('Bonjour ').$first_name.','."<br>".
        __('Vous avez demander la réinitialisation de votre mot de passe !<br>Cliquez sur le lien suivant pour réinitialiser votre mot de passe').':<br>'.
             '<a href="https://exolingo.com/reset.php?email='.$email.'&hash='.$hash.'">https://exolingo.com/reset.php?email='.$email.'&hash='.$hash.'</a>';


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
        echo $message;
        $result=mail($destinataire,$sujet,$message,$headers,"-f ".$email_expediteur) ;
         if (!$result)
         {
         $_SESSION['message'] =__("Le mail de réinitialisation de votre mot de passe n'a pas pu être envoyé. Merci de contacter l'administrateur du site (M Elisée).");
         echo "Le mail de conf n a pas plus envoyé";
         header("location: error.php");
         }
         else
         {
         echo "Le mail de conf a été envoyé";
         header("location: message.php");
         }




  }
}
include "css/css.html";
?>
<!DOCTYPE html>
<html >
 <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo __("Réinitialisation de votre mot de passe");?></title>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/main.css" rel="stylesheet">
    <style>
      .navbar{
        margin-bottom:0;
        border-radius:0;
      }
    </style>
</head>

<body class="fond">



    <!-- JUMBOTRON -->
    <div class="jumbotron text-center">
      <div class="container">

        <h1>ExoLingo</h1>

	     </div>
    </div>


  <div class="center">
  <div class="form">

    <h1><?php echo __("Réinitialisation de votre mot de passe");?></h1>

    <form action="forgot.php" method="post">
     <div class="field-wrap">
      <label>
        <?php echo __("Adresse email");?><span class="req">*</span>
      </label>
      <input type="email"required autocomplete="off" name="email"/>
    </div>
    <button class="button button-block"/><?php echo __("Réinitialisation");?></button><br>

    </form>
    <button class="button button-block" onclick="window.location='index.php'"/><?php echo __("Retour");?></button>
  </div>
  </div>
<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/index.js"></script>
</body>

</html>
