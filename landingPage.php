<?php
/* Main page with two forms: sign up and log in */
require 'db.php';
session_start();
include_once ("local_lang.php");
$state = sha1(openssl_random_pseudo_bytes(1024));
  $_SESSION['state']=$state;
  // Set the client ID, token state, and application name in the HTML while
  // serving it.
  echo '<script>state="'.$_SESSION["state"].'";</script>';

?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="google-signin-client_id" content="4631499565-5vhpg20lg2741qdoe7r4mvl4uc7qnfor.apps.googleusercontent.com">
    <meta name="description" content="ExoLingo est une plateforme ludique d'apprentissage du vocabulaire pour les professeurs et les élèves." />
    <meta name="keywords" content="exolingo, vocabulaire, langue, aprrendre, classe, flashcards, flash cards, révision espacées" />
    <title>exoLingo</title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" type="text/css">
    <!--<link href="css/bootstrap.min.css" rel="stylesheet">-->
	  <link href="css/main.css?v=2" rel="stylesheet">
    <link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16" />
    <script src='js/cookiesManager.js'></script>
    <script src='js/jquery-3.3.1.min.js'></script>
    <!--<script src="https://apis.google.com/js/client:platform.js?onload=start" async defer></script>-->
    <!--<script src="https://apis.google.com/js/client:platform.js" async defer></script>-->
    <style>
    @keyframes floating {
        	from {
        		top: 10px;
        	}
        	50% {
          		top: -10px;
          	}
          to {
            		top: 10px;
            }
        }
    .floating{position:relative;animation: 4s floating linear infinite}
      .navbar{
        margin-bottom:0;
        border-radius:0;
      }
      #customBtnConnect{box-sizing: border-box;

    width:100%;}
    .inputTextLog{padding:12px;background-color:white; border:none;border-bottom:grey 2px solid;font-size:1em;}
    .form{max-width:400px;position:relative;padding-bottom:50px;}
    .forgot-link{color:grey;}
    .changeTab{color:grey; font-size:0.8em;position:absolute;text-align:left;bottom:5px;}
    .tab-content{margin-top:50px;}
    #gSignInWrapper:hover{background-color:#f0f0f0;}
    #gSignInWrapper{background-color:#e0e0e0;}
    input:invalid{border-bottom:2px solid var(--mycolor2);}
    .choixLangue{position:absolute; top:0;right:50px;padding:10px;border-radius:0 0 30px 30px;box-shadow:0 0 3px grey;text-align:center;}
    .selectLang ul li{list-style-type: none;}
    .selectLang ul {padding:0;}

    </style>
</head>

<?php
$logOut=0;
if(isset($_GET["logOut"]))
{
  $logOut=(int)$_GET["logOut"];
}
echo "<script>logOut=".$logOut.";</script>";
$url="";
if(isset($_SESSION['url']))
{$url=$_SESSION['url'];}
if($url==""){$url="decks.php";}
if ( isset($_SESSION['logged_in']) && $_SESSION['logged_in']== 1) {
  header("location: ".$url);
  exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
  //foreach($_POST as $key => $value){echo ($key." : ".$value."<br>");}
    if (isset($_POST['login'])) { //user logging in
		require 'login.php';
    }

    elseif (isset($_POST['register_prof'])) { //user registering
        require 'register.php';
    }
	elseif (isset($_POST['register_eleve'])) { //user registering
        require 'register.php';
    }
}
?>

  <body class="fond" style="display:flex;height:100vh;background-color:white;">



    <!-- JUMBOTRON
    <div class="jumbotron text-center">
      <div class="container">



		<!--<h2>l'outil ultime pour faire apprendre le vocabulaire</h2>-->
	<!--</div>
