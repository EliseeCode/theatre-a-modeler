<?php
/* Displays all error messages */
include_once ("db.php");
session_start();


if(!isset($_SESSION['user_id']) || !isset($_SESSION['email'])){header("location:checkLoginCookie.php");exit();}
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
//get users data
$sql="SELECT notification,fame,lang,type FROM users WHERE user_id=".$user_id;
$result = $mysqli->query($sql);
$user = $result->fetch_assoc();
$result->free();
echo "<script>user=".json_encode($user).";</script>";

?>
<!DOCTYPE html>
<html >
 <head>


    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil</title>
    <!-- Bootstrap -->
    <link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16" />
    <link href="css/main.css?ver=<?php echo filemtime('css/main.css');?>" rel="stylesheet">
    <link href="css/styleEntete.css?ver=<?php echo filemtime('css/styleEntete.css');?>" rel="stylesheet">
    <link href="css/myStyle.css?ver=<?php echo filemtime('css/myStyle.css');?>" rel="stylesheet">
    <link href="css/navStyle.css?ver=<?php echo filemtime('css/navStyle.css');?>" rel="stylesheet">
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/cookiesManager.js"></script>
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
echo "<script>email=".json_encode($email).";</script>";

?>

<body class="fond">
  <?php include "entete.php";?>
  <script>
  //$(".buttonHome").hide();
  //$(".buttonMesClasses").hide();
  //$(".buttonMyDecks").hide();
  //$(".buttonMyClass").hide();
  //$(".settingClass,.codeEntete").hide();
  $(".enteteBtn:not(.enteteProfil)").hide();
  $('.desktop').menuBreaker();

  </script>







    <!-- JUMBOTRON -->
<style>
.form2{display: inline-block;
    background: var(--mycolor1);
    padding: 40px;
    max-width: 70%;
    margin: 40px auto;
    border-radius: 4px;
    /* box-shadow: 0 4px 10px 4px rgba(19, 35, 47, 0.3); */
    border-bottom: 8px solid var(--mycolor3);
    color: var(--mycolor4);}
.infoC{color:darkgrey;}
.lien{color:var(--mycolor2);}
.class_item{border: 1px solid grey;
    display: inline-block;
    margin: 5px;
    padding: 10px 10px;
    text-align:center;
    position:relative;}
.class_item:hover{box-shadow:1px 1px 5px grey;top:-2px; left:-2px;}
.listprof{font-style:italic;color:darkgrey;max-height:100px;overflow:auto;}
.myclasses{display:block;padding:10px;text-align:center;}
.notificationMail,.fame{display: inline;
    width: 42px;
    top: 2px;
    position: relative;}
.codeAcces{display:none;}
.wrong{box-shadow: 0px 0px 4px red;}
.codecache{background-color:lightgrey;color:lightgrey;transition:1s;padding:5px 10px;}
.codecache:hover{color:white;background-color:grey;}
</style>
	<div class="center" style="padding-top:70px;">
<div class="form2" style="position:relative;padding-bottom:70px;">
  <img src='img/userBlanc.png' width="100px">
    <h1 class="name" style="color:var(--mycolor4);"></h1>
    <div style="text-align:left;margin-top:40px;">
    <div><?php echo __('Adresse mail');?> : <span class="infoC mailadress"></span></div>
    <div><?php echo __("Mon rôle");?> :
      <select class="userType" onchange="changeUserType(this.value);">
        <option value="autodidact"><?php echo __("Autodidacte");?></option>
        <option value="eleve"><?php echo __("Elève");?></option>
        <option value="prof"><?php echo __("Professeur");?></option>
      </select>
        <br>

    <div><?php echo __('Police spéciale pour dyslexique');?> :<input type="checkbox" class="dyslexique" onchange="changeDys(this.checked);"></div><br>
    <div><?php echo __('Recevoir des emails de rappel');?> :<input type="checkbox" class="notificationMail" onchange="changeNotif(this.checked);"></div><br>
    <div><?php echo __("Apparaitre dans le tableau d'honneur");?> :<input type="checkbox" class="fame" onchange="changeFame(this.checked);"></div><br>
    <div><?php echo __("Langue");?> :<select class="lang" onchange="changeLang(this);">
    </select></div><br>


    <div class="licences_container">
      <hr>
        <h3>Licences</h3>
    </div>
    <hr>
    <div><a href="forgot.php" class="lien"><?php echo __("Reinitialiser le mot de passe");?></a></div><br>
    <div><a href="logout.php" class="lien"><?php echo __("Deconnexion");?></a></div><br>
    <div><a href="decks.php" class="lien" style="position:absolute; bottom:10px; right:10px;"><?php echo ("Retour");?></a></div>
    <hr>
    <h3><?php echo __("Danger");?></h3>
    <div><a href="#" class="lien" onclick="deleteUser();" style="color:red;"><?php echo __("Supprimer le compte");?></a></div><br>
    <hr>
    <!--<li><?php echo $local_lang_cause;?></li>
    <li><?php echo substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,5);?></li>
    <li><?php echo in_array($local_lang, $acceptLang);?></li>
    <li><?php echo 'locallang:'.$local_lang;?></li>
    <li><?php echo 'langinterface:'.$lang_interface;?></li>
    <li><?php echo 'session locallang:'.$_SESSION['local_lang'];?></li>
    <li><?php echo 'old session:'.$oldSessionLang;?></li>-->
    </div>
