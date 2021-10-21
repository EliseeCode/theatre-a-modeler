<?php
include_once ("db.php");
session_start();
//header("location:decks.php?categorie=myDecks");exit();
$explodeURI=explode('/',$_SERVER['REQUEST_URI']);
$_SESSION['url']=end($explodeURI);
if(!isset($_SESSION['user_id'])){header("location:checkLoginCookie.php");exit();}
    // Makes it easier to read
	  $user_id = $_SESSION['user_id'];
    $first_name = $_SESSION['first_name'];
    $last_name = $_SESSION['last_name'];
    $email = $_SESSION['email'];
    $active = $_SESSION['active'];
		$type = $_SESSION['type'];
		//get language.
		if(isset($_GET["target_lang"])){$_SESSION["target_lang"]=(int)$_GET["target_lang"];}
		if(!isset($_SESSION["target_lang"])){
			$sql="SELECT lang.lang_id,lang.lang_code2,lang.lang_code2_2 FROM user_target_lang LEFT JOIN lang on lang.lang_id=user_target_lang.lang_id WHERE user_target_lang.user_id=".$user_id." ORDER BY user_target_lang.changed_time DESC LIMIT 1";
			$result = $mysqli->query($sql);
			$flag =$result->num_rows;
		  if($flag==1){
				$row = $result->fetch_assoc();
				$_SESSION["target_lang_code2"]=$row["lang_code2"];
				$_SESSION["target_lang_code2_2"]=$row["lang_code2_2"];
				$_SESSION["target_lang"]=(int)$row["lang_id"];
			}
			else{
				header("location:lang.php");
				exit();
			}
		}
		$target_lang=$_SESSION["target_lang"];

		echo "<script>fullUserName='".$first_name." ".$last_name."';</script>";
		echo "<script>userType='".$type."';</script>";
		echo "<script>user_id=".$user_id.";</script>";
		$_SESSION['url']="";
		$classes=array();
		$oldClassId=-1;
		$sql="SELECT my_user_class.role,classes.lang, classes.class_id,classes.class_name,classes.promo,classes.status,IF(my_user_class.user_id IS NULL,0,1) as enroll
		FROM classes LEFT JOIN  (SELECT * FROM user_class WHERE user_class.user_id=".$user_id.") AS my_user_class ON my_user_class.class_id=classes.class_id WHERE classes.active=1 AND (my_user_class.user_id=".$user_id." OR classes.status='public')";

		$result = $mysqli->query($sql);
		while($row = $result->fetch_assoc())
		{
		  $classes[$row["class_id"]]=$row;
		}
		$result->free();

		echo "<script>classes=".json_encode($classes).";</script>";

?>
<!DOCTYPE html>
<html >
 <head>


    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Classes</title>
    <!-- Bootstrap -->
	  <link href="css/main.css?ver=<?php echo filemtime('css/main.css');?>" rel="stylesheet">
		<link href="css/styleEntete.css?ver=<?php echo filemtime('css/styleEntete.css');?>" rel="stylesheet">
	  <link href="css/deck.css?ver=<?php echo filemtime('css/deck.css');?>" rel="stylesheet">
	  <link href="css/card.css?ver=<?php echo filemtime('css/card.css');?>" rel="stylesheet">
		<link href="css/myStyle.css?ver=<?php echo filemtime('css/myStyle.css');?>" rel="stylesheet">
		<link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16" />
		<link rel="stylesheet" type="text/css" href="css/navStyle.css"/>
		<script src="js/jquery-3.3.1.min.js"></script>
		<script src="js/jquery-ui.js"></script>
		<script src="js/cookiesManager.js"></script>
		<script src="js/vue.js"></script>
		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-140408884-1"></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());

		  gtag('config', 'UA-140408884-1');
		</script>

		<style>
      .navbar{
        margin-bottom:0;
        border-radius:0;
      }
			.ClassContent{position:relative;}

			.buttonJoin,.buttonOpen{cursor:hand;margin:10px;padding:10px;color:white;}
			.buttonJoin{background-color:var(--mycolor5);}
			.buttonOpen{background-color:var(--mycolor2bis);}
			#public_class_list>.classItem>.keyPrivateClass{display:none;}
			#private_class_list>.classItem>.keyPrivateClass,#archive_class_list>.classItem>.keyPrivateClass{
				background-image:url(img/key.png);
				background-size:contain;
				background-repeat:no-repeat;
				background-position:top right;
				width:30px;
				height:30px;
				position:absolute;
				top:30px;
				right:30px;
				filter:grayscale(1);
			}
			.img_role_class{position:absolute;top:-18px;left:-3px;transform:rotate(-20deg);}
			.img_role_class>img{width:60px;filter: invert(0.3);}
			#list_class{margin-top: 30px;}
			#ArchiveSlideBtn{display:none;margin-bottom:30px;}

	  </style>