</div>-->
  <div class="choixLangue"><img src="img/lang.png" width="30px" onclick="$('.selectLang').show();"><div style="display:none;" class="selectLang"><ul><li><a href="index.php?lang=fr_FR">Français</a></li><li><a href="index.php?lang=en_US">English</a></li></div></div>

  <div class="center" style="margin:auto;">
    <div style="margin:0px 0 0px;">
      <img src="img/logo.png" class="floating" width="100px" style="margin-bottom:40px;">
      <h1><span class="titre1" style="font-weight:normal"><b >exo</b><span style="color:var(--mycolor2bis);">Lingo</span></span></h1>

  </div>

      <div class="form">
        <div>
          <div id="gSignInWrapper" onclick="oauthSignIn();">
            <div id="customBtnConnect" class="customGPlusSignIn">
              <span class="icon"></span>
              <span class="buttonText"><?php echo __("Se connecter ou s'inscrire avec Google");?></span>
            </div>
          </div>
        </div>
          <div id="name"></div>

        <div class="tab-content">

	    <!--===========================================-->
		<!--===========================================-->
         <div id="login">
          <!--<h3 class="titre-form">On est de retour ?</h3><br>-->
          <form action="index.php" method="post" autocomplete="off">
            <div class="field-wrap">
              <input class='inputTextLog' id="connexEmail" placeholder="<?php echo __("Adresse mail");?>" type="email" style="display:inline-block;" required autocomplete="off" name="email"/><!--<span style="display:inline-block;width:40%;color:black;border: 1px grey solid; padding: 5px;"> @nds.k12.tr</span>-->
          </div>
          <div class="field-wrap">
            <input class='inputTextLog' id="connexPassword" placeholder="<?php echo __("Mot de passe");?>" type="password" required autocomplete="off" name="password"/>
          </div>

          <p class="forgot"><a href="forgot.php" class="forgot-link" style="font-size:0.8em;color:grey;"><?php echo __("Mot de passe oublié ?");?></a></p>
          <br><div>
          <button type="submit" class="button button-block" id="connexBtn" name="login" /><?php echo __("Connexion");?></button>
          </div>
          <div class="changeTab"><?php echo __("Si vous n'avez pas encore de compte");?> : <a href="#signup"><?php echo __("Inscription");?></a></div>
          </form>
          <br>

          <!--<div class="g-signin2" data-onsuccess="onConnect" data-width="300">Connexion via Google</div>-->
        </div>

		<div id="signup">
		<div id="inscr_eleve">
          <!--<h3 class="titre-form">Alors comme ça on veut apprendre le français ?</h3><br>-->

          <form action="index.php" method="post" >

          <div class="top-row">
            <div class="field-wrap">
              <input class='inputTextLog' type="text" placeholder="<?php echo __("Prénom");?>" required name='firstname' />
            </div>

            <div class="field-wrap">
              <input class='inputTextLog' type="text" placeholder="<?php echo __("Nom");?>" required  name='lastname' />
            </div>
          </div>

          <div class="field-wrap">
            <input class='inputTextLog' type="email" placeholder="<?php echo __("Adresse mail");?>" required  name='email' />
          </div>

          <div class="field-wrap">
            <input class='inputTextLog' type="password" required placeholder="<?php echo __("Mot de passe");?>" name='password'/>
          </div>


          <input type="hidden" name="type" value="eleve">
		      <input type="hidden" name="passwordProf" value="">
          <br>
          <div>
            <button type="submit" class="button button-block" name="register_eleve" />S'enregistrer</button>
          </div>

          <div class="changeTab"><?php echo __("Vous avez déjà un compte");?> : <a href="#login"><?php echo __("Connexion");?></a></div>
          </form>


      </div><!--fin inscription éléves-->
        </div>  <!--fin sign up-->

      </div><!-- tab-content -->



<!--<div id="my-signin2"></div>-->
<!--<div class="g-signin2" data-onsuccess="onConnect" onclick="clicFlag();">Connexion via Google</div>-->
</div> <!-- /form -->
<div class="section" style="display:none;">
<p><?php echo __("Voici quelques articles et sites internets lié à l'apprentissage utilisé sur ExoLingo:");?></p>
<div><a href="https://www.supermemo.com/en/archives1990-2015/english/algsm11"><?php echo __("SuperMemo, l'algorithme d'apprentissage espacé");?></a></div>
<div><img src="img/graphSupermemo.png" width="90%"></div>
<div><a href="http://www.sansforgetica.rmit/"><?php echo __("Police de caractère 'sans Forgetica'");?></a></div>
</ul>
</div>
<script>
$('.changeTab').on('click', function (e) {
  e.preventDefault();
  target = $(this).find("a").attr('href');
  $('.tab-content > div').not(target).hide();
  $(target).fadeIn(600);

});
// Parse query string to see if page request is coming from OAuth 2.0 server.

/*
 * Create form to request access token from Google's OAuth 2.0 server.
 */