</div>
</div>
<script src='js/jquery-3.3.1.min.js'></script>
<script>
if(readCookie("dys")){
  $('<style>*{ font-family: "OpenDyslexic" !important;</style>').appendTo('head');
}
if(readCookie("dys")){$(".dyslexique").prop('checked', true);}
if(user.notification=="1"){$(".notificationMail").prop('checked', true);}
if(user.fame=="1"){$(".fame").prop('checked', true);}
$(".userType").val(user.type);
$('.name').html(first_name+" "+last_name);
$('.mailadress').html(email);
function FillInLang(){
  $.getJSON("ajax.php?action=getAllLang", function(result)
  {
    for(k in result)
    {
    lang_interface=result[k].lang_interface;
    lang_code2=result[k].lang_code2;
    lang_name=result[k].lang_name_Origin;
    if(lang_interface==1){
      $(".lang").append('<option value="'+lang_code2+'">'+lang_name+'</option>');
      }
    }
    $(".lang").val(user.lang);
  });
}
FillInLang();
function deleteUser(){
  confirmDelete=prompt('Ecrire "DELETE" pour supprimer votre compte associé à '+email);
  if(confirmDelete.toUpperCase()=="DELETE")
  {
    $.getJSON("ajax.php?action=delUser", function(result){location.href="./logout.php";});
  }
}
function showWindow()
{$('.fenetreSombre').remove();
$('body').append("<div class='fenetreSombre' onclick='$(this).remove();'><div style='text-align:center;' class='fenetreClaire' onclick='event.stopPropagation();'>"
+"</div></div>");
}
school_id="";
function schoolSelected()
{
  school_id=$("#choixEtablissement").val();
  console.log(school_id);
  if(school_id!=''){

  if(school_id=="autre"){$('.fenetreClaire').html("Pour mettre en place VocaCraft dans votre établissement, merci de contacter l'administrateur : <a href='mailTo:reclus.elisee@gmail.com'>ici</a>");}
  else{$('.codeAcces').slideDown();}

  }
}
$.getJSON("ajax.php?action=getlicencesUser", function(dataLicences){
  console.log("licences",dataLicences);
  nbreLicence=0;
  trans_interface_flag=false;
for(licenceRk in dataLicences)
  {
    if(dataLicences[licenceRk].active==1)
    {$(".licences_container").append("<div class='licence_item'>"+dataLicences[licenceRk].licence_type+" depuis le "+dataLicences[licenceRk].licence_starting_date+"</div>");
    if(dataLicences[licenceRk].licence_type=="trans_interface"){trans_interface_flag=true;}
    if(dataLicences[licenceRk].licence_type=="trans_deck"){trans_deck_flag=true;}
    nbreLicence++;}
  }
if(nbreLicence==0){$(".licences_container").remove();}
if(trans_interface_flag){$(".licences_container").append("<a href='translate.php'><button>traduire l'interface</button></a>");}
if(trans_deck_flag){$(".licences_container").append("<br><a href='check_decks.php'><button>traduire les listes</button></a>");}
});

function checkPasswords()
{passwordSchool=$(".codeSchool").val();
  $.getJSON("ajax.php?action=checkSchoolPassword&pass="+passwordSchool+"&school_id="+school_id, function(passValidation){
  if(passValidation==1){window.location.reload();}
  else{$(".codeSchool").addClass("wrong");}
});

}
function changeDys(dysValue)
{
  if(dysValue){createCookie("dys",1,365)}else{eraseCookie("dys")}
}
function changeNotif(notifValue)
{
console.log(notifValue);
if(notifValue){newVal=1;}else{newVal=0;}
$.getJSON("ajax.php?action=changeNotif&val="+newVal, function(result)
{
console.log("done");
});
}
function changeFame(fameValue)
{
console.log(fameValue);
if(fameValue){newVal=1;}else{newVal=0;}
$.getJSON("ajax.php?action=changeFame&val="+newVal, function(result)
{
console.log("done");
});
}
function changeUserType(typeValue)
{
console.log(typeValue);
$.getJSON("ajax.php?action=changeUserType&val="+typeValue, function(result)
{
console.log("done");
});
}
function changeLang(sel)
{
  langValue=sel.value;
$.getJSON("ajax.php?action=changeLang&val="+langValue, function(result)
{
console.log("done");
window.location.href="profile.php?lang="+langValue;
});
}
function sedesinscrire(class_id)
{
  	outConfirm=confirm("<?php echo __("Etes-vous sur de vouloir quitter cette classe ?");?>");
  if(outConfirm){
  	$.getJSON("ajax.php?action=goOutClass&class_id="+class_id, function(result)
  	{
    console.log(result);
  	console.log("out done");
  	window.location.reload();
  	});
  }
}

$.getJSON("ajax.php?action=getStatsTodayUser", function(result){
	console.log(result.stats);
	nbreMotsEnMemoire=result.nbreMotsEnMemoire;
	$('#objectif').html(nbreMotsEnMemoire);
});
</script>
</body>
</html>
