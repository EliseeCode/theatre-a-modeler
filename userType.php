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
$type = $_SESSION['type'];
echo "<script>fullUserName='".$first_name." ".$last_name."';</script>";
echo "<script>userType='".$type."';</script>";
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
  .userTypeBtn{background-color:white;cursor:hand;box-shadow:0 0 3px grey; width:250px; height:250px; padding:10px 20px;margin:40px 20px; display:inline-flex;flex-direction:column;}
  .userTypeBtn:hover{background-color:white;transform:scale(1.1);}
  .userTypeBtn img{margin:auto;width:50%;height:50%;}
  .layover{position:fixed;top:0;bottom:0;width:100%;background-color:var(--fond);padding-top:100px;text-align:center;}
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


	<div class="center" style="padding-top:100px;">
    <div>
      <h3 id="titreChoixUserType" class="decalagetitreDroite"><?php echo __("Qui êtes vous ?");?></h3>
      <div id="type_container">
        <div class='userTypeBtn' id='userTypeBtnautodidact' onclick='changeUserType("autodidact");'><img src='img/icon_perso.png'><h2><?php echo __("Autodidacte");?></h2></div>
        <div class='userTypeBtn' id='userTypeBtnEleve' onclick='changeUserType("eleve");'><img src='img/icon_eleve.png'><h2><?php echo __("Elève");?></h2></div>
        <div class='userTypeBtn' id='userTypeBtnProf' onclick='changeUserType("prof");'><img src='img/icon_prof.png'><h2><?php echo __("Professeur");?></h2></div>
      </div>
    </div>
  </div>
<script src='js/jquery-3.3.1.min.js'></script>
<script>
function changeUserType(newType)
{
	$.getJSON("ajax.php?action=changeUserType&val="+newType, function(result)
	{
	console.log("done");
  location.href="lang.php";
	});
}
</script>
</body>
</html>
