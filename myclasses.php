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
		echo "<script>fullUserName='".$first_name." ".$last_name."';</script>";
		echo "<script>type='".$type."';</script>";
		echo "<script>user_id=".$user_id.";</script>";
		$_SESSION['url']="";
		$classes=array();
		$oldClassId=-1;
		$sql="SELECT classes.class_id,classes.class_name,classes.promo,classes.status,user_class.position
		FROM classes LEFT JOIN  user_class ON user_class.class_id=classes.class_id WHERE user_class.user_id=".$user_id;
		$result = $mysqli->query($sql);
		$virgule="";
		$classes_id_list="";
		while($row = $result->fetch_assoc())
		{
		  $classes_id_list.=$virgule.$row["class_id"];
		  $virgule=" ,";
		  $classes[$row["class_id"]]=array('class_id'=>$row["class_id"],"class_name"=>$row["class_name"],"promo"=>$row["promo"],"status"=>$row["status"],"nbreEleves"=>0,"listProf"=>array(),"position"=>$row["position"]);
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
    <title>Mes classes</title>
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
		<style>
      .navbar{
        margin-bottom:0;
        border-radius:0;
      }
    </style>
</head>

<body class="fond">
	<?php include "entete.php";?>
	<?php include "classData.php";?>
	<script>
	$(".buttonRetourList").hide();
	$(".buttonMyClass").hide();

	classesUpdateButton();
	$(".buttonMyDecks").show();
	$('.desktop').menuBreaker();
	</script>
<style>


</style>

<div class="center classPage" style="margin-top:100px;">
	<div id="list_class" class="sortable">
		<h2><?php echo __("Mes classes");?></h2>
		<div id="archiveFolder" style="display:none;"><a href="#" onclick="$('#archiveClassContainer').toggle();">Montrer/cacher les classes archivées</a><div id="archiveClassContainer" style="display:none;"></div></div>
			<!--<div id="addClassItem" class="classItem">
				<div id="formClass" class="addPanoClass panoClass">
					<div class="ClassContent centerContainer">
							<h1 class="addClassSign" onclick="$('.addClassSign').hide();$('#menuOptionClass').show();">Ajouter / Rejoindre une classe</h1>
							<div id="menuOptionClass" class="newClassPage">
								<div class="btnSobre" onclick='$("#menuOptionClass").hide();$(".addClassSign").show();createClass();'>Créer une classe</div>
								<div class="btnSobre" onclick='$("#menuOptionClass").hide();$(".addClassSign").show();showJoinClassWindow();'>Rejoindre une classe avec un code</div>
							</div>
					</div>
				</div>
		  </div>-->
	  </div>

</div>







</div>
<script>

function showClasses()
{
myClasses=[];
otherClasses=[];
positionMax=-1;
//trouver positionMax
for(k in classes)
{
	if(positionMax<classes[k].position){positionMax=classes[k].position;}
}
for(pos=-1;pos<=positionMax;pos++)
{
	for(k in classes)
	{
		if(classes[k].position==pos){myClasses.push(classes[k]);}
	}
}

for(pos in myClasses)
	{
	position=	myClasses[pos].position;
	class_id=	myClasses[pos].class_id;
	class_name=myClasses[pos].class_name;
	promo=myClasses[pos].promo;
	status=myClasses[pos].status;
	nbreEleves=myClasses[pos].nbreEleves;
	listProf=myClasses[pos].listProf.join('<br>');
	if(status=="ok")
		{
			if(position!=-1){//classe active
			$("#list_class").append('<div class="classItem" id="classItem_'+class_id+'">'
			+'<div class="buttonMenuClass" onclick="settingClass('+class_id+');"><img src="img/tripoint.png" width="40px">'
			+'</div>'
			+'<div id="class_'+class_id+'" class="panoClass"></div></div>');
			$('#class_'+class_id).append('<div class="ClassContent" onClick="openClass('+class_id+');"></div>');
			$('#class_'+class_id+' > .ClassContent').append('<div class="name InfoClass">'+class_name+'</div><div class="promo InfoClass">'+promo+'</div><span class="nbreEleves" title="<?php echo __("nombre d\'élèves");?>">('+nbreEleves+')</span>');
			}
			/*else{//classe ou je ne suis pas prof
			$("#list_class").append('<div class="classItem foreignClass" id="classItem_'+class_id+'"><div id="class_'+class_id+'" class="panoClass"></div></div>');
			$('#class_'+class_id).append('<div class="ClassContent" onClick="openClass('+class_id+');"></div>');
			$('#class_'+class_id+' > .ClassContent').append('<div class="name InfoClass">'+class_name+' <span class="nbreEleves" title="nombre d\'élèves">('+nbreEleves+')</span></div><div class="promo InfoClass">'+promo+'</div><div class="listProf"><div style="text-align:left;color:grey;">Professeur(s):</div>'+listProf+'</div>');
				$('#class_'+class_id+' > .ClassContent').append('<a href="#" class="joinText" onclick="joinClass('+class_id+');"><div>Rejoindre cette classe</div></a>');
			}*/
			if(listProf==""){$('#class_'+class_id).find(".listProf").hide();}
		}
	else if(status=="archive")
		{
			$('#archiveFolder').show();
			$("#archiveClassContainer").append('<div class="classItem">'
			+'<div class="buttonMenuClass" onclick="settingClass('+class_id+');"><img src="img/tripoint.png" width="40px"></div>'
			+'<div id="class_'+class_id+'" class="panoClass"></div></div>')
			$('#class_'+class_id).append('<div class="ClassContent" onClick="openClass('+class_id+');"></div>');
			$('#class_'+class_id+' > .ClassContent').append('<div class="name InfoClass">'+class_name+'</div><div class="promo InfoClass">'+promo+'</div><span class="nbreEleves" title="nombre d\'élèves">('+nbreEleves+')</span>');

			$('#class_'+class_id).parent().find(".addArchive").hide();
			$('#class_'+class_id).find(".ClassContent").after("<div class='removeArchive' onclick='RemoveFromArchive("+class_id+");'><?php echo __("Sortir de l'archive");?></div>");
		}

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
}
showClasses();

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
function openClass(class_id)
{window.location.href="decks.php?categorie=myClass&class_id="+class_id;
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