</head>

<body class="fond">
	<?php include "entete.php";?>
	<?php include "classData.php";?>
	<script>
	//$(".buttonRetourList").hide();
	//$(".buttonMyClass").hide();
	//$(".settingClass").hide();
	//$(".class_name").html("<?php echo __('Cours');?>")
	$(".enteteBtn:not(.enteteClasses)").hide();
	langUpdateButton();
	$(".buttonMyDecks").show();

	</script>

<div id="vueApp" class="center classPage bodyContent" style="padding-top:20px;">
	<h2><?php echo __("Classes");?></h2>
	<div id="list_class" class="sortable">



			<div id="addClassItem" class="classItem ClassItemJoinAdd">
				<div id="" class="addPanoClass panoClass" onclick="createClass();">
					<div class="ClassContent centerContainer" style="margin:auto;">
								<div style="margin:auto;"><div style="width:30px;height:30px;border:3px solid var(--mycolor2);;border-radius:50%;font-size:29px;line-height:22px;display:inline-block;">+</div><br>Nouveau cours</div>
					</div>
				</div>
		  </div>

			<div id="joinClassItem" class="classItem ClassItemJoinAdd">
				<div class="panoClass">
					<div class="ClassContent">
						<div class="name InfoClass"><?php echo __("Cours privé");?></div>
						<input type='text' size='4' class='inputCodeMobile' placeholder='CODE' style='width:100%;text-align:center;' onkeyup='$(".msgErrorCode").html("");if(this.value.length>4){this.value=this.value.substr(0,4);}'><br>
						<div class='msgError msgErrorCode'></div>
						<div class='buttonJoin' onclick='joinClassWithCode2();'><?php echo __("Rejoindre");?></div>
					</div>
				</div>
			</div>

			<div id="MyLexiconItem" class="classItem">
				<a href="decks.php?categorie=myDecks">
					<div id="" class="panoClass">
						<div class="ClassContent centerContainer" style="margin:auto;">
								<div style="color:var(--mycolor2);"><img src="img/icon_perso.png" style="width:40px;"><br><?php echo __("Ma bibliothèque");?></div>
						</div>
					</div>
				</a>
			</div>

			<div id="private_class_list" style="display:inline;"><h2><?php echo __("Mes cours");?></h2></div>
			<div id="public_class_list" style="display:inline;"><h2><?php echo __("Cours disponible");?></h2></div>
			<div id="ArchiveSlideBtn"><a href="#" onclick="$('#archive_class_list').slideToggle('slow');"><?php echo __("Afficher/Cacher les classes archivées");?></a></div>
			<div id="archive_class_list" style="display:none;"></div>
	 </div>

</div>

<script>
var app = new Vue({
  el: '#vueApp',
  data: {
    show:false

  }
});


if(userType==""){
	location.href="lang.php";
}else{
	loadUserTypeInterface();
}

function loadUserTypeInterface()
{
	$("#list_langue").hide();
	if(userType=="prof"){$("#public_class_list").hide();$("#joinClassItem").find(".name").html("Rejoindre le cours d'un collègue");}
	else{$("#addClassItem").hide();$("#public_class_list").show();$("#joinClassItem").find(".name").html("Rejoindre le cours d'un professeur");}
}
function showWindow(){
$('.fenetreSombre').remove();
$('body').append("<div class='fenetreSombre' onclick='$(this).remove();'><div style='text-align:center;' class='fenetreClaire' onclick='event.stopPropagation();'>"
+"</div></div>");
}
function MAJ_lang_cible(){
lang=$(".select_lang").val();
console.log(lang);
showClasses(lang)
}

