<?php
/* Displays all error messages */
include_once ("db.php");
session_start();
include_once ("local_lang.php");
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

        <h1><span class="titre1">exoLongo</span></h1>

	</div>
    </div>
	<div class="center">
<div class="form">
    <h1><?= 'Ça roule'; ?></h1>
    <p>
    <?php
    if( isset($_SESSION['message']) AND !empty($_SESSION['message']) )
        {echo $_SESSION['message'];}
    else{header( "location: index.php" );}
    ?>
    </p>
    <a href="index.php"><button class="button button-block"/><?php echo __("Retour");?></button></a>
</div>
</div>
</body>
</html>