function oauthSignIn() {
  // Google's OAuth 2.0 endpoint for requesting an access token
  var oauth2Endpoint = 'https://accounts.google.com/o/oauth2/v2/auth';

  // Create <form> element to submit parameters to OAuth 2.0 endpoint.
  var form = document.createElement('form');
  form.setAttribute('method', 'GET'); // Send as a GET request.
  form.setAttribute('action', oauth2Endpoint);
  // Parameters to pass to OAuth 2.0 endpoint.
  var params = {'client_id': '401372344736-b44ekq6ceieeebvbr3lr4dilc5fjcj1k.apps.googleusercontent.com',
                'redirect_uri': 'https://www.exolingo.com/loginGoogle.php',
                'response_type': 'code',
                //'scope': 'https://www.googleapis.com/auth/drive.metadata.readonly',
                'scope': 'openid profile email',
                'include_granted_scopes': 'true',
                'state': state};

  // Add form parameters as hidden input values.
  for (var p in params) {
    var input = document.createElement('input');
    input.setAttribute('type', 'hidden');
    input.setAttribute('name', p);
    input.setAttribute('value', params[p]);
    form.appendChild(input);
  }

  // Add form to page and submit it to open the OAuth 2.0 endpoint.
  document.body.appendChild(form);
  form.submit();
}
/*var revokeAllScopes = function() {
  auth2.disconnect();
}
function start(){
  console.log("func start");
  gapi.load('auth2', function() {
    console.log("start");
    auth2 = gapi.auth2.init({
      client_id: '4631499565-5vhpg20lg2741qdoe7r4mvl4uc7qnfor.apps.googleusercontent.com',
      // Scopes to request in addition to 'profile' and 'email'
      //scope: 'additional_scope'
    });
    attachSignin(document.getElementById('customBtnConnect'));
  });
}
function attachSignin(element) {
    console.log(element.id);
    auth2.attachClickHandler(element, {},
        function(googleUser) {
          document.getElementById('name').innerText = "Signed in: " +
              googleUser.getBasicProfile().getName();
              id_token = googleUser.getAuthResponse().id_token;
              url='storeauthcode.php?type=connect&idtoken='+id_token;
              if(id_token){window.location.href=url;}
        }, function(error) {
          alert(JSON.stringify(error, undefined, 2));
        });
  }

/*function onRegistertest(googleUser){
  var profile = googleUser.getBasicProfile();
  id_token = googleUser.getAuthResponse().id_token;
  console.log('ID: ' + profile.getId()); // Do not send to your backend! Use an ID token instead.
  console.log('Name: ' + profile.getName());
  console.log('Image URL: ' + profile.getImageUrl());
  console.log('Email: ' + profile.getEmail()); // This is null if the 'email' scope is not present.
  url='storeauthcode.php?type=register&idtoken='+id_token;
  console.log("Redirected");
  if(id_token){window.location.href=url;}
}
function onConnecttest(googleUser) {
  var profile = googleUser.getBasicProfile();
  id_token = googleUser.getAuthResponse().id_token;
  console.log('ID: ' + profile.getId()); // Do not send to your backend! Use an ID token instead.
  console.log('Name: ' + profile.getName());
  console.log('Image URL: ' + profile.getImageUrl());
  console.log('Email: ' + profile.getEmail()); // This is null if the 'email' scope is not present.
  url='storeauthcode.php?type=connect&idtoken='+id_token;
  //console.log("Redirected");
  if(id_token){window.location.href=url;}
}*/
</script>


<style type="text/css">
    #customBtnConnect {
      display: flex;

      color: #444;
      padding:2px;

    }
    #customBtnConnect:hover {
      cursor: pointer;
    }
    span.label {
      font-family: serif;
      font-weight: normal;
      vertical-align:middle;
    }
    span.icon {
      background: url('img/google.png') transparent 2px 50% no-repeat;
      display: inline-block;
      width: 20px;
      min-height: 20px;
      background-size: 36px 36px;
      float:left;
      vertical-align:middle;
      padding: 20px;
      background-color: white;
    }
    span.buttonText {
      display: inline-block;

      vertical-align: middle;
      padding-left: 42px;
      padding-right: 42px;
      font-size: 1em;
      font-weight: bold;
      color:#303030;
      padding:5px;
      margin:auto;
      width:100%;
      /* Use the Roboto font that is loaded in the <head> */
      font-family: 'Roboto', sans-serif;
    }
  </style>


<!--<script src="https://apis.google.com/js/platform.js?onload=start" async defer></script>-->
</div><!-- /center -->
    <script src='js/jquery-3.3.1.min.js'></script>
    <script src="js/index.js"></script>
<script>

if(readCookie("login") && readCookie("hash"))
{
  newAdress="loginCookie.php?login="+readCookie("login")+"&hash="+readCookie("hash");
window.location=newAdress;
}
</script>

</body>

</html>
