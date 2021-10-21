<?php
include_once ("db.php");
session_start();
$explodeURI=explode('/',$_SERVER['REQUEST_URI']);


		if(!isset($_SESSION['user_id'])){header("location:checkLoginCookie.php");exit();}
    // Makes it easier to read
	  $user_id = $_SESSION['user_id'];
		//autorise que les personnes avec la licence trans_deck_#lang_id
		$sql="SELECT COUNT(*) as flagLicence FROM user_licence
		LEFT JOIN licences ON user_licence.licence_id=licences.licence_id WHERE user_licence.user_id=".$user_id." AND licences.active=1 AND licences.date_fin>CURRENT_TIMESTAMP AND licence_type='trans_deck' LIMIT 1";
		$result = $mysqli->query($sql);
		$row = $result->fetch_assoc();
		if($row["flagLicence"]==0)
		{header("location:index.php?c=flagLicence");exit();}
		$result->close();



    $first_name = $_SESSION['first_name'];
    $last_name = $_SESSION['last_name'];
    $email = $_SESSION['email'];
    $active = $_SESSION['active'];
		$type = $_SESSION['type'];
		$classe = $_SESSION['classe'];
		$target_lang_id = $_SESSION['target_lang'];
		$lang_array=array();
		$lang_id="";
		//Récupération des langues autorisés
		$sql="SELECT lang.* FROM user_licence
		LEFT JOIN licences ON user_licence.licence_id=licences.licence_id
		LEFT JOIN lang ON licences.lang_id=lang.lang_id
		 WHERE user_licence.user_id=".$user_id." AND licences.active=1 AND licences.date_fin>CURRENT_TIMESTAMP AND licence_type='trans_deck'";
		$result = $mysqli->query($sql);
		$num_rows = $result->num_rows;
		if($num_rows==0){header("location:index.php?c=recupLang");exit();}
		$dataLang=array();
		while($row = $result->fetch_assoc())
		{array_push($dataLang,$row);
		 array_push($lang_array,$row["lang_id"]);
	  }
		$result->close();
		//Verification langue.
		$showDeck=false;
		if(isset($_GET['target_lang']))
		{
			$lang_id=(int)$_GET['target_lang'];
			if (in_array($lang_id, $lang_array))
			  {
				$_SESSION['target_lang']=$lang_id;
				$showDeck=true;
			  }
			else{
				header("location:index.php?c=langNotInArray");exit();
				$lang_id="";
				}
		}

		echo "<script>fullUserName='".$first_name." ".$last_name."';</script>";

	if($showDeck){
		//recupération infos sur le deck :
		$myDecks=array();
		$result = $mysqli->query('SELECT decks.deck_id,decks.checked,decks.deck_name,decks.hasImage, decks.status FROM decks
		 WHERE decks.lang_id = ' . $lang_id.' AND decks.status="public"');
		while ($row = $result->fetch_assoc()) {
		array_push($myDecks,$row);
		}
		$result->free();
		//récupération des cartes
		$cards=array();
		$result = $mysqli->query('SELECT cards.alert,cards.card_id,cards.deck_id,cards.mot,cards.mot_trad,cards.hasImage,cards.hasAudio,alerte.comment FROM cards
			LEFT JOIN alerte ON alerte.card_id=cards.card_id
			LEFT JOIN decks ON decks.deck_id=cards.deck_id
			WHERE decks.lang_id = ' . $lang_id.' AND decks.status="public" ORDER BY cards.alert ASC, cards.card_id ASC');
		while ($row = $result->fetch_assoc()) {
						array_push($cards,$row);
				}
		$result->free();
		//Mot d'origine
		$originWord=array();
		$result = $mysqli->query('SELECT cards.mot,Transl_cards.card_id FROM cards
			inner JOIN (SELECT cards.hasImage,cards.card_id From decks
				inner JOIN cards ON decks.deck_id=cards.deck_id Where decks.lang_id = '.$lang_id.' AND decks.status="public") AS Transl_cards
			ON Transl_cards.hasImage=cards.card_id WHERE 1');
		while ($row = $result->fetch_assoc()) {
						$originWord[$row["card_id"]]=$row["mot"];
				}
		$result->free();

		//recupération des phrases:
		$sentences=array();
		$result = $mysqli->query('SELECT cards.card_id,card_sentence.sentence_id,card_sentence.sentence FROM cards LEFT JOIN card_sentence ON cards.card_id=card_sentence.card_id LEFT JOIN decks ON decks.deck_id=cards.deck_id WHERE decks.lang_id = ' . $lang_id.' AND decks.status="public"');
		while ($row = $result->fetch_assoc()) {
			if(isset($sentences[$row['card_id']]))
					{array_push($sentences[$row['card_id']],$row);}
			else {
				if($row["sentence"]!=null)
				{$sentences[$row['card_id']]=array($row);}
				else
				{$sentences[$row['card_id']]=array();}
				}
		}
		$result->free();

		echo "<script>originWord=".json_encode($originWord).";</script>";
		echo "<script>decks=".json_encode($myDecks).";</script>";
		echo "<script>cards=".json_encode($cards).";</script>";
		echo "<script>sentences=".json_encode($sentences).";</script>";
	}

		echo "<script>lang_id=".json_encode($lang_id).";</script>";
		echo "<script>showDeck=".json_encode($showDeck).";</script>";
		echo "<script>dataLang=".json_encode($dataLang).";</script>";

?>
<!DOCTYPE html>
<html >
 <head>


    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verification des listes</title>
    <!-- Bootstrap
		<link href="css/bootstrap.min.css" rel="stylesheet">-->
	  <link href="css/main.css?ver=<?php echo filemtime('css/main.css');?>" rel="stylesheet">
		<link href="css/styleEntete.css?ver=<?php echo filemtime('css/styleEntete.css');?>" rel="stylesheet">
	  <link href="css/deck.css?ver=<?php echo filemtime('css/deck.css');?>" rel="stylesheet">
	  <link href="css/card.css?ver=<?php echo filemtime('css/card.css');?>" rel="stylesheet">
		<link href="css/myStyle.css?ver=<?php echo filemtime('css/myStyle.css');?>" rel="stylesheet">
		<link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16" />
    <link rel="stylesheet" type="text/css" href="css/navStyle.css"/>
		<link rel="stylesheet" type="text/css" href="css/jquery-te-1.4.0.css"/>
		<script src="js/jquery-3.3.1.min.js"></script>
		<script src="js/jquery-te-1.4.0.min.js"></script>
		<script src="js/recorder2.js"></script>
  	<script src="js/app.js"></script>
		<script src="socket.io/socket.io.js"></script>
		<style>
      .navbar{
        margin-bottom:0;
        border-radius:0;
      }
			.addContentTitre{
				font-size:1.3em;
				margin-bottom:30px;
			}
			.addContentIcon{
				padding:5px;
			  display:inline-block;
				margin:10px;
				cursor:hand;
				vertical-align:middle;
				background-color:#fafafa;
				background-position: center center;
				background-repeat:no-repeat;
				background-size: 60%;
    	width: 50px;
    	height: 40px;
    	border-radius: 50% 20%;
			box-shadow: 0px 0px 4px grey;
			}
			.addContentIconCard{
				padding:5px;
			  display:inline-block;
				cursor:hand;
				vertical-align:middle;
				background-color:#fafafa;
				background-position: center center;
				background-repeat:no-repeat;
				background-size: 60%;
    	width: 50px;
    	height: 40px;
    	border-radius: 50% 20%;
			box-shadow: 0px 0px 4px grey;
			float:left;
			}
			.addBubbleIcon:after{
				content:"+";
				font-size:2em;
				color:var(--mycolor2);
				border-radius:50%;
				padding:3px;
				position:relative;
				top: 20px;
				left: 16px;
				background-color: white;
				line-height: 24px;
				padding: 0 4px;
				height: 26px;
				display: inline-block;
				box-shadow: 1px 1px 4px grey;
			}
			.searchBubbleIcon:after{
				content:"";
				border-radius:50%;
				padding:12px;
				position:relative;
				top: 20px;
				left: 16px;
				background-color: white;
				line-height: 24px;
				height: 26px;
				display: inline-block;
				box-shadow: 1px 1px 4px grey;
				background-image:url("img/loupeBleu.png");
				background-size: 55% 55%;
        background-position: center center;
				background-repeat:no-repeat;
			}
			.delBubbleIcon:after{
				content:"-";
				font-size:2em;
				color:red;
				border-radius:50%;
				padding:3px;
				position:relative;
				top: 20px;
				left: 16px;
				background-color: white;
				line-height: 24px;
				padding: 0 6px;
				height: 26px;
				display: inline-block;
				box-shadow: 1px 1px 4px grey;
			}
			.addContentIconAudio {background-image:url(img/haut_parleur.png); margin: 0px;}
			.addContentIconPhrase{background-image:url(img/sentence.png); margin: 10px;}
			.crea{
				padding:10px;
				background-color:var(--mycolor2);
				color:white;
				text-align:center;
				display:inline-block;
				cursor:hand;
				border:none;
				box-sizing: border-box;
				border-radius:4px;
				border-bottom: #00000045 5px solid;
				position:relative;
				top:-2px;
			}
			.creaMagic{padding:3px;
			background-color:var(--mycolor2bis);
			color:white;
			text-align:center;
			display:inline-block;
			cursor:hand;
			border:none;
			vertical-align: middle;
			}
			.creaMagic img{width:30px;}
			#new_mot{padding:10px;}
			.input_text{margin-right:0;}
			.addContentContainer{text-align:center;padding:10px;vertical-align:middle;}
			.del_content{margin-top:30px;}
			audio{filter: drop-shadow(1px 1px 3px);margin:20px 0;}
			#previewArea{text-align: center;
    margin-top: 30px;
    background-color: white;
    display: inline-block;
    box-shadow: 0px 0px 4px grey;
		max-width:640px;
		width:100%;
		max-height:700px;
		overflow:auto;
		margin-bottom:20px;}
			#windowGlobalContainer{display:none;}
			.backIcon{display: inline-block;
		    width: 35px;
		    float: left;
		    background-image: url(img/back.png);
		    height: 25px;
		    background-size: 35px 25px;
				opacity:0.6;}
		.backIcon:hover{opacity:1;}
		.valider{background-color:var(--mycolor2); padding:5px;display:inline-block;color:white;opacity:0.7; border-radius:5px;margin:10px;}
		.valider:hover{opacity:1;}
		.annuler{background-color:red; padding:5px;display:inline-block;color:white;opacity:0.4; border-radius:5px;margin:10px;}
		.annuler:hover{opacity:1;}
		.previewPoster{max-width:80%;max-height:60%;margin:20px;padding:5px; border:1px solid grey;}
		.posterPreview{max-width:80%;max-height:600px;}
		#inputRechercheImage{padding:10px;width:96%;}

		.slideUp{display:none;}
		.accordeonsTop .slideUpTop:after{content:"";background-image:url(img/arrow_down.png);background-size:cover;width:20px;height:20px;padding-left:15px;margin-left:15px;float:right;}
		.accordeonsTop label:not(.slideUpTop):after{content:"";background-image:url(img/arrow_down.png);background-size:cover;width:20px;height:20px;padding-left:15px;margin-left:15px;float:right;transform:rotate(180deg);}
		.accordeonsTop label:after{transition:0.5s;}
		.ligneEditCard{margin:0;}
		.languageToShowContainer .tinyFlag{margin:20px;transform:scale(1.3);}
		.languageToShowContainer .tinyFlag:hover{margin:20px;transform:scale(1.5);}
		.lang_flag_item{text-align:left;vertical-align:middle;display:inline-block;background-color:white;padding:5px;margin:5px;box-shadow:0 0 5px grey;width:250px;height:60px;overflow: hidden;;}
		.selectedLangFlag{box-shadow:0 0 0 5px var(--mycolor2bis);;background-image:url(img/visi.png);background-size:15px 15px;background-position:right 10px center;background-repeat:no-repeat;}
		</style>
</head>

<body class="fond">
	<?php include "entete.php";?>
<script>
$(".buttonHome").hide();
$(".buttonMesClasses").hide();
$(".buttonMyDecks").hide();
$(".buttonMyClass").hide();
$(".settingClass").hide();
langUpdateButton();

</script>

	<!--   ============FIN ENTETE=================-->



<div class="center bodyContent" style="display:flex;flex-direction:column;padding-top:40px;">
<h1>Interface traducteur/vérificateur</h1>
<div class="accordeonsTop" onclick="retract(this);">
		<label style="background-color:white;" class="label_edition">
			Langues à afficher :
		</label>
</div>
<div class="retractable">
	<div class="languageToShowContainer"></div>
</div>
	<div id="fullDeck_container">





	</div>
</div><!--center-->
<div id="onTopBottom" tabindex="100"></div>
<style>
.labelClass{width:110px;position:relative;display:inline-block;vertical-align:middle;text-align: left;margin-left: 60px;}
.class_item:hover{box-shadow:2px 2px 5px grey;top:-2px;left:-2px;}
.class_item{display: inline-flex;
    margin: 3px;
    min-height: 60px;
    background-color: white;
		position:relative;
	  padding:10px;
		border-radius:4px;
		box-sizing: border-box;
		border-bottom: #00000045 5px solid;}
.promo{font-size:0.7em;}
.status_txt{display:block;font-size:0.7em;}
.visibilityIcon{position:absolute;background-position:center center;background-repeat:no-repeat;width:22px;top:0;right:0;}
.hidden{display:none;}
.class_nameSpan{font-size:1.2em;}
.class_status_ok{background-color:var(--mycolor2);color:white;}
.class_status_waiting{background-color:#ffcc99;}
.class_status_build{background-color:lightgrey;opacity:0.8;}
.class_status_coop{background-color:#5976e4;color:white;}
.block_edit{
	  position: relative;
    text-align: center;
    margin: auto;
    background-color: white;
    border-radius: 3px;
    border: 1px lightgrey solid;
    padding: 10px;
		max-width:1200px;
		width:98%;
		display:block;
	}

.imgToSelect{width:100px; height:100px; background-size:cover;background-position:center center; display:inline-block;margin:30px;}
.imgSelected{border:8px solid gold;margin:20px;}
.tradIcon{background-image:url("img/translate_oublie.png");width:30px;height:30px;background-size:contain;position:absolute;bottom:0;right:0;}
.addSentenceYourself{margin-left:35px;}
#disconnected{position:fixed;top:0;bottom:0;left:0;right:0;display:flex;z-index:9}
.img_deck_preview{object-fit:cover;}
#addContentIconImageDeck{position:relative;top:-30px;}
.statusState{width:150px;padding:8px;}
.emailInputPartage,.droitPartage,.droitPartage option{padding:8px;}
.edit_front, .edit_back {width:100px;height:100px;vertical-align:top;}
.edit_back {width:200px;}
.ligneEditCard{min-height:auto;}
.icons_card_container{width:200px;}

.slideUp{display:none;}
.accordeonsTop .slideUpTop:after{content:"";background-image:url(img/arrow_down.png);background-size:cover;width:20px;height:20px;padding:10px 15px;margin:0 15px;float:right;}
.accordeonsTop .slideBtn:not(.slideUpTop):after{content:"";background-image:url(img/arrow_down.png);background-size:cover;width:20px;height:20px;padding:10px 15px;margin:0 15px;float:right;transform:rotate(180deg);}
.accordeonsTop .slideBtn:after{transition:0.5s;}
.checkedDeck{color:white;background-color:red;display:inline-block;padding:10px;margin:0 20px;}
.checkedDeckON{background-color:lime;}
.img_deck_preview{width:100px;height:100px;margin:0 10px;}
.slideBtn{width:100px;height:30px;display:inline-block;}
.mot_trad_card{opacity:0.1;}
.selectedLang{box-shadow:0 0 0 5px #6090CC;background-image:url(img/stylo.png);background-size:15px 15px;background-position:right 10px center;background-repeat:no-repeat;}

</style>

<script>

for(k in dataLang)
{
	lang_code2=dataLang[k].lang_code2;
	lang_id_data=dataLang[k].lang_id;
	lang_name=dataLang[k].lang_name;
	lang_interface_build=dataLang[k].interface_build;
	lang_deck=dataLang[k].lang_deck;
	if(lang_deck==1)
	{
		$(".selectLang").append("<option value='"+lang_id_data+"'>"+lang_name+"</option>");
		if($('.lang_flag_'+lang_id_data).length==0){
				$(".languageToShowContainer").append(`
					<a href='check_decks.php?target_lang=`+lang_id_data+`' title='`+lang_name+`' class='invisible lang_flag_item lang_flag_`+lang_id_data+`'>
						<span class='tinyFlag flag_`+lang_code2+`'></span>
						<span>`+lang_name+`</span>
					</a>`);
				}
	}
	$(".lang_flag_"+lang_id).addClass("selectedLang");

}
function retract(that)
{$(that).parent().find('.retractable').slideToggle(1000);
 $(that).find(".slideBtn").toggleClass("slideUpTop");
}

<?php
if($showDeck){
?>
console.log(decks,cards,sentences);
for(k in decks)
{
deck_name=decks[k].deck_name;
deck_id=decks[k].deck_id;
checkedDeck=decks[k].checked;
deck_hasImage=decks[k].hasImage;
$("#fullDeck_container").append(
	`<div id="fullDeck_item_`+deck_id+`" class="fullDeck_item">
			<div class="deck_info block_edit accordeonsTop" onclick="retract(this);">
					<img src="" class="img_deck_preview">
					<span class='deck_name' onclick="event.stopPropagation();"><input type="text" style="font-size:2em;text-align:center;max-width:60%;" class="titre_deck_edition" value="`+deck_name+`" onBlur="MAJ_deck(`+deck_id+`);"></span>
					<div class="checkedDeck" onclick="toggleCheck(`+deck_id+`);event.stopPropagation();">Liste à vérifier</div>
					<!--<div class="checkedDeck" onclick="addSentenceAuto(`+deck_id+`);event.stopPropagation();">Ajouter Phrase Auto</div>-->
					<div><div class='slideBtn slideUpTop'></div></div>
			</div>
			<div class="retractable slideUp">
				<div class="list_card block_edit">
					<div class='table_card'></div>
					<div class="checkedDeck" onclick="toggleCheck(`+deck_id+`);event.stopPropagation();">Liste à vérifier</div>
				</div>
			</div>
		</div><br><br>`
);
if(deck_hasImage==0){
	$("#fullDeck_item_"+deck_id).find('.img_deck_preview').attr('src','img/default_deck.png');}
else{
	cacheBreaker=new Date().getTime();
	$("#fullDeck_item_"+deck_id).find('.img_deck_preview').attr('src','deck_img/deck_'+deck_hasImage+'.png?v='+cacheBreaker);
}

if(checkedDeck==1){$("#fullDeck_item_"+deck_id).find(".checkedDeck").addClass("checkedDeckON").html("vérifié !");}
}

for(k in cards)
{
	cards[k].sentences=[];
	card_id=cards[k].card_id;
	for(j in sentences[card_id])
		{
			cards[k].sentences.push({sentence_id:sentences[card_id][j].sentence_id,sentence:sentences[card_id][j].sentence});
		}

}
function addSentenceAuto(deck_id){
	$.getJSON("ajax_admin.php?action=addSentenceAuto&deck_id="+deck_id, function(result){
		console.log(result);
	});
}
function toggleCheck(deck_id){
	$("#fullDeck_item_"+deck_id).find(".checkedDeck").toggleClass("checkedDeckON");
	deckIsChecked=$("#fullDeck_item_"+deck_id).find(".checkedDeck").hasClass("checkedDeckON");
	if(deckIsChecked){
		$("#fullDeck_item_"+deck_id).find(".checkedDeck").html("vérifié !");
		$("#fullDeck_item_"+deck_id).find('.retractable').slideUp(1000);
		$("#fullDeck_item_"+deck_id).find(".deck_name").addClass("slideUpTop");
		location.href="#fullDeck_item_"+deck_id;
		checkedValue=1;
	}else {
		$("#fullDeck_item_"+deck_id).find(".checkedDeck").html("Liste à vérifier");
		checkedValue=0;
	}

	$.getJSON("ajax.php?action=updateCheckedDeck&deck_id="+deck_id+"&checked="+checkedValue, function(result){
		console.log(result);
	});
}

//creer fonction pour ouverture de la fenetre avec le bon content loader
//créer une fonction pour gerer l'upload
function showWindow(){
$('.fenetreSombre').remove();
$('body').append("<div class='fenetreSombre' onclick='$(this).remove();'><div style='text-align:center;' class='fenetreClaire' onclick='event.stopPropagation();'>"
+"</div></div>");
}

function closeWindow()
{
	$(".fenetreSombre").remove();}


var cacheBreaker=new Date().getTime();
var record_flag=false;
var nbre_ligne=0;
var img_card_id=0;

//CARDS

for(rg in cards)
{
	creer_ligne(rg)
}

function MAJ_deck(deck_id){
	new_deck_name=$("#fullDeck_item_"+deck_id).find('.titre_deck_edition').val();
	$.getJSON("ajax.php?action=updateDeck&deck_id="+deck_id+"&deck_name="+new_deck_name, function(result){
		console.log(result);
	});

}

function MAJ_auto(card_id)
{
	mot=$('#ligne_carte_'+card_id).find(".mot_card").val().trim();
	mot_trad=$('#ligne_carte_'+card_id).find(".mot_trad_card").val().trim();

	console.log(mot+mot_trad);
	$.getJSON("ajax.php?action=updateCard&card_id="+card_id,{mot:mot,mot_trad:mot_trad}, function(result){});
	for(k in cards){
		if(cards[k].card_id==card_id){
			cards[k].mot=mot;
			cards[k].mot_trad=mot_trad;
		}
	}
}
function emptyOnTopBottom(){
	$("#onTopBottom").html('');
}
function showOnTop(id)
{
	img_card_id=id;
	mot=$('#ligne_carte_'+id).find(".mot_card").val();
	mot=mot.replace("'"," ");
	$('#onTopBottom').html("");
	$('#onTopBottom').prepend("<div style='position:absolute; right:10px;top:0;color:red;font-size:0.8em;' onclick='emptyOnTopBottom();'><img src='img/close.png' width='30px'></div>");
	$('#onTopBottom').append("<div id='type_img_pixabay' style='margin:20px;vertical-align:middle;display:inline-block;margin-left:20px;text-align:center;'><div>");
	$('#onTopBottom').append("<div id='resultat_pixabay' style='margin-20px;vertical-align:middle;display:inline-block;margin-left:20px;'><div>");
	$('#type_img_pixabay').append("<input type='text' id='inputRechercheImage' value='"+mot+"'><br>");
  $('#type_img_pixabay').append("<div class='buttonTypePixabay' onclick='loadPixabayAPI("+id+",\"all\");'><?php echo __("Recherche");?></div><br>");
	//$('#type_img_pixabay').append("<div class='buttonTypePixabay' onclick='loadPixabayAPI("+id+",\"photo\");'>Recherche de photos</div><br>");
	//$('#type_img_pixabay').append("<div class='buttonTypePixabay' onclick='loadPixabayAPI("+id+",\"illustration\");'>Recherche d'illustration</div><br>");
	//$('#type_img_pixabay').append("<div class='buttonTypePixabay' onclick='loadPixabayAPI("+id+",\"vector\");'>Recherche d'image vectorisée</div><br>");
	$('#type_img_pixabay').append("<div class='buttonImportFile' onclick='$(\"#input_image_card\").click();'><?php echo __("Importer un fichier");?></div>");
	$('#type_img_pixabay').append("<div class='buttonImportFile' onclick='url=prompt();save_img("+id+",url)'><?php echo __("Depuis url");?></div><br>");
	loadPixabayAPI(id,"all");
}

$(document).mouseup(function(e)
{
	var container = $('#onTopBottom');
	if (!container.is(e.target) && container.has(e.target).length === 0)
	{
		emptyOnTopBottom();
	}
});

function creer_ligne(rg)
{
	//console.log("creer_ligne",rg);
	nbre_ligne++;
	alertCard=cards[rg]["alert"];
	alertComment=cards[rg]["comment"];
	if(alertComment==null){alertComment="";}
	id=cards[rg]["card_id"];
	mot=cards[rg]["mot"];
	mot_trad=cards[rg]["mot_trad"];
	hasImage=cards[rg]["hasImage"];
	hasAudio=cards[rg]["hasAudio"];
	deck_id=cards[rg]["deck_id"];
	originalWord=originWord[id];
	sentencesCard=sentences[id];
	if(alertCard=="1"){alertClass="alertEditON";alertCloche="alertON_icon";}else{alertClass="";alertCloche="alertOFF_icon";}
		html_ligne='';
		html_ligne+=`<div id="ligne_carte_`+id+`" class="ligneEditCard `+alertClass+`" height="40px;">
									<div class="alertCardContainer">
										<div class="alert_button_ligne icon_back `+alertCloche+`" onclick="toggleAlert(`+id+`);"></div>
										<div class="msgAlertcard">`+alertComment+`</div>
									</div>
									<div class="blockCard">
										<div class="edit_front card" style="background-image:url(\'card_img/card_`+hasImage+`.png?v=`+cacheBreaker+`\')">
											<input class="mot_trad_card" type="text" value="`+mot_trad+`" onkeyup="MAJ_auto(`+id+`);">
											<div class="icons_card_container">
										</div>
									</div>
									<div class="edit_back card">
										<input class="mot_card" style="font-family: caviar;" type="text" value="`+mot+`" onkeyup="MAJ_auto(`+id+`);">
									</div>
									<div class='motOriginal' style="text-align: left;padding-left:10px;">`+originalWord+`</div>
									<div class="icons_card_container">
										<div class="addContentIconCard addContentIconAudio delBubbleIcon" onclick="del_audio(`+id+`);"></div>
										<button class="icon_back recording_icon" onclick="if(record_flag){stopRecording(this,`+id+`);record_flag=false;}else{startRecording(this);record_flag=true;}"></button>
										<div class="icon_back icon_audio" onclick="play_audio(`+id+`)";event.stopPropagation();></div>
									</div>
								</div>
								<div class="sentences_container">
									<div class="sentence_container_block"></div>
									<div class="addSentenceYourself" title="<?php echo __("Ajouter une phrase manuellement");?>" onclick="addSentenceYourself(`+id+`);">
										<input type="text" class="modifiedSent" readonly placeholder="<?php echo __("Nouvelle phrase avec mot entre * ou bien sa définition");?>">
									</div>
									<div class="rechercheSentence addContentIconCard addContentIconPhrase searchBubbleIcon" onclick="showOnTopSentence(`+id+`);" title="<?php echo __("Rechercher des phrases avec : ");?>`+mot+`"></div>
								</div>
							</div>`;
		$("#fullDeck_item_"+deck_id).find('.table_card').prepend(html_ligne);
		if(alertComment==''){	$('#ligne_carte_'+id).find(".msgAlertcard").hide();}
		if(hasImage>0){
			//$('#ligne_carte_'+id).find(".addContentIconImage").addClass("delBubbleIcon").off().attr('onclick','del_card_img('+id+');');
		}else{
			$('#ligne_carte_'+id).find(".edit_front").css("background-image","url(img/default_card.png)");
			//$('#ligne_carte_'+id).find(".addContentIconImage").addClass("addBubbleIcon").off().attr('onclick','showOnTop('+id+');');
		}
		if(hasAudio==1){
			$('#ligne_carte_'+id).find(".icon_audio").show();
			$('#ligne_carte_'+id).find(".addContentIconAudio").show();
			$('#ligne_carte_'+id).find(".addContentIconAudio").addClass("delBubbleIcon").off().attr('onclick','del_audio('+id+');');
		}else{
			$('#ligne_carte_'+id).find(".addContentIconAudio").hide();
			$('#ligne_carte_'+id).find(".icon_audio").hide();
			$('#ligne_carte_'+id).find(".addContentIconAudio").addClass("addBubbleIcon");}
			//Gestion des phrases:
			for(sent_rk in sentencesCard)
			{
				sentence=sentencesCard[sent_rk].sentence;
				sentence_id=sentencesCard[sent_rk].sentence_id;
				sentence=sentence.replace("*","<span style='color:orange;'>",1);
				sentence=sentence.replace("*","</span>",1);
				//console.log(sentence);
					$('#ligne_carte_'+id).find(".sentence_container_block").append("<div class='sentenceBlock' id='sent_"+sentence_id+"'><div class='delSentenceIcon icon_back' onclick='delSentenceFound("+sentence_id+","+id+");'></div><div id='sentence_"+sentence_id+"' onclick='changeSentToEditCard("+sentence_id+");' class='one_sentence'>"+sentence+"</div><input style='display:none;' type='text' class='modifiedSent' value='' onblur='console.log(2);goBackOneSentenceCard("+sentence_id+");'></div>");
			}
}

sentence_found=[];
function rechercheSent(id)
{
	//console.log(id);
	mot=$("#inputRechercheSentence").val();
	mot=mot.replace("'"," ");
	$.getJSON("ajax.php?action=getPhrasePlus&mot="+mot, function(result){
	//console.log(result);
	sentence_found=result;
	$('#resultat_sentence').html('');
	for(i in result)
	{
		//maPhrase=result[i].phrase.replace(mot,"<span style='color:orange;'>"+mot+"</span>")
		maPhrase=result[i].phrase.replace(new RegExp("("+mot+")", 'gi'),"<span style='color:orange;'>$1</span>")
		//(blue)/gi, "red$1"
		//console.log(maPhrase);
		$('#resultat_sentence').append("<div id='sentenceFound_"+i+"' class='sentenceBlock' style='cursor:hand;'><div class='one_sentence' onclick='addSentenceFound("+i+","+id+");'>"+maPhrase+"</div></div>");
	}
	if(result.length==0){$('#resultat_sentence').append("<?php echo __("Aucun résultat pour :");?><span color='grey'>"+mot+"</span>");}

	//$('#resultat_sentence').append('<br><div class="btnMoyen" id="addSentenceYourself" onclick="addSentenceYourself('+id+');">Ajouter une phrase manuellement</div>');
  //$('#resultat_sentence').append("<div class='sentenceBlock'><div class='addSentenceIcon icon_back' onclick='addSentenceYourselfFromSearch("+id+");'></div><input style='' id='myNewSentence' placeholder='Nouvelle phrase avec "+mot+" entre * ou bien sa définition' type='text' class='modifiedSent' value=''></div>");
	});
	//$("#microphone").hide();
}

function addSentenceYourself(id)
{
	mot=$('#ligne_carte_'+id).find(".mot_card").val();
	thatSentence="";
	$.getJSON("ajax.php?action=addOneSentenceToCard&card_id=" + id+"&sentence="+thatSentence, function(result){
		sentence_id=result;
		showSentenceInCard(id,sentence_id,thatSentence);
		changeSentToEditCard(sentence_id);
			});
}
function addSentenceYourselfFromSearch(id)
{
	thatSentence=$("#myNewSentence").val();
	$.getJSON("ajax.php?action=addOneSentenceToCard&card_id=" + id+"&sentence="+thatSentence, function(result){
		sentence_id=result;
		for(k in cards){
			if(cards[k].card_id==id){
				cards[k].sentences.push({sentence_id:sentence_id,sentence:thatSentence});

			}
		}
		thatSentence=thatSentence.replace("*","<span style='color:orange;'>",1);
		thatSentence=thatSentence.replace("*","</span>",1);
		showSentenceInCard(id,sentence_id,thatSentence);
		changeSentToEditCard(sentence_id);
		});
}
function changeSentToEditCard(sentence_id)
{
	thatSentence=$("#sent_"+sentence_id).find(".one_sentence").html();
	$("#sent_"+sentence_id).find(".one_sentence").hide();
	//console.log(thatSentence);
	//console.log(sentence_id);
	thatSentence=thatSentence.replace('<span style="color:orange;">',"*");
	thatSentence=thatSentence.replace("</span>","*");
	thatSentence=thatSentence.replace("'","\'");
	$("#sent_"+sentence_id).find(".modifiedSent").val(thatSentence).show();
	$("#sent_"+sentence_id).find(".modifiedSent").focus();
	//$("#sent_"+sentence_id).find(".modifiedSent").off('blur');
	//$("#sent_"+sentence_id).find(".modifiedSent").on("blur",function(){console.log(3);goBackOneSentenceCard(sentence_id);})
}
function changeSentToEdit(i)
{
	thatSentence=$("#sentenceFound_"+i).find(".one_sentence").html();
	$("#sentenceFound_"+i).find(".one_sentence").hide();
	//console.log(thatSentence);
	thatSentence=thatSentence.replace('<span style="color:orange;">',"*");
	thatSentence=thatSentence.replace("</span>","*");
	thatSentence=thatSentence.replace("'","\'");
	$("#sentenceFound_"+i).find(".modifiedSent").val(thatSentence).show();
	$("#sentenceFound_"+i).find(".modifiedSent").focus();
	$("#sentenceFound_"+i).find(".modifiedSent").off().on("blur",function(){goBackOneSentence(i);})
}
function goBackOneSentenceCard(sentence_id)
{
	//console.log("goBackOneSentenceCard");
	sentence=$("#sent_"+sentence_id).find(".modifiedSent").val();
	card_id=parseInt($("#sent_"+sentence_id).closest( ".ligneEditCard " ).attr('id').substr(12));
	StaredSentence=sentence;
	nbreEtoile=0;
	for(k in sentence)
	{if(sentence[k]=="*"){nbreEtoile++;}}
	if(nbreEtoile==1){sentence+="*";}
	//console.log("bon nombre d'étoiles");
	sentence=sentence.replace("*","<span style='color:orange;'>",1);
	sentence=sentence.replace("*","</span>",1);
	$("#sent_"+sentence_id).find(".modifiedSent").hide();
	$("#sent_"+sentence_id).find(".one_sentence").html(sentence).show();
	if(sentence==""){
		$("#sent_"+sentence_id).find(".modifiedSent").off('blur');
		$("#sent_"+sentence_id).remove();
	}
	$.getJSON("ajax.php?action=updateOneSentenceToCard&sentence="+StaredSentence+"&sentence_id="+sentence_id, function(result){
		for(k in cards){
			if(cards[k].card_id==card_id){
				cards[k].sentences.push({sentence_id:sentence_id,sentence:StaredSentence});
			}
		}
	});

	//console.log(thatSentence);
}
function goBackOneSentence(i)
{
	sentence=$("#sentenceFound_"+i).find(".modifiedSent").val();
	nbreEtoile=0;
	for(k in sentence)
	{if(sentence[k]=="*"){nbreEtoile++;}}
	if(nbreEtoile==2 || nbreEtoile==0){console.log("bon nombre d'étoiles");
	sentence=sentence.replace("*","<span style='color:orange;'>",1);
	sentence=sentence.replace("*","</span>",1);
	$("#sentenceFound_"+i).find(".modifiedSent").hide();
	$("#sentenceFound_"+i).find(".one_sentence").html(sentence).show();

	}
	else{console.log("mauvais nombre d'étoiles");}
	console.log(thatSentence);
}
function showOnTopSentence(id)
{
	mot=$('#ligne_carte_'+id).find(".mot_card").val();
	mot=mot.replace("'"," ");
	console.log(mot);
	$('#onTopBottom').html("");
	$('#onTopBottom').prepend("<div style='position:absolute; right:10px;top:0;color:red;font-size:0.8em;' onclick='emptyOnTopBottom();'><img src='img/close.png' width='30px'></div>");
	$('#onTopBottom').append("<div id='type_recherche_sentence' style='float:left; width:200px;margin:20px;vertical-align:middle;display:block;margin-left:20px;text-align:center;'><div>");
	$('#onTopBottom').append("<div id='resultat_sentence' style='margin:20px;vertical-align:middle;display:block;overflow-y:auto;margin-left:20px;max-height:400px;'><div>");
	$('#type_recherche_sentence').append("<input type='text' id='inputRechercheSentence' value='"+mot+"'><br>");
  $('#type_recherche_sentence').append("<div class='buttonTypeSentence' onclick='rechercheSent("+id+");'><?php echo __("Rechercher");?></div><br>");
	rechercheSent(id);
}
function addSentenceFound(i,id)
{
	thatSentence=$("#sentenceFound_"+i).find(".one_sentence").html();
	$("#sentenceFound_"+i).hide();
	console.log(thatSentence);
	thatSentence=thatSentence.replace('<span style="color:orange;">',"*");
	thatSentence=thatSentence.replace("</span>","*");
	thatSentence=thatSentence.replace("'","\'");
	$.getJSON("ajax.php?action=addOneSentenceToCard&card_id=" + id+"&sentence="+thatSentence, function(result){
		sentence_id=result;
		for(k in cards){
			if(cards[k].card_id==id){
				cards[k].sentences.push({sentence_id:sentence_id,sentence:thatSentence});
			}
		}
		thatSentence=thatSentence.replace("*","<span style='color:orange;'>",1);
		thatSentence=thatSentence.replace("*","</span>",1);
		showSentenceInCard(id,sentence_id,thatSentence);
			});
	//addOneSentenceToCard
}
function showSentenceInCard(id,sentence_id,sentence)
{
	if($("#sent_"+sentence_id).length==0)
	{$('#ligne_carte_'+id).find(".sentence_container_block").append("<div class='sentenceBlock' id='sent_"+sentence_id+"'><div class='delSentenceIcon icon_back' onclick='delSentenceFound("+sentence_id+","+id+");'></div><div id='sentence_"+sentence_id+"'  onclick='changeSentToEditCard("+sentence_id+");' class='one_sentence'>"+sentence+"</div><input style='display:none;' type='text' class='modifiedSent' value='' onblur='console.log(1);goBackOneSentenceCard("+sentence_id+");'></div>");}
	else{
		$("#sentence_"+sentence_id).html(sentence);
	}
}
function delSentenceFound(sentence_id,card_id)
{
	$('#ligne_carte_'+card_id).find("#sent_"+sentence_id).hide();
	$.getJSON("ajax.php?action=delOneSentenceToCard&card_id="+card_id+"&sentence_id="+sentence_id, function(result){
		for(k in cards){
			if(cards[k].card_id==id){
				for(j in cards[k].sentences)
					{if(cards[k].sentences[j].sentence_id==sentence_id)
						{
							cards[k].sentences.splice(j,1);
							}
						}
					}
		}
	});
}

function play_audio(card_id)
{console.log("on demande l'écoute de "+card_id);

	if(!$("#audio_"+card_id).length){
	console.log("creation de l'élem Audio");
	soundFile="card_audio/card_"+card_id+".wav?v="+cacheBreaker;
	$("body").append('<audio id="audio_'+card_id+'" src="'+soundFile+'">');
	}
	$("#audio_"+card_id).get(0).play();
}


function toggleAlert(id)
{
  if(	$("#ligne_carte_"+id).find(".alert_button_ligne").hasClass("alertON_icon"))
  {$("#ligne_carte_"+id).find(".alert_button_ligne").removeClass("alertON_icon").addClass("alertOFF_icon");
	$("#ligne_carte_"+id).removeClass("alertEditON");
	$("#ligne_carte_"+id).find(".alert_button_ligne").html("");
	$('#ligne_carte_'+id).find(".msgAlertcard").hide();
  $.getJSON("ajax.php?action=deleteAlert&card_id="+id, function(results){
		console.log("delAlert",id,cards);
		for(k in cards){
			if(cards[k].card_id==id){
				cards[k].alert="0";
				cards[k].comment="";
			}
		}
	});
  }
  else {
    alert_comment=prompt("<?php echo __("Quel est le problème avec cette carte ?");?>");
    if(alert_comment!="" && alert_comment!=null){
			$('#ligne_carte_'+id).find(".msgAlertcard").show();
      $("#ligne_carte_"+id).find(".alert_button_ligne").addClass("alertON_icon").removeClass("alertOFF_icon");
			$("#ligne_carte_"+id).addClass("alertEditON");
			$("#ligne_carte_"+id).find(".msgAlertcard").html(alert_comment);
      $.getJSON("ajax.php?action=addAlert&card_id="+id+"&alert_comment="+alert_comment, function(results){
				console.log("addAlert",id,cards);
				for(k in cards){
					if(cards[k].card_id==id){
						cards[k].alert="1";
						cards[k].comment=alert_comment;

					}
				}
			});
    }
  }
}



	function del_audio(card_id)
{
	console.log("remove sound");
	$.getJSON("ajax.php?action=cardHasNoAudio&card_id=" + card_id, function(result){
		for(k in cards){
			if(cards[k].card_id==card_id){
				cards[k].hasAudio=0;


			}
		}
		$('#ligne_carte_'+card_id).find(".addContentIconAudio").hide();
		$('#ligne_carte_'+card_id).find(".icon_audio").hide();
		$('#ligne_carte_'+card_id).find(".addContentIconAudio").addClass("addBubbleIcon");

	});
			//$('#play_son_'+card_id+' > audio').remove();
			//$('#suppr_son_'+card_id).html('');
}

<?php
}
?>


</script>


<script src="js/index.js"></script>

</body>
</html>