function showClasses(lang)
{
	$("#ArchiveSlideBtn").hide();
	myClasses=[];
	otherClasses=[];
	$(".classItem:not(.ClassItemJoinAdd,#MyLexiconItem)").remove();
	for(pos in classes)
	{
	classLang=classes[pos].lang;
	status=classes[pos].status;
	//if(lang==classLang || (lang=="all" && status=="ok"))
	//{
	class_id=	classes[pos].class_id;
	class_name=classes[pos].class_name;
	promo=classes[pos].promo;

	enroll=classes[pos].enroll;
	role=classes[pos].role;
	if(status=="public")
	{dest_list="public_class_list";}
	else if(status=="ok")
	{dest_list="private_class_list";}
	else
	{dest_list="archive_class_list";$("#ArchiveSlideBtn").show();}


			$("#"+dest_list).append('<div class="classItem" id="classItem_'+class_id+'">'
			+'<div id="class_'+class_id+'" class="panoClass"></div><div class="keyPrivateClass" title="<?php echo __("Cours privé");?>"></div></div>');
			$('#class_'+class_id).append('<div class="ClassContent"></div>');
			$('#class_'+class_id+' > .ClassContent').append('<div class="name InfoClass">'+class_name+'</div><div class="promo InfoClass">'+promo+'</div>');
			if(enroll==0){$('#class_'+class_id+' > .ClassContent').append('<div class="buttonJoin"  onclick="joinClass('+class_id+');"><?php echo __("Rejoindre");?></div>');}
			else{$('#class_'+class_id+' > .ClassContent').append('<div class="buttonOpen"  onclick="openClass('+class_id+');"><?php echo __("Ouvrir");?></div>');}

			if(role=="eleve"){$('#class_'+class_id).append('<div class="img_role_class"><img src="img/icon_eleve.png"></div>');}
			else if(role=="prof"){$('#class_'+class_id).append('<div class="img_role_class"><img src="img/icon_prof.png"></div>');
			}

			if(role=="prof" && status!="archive")
			{
				$('#class_'+class_id).append(`<button class="archiveBtn" onclick="archiveClass(`+class_id+`)" value="">Archiver</button>`);
			}
			if(role=="prof")
			{
				$('#class_'+class_id).append(`<button class="deleteBtn" onclick="deleteClass(`+class_id+`)" value="" style="color:red;">supprimer</button>`);
			}
			if(role=="eleve")
			{
				$('#class_'+class_id).append(`<button class="goOutBtn" onclick="goOutClass(`+class_id+`)" value="" style="color:yellow;">goOut</button>`);
			}



			/*else{//classe ou je ne suis pas prof
			$("#list_class").append('<div class="classItem foreignClass" id="classItem_'+class_id+'"><div id="class_'+class_id+'" class="panoClass"></div></div>');
			$('#class_'+class_id).append('<div class="ClassContent" onClick="openClass('+class_id+');"></div>');
			$('#class_'+class_id+' > .ClassContent').append('<div class="name InfoClass">'+class_name+' <span class="nbreEleves" title="nombre d\'élèves">('+nbreEleves+')</span></div><div class="promo InfoClass">'+promo+'</div><div class="listProf"><div style="text-align:left;color:grey;">Professeur(s):</div>'+listProf+'</div>');
				$('#class_'+class_id+' > .ClassContent').append('<a href="#" class="joinText" onclick="joinClass('+class_id+');"><div>Rejoindre cette classe</div></a>');
			}*/


	//}//fin if lang
	}
	$(".buttonMenuClass").unbind();
	$(".buttonMenuClass").on("click",function(e){$(this).find(".panneauIconMenuClass").slideDown();});
	$(".buttonMenuClass").on("mouseleave",function(e){$(this).find(".panneauIconMenuClass").slideUp();})
	$('.foreignClass').appendTo("#list_class");
	$(".sortable").sortable({
	items: ".classItem",
	update: function (event, ui) {
	var data = $(this).sortable('serialize');
	console.log(data);
	$.getJSON({
				data: data,
				type: 'POST',
				url: 'ajax.php?action=setOrderClasses'
		},function(result){console.log(result);});
		}
	});

	if($("#public_class_list .classItem").length==0){$("#public_class_list").hide();}else{$("#public_class_list").show();}
	if($("#archive_class_list .classItem").length==0){$("#archiveSlideBtn").hide();}else{$("#archiveSlideBtn").show();}
	if($("#private_class_list .classItem").length==0){$("#private_class_list").hide();}else{$("#private_class_list").show();}
}

showClasses("all");

