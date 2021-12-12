<?php
/* Main page with two forms: sign up and log in */
require 'db.php';
session_start();
include_once ("local_lang.php");
$state = sha1(openssl_random_pseudo_bytes(1024));
  $_SESSION['state']=$state;
  $errorMsg="";
if (isset($_SESSION['message'])){$errorMsg=$_SESSION['message'];}
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
  //foreach($_POST as $key => $value){echo ($key." : ".$value."<br>");}
    if (isset($_POST['login'])) { //user logging in
		  require 'login.php';
      exit();
    }

  	elseif (isset($_POST['register'])) { //user registering
      require 'register.php';
      exit();
    }
}

if(isset($_GET["target_lang"])){$_SESSION["target_lang"]=(int)$_GET["target_lang"];}
echo "<script>lang_interface=".json_encode($lang_interface).";</script>";

//Manage redirection with SESSION's url attribute.
$logOut=0;
if(isset($_GET["logOut"]))
{
  $logOut=(int)$_GET["logOut"];
}
//echo "<script>logOut=".$logOut.";</script>";
$url="";
if(isset($_SESSION['url']))
{$url=$_SESSION['url'];}
if($url==""){$url="decks.php";}
if ( isset($_SESSION['logged_in']) && $_SESSION['logged_in']== 1) {
  header("location: ".$url);
  exit();
}


?>
<!DOCTYPE html>
<html lang="<?php echo $lang_interface;?>">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="google" content="notranslate">
  
    <meta name="description" content="ExoLingo est une plateforme ludique d'apprentissage du vocabulaire pour les professeurs et les élèves." />
    <meta name="keywords" content="exolingo, vocabulaire, langue, aprrendre, classe, flashcards, flash cards, révision espacées" />
    <title>ExoLingo</title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/myStyle.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" type="text/css">
    <!--<link href="css/bootstrap.min.css" rel="stylesheet">-->
	  <link href="css/main.css?v=2" rel="stylesheet">
    <link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16" />
    <script src='js/cookiesManager.js'></script>
    <script src='js/jquery-3.3.1.min.js'></script>

  	<script>
  	  window.dataLayer = window.dataLayer || [];
  	  function gtag(){dataLayer.push(arguments);}
  	</script>
  
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
    .msgError{color:var(--rouge);}
    </style>
</head>

<?php
echo '<script>state="'.$_SESSION["state"].'";</script>';
?>
<style>
  .bigContainer{height: 100%;
    width:100%;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-direction: row;
    flex-direction: row;
    overflow: hidden;}
@media (min-width: 960px)
{
.auth-sidebar-content {
    display: -ms-flexbox;
    display: flex;
    flex:2;
    text-align:center;
    -ms-flex-direction: column;
    flex-direction: column;
    -ms-flex-pack: justify;
    justify-content: space-between;
    height: 100%;
    background-image:url(img/authSide.svg);
    background-size:cover;}}

@media (max-width: 960px)
{
  .auth-sidebar-content {
      display: none};
}
.auth-form-content{
  flex: 4;
  text-align:center;
  padding-top:40px;
  display:flex;
}

.choixLangue{width: 300px;
    max-width: 100%;
    text-align: left;min-width:100px;position:relative;top:-5px;display:inline-block; border-radius:0 0 30px 30px;text-align:center;z-index:1031;}
    .selectLang ul li{list-style-type: none;}
    .selectLang ul {padding:0;}
.tiretteLang{min-width:100px;transition:1s;z-index:1032;overflow:visible;height:0;right:20px;text-align:center;position:absolute;top:0;}
.lang_item{text-decoration:none;color:black;padding:5px; margin:10px;display:inline-block;border-radius:10px;}

.choixLang_item{display:inline-block;width:90px;box-shadow:0 0 3px grey;margin:3px;padding:10px;background-color:white;border-radius:5px;text-align:left;}
.choixLang_item:hover{transform:scale(1.1);}
.lang_name{padding-left:10px;text-transform: uppercase;}
.card--login{padding: 30px;
    background-color: white;
    box-shadow: 0 0 40px #00000040;
    border-radius: 10px;}
</style>
  <body translate="no" class="fond" style="display:flex;height:100vh;background-color:white;">



    
<div class="tiretteLang">
  <div class="choixLangue" onclick="$('.selectLang').slideToggle();">
    <div class="interfaceLangActive"></div>
    <div class="selectLang" style="display:none;">
      <ul class="interfaceLangChoice">
      <ul>
    </div>
  </div>
