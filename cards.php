<?php

include_once ("db.php");
session_start();
$explodeURI=explode('/',$_SERVER['REQUEST_URI']);
$_SESSION['url']=end($explodeURI);
if(!isset($_SESSION['user_id'])){header("location:checkLoginCookie.php");exit();}

    // Makes it easier to read
		//if(!isset($_SESSION['user_id'])){header('Location: logout.php');}
	  $user_id = $_SESSION['user_id'];
    $first_name = $_SESSION['first_name'];
    $last_name = $_SESSION['last_name'];
    $email = $_SESSION['email'];
    $active = $_SESSION['active'];
		$classe = $_SESSION['classe'];
		$userType = $_SESSION['type'];
		$deck_id=(int)$_GET["deck_id"];
		$lang_code2_2_deck="fr_FR";
		// if deck(>0) not in mon lexique => ajouter.
		if($deck_id>0){
			$sql='SELECT deck_class.id FROM deck_class LEFT JOIN classes ON classes.class_id=deck_class.class_id WHERE classes.creator_id='.$user_id.' AND classes.status="perso" AND deck_class.deck_id='.$deck_id;
			$result = $mysqli->query($sql);
			$flag =$result->num_rows;
			if($flag==0)
			{	$sql='INSERT INTO deck_class (deck_id, class_id, position,status) SELECT '.$deck_id.',class_id,0,"ok" FROM classes WHERE creator_id='.$user_id.' AND status="perso"';
				$mysqli->query($sql);
			}
		}
		//========================
		include_once ("local_lang.php");

		$GContent=array("texte"=>"","hasPoster"=>0,"hasAudio"=>0,"youtube_id"=>"");
		if($deck_id!=-1)
		{	$result = $mysqli->query('SELECT decks.texte,decks.hasPoster,decks.hasAudio,decks.youtube_id FROM decks WHERE decks.deck_id = ' . $deck_id);
			while ($row = $result->fetch_assoc()) {
				$GContent = $row;
			}
			$result->free();
		}

		$cards=array();
		//recupération des données
		$sql="";
		if($deck_id==-1)
		{
			if(isset($_GET["class_id"])){
				$class_id=(int)$_GET["class_id"];
				$lang_id=(int)$_SESSION["target_lang"];
				$current_tps=time();
				$deadDelay=30*24*60*60;
				$sql="SELECT distinct card_sentence.status as sentence_status,
							lang.lang_code2_2,
							IF(favorite.card_id IS NULL,0,1) as fav,
							card_sentence.sentence,
							verbes.verbe,
							cards.card_id,
							cards.deck_id,
							cards.mot,
							cards.mot_trad,
							cards.hasImage,
							cards.hasAudio,
							cards.alert,
							activite.user_id,
							activite.LastRD,
							activite.OptimalRD,
							activite.puissance FROM cards
				LEFT JOIN (SELECT * FROM `activite` WHERE activite.user_id=".$user_id." AND activite.OptimalRD<".$current_tps." AND activite.OptimalRD+".$deadDelay.">".$current_tps.") AS activite ON activite.card_id=cards.card_id
				LEFT JOIN verbes ON verbes.verbe=cards.mot
				LEFT JOIN (SELECT * from favorite WHERE favorite.user_id=".$user_id.") as favorite ON favorite.card_id=cards.card_id
				LEFT JOIN card_sentence ON card_sentence.card_id=cards.card_id
				LEFT JOIN deck_class ON deck_class.deck_id=cards.deck_id
				LEFT JOIN decks ON decks.deck_id=deck_class.deck_id
				LEFT JOIN lang ON cards.lang_id=lang.lang_id
				WHERE cards.active=1 AND decks.active=1 AND decks.lang_id=".$lang_id." AND deck_class.class_id=".$class_id." AND activite.user_id=".$user_id." ORDER BY cards.position ASC";
				//echo "<script>console.log('".$sql."');</script>";
			}
		 }
		else if($deck_id==-2)//cartes favorite
		{$lang_id=(int)$_SESSION["target_lang"];
			$sql="SELECT distinct card_sentence.status as sentence_status, 1 as fav,lang.lang_code2_2,card_sentence.sentence,verbes.verbe,cards.card_id, cards.deck_id, cards.mot,cards.mot_trad, cards.hasImage, cards.hasAudio,cards.alert, activite.user_id, activite.LastRD, activite.OptimalRD,activite.puissance FROM cards
		LEFT JOIN `favorite` ON cards.card_id=favorite.card_id
		LEFT JOIN (SELECT * FROM `activite` WHERE activite.user_id=".$user_id.") AS activite ON activite.card_id=cards.card_id
		LEFT JOIN verbes ON verbes.verbe=cards.mot
		LEFT JOIN card_sentence ON card_sentence.card_id=cards.card_id
		LEFT JOIN lang ON cards.lang_id=lang.lang_id
		WHERE cards.active=1 AND favorite.user_id=".$user_id." AND cards.lang_id=".$lang_id." ORDER BY favorite.addTime DESC";
		}
		else
		{$sql="SELECT distinct card_sentence.status as sentence_status,IF(favorite.card_id IS NULL,0,1) as fav,lang.lang_code2_2,
		card_sentence.sentence,verbes.verbe,cards.card_id, cards.deck_id, cards.mot,cards.mot_trad,cards.hasImage, cards.hasAudio,cards.alert, activite.user_id, activite.LastRD, activite.OptimalRD,activite.puissance FROM cards
		LEFT JOIN (SELECT * FROM `activite` WHERE activite.user_id=".$user_id.") AS activite ON activite.card_id=cards.card_id
		LEFT JOIN verbes ON verbes.verbe=cards.mot
		LEFT JOIN (SELECT * from favorite WHERE favorite.user_id=".$user_id.") as favorite ON favorite.card_id=cards.card_id
		LEFT JOIN card_sentence ON card_sentence.card_id=cards.card_id
		LEFT JOIN lang ON cards.lang_id=lang.lang_id
		WHERE cards.active=1 AND deck_id=".$deck_id." ORDER BY cards.position ASC";
		}

		$result = $mysqli->query($sql);
		$current_tps=time();
		
		while ($row = $result->fetch_assoc()) {
			
						if($row['OptimalRD']==null || $row['OptimalRD']==$row['LastRD']){$row['puissance']=0;}
						else{
							$OptRD = new DateTime($row['OptimalRD']);
							$OptRD=date_timestamp_get($OptRD);
							$row['OptimalRD']=$OptRD;
							$LstRD = new DateTime($row['LastRD']);
							$LstRD=date_timestamp_get($LstRD);
							$row['LastRD']=$LstRD;
							$row['puissance']=round(max(0,$row['puissance']*($OptRD-$current_tps)/($OptRD-$LstRD)));
						}
						array_push($cards,$row);
		}
		$result->free();
		$droit="";
		if($deck_id==-1)
		{$deck_name=__("Cartes à réviser");
		$creator_id=-1;
		$alertDeck=-1;
		$alert_type=0;
		if(count($cards)==0){header("location:decks.php?categorie=last");exit();}
		}
		else if($deck_id==-2)
		{
		$deck_name=__("Cartes favorites");
		$creator_id=-1;
		$alertDeck=-1;
		$alert_type=0;}
		else
		{$sql="SELECT deck_name,decks.user_id,decks.alertDeck,lang.lang_code2_2 FROM decks left join lang on lang.lang_id=decks.lang_id WHERE decks.deck_id=".$deck_id;
		$result = $mysqli->query($sql);
		$row = $result->fetch_assoc();
		$deck_name=$row["deck_name"];
		$creator_id=$row["user_id"];
		$alertDeck=$row["alertDeck"];
		$lang_code2_2_deck=$row["lang_code2_2"];
		$result->free();
		$sql="SELECT droit FROM user_deck_droit WHERE user_id=".$user_id." AND deck_id=".$deck_id;
		$result = $mysqli->query($sql);
		$row = $result->fetch_assoc();
		$droit=$row['droit'];
		$result->free();
		}

		//regarder si le deck est en coop sur l'une des classe de l'élève
		$deck_status_coop=0;
		$sql="SELECT count(*) as isCoop FROM deck_class LEFT JOIN user_class ON user_class.class_id=deck_class.class_id WHERE user_class.user_id=".$user_id." AND deck_class.deck_id=".$deck_id." AND deck_class.status='coop'";
		$result = $mysqli->query($sql);
		$row = $result->fetch_assoc();
		$deck_status_coop=$row['isCoop'];
		$result->free();
		echo "<script>deck_status_coop=".json_encode($deck_status_coop).";</script>";
		$cardsById=array();
		foreach ($cards as $card) {
			if(!isset($cardsById[$card["card_id"]]))
		    {$cardsById[$card["card_id"]]=$card;
				if($card['sentence']==null){$cardsById[$card["card_id"]]["sentences"]=array();}
				else{$cardsById[$card["card_id"]]["sentences"]=array($card['sentence']);}
				}
			else
				{
				if($card['sentence']!=null){array_push($cardsById[$card["card_id"]]["sentences"],$card['sentence']);}
				}
		}


		if(isset($_GET["game"])){echo "<script>getGame=".json_encode($_GET["game"]).";</script>";}
		else{echo "<script>getGame='';</script>";}
		echo "<script>cardsById=".json_encode($cardsById).";</script>";
		echo "<script>user_id=".json_encode($user_id).";</script>";
		echo "<script>deck_id=".json_encode($deck_id).";</script>";
		echo "<script>alertDeck=".json_encode($alertDeck).";</script>";
		echo "<script>fullUserName='".$first_name." ".$last_name."';</script>";
		echo "<script>user_id=".$user_id.";</script>";
		echo "<script>userType='".$userType."';</script>";
		echo "<script>lang_code2_2_deck='".$lang_code2_2_deck."';</script>";

		echo "<script>droit=".json_encode($droit).";</script>";
		echo "<script>creator_id=".json_encode($creator_id).";</script>";
		echo "<script>deck_name=".json_encode($deck_name).";</script>";
		echo "<script>cards=".json_encode($cards).";console.log(cards);</script>";
		echo "<script>GContent=".json_encode($GContent).";</script>";
		//==============================