/*function showClassPanel(class_id,archiv)
{
	class_name="";
	promo="";
	listProf="";
	status="";
	for(pos in classes){if(classes[pos].class_id==class_id){
		class_name=classes[pos].class_name;
		promo=classes[pos].promo;
		listProf=classes[pos].listProf.join('<br>');
		status=classes[pos].status;
	}}
	$.getJSON("ajax.php?action=getClassInfo&class_id="+class_id, function(result)
	{
			code=result.code;
			class_name=result.class_name;
			promo=result.promo;
		$('body').append("<div class='fenetreSombre' onclick='$(this).remove();'><div class='fenetreClaire' onclick='event.stopPropagation();'><img src='img/close.png' class='closeWindowIcon' onclick='$(\".fenetreSombre\").remove();'>"
		+"<h3 style='text-align:center;margin:0 0 50px 0;'>"+class_name+"<br><span style='font-size:0.8em;color:grey;'>"+promo+"</span></h3><div id='displayOptionClass'></div></div></div>");

		$("#displayOptionClass").append(''//'<div class="inviterClass iconClass" onclick="inviter('+class_id+')">Inviter</div>'
		+"<div style='text-align:center;'>Le code pour rejoindre la classe est :</div><div style='text-align:center;margin:30px 0;'><div class='code'>"+code+"</div></div>"
		+"<br><div style='text-align:center;'>Vous pouvez également envoyer le lien suivant par mail :<div style='text-align:center;margin:30px 0;'><input type='text' id='inputCodeLink' readonly value='www.exolingo.com/joinClass.php?code="+code+"'><div class='copyLink' onclick='copyLink();'>Copier</div></div></div>"
		+'<hr><div class="modifyClass iconClass" onclick="modifyClass('+class_id+');">Modifier</div>'
		+'<div class="addArchive iconClass" onclick="addToArchive('+class_id+');">Archiver</div>'
		+'<div class="listProf iconClass"><div style="text-align:left;color:grey;">Professeur(s):</div>'+listProf+'</div>'
		+'<div class="goOutClass iconClass" onclick="goOutClass('+class_id+');" title="Ne plus être enseignant dans cette classe">Se désinscrire</div>'
		+'<div class="close iconClass" onclick="delClass('+class_id+')">Supprimer</div>');
		if(status=="archive"){$(".addArchive").after('<div class="removeArchive iconClass" onclick="RemoveFromArchive('+class_id+')">Sortir des archive</div>');
		$(".addArchive").remove();
		}
	});
}
*/

function archiveClass(class_id)
{
	$.getJSON("ajax.php?action=addToArchive&class_id="+class_id, function(result){
			$('#class_'+class_id).find(".archiveBtn").remove();
	});
}
function goOutClass(class_id)
{
	$.getJSON("ajax.php?action=goOutClass&class_id="+class_id, function(result){
			$('#class_'+class_id).remove();
	});
}
function deleteClass(class_id)
{
	$.getJSON("ajax.php?action=delClass&class_id="+class_id, function(result){
			$('#class_'+class_id).remove();
	});
}
function openClass(class_id)
{
	window.location.href="decks.php?categorie=myClass&class_id="+class_id;
}
function joinClass(class_id){
	$.getJSON("ajax.php?action=joinPublicClass&class_id="+class_id, function(result){
			window.location.href="decks.php?categorie=myClass&class_id="+class_id;
	});
}

function createClass(){
  $('body').append(`<div class='fenetreSombre' onclick='$(this).remove();'><div style='text-align:center;' class='fenetreClaire' onclick='event.stopPropagation();'><img src='img/close.png' class='closeWindowIcon' onclick='$(\".fenetreSombre\").remove();'>
  <h3><?php echo __("Nouveau cours");?></h3>
  <p>	<div class='addClassContent newClassPage'>
      <input class='inputInfoClass' id='class_name' type='text' style='display:inline-block;' autocomplete='off' name='class_name' placeholder='<?php echo __("Nom du cours");?>'/>
      <input class='inputInfoClass' id='promo' type='text' autocomplete='on' name='promo' value='2019-2020' placeholder='2019-2020'/>
  <span><?php echo __("Langue enseignée");?> :<?php echo $_SESSION['target_lang_name']?></span>
  <br>
     <button class='ButtonInfoClass' class='button' onClick='newClass();'><?php echo __("Créer");?></button>
    </div></p>
  </div></div>`);
  // //importation des langues de l'utilisateur dans le menu déroulant
  // $.getJSON("ajax.php?action=getUserTargetLang", function(result)
  // {
  //   for(langRk=0;langRk<result.length;langRk++)
  //   {
  //     $(".select_lang_creaClass").append("<option value='"+result[langRk].lang_id+"'>"+result[langRk].lang_name+"</option>");
  //   }
  //   if(result.length>0){
  //     $(".select_lang_creaClass").val(result[0].lang_id);
  //   }
  // });
}
function newClass(){
	class_name=$("#class_name").val();
	promo=$("#promo").val();
	$(".fenetreSombre").remove();
	$.getJSON("ajax.php?action=addClass&class_name="+class_name+"&promo="+promo, function(result)
	{
    console.log(result);
    if(result.status=="ok")
		{
		class_id=result.class_id;
    window.location.href='decks.php?categorie=myClass&class_id='+class_id;
    }
    else if(result.status=="limit")
    {alert("Nombre de classes maximum atteint.");}
	})
}

$.getJSON("ajax.php?action=getStatsTodayUser", function(result){
	console.log(result.stats);
	nbreMotsEnMemoire=result.nbreMotsEnMemoire;
	$('#objectif').html(nbreMotsEnMemoire);
});
</script>

<script src="js/index.js"></script>

</body>
</html>