</div>

  <div class="bigContainer">
    <div class="auth-sidebar-content" >
      <div style="margin:50px 0px 0 0px;display:flex;flex-direction:column;">
        <div style="margin:auto;">
          <img src="img/logo1.svg" class="floating" width="100px" style="margin-bottom:40px;">
          <h1><span class="titre1" style="font-weight:normal;color:white;">ExoLingo</span></h1>
        </div>
        <img style="margin:auto;margin-top:100px;" src="img/authsidePerso.png" width="80%">
      </div>
    </div>

    <div class="auth-form-content">
      <a href="index.php" style="position:absolute;top:30px;left:30px;"><img src="img/back2.png" width="40px"></a>
      <div class="card--login" style="margin:auto;text-align:left;">
      <h2 class="titleLogin"><?php echo __("Connexion à ExoLingo");?></h2>
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
        <div class="msgError"><?php echo $errorMsg;?></div>
        <div class="tab-content">

	    <!--===========================================-->
		<!--===========================================-->
         <div id="login">
          <!--<h3 class="titre-form">On est de retour ?</h3><br>-->
            <form action="loginPage.php" method="post" autocomplete="off">
              <div class="field-wrap">
                <input class='inputTextLog' id="connexEmail" placeholder="<?php echo __("Adresse mail");?>" type="email" style="display:inline-block;" required autocomplete="username" name="email"/><!--<span style="display:inline-block;width:40%;color:black;border: 1px grey solid; padding: 5px;"> @nds.k12.tr</span>-->
            </div>
            <div class="field-wrap">
              <input class='inputTextLog' id="connexPassword" placeholder="<?php echo __("Mot de passe");?>" type="password" required autocomplete="current-password" name="password"/>
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

          <form action="loginPage.php" method="post" >

          <div class="top-row">
            <div class="field-wrap">
              <input class='inputTextLog' type="text" placeholder="<?php echo __("Prénom");?>" required name='firstname' />
            </div>

            <div class="field-wrap">
              <input class='inputTextLog' type="text" placeholder="<?php echo __("Nom");?>" required  name='lastname' />
            </div>
          </div>

          <div class="field-wrap">
            <input class='inputTextLog' type="email" placeholder="<?php echo __("Adresse mail");?>" autocomplete="username" required  name='email' />
          </div>

          <div class="field-wrap">
            <input class='inputTextLog' type="password" required autocomplete="new-password" placeholder="<?php echo __("Mot de passe");?>" name='password'/>
          </div>
          <div class="field-wrap">
            <input type="checkbox" name="CGU" required style="transform:scale(1.3);width:30px;display:inline-block;margin:10px;"><?php echo __("J'accepte <a href='CGV.php'>les CGU et les CGV</a> et <a href='TermsAndConditions.php'>les termes et conditions d'utilisation</a>");?>
          </div>
          <input type="hidden" name="type" value="eleve">
		      <input type="hidden" name="passwordProf" value="">
          <br>
          <div>
            <button type="submit" class="button button-block" name="register" /><?php echo __("S'enregistrer");?></button>
          </div>

          <div class="changeTab"><?php echo __("Vous avez déjà un compte");?> : <a href="#login"><?php echo __("Connexion");?></a></div>
          </form>


        </div><!--fin inscription éléves-->
          </div>  <!--fin sign up-->
        </div><!-- tab-content -->



    <!--<div id="my-signin2"></div>-->
    <!--<div class="g-signin2" data-onsuccess="onConnect" onclick="clicFlag();">Connexion via Google</div>-->
    </div> <!-- /form -->
  </div>
    <div class="section" style="display:none;">
    <p><?php echo __("Voici quelques articles et sites internets lié à l'apprentissage utilisé sur ExoLingo:");?></p>
    <div><a href="https://www.supermemo.com/en/archives1990-2015/english/algsm11"><?php echo __("SuperMemo, l'algorithme d'apprentissage espacé");?></a></div>
    <div><img src="img/graphSupermemo.png" width="90%"></div>
    <div><a href="http://www.sansforgetica.rmit/"><?php echo __("Police de caractère 'sans Forgetica'");?></a></div>
    </ul>
    </div>
</div>
<script>
function FillInLang(){
  console.log("fillinlang")
  $.getJSON("ajax.php?action=getAllLang", function(result)
  {
    console.log(result);
    $(".interfaceLangActive").html("<div class='choixLang_item'><span class='tinyFlag flag_"+lang_interface+"'></span><span class='lang_name'>"+lang_interface+"</span></div>");
    for(k in result)
    {
    lang_code2=result[k].lang_code2;
    lang_interface_active=result[k].lang_interface;
    if(lang_interface_active==1 && lang_interface!=lang_code2){
      lang_name=result[k].lang_name_Origin;
      if($('interfaceLangChoice_'+lang_code2).length==0)
        {$(".interfaceLangChoice").append('<a href="loginPage.php?lang='+lang_code2+'"><li class="interfaceLangChoice_'+lang_code2+' choixLang_item"><span class="tinyFlag flag_'+lang_code2+'"></span><span class="lang_name">'+lang_code2+'</span></li></a>');
        }
      }
    }
  });
}
FillInLang();


$('.changeTab').on('click', function (e) {
  e.preventDefault();
  target = $(this).find("a").attr('href');
  $('.tab-content > div').not(target).hide();
  $(target).fadeIn(600);
  if(target!="#signup"){$(".titleLogin").html("<?php echo __("Connexion à ExoLingo");?>");}
  else{$(".titleLogin").html("<?php echo __("Inscription à ExoLingo");?>");}
});
//quand on arrive on a :
target="#signup";
$('.tab-content > div').not(target).hide();
$(target).show();
if(target!="#signup"){$(".titleLogin").html("<?php echo __("Connexion à ExoLingo");?>");}
else{$(".titleLogin").html("<?php echo __("Inscription à ExoLingo");?>");}
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
                'redirect_uri': 'https://'+window.location.host+'/loginGoogle.php',
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
    <?php
    include_once ("cookiePolicyPopUp.php");
    ?>
<script>

 if(readCookie("login") && readCookie("hash"))
 {
 newAdress="loginCookie.php?login="+readCookie("login")+"&hash="+readCookie("hash");
 window.location=newAdress;
 }
</script>

</body>

</html>
