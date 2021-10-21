<?php
include_once ("db.php");
session_start();
if($_SERVER['HTTP_HOST']=="www.vocabulaire.ovh" || $_SERVER['HTTP_HOST']=="vocabulaire.ovh"){header('Location: https://exolingo.com');exit();}
include_once ("local_lang.php");
//récupération des infos pour le compteur.
		$ActiviteGlobal=array();
		$sql="SELECT COUNT(*) as num FROM activiteGlobal WHERE 1";
		$result = $mysqli->query($sql);
		$row = $result->fetch_assoc();
						$nbreTotalExo=$row["num"];
		$result->close();


		$sql="SELECT COUNT(*) as num FROM users WHERE active=1";
		$result = $mysqli->query($sql);
		$row = $result->fetch_assoc();
						$nbreTotalUsers=$row["num"];
		$result->close();


		$sql="SELECT COUNT(*) as nbreCards FROM cards WHERE 1";
		$result = $mysqli->query($sql);
		$row = $result->fetch_assoc();
		$nbreTotalCards=$row["nbreCards"];
		$result->close();
		$connextButtonText=__("Se connecter");
		if(isset($_SESSION["first_name"])){$connextButtonText=__("Salut")." <span class='nameUser'>".$_SESSION["first_name"]."</span>".__("! Clique ici");}
?>
<!DOCTYPE html>
<html translate="no" lang="<?php echo($lang_interface); ?>">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
	<meta name="google" content="notranslate">
	<link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
  <link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16" />
  <title>ExoLingo</title>

  <!-- Custom fonts for this theme -->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css">

  <!-- Theme CSS -->
	<link href="css/myStyle.css" rel="stylesheet">
  <link href="css/freelancer.min.css" rel="stylesheet">

	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script src="js/cookiesManager.js"></script>
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-140408884-1"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
		//gtag call moved to cookiePolicyPopUp.php

	</script>

<script data-ad-client="ca-pub-6857139934529342" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
</head>
<style>
.feat-item-fo:hover {

    -webkit-box-shadow: 0px 10px 25px rgba(0,0,0,.1);
    -moz-box-shadow: 0px 10px 25px rgba(0,0,0,.1);
    -o-box-shadow: 0px 10px 25px rgba(0,0,0,.1);
    box-shadow: 0px 10px 25px rgba(0,0,0,.1);
    position: relative;
    z-index: 9;
    transform: translate(0,-5px);
    border: 0px solid transparent;}
.feat-item-fo {padding:20px;
    text-align:center;}