?>
<!DOCTYPE html>
<html >
 <head>


    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cartes</title>
    <!-- Bootstrap -->
	  <link href="css/main.css?ver=<?php echo filemtime('css/main.css');?>" rel="stylesheet">
		<link href="css/styleEntete.css?ver=<?php echo filemtime('css/styleEntete.css');?>" rel="stylesheet">
	  <link href="css/deck.css?ver=<?php echo filemtime('css/deck.css');?>" rel="stylesheet">
	  <link href="css/card.css?ver=<?php echo filemtime('css/card.css');?>" rel="stylesheet">
		<link href="css/myStyle.css?ver=<?php echo filemtime('css/myStyle.css');?>" rel="stylesheet">
		<link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16" />
		<link rel="stylesheet" type="text/css" href="css/navStyle.css"/>
		<link rel="stylesheet" type="text/css" href="css/rewardStyle.css"/>
		<script src="js/jquery-3.3.1.min.js"></script>
		<script src='js/cookiesManager.js'></script>
		<script src="js/jquery-ui.js"></script>
		<script src="socket.io/socket.io.js"></script>
		
	<style>
		/*pour les petits ecrans*/
		@media only screen and (max-width: 600px) {
			#navRight{margin:0;padding:10px;position:relative;bottom:35px;right:0;width:100%;z-index:3;display:inline-block;background-color:var(--fond);}
			#navRight:after{content:"";height:85px;background-color:var(--fond);position:absolute;bottom:-85px;width:100%;left:0;}
			.shift250 {
    padding-right: 0px !important;
			}
			.separateurHorizontal{margin-top:5px !important;}
			.diffLabel {
    background-color: var(--fond) !important;}
			#BottomContainerNav{display:inline;flex-direction:column-reverse;}
			.bodyContent{margin-top:60px;width: 100%;}
			#jouerLink{box-shadow: 0 0px 3px grey;position:fixed;bottom:0px;left:0px;right:0px;color:white;text-align:center;z-index:2;background-color:white;height:80px;}
			#jouerLink a{background-color:var(--mycolor2);color:white;text-align:center;padding:10px 30px;width: 90%;
    display: inline-block;
    margin-top: 10px;}
			#XPcontainer{z-index:1;}
			#game_container{padding:10px;}
		}
		/*pour les grands ecrans*/
		@media only screen and (min-width: 600px) {
			#navRight{overflow: auto;position:relative;width:350px;padding-top:10px;z-index:3;
			display:inline-block;vertical-align:top;}
			/*#navRight:before{content:"";box-shadow:1px 0px 2px grey;width:1px;height:50%;margin-top:25%;position:absolute;left:0;}*/
			#deck_name{padding-left:30px;}
		 #navRight .btn{width:80%;margin:5px auto;}
		 .bodyContent{margin-top:0px;}
		 #navbar{margin-top:-60px;}
		 #jouerLink{display:none;}
		}


			audio{filter: drop-shadow(1px 1px 3px);margin:20px;}

			/*.shift250{padding-right:350px;}*/
			/*#navRight{background-color:white;box-shadow:0 0 3px grey;}*/
			.btnEntr{text-align:left;background-color:white; padding:0;color:black;border:1px #e0e0e0 solid;}
			.BtnEntr:hover{box-shadow: 0 5px 5px #00000020;background-color:#f0f0f0;}
			.BtnEntr:hover>img{}
			.BtnEdit,.BtnClass{background-color:var(--mycolor2);text-align:left;}/*#5f8cf6*/
			.BtnEdit:hover,.BtnClass:hover{background-color: var(--mycolor2fonce);}
			.btnVali{background-color:orange;font-size:1.3em;border-bottom: 6px #00000060 solid; text-align:center;}
			.btnVali:hover{background-color:#f18619;}

			td > .flip-container,td > .flip-container >.flipper>*{
				max-width: 30vw;
    		max-height: 30vw;}
			.btn{
					width:100%;
    			vertical-align: middle;
    			min-height: 50px;
					margin-top:5px;
					padding:0;
					border: none;
					box-shadow: 0 2px 5px #00000080;
			}

			.btnGoValidation{
    padding: 10px;
    border-radius: 4px;
    display: inline-block;
		margin:10px;
    width: 200px;
    display: inline-block;
		}
		#navRight .btn{border-radius:2px;position:relative;}
			.labelBtn{display:inline-block;text-align:left;padding-left:30px;vertical-align:middle;}
			.navbarUp{margin-top:-70px;}
			.openMenuUp{margin-top:70px;}
			#indicateurClassMenu{display:none !important;}
			.progressbarDeck{top:0;padding:6px 0;bottom:inherit;width:0%;}
			.selectMoreText{color:grey;margin:30px 0;padding:10px;}
			.progressbarContainer{height:13px;display:none;}
			.card_back_btn{z-index:2;position:absolute;top:20px;left:20px;}
			.card_back_btn a{width:40px;height:40px;background-image:url(img/arrow_left.png);background-size:cover;display:inline-block;}
			.editDeck{vertical-align: middle;
    background-repeat: no-repeat;
    background-image: url(img/stylo.png);
    filter: grayscale(1);
    width: 20px;
    height: 20px;
    margin: 0px;
    display: inline-block;
    background-size: cover;
    cursor: pointer;}
		.selectionOption{display:inline-block;cursor: pointer;background-color:transparent;color:var(--mycolor2);border:none;font-size:1em;padding:0 30px;}
		.selectionOption:hover{color:var(--mycolor2bis);}
		#cardToReview .card{cursor:hand;}
		.exoIcon{width:30px;height:30px;display:inline-block;opacity:0.5;margin-left:10px;vertical-align: middle;}
		.btn:hover .exoIcon{opacity:0.7;}
		.separateurHorizontal{width:80%;margin:auto;color:black;text-align:center;position:relative;}
		.separateurHorizontal:before{z-index: -1;content:"";width:100%;border-top:1px grey solid;position:absolute;top:10px;left:0;}
		.diffLabel{background-color:white;padding:0 20px;}
		</style>
		
</head>

<body class="fond fondDeck">

<?php include "entete.php";?>

<script>
if(readCookie("dys")){
	$('<style>*{ font-family: "OpenDyslexic" !important;</style>').appendTo('head');
}

$(".enteteBtn:not(.enteteCards)").hide();
</script>


<div class="center bodyContent">
	<div class="progressbarContainer"><div class="progressbarDeck"></div></div>
	<div class="card_back_btn"></div>
	<div id="game_container" class="whiteTile shift250"></div>

	<div id='jouerLink'><a href="#navRight"><?php echo __("Jouer");?></a></div>
	<div id='navRight' class="whiteTile">
		<h2 class="decalageTitreDroite"><?php echo __("Jeux");?></h2>
		<h3 class="consigne2 decalageTitreDroite" style="color:grey;font-size:1em;color:#a5a5a5;"><?php echo __("2) Selectionner un type de jeu");?></h3>
		<!--<button class='btn BtnEdit' id='BtnEdit' onclick='editDeck();'><span class='labelBtn'><?php //echo __("Edition");?></span></button>-->
			<!--<button class='btn BtnClass' id='BtnMiseEnBoite' onclick='MiseEnBoite();'><span class='labelBtn'><?php //echo __("Mise en boîte");?></span></button>-->
		<div class="nbreCardsSelectedContainer"><span class="nbreCardsSelected">-</span> <?php echo __("cartes sélectionnées");?></div>
		<div id='BottomContainerNav' style="width:100%;margin: auto;">

			<button class='btn BtnClass' id='BtnQuizEnClass' onclick='quizEnClasse();'><img src="img/exoQuiz.png" class='exoIcon'><span class='labelBtn'><?php echo __("Quiz en classe");?></span></button>


			<button class='btn BtnClass' id='BtnReconstitution' onclick='PlayReconstitution();'><img src="img/exoDialog.png" class='exoIcon'><span class='labelBtn'><?php echo __("Reconstitution du dialogue");?></span></button>
			<!-- <div class="separateurHorizontal"><span class="diffLabel">1</span></div> -->
			<button class='btn BtnEntr' id='BtnReview' onclick='games=["discover"];init_game();'><img src="img/loupe.png" class='exoIcon'><span class='labelBtn'><?php echo __('Découverte');?></span></button>
			<!-- <div class="separateurHorizontal"><span class="diffLabel">2</span></div> -->
			<button class='btn BtnEntr' id='BtnquizMixLetter' onclick='games=["quizMixLetter"];init_game();'><img src="img/exoCrazyLetter.png" class='exoIcon'><span class='labelBtn'><?php echo __('Quiz Lettres folles');?></span></button>
			<button class='btn BtnEntr' id='BtnQCM' onclick='games=["QCMmot2image"];init_game();'><img src="img/exoQCMimg.png" class='exoIcon'><span class='labelBtn'><?php echo __('QCM');?><br><span style="font-size:0.8em;"><?php echo __("Trouver l'image");?></span></span></button>
			<button class='btn BtnEntr' id='BtnQCM2' onclick='games=["QCMimage2mot"];init_game();'><img src="img/exoQCMtext.png" class='exoIcon'><span class='labelBtn'><?php echo __('QCM');?><br><span style="font-size:0.8em;"><?php echo __("Trouver le mot");?></span></span></button>
			<button class='btn BtnEntr' id='BtnDicte' onclick='games=["dictée"];init_game();'><img src="img/exoDictee.png" class='exoIcon'><span class='labelBtn'><?php echo __("Dictée");?></span></button>
			<button class='btn BtnEntr' id='BtnLettre' onclick='games=["bazarLettre"];init_game();'><img src="img/exoMixLetter.png" class='exoIcon'><span class='labelBtn'><?php echo __("Bazar de lettres");?></span></button>
			<!-- <div class="separateurHorizontal"><span class="diffLabel">3</span></div> -->
			<!-- <button class='btn BtnEntr' id='BtnBazarMot' onclick='games=["bazarMot"];init_game();'><div class='exoIcon'></div><span class='labelBtn'><?php echo __("Bazar de mots");?></span></button> -->
			<button class='btn BtnEntr' id='BtngridLetter' onclick='games=["xWord"];init_game();'><img src="img/exoCrossWord.png" class='exoIcon'><span class='labelBtn'><?php echo __("Mots croisés");?></span></button>
			<button class='btn BtnEntr' id='BtnXWord' onclick='games=["gridLetter"];init_game();'><img src="img/exoGrid.png" class='exoIcon'><span class='labelBtn'><?php echo __("Grille de lettres");?></span></button>
			<!-- <div class="separateurHorizontal"><span class="diffLabel">4</span></div> -->
			<button class='btn BtnEntr' id='BtnProno' onclick='games=["prononciation"];init_game();'><img src="img/exoProno.png" class='exoIcon'><span class='labelBtn'><?php echo __("Prononciation");?></span></button>
			<button class='btn BtnEntr' id='BtnVali' onclick='games=["validation"];init_game();'><img src="img/exoGap.png" class='exoIcon'><span class='labelBtn'><?php echo __("Texte à trou");?></span></button>
			<!--<button class='btn btnEntr' id='BtnMeliMelo' onclick='games=["QCMmot2image","QCMimage2mot","dictée","prononciation","bazarMot","bazarLettre"];init_game();'><span class='labelBtn'><?php //echo __("Méli-mélo");?><br><span style="font-size:0.8em;"><?php //echo __("aléatoire");?></span></span></button>-->
			<div class="selectMoreText"><?php echo __("Selectionnez plus de carte pour générer les exercices");?></div>
		</div>
	</div>

	<div class="rewardpage">
		<div class="blackWindow"></div>
		<div class="notificationWindow">
		<?php include "listeTrophe.html" ?>
	</div>
	</div>
<div id="XPcontainer" class="shift250"><div id="XPbarContainer" class="glitter" style="position:relative;"><div class="XPbar"></div><span class="xp_min"></span><span class="xp_num"></span><span class="xp_lvl"></span><span class="xp_max"></span></div></div>
</div>
<audio class="victory" preload="auto">
    <source src="audio/victory.mp3" type="audio/mpeg">
    <source src="audio/victory.ogg" type="audio/ogg">
</audio>
<audio id="audio_coin" src="audio/coin.wav" preload="auto">
<audio id="audio_fail" src="audio/fail.wav" preload="auto">
<?php
include_once 'game_memory.php';
include_once 'games.php';
include_once 'xword.php';
include_once 'letterGrid.php';
include_once 'reconstitution.php';
include_once 'game_reward.php';
?>

<script>
$('.desktop').menuBreaker();
//affichage des boutons
//$("#BtnEdit,.editDeck").hide();
//if(creator_id==user_id || droit=="modif" || droit=="admin" || deck_status_coop>0){$("#BtnEdit,.editDeck").show();}

//$("#BtnQuizEnClass").hide();
//$("#BtnMiseEnBoite").hide();

if(userType!="prof"){
	$("#BtnQuizEnClass").remove();

}

var selected_card=[];
eraseCookie("selected_card");
var selected_card_done=[];
var selected_card_validated=[];
var fausse_lettre=0;
var faux_mot=0;
var pile_glissante=[];
var id_a_travailler_restant=[];
//var selected_card_order=[];
//var selected_card_order2=[];
ini_memory();

//eviter probleme avec espace qui descend la page
window.onkeydown = function(e) {
	//console.log(e.target.tagName);
	//console.log(e.target.id);
  if (e.which == 32 && (e.target.tagName == "INPUT" || e.target.tagName == "TEXTAREA")) {
		//$("#"+e.target.id).val($("#"+e.target.id).val()+" ");
		//e.stopPropagation();
		//e.preventDefault();
  }
};
getNbreCardsPerEx();
if(getGame!=""){PlayReconstitution();}
function editDeck()
{
	location.href="edit_deck.php?deck_id="+deck_id;
}
function quizEnClasse()
{
  window.location='quizProf.php?deck_id='+deck_id;
}
function MiseEnBoite()
{
  window.location='boxProf.php?deck_id='+deck_id;
}

	function toggleAlertDeck(deck_id)
	{
		if($(".alertDeck").hasClass("alertDeckON"))
		{$(".alertDeck").removeClass("alertDeckON").addClass("alertDeckOFF");
		$.getJSON("ajax.php?action=deleteAlertDeck&deck_id="+deck_id, function(results){});
		}
		else {
			alert_comment=prompt("<?php echo __("Quel est le problème avec cette liste ?");?>");
			if(alert_comment!="" && alert_comment!=null){
				$(".alertDeck").addClass("alertDeckON").removeClass("alertDeckOFF");
				$.getJSON("ajax.php?action=addAlertDeck&deck_id="+deck_id+"&alert_comment="+alert_comment, function(results){});
			}
		}
	}

	// $.getJSON("ajax.php?action=getStatsTodayUser", function(result){
	// 	console.log(result.stats);
	// 	nbreMotsEnMemoire=result.nbreMotsEnMemoire;
	// 	$('#objectif').html(nbreMotsEnMemoire);
	// });
	 function addOneWordInMemory(){
	// 	nbreMotsEnMemoire++;
	// 	$('#objectif').html(nbreMotsEnMemoire);
	 }


</script>
<?php
if(isset($_GET["exo_id"]))
{
	$exo_id=(int)$_GET["exo_id"];
	$sql="SELECT name FROM exos WHERE exo_id=".$exo_id;
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	$exo_name=$row["name"];
	$result->free();
	echo '<script>ini_memory();games=["'.$exo_name.'"];init_game();</script>';
}

?>
</body>
</html>
