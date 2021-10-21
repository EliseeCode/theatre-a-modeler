<?php
/* Displays all error messages */
include_once ("db.php");
session_start();

$explodeURI=explode('/',$_SERVER['REQUEST_URI']);
$_SESSION['url']=end($explodeURI);
if(!isset($_SESSION['user_id'])){header("location:checkLoginCookie.php");exit();}
$user_id = $_SESSION['user_id'];
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$email = $_SESSION['email'];
$active = $_SESSION['active'];
$userType = $_SESSION['type'];
if($userType==""){header("location:userType.php");exit();}
echo "<script>fullUserName='".$first_name." ".$last_name."';</script>";
echo "<script>userType='".$userType."';</script>";
echo "<script>user_id=".$user_id.";</script>";
$classes=array();
echo "<script>user=".json_encode($user).";</script>";

?>
<!DOCTYPE html>
<html >
 <head>


    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Language Choice</title>
    <!-- Bootstrap -->
    <link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16" />
    <link href="css/main.css?ver=<?php echo filemtime('css/main.css');?>" rel="stylesheet">
    <link href="css/styleEntete.css?ver=<?php echo filemtime('css/styleEntete.css');?>" rel="stylesheet">
    <link href="css/myStyle.css?ver=<?php echo filemtime('css/myStyle.css');?>" rel="stylesheet">
    <link href="css/navStyle.css?ver=<?php echo filemtime('css/navStyle.css');?>" rel="stylesheet">
    <script src="js/jquery-3.3.1.min.js"></script>
</head>
<style>
  .navbar{
    margin-bottom:0;
    border-radius:0;
  }
</style>
<?php
echo "<script>user_id=".json_encode($user_id).";</script>";
echo "<script>first_name=".json_encode($first_name).";</script>";
echo "<script>last_name=".json_encode($last_name).";</script>";

?>

<body class="fond">
  <?php include "entete.php";?>
  <script>
  $(".buttonHome").hide();
  $(".buttonMesClasses").hide();
  $(".buttonMyDecks").hide();
  $(".buttonMyClass").hide();
  $(".buttonMesLang").hide();

  $(".settingClass,.codeEntete").hide();
  $('.desktop').menuBreaker();

  </script>

<style>
.lang_item{cursor:pointer;padding:10px; margin:10px;display:inline-block;border-radius:10px;transition:1s}
.lang_item:hover{box-shadow:0 0 0 10px var(--mycolor2bis);background-color:white;}
.turningFlag{transform:perspective(100px) rotate3d(1,1,0,10deg);transition:0.5s;}
.turningFlag:hover{transform:perspective(100px) rotate3d(-1,1,0,10deg);}
</style>
	<div class="center" style="padding-top:100px;">
    <div>
      <h3 id="titreChoixLangue" class="decalagetitreDroite"></h3>
      <div id="lang_container">
      </div>
      <div style="width:80%;margin:auto;"><?php echo __("Si la langue que vous enseignez n'est pas présente ici, vous pouvez nous demander de l'ajouter en nous envoyant");?> <a href="index.php#contact"><?php echo __("un petit message");?></a></div>
    </div>
  </div>
<script src='js/jquery-3.3.1.min.js'></script>
<script>
loadUserTypeInterface();

function loadUserTypeInterface()
{
  if(userType=="eleve" || userType=="autodidact")
  {$("#titreChoixLangue").html("<?php echo __("Quelle langue apprenez-vous ?");?>");}
  else{
  $("#titreChoixLangue").html("<?php echo __("Quelle Langue enseignez-vous ?");?>");
  }
}

$.getJSON("ajax.php?action=getTargetLang", function(result)
{
for(langRk in result)
{
  $("#lang_container").append("<div class='lang_item' onclick='changeTargetLang(\""+result[langRk].lang_id+"\")'><div class='turningFlag flagStd flag_"+result[langRk].lang_code2+"'></div><div>"+result[langRk].lang_name+"</div></div>");
}
});
function changeTargetLang(lang_id)
{
$.getJSON("ajax.php?action=changeTargetLang&lang_id="+lang_id, function(result)
{

  location.href="decks.php?categorie=explore&target_lang="+lang_id;
});
}

</script>
</body>
</html>