.feat-item-fo img{width:70px;}
/*linear-gradient(90deg,#0aff9e,#1eb289)*/
.btnStartNow{background: linear-gradient(270deg,#e13e5e,#e918c2);
    border-radius: 4px;
    color: #fff;
    font-size: 1.3em;
    padding: 17px 27px;
    display: inline-block;
    border-radius:40px;
  border:none;
  margin:50px 30px;
transition:1s;
text-decoration:none;color:white;}

.btnStartNow:hover{background: linear-gradient(270deg,#E1396B,#E099A1);transform:scale(1.2);color:white;text-decoration:none;}
.flag{margin:15px;display:inline-block;transition:0.2s;padding:10px;}
.flag{box-shadow:0px 0 2px #e0e0e0;border-radius:5px;}
.flag:hover{box-shadow:0px 0 5px grey;}
.flag img{width:60px; height:40px;box-shadow:0 0 2px grey;}
.conter{color:#e13e5e;background: #f0f0f0;border-radius:10px;margin:20px;font-size:1em;}
.imgConter{background: linear-gradient(0deg,#e13e5e,#e918c2);padding:10px;object-fit:contain;height:70px;border-radius:35px;margin:10px;}
.numconter{font-size:1.5em;font-weight:bold;}
.labelconter{opacity:0.8;color:grey;}
.page-section{padding:2rem 0}
.bg-secondary {
    background-color: white!important;
}
#mainNav .navbar-nav li.nav-item a.nav-link {
    color: #303030;}
#mainNav .navbar-brand {
    color:  #303030;
}
.justifyP{text-align: justify;
  text-justify: inter-word;}
	#mainNav .navbar-nav li.nav-item a.nav-link:active, #mainNav .navbar-nav li.nav-item a.nav-link:focus {
	 color: #303030;
	}
.nameUser{text-transform:capitalize;}
.choixLangue{display:inline-block;background-color:white; padding:10px;border-radius:0 0 30px 30px;box-shadow:0px 2px 3px grey;text-align:center;z-index:1031;}
    .selectLang ul li{list-style-type: none;}
    .selectLang ul {padding:0;}
#mainNav{box-shadow:0 0 3px grey;}
.tiretteLang{transition:1s;z-index:1032;overflow:visible;height:0;right:0px;width:115px;text-align:center;position:absolute;bottom:0;}
.lang_item{text-decoration:none;color:black;padding:5px; margin:10px;display:inline-block;box-shadow:0 0 3px grey;background-color:#ffffff80;border-radius:10px;}
.flagStd {width:80px;height:50px;margin:5px;}
</style>
<body id="page-top" translate="no">

  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg bg-secondary fixed-top" id="mainNav">

    <div class="container">
      <a class="navbar-brand js-scroll-trigger" href="#page-top"><img src="img/logo.svg" width="40px" style="margin-right:30px;">ExoLingo</a>
      <button class="navbar-toggler navbar-toggler-right text-uppercase font-weight-bold bg-primary text-white rounded" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <?php echo __("Menu");?>
        <i class="fas fa-bars"></i>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">

        <ul class="navbar-nav ml-auto">

          <li class="nav-item mx-0 mx-lg-1">
            <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="#conteur"><?php echo __("Communauté");?></a>
          </li>

          <li class="nav-item mx-0 mx-lg-1">
            <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="#kesako"><?php echo __("Philosophie");?></a>
          </li>

          <li class="nav-item mx-0 mx-lg-1">
            <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="#about"><?php echo __("Génèse");?></a>
          </li>
					<!--<li class="nav-item mx-0 mx-lg-1">
            <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="./Payments"><?php echo __("Tarifs");?></a>
          </li>-->
          <li class="nav-item mx-0 mx-lg-1">
            <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="#contact"><?php echo __("Contact");?></a>
          </li>



        </ul>
				
      </div>
			<div class="tiretteLang">
				<div class="choixLangue" onclick="$('.selectLang').slideToggle();" onmouseleave="$('.selectLang').slideUp();">
					<div class="tinyFlag flag_<?php echo $lang_interface;?>" ></div>
					<div style="display:none;" class="selectLang">
						<ul>
							<li><a href="index.php?lang=fr_FR">Français</a></li>
							<li><a href="index.php?lang=en_US">English</a></li>
						<ul>
					</div>
			</div>
			</div>
    </div>

  </nav>

  <!-- Masthead -->
  <header class="masthead text-white text-center" style="padding-top:150px;background-image:url(img/fond2.svg);background-repeat:no-repeat;background-position:bottom left;background-size:cover;">
    <div class="container d-flex align-items-center flex-column" >

      <!-- Masthead Avatar Image -->
      <!--<img src="img/logo1.svg" alt="" style="width:200px;max-width:90%;margin:30px 0 50px;">-->

      <!-- Masthead Heading -->
      <!--<h1 class="heading mb-0"><?php //echo __("Apprentissage ludique du vocabulaire en classe et à la maison");?></h1>-->
			<h1 class="heading mb-0"><?php echo __("Un complément ludique de vocabulaire<br>en classe et à la maison.");?></h1>
      <!-- Icon Divider -->
            <!-- Masthead Subheading -->
		<p class="masthead-subheading font-weight-light mb-0" style="background-color:yellow; color:black; padding:40px; margin:30px 0;font-size:1.3em;opacity:0.7;"><?php echo __("Exolingo est en maintenance pendant les vacances.");?></p>
      	
      <p class="masthead-subheading font-weight-light mb-0" style="margin:30px 0;font-size:1.3em;opacity:0.7;"><?php echo __("Gardez le contrôle avec ExoLingo. Choisissez quoi et comment apprendre.");?></p>
      
      

    </div>
  </header>

	  <section class="page-section" id="conteur" style="padding-top:0rem;text-align:center;">
    <h3 class="text-center text-secondary mb-0" style="padding-bottom:40px;"><?php echo __("Rejoignez la communauté");?></h3>

    <div class="container">
    <div class="row">
      <div class="col-md-4 ">
        <div class="feat-item-fo conter">
          <img class="imgConter" src="img/statW.png"><div class="numconter"><?php echo number_format ( $nbreTotalExo , 0 , "," ,  " " );?></div><div class="labelconter"><?php echo __("exercices");?></div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="feat-item-fo conter">
          <img class="imgConter" src="img/usersW.png"><div class="numconter"><?php echo number_format ($nbreTotalUsers, 0 , "," ,  " ");?></div><div class="labelconter"><?php echo __("élèves et professeurs");?></div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="feat-item-fo conter">
          <img class="imgConter" src="img/cardsW.png"><div class="numconter"><?php echo number_format ($nbreTotalCards, 0 , "," ,  " ");?></div><div class="labelconter"><?php echo __("cartes");?></div>
        </div>
      </div>
    </div>
  </div>
  </section>

  <!--<section class="" id="langues" style="padding-top:0rem;text-align:center;">
    <div style="display:inline-block;margin-top:50px;">
    <div class="flag"><img src="img/flag/France.png"><div class="labelLang">Français</div></div>
    <div class="flag"><img src="img/flag/UK.png"><div class="labelLang">Anglais</div></div>
    <div class="flag"><img src="img/flag/esperanto.jpg"><div class="labelLang">Esperanto</div></div>
    <div class="flag"><img src="img/flag/China.png"><div class="labelLang">Chinois</div></div>
    <div class="flag"><img src="img/flag/Italy.png"><div class="labelLang">Italien</div></div>
    <div class="flag"><img src="img/flag/Germany.png"><div class="labelLang">Allemand</div></div>
    <div class="flag"><img src="img/flag/Lithuania.png"><div class="labelLang">Lituanien</div></div>
    <div class="flag"><img src="img/flag/Turkey.png"><div class="labelLang">Turque</div></div>
    <div class="flag"><img src="img/flag/arabic.png"><div class="labelLang">Arabic</div></div>
    <div class="flag"><img src="img/flag/Russe.png"><div class="labelLang">Russe</div></div>
  </div>
</section>-->
  <!-- Portfolio Section -->
  <section class="page-section portfolio" id="kesako">
    <div class="container">

      <!-- Portfolio Section Heading -->
      <h3 class="text-center text-secondary mb-0" style="padding-bottom:40px;"><?php echo __("Philosophie");?></h3>



      <!-- Portfolio Grid Items -->
      <div class="row">
        <div class="col-md-4">
          <div class="feat-item-fo">
            <img src="img/iconContext.png">
            <h4><?php echo __("L'apprenant garde le contrôle");?></h4>
            <p class="justifyP"><?php echo __("L'apprenant garde le contrôle de ce qu'il souhaite apprendre et comment l'apprendre en choisissant ses cartes puis un type de jeu.");?></p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feat-item-fo">
            <img src="img/iconColaboration.png">
            <h4><?php echo __("Collaboratif");?></h4>
            <p class="justifyP"><?php echo __("La création de contenu peut se faire à plusieurs simultanément. ");?><br><?php echo __("Créer de super listes et gagner des points si vos amis les utilisent.");?></p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feat-item-fo">
            <img src="img/iconGame.png">
            <h4><?php echo __("Ludique");?></h4>
            <p class="justifyP"><?php echo __("En plus des révisions sous forme de jeu, gagnez des points et passez des niveaux. Soyez le premier de votre classe dans le tableau d'avancement hebdomadaire.");?></p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feat-item-fo">
            <img src="img/iconLearning.png">
            <h4><?php echo __("Apprendre pour toujours");?></h4>
            <p class="justifyP"><?php echo __("On sait que la méthode la plus efficace est de travailler en espacant les révisions.");?> <br><?php echo __("ExoLingo vous proposera de revoir les cartes que vous êtes sur le point d'oublier.");?></p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feat-item-fo">
            <img src="img/iconVictory.png">
            <h4><?php echo __("Quiz en classe");?></h4>
            <p class="justifyP"><?php echo __("Une activité que les élèves adorent avec ExoLingo. Faite votre évaluation en classe avec les smartphones ou les ordinateurs des élèves. 15 seconde par question... Qui sera le champion ?");?></p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feat-item-fo">
            <img src="img/iconSchool.png">
            <h4><?php echo __("Dans la classe");?></h4>
            <p class="justifyP"><?php echo __("ExoLingo s'intègre parfaitement en classe avec les évaluations et un suivi individualisé des élèves par leurs professeurs.");?></p>
          </div>
        </div>

      </div>
      <!-- /.row -->
        <center><a href="loginPage.php" class="btnStartNow" style=""><?php echo __("Commencer maintenant !");?></a></center>

    </div>

  </section>

  <!-- About Section -->
  <section class="page-section bg-primary text-white mb-0" id="about">
    <div class="container">

      <!-- About Section Heading -->
      <h3 class="text-center text-white mb-0" style="padding-bottom:40px;"><?php echo __("Génèse");?></h3>

      <!-- About Section Content -->
      <div class="row">
        <div class="col-md-8 ml-auto mr-auto" style="font-size:1.2em;">
          <p><?php echo __("L'histoire de ce site commence en 2014. Tombé amoureux en Lituanie, j'ai voulu apprendre cette langue mais les ressource trouvées n'étaient pas satisfaisantes.");?></p>
          <p><?php echo __("C'est pour cela que cette plateforme a été créée. Elle permet à l'apprenant de choisir les mots les plus pertinents pour constituer son vocabulaire,
          de gardez la maîtrise de ce qu'il mémorise et la façon dont il l'apprend. L'algorithme se charge de lui rappeler quand réviser.");?></p>
					<p><?php echo __("L'algorithme espace les révisions et vous proposera de revoir les cartes de moins en moins souvent en fonction des paramètres suivants :");?>
						<ul>
							<li><?php echo __("Assiduité de l'apprenant");?></li>
							<li><?php echo __("Type d'exercice réalisé");?></li>
							<li><?php echo __("Temps écoulé depuis la dernière révision");?></li>
							<li><?php echo __("Succés et échecs précédents");?></li>
							<li><?php echo __("Difficulté de la carte");?></li>
						</ul>
					</p>
				  <p><?php echo __("ExoLingo est utilisé au lycée Notre-Dame de Sion à Istanbul pour l'enseignement du français et a été rapidement plébicité par ses élèves et ses professeurs.");?>
          <br><?php echo __("Encouragé par leurs enthousiasme, je souhaite diffuser cet outil au plus grand nombre.");?></p>

          <div style="text-align:right;"><i>Elisée Reclus</i></div>
        </div>
      </div>

      <!-- About Section Button -->


    </div>
  </section>

  <!-- Contact Section -->
  <section class="page-section" id="contact">
    <div class="container">

      <!-- Contact Section Heading -->
      <h3 class="text-center text-secondary mb-0"><?php echo __("Pour me contacter");?></h3>



      <!-- Contact Section Form -->
      <div class="row">


        <div class="col-lg-8 mx-auto">
          <!-- To configure the contact form email address, go to mail/contact_me.php and update the email address in the PHP file on line 19. -->
          <form name="sentMessage" id="contactForm" novalidate="novalidate">
            <div class="control-group">
              <div class="form-group floating-label-form-group controls mb-0 pb-2">
                <label><?php echo __("Prénom");?></label>
                <input class="form-control" id="name" type="text" placeholder="Nom" required="required" data-validation-required-message="Please enter your name.">
                <p class="help-block text-danger"></p>
              </div>
            </div>
            <div class="control-group">
              <div class="form-group floating-label-form-group controls mb-0 pb-2">
                <label><?php echo __("Adresse email");?></label>
                <input class="form-control" id="email" type="email" placeholder="Adresse email" required="required" data-validation-required-message="Please enter your email address.">
                <p class="help-block text-danger"></p>
              </div>
            </div>

            <div class="control-group">
              <div class="form-group floating-label-form-group controls mb-0 pb-2">
                <label><?php echo __("Message");?></label>
                <textarea class="form-control" id="message" rows="5" placeholder="Message" required="required" data-validation-required-message="Please enter a message."></textarea>
                <p class="help-block text-danger"></p>
              </div>
            </div>
            <br>
            <div id="success"></div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary btn-xl" id="sendMessageButton"><?php echo __("Envoyer");?></button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </section>

  <!-- Footer -->
  <footer class="footer text-center">
    <div class="container">
      <div class="row">
				<ul>
				<li><a href='CGV.php'>les CGU et les CGV</a></li>
				<li><a href='TermsAndConditions.php'>les termes et conditions d'utilisation</a></li>
				</ul>
				<!--<li><?php //echo $local_lang_cause;?></li>
				<li><?php //echo substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,5);?></li>
				<li><?php //echo in_array($local_lang, $acceptLang);?></li>
				<li><?php //echo 'locallang:'.$local_lang;?></li>
				<li><?php //echo 'langinterface:'.$lang_interface;?></li>
				<li><?php //echo 'session locallang:'.$_SESSION['local_lang'];?></li>
				<li><?php //echo 'old session:'.$oldSessionLang;?></li>
         Footer Location
        <div class="col-lg-4 mb-5 mb-lg-0">
          <h4 class="text-uppercase mb-4">Location</h4>
          <p class="lead mb-0">30 Bld Béranger
            <br>37000 Tours</p>
        </div>

        <!-- Footer Social Icons -->


        <!-- Footer About Text -->


      </div>
    </div>
  </footer>
  <!-- Copyright Section -->
  <section class="copyright py-4 text-center text-white">
    <div class="container">
      <small>Copyright &copy; ExoLingo 2019</small>
    </div>
  </section>

  <!-- Scroll to Top Button (Only visible on small and extra-small screen sizes) -->
  <div class="scroll-to-top d-lg-none position-fixed ">
    <a class="js-scroll-trigger d-block text-center text-white rounded" href="#page-top">
      <i class="fa fa-chevron-up"></i>
    </a>
  </div>

  <!-- Portfolio Modals -->
  <!-- Bootstrap core JavaScript -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Plugin JavaScript -->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Contact Form JavaScript -->
  <script src="js/jqBootstrapValidation.js"></script>
  <script src="js/contact_me.js"></script>

  <!-- Custom scripts for this template -->
  <script src="js/freelancer.min.js"></script>
	<?php
	include_once ("cookiePolicyPopUp.php");
	?>
</body>

</html>
