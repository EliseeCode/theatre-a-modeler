<?php
/* Main page with two forms: sign up and log in */
session_start();
require 'db.php';

?>
<!DOCTYPE html>
<html lang="fr">
  <head>
  <?php include 'css/css.html'; ?>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="google-signin-client_id" content="4631499565-5vhpg20lg2741qdoe7r4mvl4uc7qnfor.apps.googleusercontent.com">
    <title>Vocabulaire</title>
    <!-- Bootstrap -->
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" type="text/css">
    <link href="css/bootstrap.min.css" rel="stylesheet">
	  <link href="css/main.css?v=2" rel="stylesheet">
    <link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16" />
    <script src='js/cookiesManager.js'></script>
    <script src='js/jquery-3.3.1.min.js'></script>
    <!--<script src="https://apis.google.com/js/client:platform.js?onload=start" async defer></script>-->
    <!--<script src="https://apis.google.com/js/client:platform.js" async defer></script>-->
    <style>
      .navbar{
        margin-bottom:0;
        border-radius:0;
      }
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

  <body class="fond">



    <!-- JUMBOTRON -->
    <div class="jumbotron text-center">
      <div class="container">

        <h1><span class="titre1" style="font-weight:normal"><b>Voca</b>Craft</span></h1>

		<h2>l'outil ultime pour faire apprendre le vocabulaire</h2>
	</div>
    </div>

  <div class="center">
      <div class="form">

      <ul class="tab-group">
	    <!--<li class="tab"><a href="#edition">Editer</a></li>-->
        <li class="tab"><a href="#signup">Inscription</a></li>
        <li class="tab active"><a href="#login">Connexion</a></li>
      </ul>


      <div class="tab-content">

	    <!--===========================================-->
		<!--===========================================-->
         <div id="login">
          <h3 class="titre-form">On est de retour ?</h3><br>
          <form action="index.php" method="post" autocomplete="off">
            <div class="field-wrap">
            <label>
              Adresse mail<span class="req">*</span>
            </label>
            <div style="text-align:left;"><input id="connexEmail" type="email" style="display:inline-block;" required autocomplete="off" name="email"/><!--<span style="display:inline-block;width:40%;color:black;border: 1px grey solid; padding: 5px;"> @nds.k12.tr</span>-->
            </div>
          </div>
          <div class="field-wrap">
            <label>
              Mot de passe<span class="req">*</span>
            </label>
            <input id="connexPassword" type="password" required autocomplete="off" name="password"/>
          </div>
          <div class="field-wrap">
            <input type="checkbox" name="rememberMe" style="width:30px;display:inline;"/>
            <label>
              Se souvenir de moi
            </label>
          </div>
          <p class="forgot"><a href="forgot.php" class="forgot-link">Mot de passe oublié?</a></p>
          <button type="submit" class="button button-block" id="connexBtn" name="login" />Connexion</button>
          </form>
          <br>

          <!--<div class="g-signin2" data-onsuccess="onConnect" data-width="300">Connexion via Google</div>-->
        </div>

		<div id="signup">
		<div id="inscr_eleve">
          <h3 class="titre-form">Alors comme ça on veut apprendre le français ?</h3><br>

          <form action="index.php" method="post" autocomplete="off">

          <div class="top-row">
            <div class="field-wrap">
              <label>
                Prénom<span class="req">*</span>
              </label>
              <input type="text" required autocomplete="off" name='firstname' />
            </div>

            <div class="field-wrap">
              <label>
                Nom<span class="req">*</span>
              </label>
              <input type="text"required autocomplete="off" name='lastname' />
            </div>
          </div>

          <div class="field-wrap">
            <label>
              Adresse mail<span class="req">*</span>
            </label>
            <input type="email"required autocomplete="off" name='email' />
          </div>

          <div class="field-wrap">
            <label>
              Définir un mot de passe<span class="req">*</span>
            </label>
            <input type="password" required autocomplete="off" name='password'/>
          </div>


          <input type="hidden" name="type" value="eleve">
		      <input type="hidden" name="passwordProf" value="">
          <button type="submit" class="button button-block" name="register_eleve" />S'enregistrer</button>
          </form>


      </div><!--fin inscription éléves-->
        </div>  <!--fin sign up-->

      </div><!-- tab-content -->
      <hr>
      <div id="gSignInWrapper">
    <span class="label">Sign in with:</span>
    <div id="customBtnConnect" class="customGPlusSignIn">
      <span class="icon"></span>
      <span class="buttonText">Se connecter ou s'inscrire avec Google</span>
    </div>
  </div>
  <div id="name"></div>

<!--<div id="my-signin2"></div>-->
<!--<div class="g-signin2" data-onsuccess="onConnect" onclick="clicFlag();">Connexion via Google</div>-->
</div> <!-- /form -->

<script>
var revokeAllScopes = function() {
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
      background: #4285F4;
      color: #444;
      width: 100%;
      box-shadow: 1px 1px 1px grey;
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
      background: url('img/google.png') transparent 10px 50% no-repeat;
      display: inline-block;
      width: 30px;
      min-height: 30px;
      background-size: 36px 36px;
      float:left;
      vertical-align:middle;
      padding: 28px;
      background-color: white;
    }
    span.buttonText {
      display: inline-block;
      vertical-align: middle;
      padding-left: 42px;
      padding-right: 42px;
      font-size: 2rem;
      font-weight: bold;
      color:white;
      padding:10px;
      margin:auto;
      /* Use the Roboto font that is loaded in the <head> */
      font-family: 'Roboto', sans-serif;
    }
  </style>


<script src="https://apis.google.com/js/platform.js?onload=start" async defer></script>

<div class="fusee"><img src="img/fusee.png" style="width:100%;"></div>
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
