<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
require 'db.php';
session_start();
include_once ("local_lang.php");
//echo '<pre>';
//var_dump(session_id()); // Le même devrait être affiché à chaque page
//var_dump($_SESSION);
//echo '</pre>';

// Check if user is logged in using the session variable

    // Makes it easier to read
	$user_id = $_SESSION['user_id'];
    $first_name = $_SESSION['first_name'];
    $last_name = $_SESSION['last_name'];
    $email = $_SESSION['email'];
    $active = $_SESSION['active'];

?>
<!DOCTYPE html>
<html >
 <head>
  <?php include 'css/css.html'; ?>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Messages</title>
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
          <a class="navbar-brand" href="loginPage.php"><span class="titre1">ExoLingo</span></a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav">
          </ul>
		    <ul class="nav navbar-nav navbar-right">
			<!--<li><a href="#signup"><span class="glyphicon glyphicon-user"></span> </a></li>-->
			<li><a href="loginPage.php"><span class="glyphicon glyphicon-log-out"></span> retour</a></li>
			</ul>
        </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>

    <!-- JUMBOTRON -->
    <div class="jumbotron text-center">
      <div class="container">

        <h1><span class="titre1">ExoLingo</span></h1>
 <br>
		</p>
	</div>
    </div>















<div class="center">
  <div class="form">

          <p>
          <?php
          // Display message about account verification link only once
          if ( isset($_SESSION['message']) )
          {
              echo $_SESSION['message'];
              unset( $_SESSION['message'] );
          }
          ?>
          </p>
          <h2 class="titre-form"><?php echo $first_name.' '.$last_name.' '.$user_id; ?></h2>
          <p><?= $email ?></p>

          <a href="logout.php"><button class="button button-block" name="logout"/>Deconnexion</button></a>

    </div>
</div>
<script src="js/jquery-3.3.1.min.js"></script>


</body>
</html>
