<?php
/* The password reset form, the link to this page is included
   from the forgot.php email message
*/
include_once ("db.php");
session_start();
include_once ("local_lang.php");

// Make sure email and hash variables aren't empty
if( isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['hash']) && !empty($_GET['hash']) )
{
    $email = $mysqli->escape_string($_GET['email']);
    $hash = $mysqli->escape_string($_GET['hash']);

    // Make sure user email with matching hash exist
    $result = $mysqli->query("SELECT * FROM users WHERE email='$email' AND hash='$hash'");

    if ( $result->num_rows == 0 )
    {
        $_SESSION['message'] = __("L'URL pour la réinitialisation du mot de passe est erronée !");
        header("location: error.php");
    }
}
else {
    $_SESSION['message'] = __("Veuillez essayer à nouveau, la vérification a échoué !");
    header("location: error.php");
}
?>
<!DOCTYPE html>
<html >
 <head>
  <?php include 'css/css.html'; ?>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Success</title>
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

<nav class="navbar navbar-inverse">
      <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php">ExoLingo</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav">
          </ul>
		  <ul class="nav navbar-nav navbar-right">
			<!--<li><a href="#signup"><span class="glyphicon glyphicon-user"></span> </a></li>-->
			<li><a href="index.php"><span class="glyphicon glyphicon-log-out"></span> <?php echo __("retour");?></a></li>
			</ul>
        </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>

    <!-- JUMBOTRON -->
    <div class="jumbotron text-center">
      <div class="container">

        <h1><span class="titre1">ExoLingo</span></h1>

	</div>
    </div>
	<div class="center">
    <div class="form">

          <h1><?php echo __("Choisissez votre nouveau mot de passe");?></h1>

          <form action="reset_password.php" method="post">

          <div class="field-wrap">
            <label>
              <?php echo __("Nouveau mot de passe");?><span class="req">*</span>
            </label>
            <input type="password"required name="newpassword" autocomplete="off"/>
          </div>

          <div class="field-wrap">
            <label>
              <?php echo __("Confirmez votre mot de passe");?><span class="req">*</span>
            </label>
            <input type="password"required name="confirmpassword" autocomplete="off"/>
          </div>

          <!-- This input field is needed, to get the email of the user -->
          <input type="hidden" name="email" value="<?= $email ?>">
          <input type="hidden" name="hash" value="<?= $hash ?>">

          <button class="button button-block"/><?php echo __("Valider");?></button>

          </form>

    </div>
  </div>
<script src='js/jquery-3.3.1.min.js'></script>
<script src="js/index.js"></script>

</body>
</html>
