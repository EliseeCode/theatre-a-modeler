<?php

include_once ("db.php");
session_start();
$explodeURI=explode('/',$_SERVER['REQUEST_URI']);
$_SESSION['url']=end($explodeURI);
$nbreDeckInClass=0;
$nbreDeckToValidate=0;
if(!isset($_SESSION['user_id']) || !isset($_SESSION['active'])){
	header("location:checkLoginCookie.php");
	exit();}
if(!isset($_SESSION["nbreCoins"]) || !isset($_SESSION["avatar_id"])){
	header("location:checkLoginCookie.php");
	exit();
}
//check session

// Makes it easier to read
$user_id = $_SESSION['user_id'];
$userType = $_SESSION['type'];
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$email = $_SESSION['email'];
$active = $_SESSION['active'];
$avatar_id=$_SESSION['avatar_id'];

		$categorie="";
		$class_id="";
		$session_class_id="";
		if(isset($_SESSION["class_id"])){
			$session_class_id=$_SESSION["class_id"];
		}

		if(isset($_GET["class_id"])){
			if(!isset($_GET["categorie"]))
			{$categorie="myClass";}
			$class_id=(int)$_GET["class_id"];
			$_SESSION['class_id']=$class_id;
		}

		if(isset($_GET["categorie"]))
		{
			$categorie=htmlspecialchars($_GET["categorie"]);
			if($categorie=="last")
			{if(isset($_SESSION["class_id"]))
				{	$class_id=(int)$_SESSION["class_id"];
			 		if($class_id==0){$categorie="myDecks";}
			 		else{$categorie="myClass";}
		 		}
				else{$categorie="myDecks";}
			}
		}else {
			$categorie="myDecks";
		}
		//update last connection
		$today=date("Y-m-d");
		$sql="UPDATE users SET LastConnection='".$today."' WHERE users.user_id=".$user_id;
		$mysqli->query($sql);

			if($categorie=="myDecks"){
				$sql="SELECT classes.class_id FROM classes WHERE classes.creator_id=".$user_id." AND classes.status='perso'";
				$result = $mysqli->query($sql);
				$row = $result->fetch_assoc();
				$class_id=(int)$row["class_id"];
				$categorie="myClass";
			}

			if($categorie=="explore"){
				$sql="SELECT classes.class_id FROM classes WHERE classes.status='explore'";
				$result = $mysqli->query($sql);
				$row = $result->fetch_assoc();
				$class_id=(int)$row["class_id"];
				$categorie="explore";
			}

		$_SESSION["class_id"]=$class_id;

		//class_id et catégories sont propres
		//get user's classes info
		$sql="SELECT classes.class_id,classes.class_name,classes.promo,classes.status,user_class.role FROM classes
		LEFT JOIN user_class ON user_class.class_id=classes.class_id
		WHERE classes.active=1 AND user_class.user_id=".$user_id;
		$myClasses=array();
		$listOfClassId=array();
		$result = $mysqli->query($sql);
		
		while ($row = $result->fetch_assoc()) {
		$myClasses[$row['class_id']]=$row;
		array_push($listOfClassId,$row['class_id']);
		}
		$result->free();
		//get DeckNumber and getClassDecks
		if($class_id!="")
		{
			//Check if user is in that class
			if(!(array_search($class_id, $listOfClassId)!==false)){
				$class_id=$listOfClassId[0];
			//	exit();
			}
			//get This ClassInfo
				$sql="SELECT lang.lang_id,lang.lang_code2, lang.lang_name,classes.class_id,classes.class_name, classes.promo, classes.status,classes.code,user_class.role FROM classes
				left join user_class on classes.class_id=user_class.class_id
				left join lang on lang.lang_id=classes.lang_id
				WHERE classes.class_id=".$class_id." AND user_class.user_id=".$user_id;

			$result = $mysqli->query($sql);
			$class_info =$result->fetch_assoc();
			$_SESSION["target_lang"]=$class_info["lang_id"];
			$result->free();

			if($class_info["status"]=="perso" || $class_info["status"]=="explore"){
				$sql="SELECT lang.lang_id,lang.lang_code2,lang.lang_name_Origin FROM user_target_lang LEFT JOIN lang ON lang.lang_id=user_target_lang.lang_id WHERE user_target_lang.user_id=".$user_id." ORDER BY user_target_lang.changed_time DESC LIMIT 1";
			  $result = $mysqli->query($sql);
			  $row = $result->fetch_assoc();
				$_SESSION["target_lang"]=$row["lang_id"];
				$class_info["lang_id"]=$_SESSION["target_lang"];

				$class_info["lang_code2"]=$row["lang_code2"];
				$class_info["lang_name"]=$row["lang_name_Origin"];
				$result->free();
			}

				//getNbre de Deck par classe
			$sql="SELECT COUNT(*) AS Nbre, deck_class.status FROM deck_class
			LEFT JOIN decks ON decks.deck_id=deck_class.deck_id
			WHERE decks.active=1 AND deck_class.class_id=".$class_id." GROUP BY deck_class.status";
			$result = $mysqli->query($sql);
			
			while ($row = $result->fetch_assoc()) {
			if($row['status']=="ok"){$nbreDeckInClass+=$row['Nbre'];}
			else if($row['status']=="coop"){$nbreDeckInClass+=$row['Nbre'];}
			else if($row['status']=="waiting"){$nbreDeckToValidate=$row['Nbre'];}
			}
			$result->free();
		}
		//get for which decks user already liked
		$sql="SELECT deck_id FROM user_deck_like WHERE user_id=".$user_id;
		$myLikes=array();
		$result = $mysqli->query($sql);
		while ($row = $result->fetch_assoc()){array_push($myLikes,$row["deck_id"]);}
		echo "<script>class_info=".json_encode($class_info).";</script>";
		echo "<script>categorie='".$categorie."';console.log('cat:'+categorie);</script>";
		echo "<script>myLikes=".json_encode($myLikes).";</script>";
		echo "<script>target_lang_code2=".json_encode($class_info["lang_code2"]).";</script>";

		$result->free();
		echo "<script>class_id='".$class_id."';</script>";
		echo "<script>userType='".$userType."';</script>";
		echo "<script>myClasses=".json_encode($myClasses).";</script>";
		echo "<script>fullUserName='".$first_name."';</script>";
		echo "<script>user_id=".$user_id.";</script>";
		echo "<script>avatar_id=".$avatar_id.";</script>";
		echo "<script>nbreDeckInClass=".json_encode($nbreDeckInClass).";</script>";
		echo "<script>nbreDeckToValidate=".json_encode($nbreDeckToValidate).";</script>";
		echo "<script>session_class_id=".json_encode($session_class_id).";</script>";
		echo "<script>lang_interface=".json_encode($_SESSION['local_lang']).";</script>";
?>
<!DOCTYPE html>
<html >
 <head>


    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Listes</title>
    <!-- Bootstrap -->
		<!-- <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" /> -->
		<link rel="stylesheet" type="text/css" href="css/navStyle.css?ver=<?php echo filemtime('css/navStyle.css');?>"/>
		<link rel="stylesheet" type="text/css" href="css/DataTables.min.css"/>
	  <link href="css/main.css?ver=<?php echo filemtime('css/main.css');?>" rel="stylesheet">
		<link href="css/styleEntete.css?ver=<?php echo filemtime('css/styleEntete.css');?>" rel="stylesheet">
	  <link href="css/deck.css?ver=<?php echo filemtime('css/deck.css');?>" rel="stylesheet">
	  <link href="css/card.css?ver=<?php echo filemtime('css/card.css');?>" rel="stylesheet">
		<link href="css/myStyle.css?ver=<?php echo filemtime('css/myStyle.css');?>" rel="stylesheet">
		<link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16" />

		<link rel="stylesheet" type="text/css" href="css/typeahead.css?ver=<?php echo filemtime('css/typeahead.css');?>"/>
		<link rel="stylesheet" type="text/css" href="css/selectize.css?ver=<?php echo filemtime('css/selectize.css');?>"/>
		<!--<link rel="stylesheet" type="text/css" href="css/haloween.css"/>-->
		<script src="js/jquery-3.3.1.min.js"></script>
		<script src="js/vue.js"></script>
		<script src="js/Moment.js"></script>
		<script src="js/charts.js"></script>
		<script src="js/cookiesManager.js"></script>
		<script src="js/jquery-ui.js"></script>
		<script src="js/DataTables.min.js"></script>
		<script src="js/typeahead.js?ver=<?php echo filemtime('js/typeahead.js');?>"></script>
		<script src="js/selectize.js"></script>

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

		</style>
</head>

<body class="fond fondDeck">
	<?php include "entete.php";?>
	<?php include "windowClass.php";?>
	<?php include "objectifs.php";?>
	<?php include "missions.php";?>
	<?php include "component/reports.php";?>
	<script>

	if(readCookie("dys")){
		$('<style>*{ font-family: "OpenDyslexic" !important;</style>').appendTo('head');
	}
	//$(".buttonRetourList").hide();
	//$(".buttonHome").hide();
	//$(".buttonMyDecks").hide();
	//$(".buttonListes").hide();
	//$(".subButtonMyClass > a").append(" ("+nbreDeckInClass+")");
  //$(".subButtonStudentDecks > a").append(" ("+nbreDeckToValidate+")");
  //if(nbreDeckToValidate!=0)
	//{
  //  $(".subButtonMyClass > a").addClass("BlinkButton");
  //}

	//$(".buttonListesIn").show();
	//$(".decktop").find(".buttonListesIn").addClass("active");

	$(".enteteBtn:not(.enteteDecks)").hide();
	if(class_info.role!="prof"){$('.enteteRoleProf').remove();}
	langUpdateButton();





	</script>
<style>
.filterTooStrict{color:grey;display:none;}
#filterContainer{float:left;margin-right:30px;width: 260px;}
.myListClass{display:none;}
.myListTag >div,.myListClass >div,.myListLang >div{margin:10px;}
.myListTag > div::before,.myListClass > div::before,.myListLang > div::before	{content: "";border-radius:2px;border:1px solid black;display:inline-block;width:20px;height:20px;margin:-5px 20px -5px 10px;}
.myListTag > .activ::before,.myListClass > .activ::before,.myListLang > .activ::before	{background-image:url(img/check4.png);background-size:contain;}

.SearchListInput{border-radius:6px;
	border:lightgrey 2px solid;
	width:300px;
	-webkit-transition:1s;
	-o-transition:1s;
	transition:1s;
	padding:8px 8px 8px 38px;
	background-color:white;
	}
	.SearchListInputContainer{position:relative;}
	.SearchListInputContainer:after{
	content:"";
	position:absolute;
	left:0;
	width:100%;
	height:40px;
	background:url("img/loupegrise.png");
	background-size:20px 20px;
	background-repeat:no-repeat;
	background-position:left 10px center;
	display: inline-block;
	z-index:9;
	pointer-events: none;
	}
.SearchListInput:focus{width:100%;}

.fame_item{clear:both;margin:0px;padding-right:10px;text-align:left;border-bottom:1px solid lightgrey;display:grid; grid-template-columns: 70px auto auto;width:340px;}
.fame_item span{margin:auto 5px;display:inline-block;vertical-align:middle;}
.fame_item .score{color:grey;font-size:0.8em;}
/*.fame_item .score:after{content:"xp";width:20px;height:20px;display: inline-block;}*/
.fame_item .lvlFame{display:block;color:grey;font-size:0.8em;}
.fame_item .pseudo{display:block;color:var(--black);}
/* .fame_item:nth-child(1){background-color:#ffb600;font-size:1.2em;}

.fame_item:nth-child(2){background-color:#FF5555;font-size:1.2em;}

.fame_item:nth-child(3){background-color:#FF2AAA;font-size:1.2em;} */
.Myfame{border:var(--mycolor2bis) 3px solid;}
.adminIcon{display:none;}

.selectedAvailableDeck{
	webkit-box-shadow: 0px 0px 0px 8px var(--mycolor2bis);
	box-shadow: 0px 0px 0px 8px var(--mycolor2bis);
	border-radius:0;
	/*webkit-box-shadow: 0px 0px 15px grey;
	box-shadow:  0px 0px 15px grey;*/
}
.selectedAvailableDeck:after{content:"";position:absolute;top:0;left:0;background-image:url(img/check2.png);background-repeat:no-repeat;background-position:center center;background-size:40px 40px;width:100px;height:100px;display:block;opacity:1;}
.lang_item{padding:10px; margin:20px;display:inline-block;box-shadow:0 0 3px grey;background-color:white;border-radius:10px;}
#import_Deck_vierge:hover{filter:none;}
.tt-hint{background-image:none !important;}
.tt-menu{width:300px;border-radius:10px;margin-top:10px;}
.category-name{padding:8px;margin:5px;background-color:#dadada;border-radius:5px;}
/*#list_deck{
	padding: 0;
  margin: 0;
  list-style: none;
	text-align:center;
  display: -webkit-box;
  display: -moz-box;
  display: -ms-flexbox;
  display: -webkit-flex;
  display: flex;

  -webkit-flex-flow: row wrap;
  justify-content: flex-start;}*/
	#import_Deck{order:-99;}
	#deck_fav{order:-99}
	#new_Deck{order:-99;}
	/*#deck_oublie{grid-area: 3/1/3/-1;}*/

	.explicationContainer{
		grid-area: 1/1/1/-1;
		max-width:100%;
	}
	.whiteTile{background-color:white;
	padding:0 20px 20px 20px;
	border-radius:10px;
	/*border:#ccc 1px solid;*/
	margin: 10px 0;
	position: relative;
	color:grey;
	width:750px;
	max-width:100%;
	box-shadow:0 10px 10px #00000030;
	text-align:left;}
	.Shinytile:before {
	content: " ";
	position: absolute;
	z-index: 1;
	top: -5px;
	left: -5px;
	right: -5px;
	bottom: -5px;
	-webkit-animation: shinyDeck 2s ease Infinite;
	        animation: shinyDeck 2s ease Infinite;
	border-color:var(--mycolor2bis);
	border-radius:16px;
	overflow:visible;
	pointer-events: none;
	}
	#listsContainer{
		display:inline-grid;
		grid-auto-flow:row;
		grid-gap:5px 0px;
		grid-template-columns: repeat(auto-fit,340px);
		/*grid-template-columns : repeat(auto-fit,380px);*/
		grid-template-rows : auto;
		width:-moz-available;
		max-width: min(100%,1200px);
		margin-top: 0;
	}
	.plusImport{width:30px;height:30px;background-color:var(--mycolor2);color:white;border-radius:50%;font-size:28px;line-height:28px;display:inline-block;}
	#bodyContentDeck{padding-bottom:100px;}
	.titleIcon{margin-right:30px;width:50px;margin:auto;vertical-align:middle;}
	.titleTile{padding-left:30px;display:inline-block;color:black;}
	.persoFlagContainer{min-width:200px;}
	.persoFlagContainer .tinyFlag{display:inline-block;margin:5px 10px;box-shadow:0 0 5px #00000030;}
	.persoFlagContainer .tinyFlag:hover{box-shadow:0 0 0 5px var(--mycolor2bis);}
	.more_lang_flag_perso{color:var(--mycolor2);font-size:0.4em;}
	.more_lang_flag_perso:hover{color:var(--mycolor2bis);}
	.OptionsDeckContainer{text-align:left;z-index:999;background-color:white;min-width:150px;min-height:30px;position:absolute;box-shadow:0 0 3px #00000030;padding:0 4px; border-radius:10px;display: flex; flex-direction: column;border:3px grey solid;}
	.OptionDeckItem{border-bottom:thin lightgrey solid;display:block;vertical-align:left;color:black;padding:5px;}
	.OptionDeckItem:hover{background-color:#f0f0f0;}
	.tileDroite{text-align:left;position:relative;}
	@media screen and (max-width: 800px) {
	 #bodyContentDeck{margin:100px auto;max-width:1700px;display: grid; grid-gap: 0px; grid-template-columns: auto 0px; grid-template-rows: auto;width:100%;justify-items: center;}
	 .colDroiteDeck{display:none;}
	}
	@media screen and (min-width: 800px) {
		#bodyContentDeck{margin:100px auto;max-width:1700px;display: grid; grid-gap: 0px; grid-template-columns: auto 400px; grid-template-rows: auto;width:100%;justify-items: center;}
		.colDroiteDeck{display:flex;flex-direction:column;}
	}
	.fame_item_more{display:none;}
	.toogleFameItem{color:var(--mycolor2);}
	.avatarFame{display:inline-block;width:70px;height:50px;}
	.avatarID{display:inline-block;width:140px;height:120px;}
</style>
<div id="superDeckContainer" style="display:flex;overflow:hidden;">
	<!-- <div id='tiretteFilter' onclick='$("#filterContainer").removeClass("filtreHidden");' style="">
		<img src="img/filter.png" width="20px" style="margin:auto;opacity:0.7;">
	</div>
	<div id="filterContainer" class="filtreHidden">

		<img src="img/close.png" class="closeWindowIcon" style="margin-top:60px;" onclick='$("#filterContainer").addClass("filtreHidden");'>
		<input type="text" class='SearchListInput' onkeyup="MAJ_list_deck();" placeholder="métier, ..." style="width: 165px;vertical-align:middle;margin-left: 15px;float:left;">
		<br>
		<div class="myListTag" >
			<h4><?php echo __("Thèmes");?></h4>
		</div>
		<div class="myListClass" >
			<h4><?php echo __("Classes");?></h4>
		</div>
		<div class="myListLang" >
			<h4><?php echo __("Langues");?></h4>
		</div>
	<!--<div style="margin:40px 0;"><a href='decks.php?categorie=myDecks' style="color:var(--mycolor2);"><?php //echo __("Afficher mes listes");?></a></div>
	</div> -->

	<div class="center bodyContent" id="bodyContentDeck">

		<div id="listsContainer" class="decalageTitreDroite sortableDeck" style="grid-area: 1 / 1 / 1 / 2;">
			<!-- <div class="block-titre">
					<h1 class="titreList hideBigScreen" style="position:relative;"></h1>
			</div> -->

				<div style="grid-area: 1 / 1 / 2 / -1;">
					<h2 style=""><?php echo __("Listes de vocabulaire");?></h2>
					<div class="SearchListInputContainer">
						<input type="text" class='SearchListInput decksBoxSearch typeahead' placeholder="<?php echo __("métier, ...");?>" style="vertical-align:middle;">
					</div>
					<div class="filterTooStrict"><?php echo __("Aucune liste n'a été trouvé");?></div>
				</div>

			<div style="grid-area: 2/1/3/-1;" class="explicationContainer"></div>
	  </div>
		<div class="colDroiteDeck" style="grid-area: 1 / 2 / 1 / 3;width:380px;">
			<!-- <div class="tileDroite whiteTile" id="progressTile">
				<h3 style="padding-top:20px;">
					<span class="xp_lvl_deck"></span>
					<span style="float:right;color:var(--mycolor2);cursor:hand;text-transform:uppercase;"  onclick="windowClass('myStats');return false;"><?php echo __("Plus");?></span>
				</h3>
				<div style="display:flex">
					<div class="avatarContainer" style="width:150px;">
						<div class="avatar avatarID scaleOver" onclick="windowClass('avatar');"></div>
						<?php if($_SESSION["premiumAvatar"]){echo "<div>*</div>";}?>
					</div>
					<div style="width:300px;">
						<div style="color:grey;">Gagne <span class="xp_max_deck"></span>xp</div>
						<div id="XPbarContainer" class="progressbar glitter" style="position:relative;margin:0;width:100%;margin:10px 0;border-radius:12px;">
							<div class="XPbar progressbar_fluid" style="border-radius:12px;"></div>
						</div>
						<span class="xpBilan"></span>
					</div>
				</div>
			</div> -->

			<div class="tileDroite whiteTile" id="objectifTile">
				<h3 style="padding-top:20px;">
					<span class=""><?php echo __("Objectif Hebdomadaire");?></span>
					<!--<span style="float:right;color:var(--mycolor2);cursor:hand;text-transform:uppercase;"  onclick="showObjectifCreation();return false;"><?php echo __("Consulter");?></span>-->
				</h3>
				<div>

					<div class="ObjectifContainer" style="">
						<div class="objectifScore" ></div>

						<div class="objectifUpdate" style="display:none;position:relative;">
							<hr>
							<div class="objectifShowEdition" style="position:relative;display:none;">
								<select class="objectifFormQuantity">
									<option value="0" selected>Aucun objectif</option>
									<option value="25" >Facile (25 exercices/semaine)</option>
									<option value="150" >Standard (150 exercices/semaine)</option>
									<option value="400" >Ambicieux (400 exercices/semaines)</option>
									<option value="700" >Diabolique (700 exercices/semaine)</option>
								</select>
								Commence le
								<select class="objectifFormDay_num">
									<option value="2" selected>Lundi</option>
									<option value="3" >Mardi</option>
									<option value="4" >Mercredi</option>
									<option value="5" >Jeudi</option>
									<option value="6" >Vendredi</option>
									<option value="7" >Samedi</option>
									<option value="1" >Dimanche</option>
								</select>
								<button class="btnStd1" onclick="changeObjectifClass();">Valider</button>
							</div>
							<div class="iconContainerTopRight">
								<span class='edit_icon miniIcon' onclick="$('.objectifShowEdition').toggle();"></span>
							</div>
						</div>
					</div>
				</div>
			</div>



			<div class="tileDroite whiteTile" id="hallOfFameContainer">
				<h3 style="text-align:left;">
					<span><?php echo __("Tableau d'honneur");?></span>
					<br>
					<span style="font-size:0.8em;color:grey;"><?php echo __("Nombre d'xp sur les 7 derniers jours.");?></span>
				</h3>
				<div id="hallOfFame">
				</div>
				<div style="float:left;cursor:hand;text-transform:uppercase;">
					<a href="#" onclick="windowClass('friends');return false;" style="color:var(--mycolor2);"><?php echo __("Suivre mes amis");?></a>
				</div>
				<div class="toogleFameItemContainer" style="float:right;cursor:hand;text-transform:uppercase;">
					<a id='moreFameItem' class='toogleFameItem' href='#' onclick='$(this).hide();$("#lessFameItem").show();$(".fame_item_more").removeClass("fame_item_more");'><?php echo __("Voir plus");?></a>
					<a id='lessFameItem' class='toogleFameItem' href='#' style="display:none;" onclick='$(this).hide();$("#moreFameItem").show();$(".fame_item:gt(5)").addClass("fame_item_more");'><?php echo __("Voir moins");?></a>
				</div>
			</div>
	</div>
	</div>

	<div id="app-2">
  <span v-bind:title="message" v-show="show">
    Hover your mouse over me for a few seconds
    to see my dynamically bound title!
  </span>
</div>

<div class="button--help" onclick="showTutorial();"><div>?</div></div>

</div>
<?php include "component/avatar.php"; ?>
<script>
var app2 = new Vue({
  el: '#app-2',
  data: {
    message: 'You loaded this page on ' + new Date().toLocaleString(),
		show:false

  }
});


$("#openMenu .left").hide();
if(class_info.status=="perso"){class_info.class_name='<?php echo __("Ma bibliothèque");?>';}
if(class_info.status=="explore"){class_info.class_name='<?php echo __("Explorer");?>';}


$(".class_name").addClass("titreList").html(class_info.class_name);
// $(".avatarID").html(`
// 	<img src='avatar/avatar_`+avatar_id+`.png' class='avatar_img avatar_XL'>
// `);

tags2id=[];
id2tags=[];
class2id=[];
lang2id=[];
id2class=[];
var listeDesTags=[];
var listeDesNoms=[];


getList(categorie);




function getHallOfFame(class_id)
{
	$.getJSON("ajax.php?action=getHallOfFame&class_id="+class_id, function(data)
	{
		console.log("data Hall of fame",data);
		$("#hallOfFameContainer").append("");
		lastScore=-1;
		rank=0;
		for(k_dataHofF in data)
		{

			pseudo=data[k_dataHofF].first_name+" "+data[k_dataHofF].last_name;
			pseudo=capitalizeFirstLetterEachWordSplitBySpace(pseudo);
			score=data[k_dataHofF].score;
			lvl=getLvlFromXp(data[k_dataHofF].nbreCoins);
			//lvlAvatar=Math.round(lvl/2);
			fame_user_id=data[k_dataHofF].user_id;
			friend_avatar_id=data[k_dataHofF].avatar_id;
			if(score!=lastScore){
				rank++;lastScore=score;
			}
		$("#hallOfFame").append(`<div class='fame_item fame_item_more fame_item_`+fame_user_id+`' >
			<div class="avatar scaleOver">
				<img src='avatar/avatar_`+friend_avatar_id+`_XS.png' class='avatar_S avatarFame'>
			</div>
			<div style='display:inline-block;text-align:left;display: flex; flex-direction: column;margin:auto 10px;'>
				<span class='pseudo'>`+pseudo+`</span>
				<span class='lvlFame'><?php echo __("Niveau");?> `+lvl+`</span>
			</div>
			<span class='score' style="justify-self: end;">`+score+` xp</span>

		</div>`);
		}
		if(data.length<=5){$(".toogleFameItemContainer").hide();}
		$('.fame_item:lt(5)').removeClass("fame_item_more");
		console.log(data.length);
		if(data.length==0){$("#hallOfFameContainer").remove();}
		$(".fame_item_"+user_id).addClass("Myfame");
	});
}

function resetNewDeck()
{
	//$("#new_Deck").remove();
	$("#import_Deck").remove();
	$("#new_Deck").remove();
	//$("#deckToImport").remove();
	//if(class_id!=""){urlEdit="edit_deck.php?deck_id=0&class_id="+class_id;}
	//else{urlEdit="edit_deck.php?deck_id=0";}
	//$("#top_deck").prepend("<div id='new_Deck' onclick='location.href=\""+urlEdit+"\"' class='deck'>"
	//+"<div id='addSign' style='margin:0;line-height:100px;'><?php //echo __("Nouveau");?></div></div>");
	if(class_info.status!="explore"){
		$("#listsContainer").append(`<div id='import_Deck' onclick='windowClass("import")' class='deck' style='display:inline-flex;flex-direction:column;'>
		<div style='margin:auto;'><div class='plusImport'>+</div><br><?php echo __("Importer une liste existante");?></div></div>`);
	}
	$("#listsContainer").append(`<div id='new_Deck' onclick='location.href="edit_deck.php?deck_id=0"' class='deck' style='display:inline-flex;flex-direction:column;'>
	<div style='margin:auto;'><div class='plusImport'>+</div><br><?php echo __("Créer une liste");?></div></div>`);
	$.getJSON("ajax.php?action=getNbreFav&class_id="+class_id, function(result)
	{
		console.log(result);
		if(result.nbreFav>0)
		 {
			 $("#listsContainer").append(`
				 <div id="deck_fav" onclick='location.href="cards.php?deck_id=-2"' class='deck' style='display:inline-flex;flex-direction:column;'>
			 	 <div style='margin:auto;line-height:normal;color:var(--mycolor2);'><img src="img/star.png" style="width:38px;"><br><span class='nbreMots'>`+result.nbreFav+` </span><?php echo __("cartes favorites");?>
				 </div></div>
				 `);
			}
	});
	//$("#top_deck").append("<div id='deckToImport'></div>");
	if(class_info.role=="eleve"){$('#import_Deck').hide();}
}
function showPersoFlag()
{
	$.getJSON("ajax.php?action=getUserTargetLang", function(result){
		for(k in result)
		{
			lang_code2=result[k].lang_code2;
			lang_id=result[k].lang_id;
			lang_name=result[k].lang_name;
			$(".persoFlagContainer").append(`<li onclick='changeLanguage(`+lang_id+`);'><a href="#"><span class='lang_flag_perso lang_flag_`+lang_id+` tinyFlag flag_`+lang_code2+`'></span>`+lang_name+`</a></li>`);
			if(k==0){$(".TinyFlagPerso").addClass('flag_'+lang_code2);}
		}
		$(".persoFlagContainer").append(`<li><a href='#' title='<?php echo __("plus de langues");?>' onclick='getMoreLanguagePerso();return false;' class='more_lang_flag_perso'><?php echo __("plus");?></a></li>`);
	});
}
function getMoreLanguagePerso(){

  $.getJSON("ajax.php?action=getTargetLang", function(result){
    for(k in result)
    {
      lang_code2=result[k].lang_code2;
      lang_id=result[k].lang_id;
      lang_name=result[k].lang_name;
      if($('.lang_flag_'+lang_id).length==0){
      $(".persoFlagContainer").append(`<li onclick='changeLanguage(`+lang_id+`);'><a href="#"><span class='lang_flag_`+lang_id+` tinyFlag flag_`+lang_code2+`'></span>`+lang_name+`</a></li>`);
      }
    }
		$(".more_lang_flag_perso").remove();
	});
}
function getList(categorie)
{
	console.log(categorie);
	$(".deck:not(#deck_oublie,#deck_fav)").remove();
	//$("#bodyContentDeck").before('<div id="select_tag_deck"><select class="select_tag" style="display:inline-block;" onchange="MAJ_list_deck_mobile();"><option class="tag_opt_deck" value="all" selected><?php echo __("Aucun filtre Thèmatique");?></option></select></div>');

	$('#consigneList').html("<h3 style='color:grey;'><?php echo __("Choisissez une liste pour jouer avec.");?></h3>");
	$('.ClassFlag').html(`<span title='`+class_info.lang_name+`' class='tinyFlag flag_`+class_info.lang_code2+`'></span>`);
	$('.TitleClassMenu').html(`<div style="display:inline-flex;flex-direction:column;vertical-align:middle;"><span style='margin-left:15px;'>`+class_info.class_name+`</span><span style='margin-left:15px;color:grey;font-size:0.7em;'>`+class_info.promo+`</span></div>`);
//<img src='img/icon_`+class_info.role+`.png' style='width:30px;vertical-align: middle;'>
	if(class_info.status=="perso"){
		$(".settingClass,.codeEntete").hide();
		$("#tiretteHallOfFame").hide();
		$(".myListClass").show();//filtre par classes
		//$('#titreList').html("<img src='img/icon_perso.png' style='width:30px;vertical-align: middle;'><span style='margin-left:15px;'><?php echo __("Ma bibliothèque");?></span>");
		$('.ClassFlag').html(`<span class="langSelectPerso">
				<span class='tinyFlag TinyFlagPerso'></span>
				<img src='img/arrow_down.png' width="20px" style="vertical-align:middle;">
			</span>
			`);
			$('.ClassFlag').after(`<ul class="persoFlagContainer submenu"></ul>`);
			// <span style='margin-left:15px;'><?php echo __("Ma bibliothèque");?></span>
			//<img src='img/icon_perso.png' style='width:30px;vertical-align: middle;'>
		showPersoFlag();
	}
	else if(class_info.status=="explore"){
		$(".settingClass,.codeEntete").hide();
		$("#tiretteHallOfFame").hide();
		$(".myListClass").show();//filtre par classes
		//$('#titreList').html("<img src='img/icon_perso.png' style='width:30px;vertical-align: middle;'><span style='margin-left:15px;'><?php echo __("Ma bibliothèque");?></span>");
		$('.titreList').html(`<span class="langSelectPerso">
				<span class='tinyFlag TinyFlagPerso'></span>
				<img src='img/arrow_down.png' width="15px">
				<div class="persoFlagContainer"></div>
			</span>
			<span style='margin-left:15px;'><?php echo __("Explorer");?></span>`);
			//<img src='img/icon_perso.png' style='width:30px;vertical-align: middle;'>
		showPersoFlag();
	}
	else{showCodeInEntete();
	}
	getHallOfFame(class_info.class_id);



	$(".buttonListesIn").addClass("active");
	//update categorie pour
  //$.getJSON("ajax.php?action=changeCategorie&categorie="+categorie, function(result){});
	//url="ajax.php?action=getDecks";
	//if(categorie=="myDecks"){url="ajax.php?action=getMyDecks";}
	//if(categorie=="myClass"){
		var url="ajax.php?action=getClassDecks&class_id="+class_id;
	//}
	//else if(categorie==""){url="ajax.php?action=getDecks";}

		$.getJSON(url, function(decks_data)
		{
			allDecksData=decks_data;
			console.log("Decks Datas",url,decks_data);
			resetNewDeck();
			// if(class_info.role=="prof"){resetNewDeck();}
			// else if(class_info.status=="perso"){resetNewDeck();}

			drawDecks(decks_data,"#listsContainer");

			// Constructing the suggestion engine
			var listeDesTagsB = new Bloodhound({
					datumTokenizer: Bloodhound.tokenizers.whitespace,
					queryTokenizer: Bloodhound.tokenizers.whitespace,
					local: listeDesTags
			});
			var listeDesNomsB = new Bloodhound({
					//datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
					datumTokenizer: Bloodhound.tokenizers.whitespace,
					queryTokenizer: Bloodhound.tokenizers.whitespace,
					local: listeDesNoms
			});

			// Initializing the typeahead
			$('.decksBoxSearch').typeahead({
					hint: true,
					highlight: true, /* Enable substring highlighting */
					minLength: 0 /* Specify minimum characters required for showing suggestions */
			},
			{
					name: 'Noms',
					source: listeDesNomsB,
					templates:{
						header: '<h3 class="category-name"><?php echo __("Listes de vocabulaire");?></h3>',
					}
			},{
					name: 'Attributs',
					source: listeDesTagsB,
					templates:{
						header: '<h3 class="category-name"><?php echo __("Thèmes");?></h3>'
					}
			});
			$('.decksBoxSearch').on("input",filterDecks);
			$('.decksBoxSearch').bind('typeahead:selected', function(obj, datum, name) {
			});
			//Evenement au click sur un deck, redirection vers ce deck.
			/*$("#list_deck > .deck").on("click",function(){
				deck_id=$(this).attr('id');
				deck_id=deck_id.substr(5,deck_id.length);
				console.log(deck_id);
					if(!isNaN(deck_id)){location.href="cards.php?deck_id="+deck_id;}
			});*/
			$("#list_deck > .classDeck").find(".close_icon").show();
			//SI aucune liste trouver, faire clignoter new deck et import deck.

			if($('.deck:not(#deck_oublie,#import_Deck):visible').length<10){$("#tiretteFilter,#select_tag_deck").hide();}
			if($('.deck:not(#deck_oublie,#import_Deck,#new_Deck):visible').length==0 && class_info.status!="explore"){
				//$('#new_Deck').addClass("shinyDeck");
				$('#import_Deck').addClass("shinyDeck");
				$(".explicationContainer").append(
 		 		`<div class="whiteTile">
 					<img src="img/stylo.png" class="titleIcon"><h3 class="titleTile"><?php echo __("Ajouter des listes de vocabulaire");?></h3>
 					<div>
 					 	<div style="margin:auto;">
 							<p><?php echo __("La classe ");?>`+class_info.class_name+`<?php echo __(" n'a aucune liste.");?></p>
 							<p><?php echo __("Vous pouvez créer vos propres listes ou bien importer des listes déjà créées parmi les listes publiques d'ExoLingo ou bien celles que vous avez déjà créées");?></p>
							<!--<div style="text-align:right;"><button onClick='windowClass("import");' class="btnStd1 ripple" style="width:auto;margin:10px;"><?php echo __("Importer une liste existante");?></button><button class="btnStd1 ripple" style="width:auto;margin:10px;" onClick='location.href="edit_deck.php?deck_id=-1;"'><?php echo __("Créer une liste");?></button></div>-->
 						</div>
 					</div>
 				</div>`);
				//if(class_info.role=="prof"||class_info.role=="perso"){showAvailableListtoImport();}
			}else{$('#import_Deck').removeClass("shinyDeck");$('#new_Deck').removeClass("shinyDeck");}
		});
}
function showOptionsDeck(deck_id,destination)
{
	droit="";
	for(rg in decks)
	{
		if(decks[rg]["deck_id"]==deck_id)
		{
		droit=decks[rg]["droit"];
		}
	}

	if($('#deck_'+deck_id).length>0){
		$(".OptionsDeckContainer").remove();
		$(destination).append(`<div class="OptionsDeckContainer"></div>`);
		var offsetTop=$('#deck_'+deck_id).position().top+10;
		var offsetLeft=$('#deck_'+deck_id).position().left+$('#deck_'+deck_id).innerWidth()-270;

		$('.OptionsDeckContainer').css({
			top:offsetTop+"px",
			left:offsetLeft+"px"
		});
		//Consulter
			$('.OptionsDeckContainer').append(`<a class='OptionDeckItem' href='cards.php?deck_id=`+deck_id+`'><span class='jumelle_icon miniIcon'></span><span><?php echo __("Consulter la liste");?></span></a>`);
		//edit
		if(droit=="admin"||droit=="modif"){
			$('.OptionsDeckContainer').append(`<a class='OptionDeckItem' href='edit_deck.php?deck_id=`+deck_id+`'><span class='edit_icon miniIcon'></span><span><?php echo __("Editer la liste");?></span></a>`);
		}
		//link
			$('.OptionsDeckContainer').append(`<a class='OptionDeckItem' href='#' onclick='copyLink(`+deck_id+`);'><span class='link_icon miniIcon'></span><span><?php echo __("Copier le lien vers cette liste");?></span></a>`);
		//Move
		if(class_id!=""){
				if(class_info.role=="prof" && destination=="#listsContainer"){
					$('.OptionsDeckContainer').append(`<a class='OptionDeckItem' href='#' onclick='moveDown(`+deck_id+`);'><span class='moveD_icon miniIcon'></span><span><?php echo __("Déplacer la liste en bas");?></span></a>`);
					$('.OptionsDeckContainer').append(`<a class='OptionDeckItem' href='#' onclick='moveUp(`+deck_id+`);'><span class='moveU_icon miniIcon'></span><span><?php echo __("Déplacer la liste en haut");?></span></a>`);
					$('.OptionsDeckContainer').append(`<a class='OptionDeckItem' href='quizProf.php?deck_id=`+deck_id+`;'><span class='quiz_icon miniIcon'></span><span><?php echo __("Faire un quiz en classe");?></span></a>`);
				}
			}
		//Copier
		if(destination=="#listsContainer"){
			$('.OptionsDeckContainer').append(`<a class='OptionDeckItem' href='#' onclick='duplik(`+deck_id+`);'><span class='copy_icon miniIcon'></span><span><?php echo __("Dupliquer la liste");?></span></a>`);
		}
		//Remove from class
		if(class_id!="" && destination=="#listsContainer"){
			if(class_info.role=="prof" || class_info.role=="perso"){
				$('.OptionsDeckContainer').append(`<a class='OptionDeckItem' href='#' onclick='removeDeck(`+deck_id+`);'><span class='close_icon miniIcon'></span><span><?php echo __("Retirer la liste de la classe");?></span></a>`);
			}
		}
		//Delete
		if(droit=="admin" && destination=="#listsContainer"){
			$('.OptionsDeckContainer').append(`<a class='OptionDeckItem' href='#' onclick='deleteDeck(`+deck_id+`);'><span class='delete_icon miniIcon'></span><span style="color:red;"><?php echo __("Supprimer la liste");?></span></a>`);
		}
		//Translate
		if(user_id==7){
			$('.OptionsDeckContainer').append(`<a class='OptionDeckItem' href='#' onclick='translateDeckMenu(`+deck_id+`);'><span class='translate_icon miniIcon'></span><span><?php echo __("Traduire la liste");?></span></a>`);
		}
	}
	$("body").off("click");
	$("body, .fenetreClaire ").on('click', function(event) {
		$(".OptionsDeckContainer").remove();
	  // var isClickInside = $(".OptionsDeckContainer").find(event.target).length;
	  // if (!isClickInside) {
	  //   $(".OptionsDeckContainer").remove();
	  // }
	});
}



function drawDecks(decks_data,destination)
{
	decks=decks_data.decks;
	console.log(decks_data);
	for(rg in decks)
	{
		deck_status=decks[rg]["deck_status"];
		creator_id=decks[rg]["user_id"];
		deck_id=decks[rg]["deck_id"];
		nbreMots=decks[rg]["nbreMots"];
		deck_class_status=decks[rg]["deck_class_status"];
		creator_type=decks[rg]["creator_type"];
		toSubmit=decks[rg]["toSubmit"];
		likes=decks[rg]["likes"];
		droit=decks[rg]["droit"];
		lang=decks[rg]["lang"];
		position=decks[rg]["position"];
		royalties=decks[rg]["royalties"];
		nbreKnown=0;
		creatorIcon="";
		tag_name=decks[rg]["tag_name"];
		class_name=decks[rg]["class_name"];
		deck_name=decks[rg]["deck_name"];
		creatorName="<?php echo __("par");?> "+toTitleCase(decks[rg]["first_name"]+" "+decks[rg]["last_name"]);
		if(creatorName=="<?php echo __("par");?> Null Null"){creatorName="";}
		//on remplis les listes:
		if(listeDesTags.indexOf(tag_name)==-1){listeDesTags.push(tag_name);}
		if(listeDesNoms.indexOf(deck_name)==-1){listeDesNoms.push(deck_name);}
		//ajout des tags
		if(tags2id[tag_name]==null){tags2id[tag_name]=[deck_id];}
		else{tags2id[tag_name].push(deck_id);}
		if(creator_id==user_id){
			creatorName="";
			//if(tags2id["Mes listes"]==null){tags2id["Mes listes"]=[deck_id];}
			//else{tags2id["Mes listes"].push(deck_id);}
		}
		//ajout des class
		if(class2id[class_name]==null){class2id[class_name]=[deck_id];}
		else{class2id[class_name].push(deck_id);}
		//ajout des lang
		if(lang2id[lang]==null){lang2id[lang]=[deck_id];}
		else{lang2id[lang].push(deck_id);}
		//ajouter le deck
		classe_creator=decks[rg]["user_classe"];
		//console.log(classe_creator);
		if(!$("#deck_"+deck_id).length){
			$(destination).append(`<div id='deck_`+deck_id+`' style='order:`+position+`;' data-position='`+position+`' class='deck' onclick='location.href="cards.php?deck_id=`+deck_id+`";'>
			<div id='img_deck_`+deck_id+`' class='img_deck'></div>
			<div class='deckIconContainer' onclick='event.stopPropagation();'>
			<span class='setting_icon' title='<?php echo __("Paramètres");?>' onclick='showOptionsDeck(`+deck_id+`,"`+destination+`");' style='right:40px;top:10px;'></span>
			</div>
			<div class='infoDeck'>
			<span class='deck_name'>`+deck_name+` </span>
			<a class='coopDeck' href='edit_deck.php?deck_id=`+deck_id+`' title='<?php echo __("Editer la liste");?>' style='right:40px;top:3px;'><?php echo __("Créer en coopération");?></a>
			<a class='refuser_icon' onclick='refuser(`+deck_id+`)' href='#' title='<?php echo __("Refuser la liste");?>' style='right:40px;top:3px;'><?php echo __("Rejeter");?></a>
			<a class='accepter_icon' onclick='accepter(`+deck_id+`)' href='#' title='<?php echo __("Valider la liste et l'ajouter à la classe");?>' style='right:40px;top:3px;'><?php echo __("Valider");?></a>
			<span class='comment'></span>
			<div class='bottomDeckContainer'>
			<div class='like_container' title='`+likes+` <?php echo __("personnes aiment cette liste");?>'><span class='toggleLikes scaleOver' onclick='toogleLike(`+deck_id+`);'></span><span class='nbreLikes'>`+likes+`</span></div>
			<div class='royalties_container scaleOver' style='display:none;' title='<?php echo __('Cette liste à récupéré')?>' `+royalties+` <?php echo __("pièces de royalties.");?>'><span class='royalties'></span><span class='nbreRoyalties'>`+royalties+`</span></div>
			<span class='creatorName'>`+creatorName+`</span>
			</div>
			</div>
			<div style='float:left;clear: both;' class='adminIcon' onclick='event.stopPropagation();'>
			<a class='public_icon miniIcon scaleOver' onclick='toggleDeckStatus("public",`+deck_id+`);$(this).toggleClass("grayscale");return false;' href='#' title='Liste public' style='right:40px;top:3px;'></a>
			<a class=\'premium_icon miniIcon scaleOver\' onclick='toggleDeckStatus("premium",`+deck_id+`);$(this).toggleClass("grayscale");return false;' href='#'  title='Liste premium' style='right:40px;top:3px;'></a></div>
			</div>`);

			//status
			//coop
			if(deck_class_status!="coop"){$("#deck_"+deck_id).find(".coopDeck").hide();}
			else{$("#deck_"+deck_id).css('order','-1');}
			//submit
			if(droit!="admin" || deck_class_status!=null){$("#deck_"+deck_id).find('.submitDeck').hide();}
			//waiting
			if(deck_class_status!="waiting"){//building ou ok
				$("#deck_"+deck_id).addClass("classDeck");
				$("#deck_"+deck_id).find(".refuser_icon, .accepter_icon").hide();
			}
			else{
					if(class_info.role=="eleve" && creator_id!=user_id){$("#deck_"+deck_id).remove();}
					if(class_info.role=="eleve" && creator_id==user_id){
						$("#deck_"+deck_id).find(".refuser_icon, .accepter_icon").hide();
						$("#deck_"+deck_id).find(".comment").html("<span style='color:orange;float:right;font-size:0.8em;'><?php echo __("En attente de validation");?></span>");
						}
			}
			//royalties
			if(creator_id==user_id){$("#deck_"+deck_id).find('.royalties_container').show();}

			//icons
			//likes
			if(myLikes.indexOf(deck_id)==-1){$("#deck_"+deck_id).find(".toggleLikes").addClass("notLiked");}


			//adminIcon
			if(deck_status!="public"){
				$("#deck_"+deck_id).find('.public_icon').addClass('grayscale');
			}
			if(deck_status!="premium"){
				$("#deck_"+deck_id).find('.premium_icon').addClass('grayscale');
			}
			if(deck_status=="shared"){
					$("#deck_"+deck_id).find(".refuser_icon, .accepter_icon").show();
					$("#deck_"+deck_id).css("order","-1");
			}

			if(decks[rg]["hasImage"]>0){
			$("#img_deck_"+deck_id).css("background-image","url(deck_img/deck_"+decks[rg]["hasImage"]+".png)");
			}
			else {
			$("#img_deck_"+deck_id).css("background-image","url(img/default_deck.png)");
			}
		}
	}
	//if(user_id==7){$('.adminIcon').show();$('.translate_icon').show();}else{$('.translate_icon').hide();}
		//pour chaque decks, indiquer le nombre de mots avec de la batterie sous forme de progressbar sous le deck
	for(k in decks_data.nbreMots)
	{
		data=decks_data;
		deck_idMot=data.nbreMots[k].deck_id;
		nbreMots=parseInt(data.nbreMots[k].NbreKnown);
		nbreMotsTotal=parseInt(data.nbreMots[k].nbreMots);
		$("#deck_"+deck_idMot).find(".nbreMots").html('('+nbreMots+'/'+nbreMotsTotal+')');
		pcentSucces=parseInt(nbreMots*100/nbreMotsTotal)+"%";
		if(nbreMotsTotal==0){pcentSucces="0%";$("#deck_"+deck_idMot).find(".nbreMots").html("(vide)");}
		$("#deck_"+deck_idMot).append("<div class='progressbarDeckContainer'><div class='progressbarDeck'></div></div>");
		$("#deck_"+deck_idMot+ " .progressbarDeck").css("width",pcentSucces);
		if(pcentSucces=="100%"){
			//$("#deck_"+deck_idMot+" > .img_deck").addClass('golden');
			//$("#deck_"+deck_idMot+ "> .progressbarDeck").css("background-color","var(mycolor2bis)");
		}
	}

	$(".tagBox").remove();
	$(".classBox").remove();
	$(".langBox").remove();
	//filtre sur les tags associés aux deck
	for(t in tags2id)
	{ if(t!="null")
			{
			$(".select_tag").append('<option class="tagBox tag_opt_deck" value="'+t+'">'+t+'</option>');
			$(".myListTag").append('<div class="tagBox" onclick="$(this).toggleClass(\'activ\');MAJ_list_deck();" data-tagname="'+t+'">'+t+'</div>');
		}
	}

	//filtre sur les classes associés
	for(c in class2id)
	{ if(c!="null")
			{
			$(".select_class").append('<option class="classBox class_opt_deck" value="'+c+'">'+c+'</option>');
			$(".myListClass").append('<div class="classBox" onclick="$(this).toggleClass(\'activ\');MAJ_list_deck();" data-classname="'+c+'">'+c+'</div>');
		}
	}
	//filtre sur les langues associés
	for(l in lang2id)
	{ if(l!="null")
			{
			$(".select_lang").append('<option class="langBox class_opt_deck" value="'+l+'">'+l+'</option>');
			$(".myListLang").append('<div class="langBox" onclick="$(this).toggleClass(\'activ\');MAJ_list_deck();" data-lang="'+l+'">'+l+'</div>');
		}
	}
	//Ordoner par ordre alphabetique les option des filtres
	$('.myListClass .classBox').sort(function(a,b) {
   return a.dataset.classname.toLowerCase() > b.dataset.classname.toLowerCase() ? 1 : -1;
	 }).appendTo('.myListClass');

	 $('.myListLang .langBox').sort(function(a,b) {
    return a.dataset.lang.toLowerCase() > b.dataset.lang.toLowerCase() ? 1 : -1;
	}).appendTo('.myListLang');

	 $('.myListTag .tagBox').sort(function(a,b) {
    return a.dataset.tagname.toLowerCase() > b.dataset.tagname.toLowerCase() ? 1 : -1;
	}).appendTo('.myListTag');

	if($(".tagBox").length==0){$(".myListTag").hide();}
	if($(".langBox").length<=1){$(".myListLang").hide();}
	//Restaurer les ancien filtres
	if(readCookie("filtreActif"))
	{
		flagFiltre=false;
		filtre=JSON.parse(readCookie("filtreActif"));
		//if(filtre.searchBox!=""){$('#SearchListInput').val(filtre.searchBox);flagFiltre=true;}
		if(filtre.searchBox!=""){$('.SearchListInput').val("");flagFiltre=true;}
		for(t in filtre.tags)
		{$(".tagBox[data-tagname='"+filtre.tags[t]+"']").addClass("activ");flagFiltre=true;}
		for(c in filtre.classFilter)
		{$(".classBox[data-classname='"+filtre.classFilter[c]+"']").addClass("activ");flagFiltre=true;}
		for(l in filtre.langFilter)
		{$(".langBox[data-lang='"+filtre.langFilter[l]+"']").addClass("activ");flagFiltre=true;}
		if(flagFiltre){$("#filterContainer").removeClass("filtreHidden");}
	}

	MAJ_list_deck();

	$("#deckToImport > .deck").find(".progressbarDeck").hide();
	$("#deckToImport > .deck").find(".knownWords").hide();

	console.log("cat"+categorie);

	if(categorie=="myClass")
		{
			if(class_id!=""){
				if(class_info.role=="prof" || class_info.role=="perso"){

					console.log("sortableGO");
					$(".sortableDeck").sortable({
					items: ".deck:not(#new_Deck,#import_Deck,#import_Deck_vierge, #deck_oublie,#deck_fav)",
					//axis:'y',
					update: function (event, ui) {
					var data = $(this).sortable('serialize');
					console.log(data);
					$.getJSON({
								data: data,
								type: 'POST',
								url: 'ajax.php?action=setOrderDeck&class_id='+class_id
						},function(result){console.log(result);});
						}
				});
				}
			}
		}

}

function copyLink(deck_id)
{
   var lien = document.location.origin + "/cards.php?deck_id=" + deck_id;

   var container = document.createElement("div");
   container.innerHTML = lien;
   //container.style.opacity = 0; // si on veut rendre invisible tout en restant "selectionable"
   document.body.appendChild(container);

   var sel = window.getSelection();
   var rangeObj = document.createRange();
   rangeObj.selectNodeContents(container);
   sel.removeAllRanges();
   sel.addRange(rangeObj);

   if (document.execCommand('copy')) {
      alert("L'URL à bien été copiée dans le presse papier, faites un coller (ctrl + v) là où vous souhaitez la coller.\r\n\r\n" + lien);
   }
   else {
      alert("Impossible de copier le lien automatiquement !\r\nSurligner le lien et faites CTRL + C pour le copier puis CTRL + V là où vous souhaitez le coller.\r\n\r\n" + lien);
   }

   document.body.removeChild(container);
}

function toggleDeckStatus(deck_status_name,deck_id,val)
{
	$.getJSON("ajax.php?action=toggleDeckStatus&deck_id="+deck_id+"&deck_status_name="+deck_status_name, function(result){});
}
function toogleLike(deck_id){
	event.stopPropagation();
	$.getJSON("ajax.php?action=togglelikeDeck&deck_id="+deck_id, function(result)
	{if(result=="delete"){
		$("#deck_"+deck_id).find(".toggleLikes").addClass("notLiked");
		nbreLike=parseInt($("#deck_"+deck_id).find(".nbreLikes").html());
		$("#deck_"+deck_id).find(".nbreLikes").html(nbreLike-1);
		}
		else if(result=="added"){$("#deck_"+deck_id).find(".toggleLikes").removeClass("notLiked");
		nbreLike=parseInt($("#deck_"+deck_id).find(".nbreLikes").html());
		$("#deck_"+deck_id).find(".nbreLikes").html(nbreLike+1);
		}
	});
}
function moveDown(deck_id)
	{$("#deck_"+deck_id).appendTo('#list_deck');
	var data = $(".sortableDeck").sortable('serialize');
	console.log(data);
	$.getJSON({
			data: data,
			type: 'POST',
			url: 'ajax.php?action=setOrderDeck&class_id='+class_id
	},function(result){console.log(result);});
}
function moveUp(deck_id)
	{$("#deck_"+deck_id).prependTo('#list_deck');
	var data = $(".sortableDeck").sortable('serialize');
	console.log(data);
	$.getJSON({
			data: data,
			type: 'POST',
			url: 'ajax.php?action=setOrderDeck&class_id='+class_id
	},function(result){console.log(result);});}
function sendOrderAndRefresh(){
	var data = $(".sortableDeck").sortable('serialize');
	console.log(data);
	$.getJSON({
			data: data,
			type: 'POST',
			url: 'ajax.php?action=setOrderDeck&class_id='+class_id
	},function(result){console.log(result);getList("myClass")});
}
function refuser(deck_id){event.stopPropagation();$("#deck_"+deck_id).remove();$.getJSON("ajax.php?action=refuserDeck&deck_id="+deck_id+"&class_id="+class_id, function(result){});}
function accepter(deck_id){event.stopPropagation();$.getJSON("ajax.php?action=accepterDeck&deck_id="+deck_id+"&class_id="+class_id, function(result){sendOrderAndRefresh();});}

//filtre
function filterByName(rk){
	reg=$('.SearchListInput').val();
	return RegExp(reg,"i").test(rk.deck_name);
}

function intersection(A,B){
	C=[];
	for(a in A){
			if(B.indexOf(A[a])!=-1){C.push(A[a]);}
	}
	return C;
}

//filtre avec les tag
function MAJ_list_deck()
{
	var tags=[];
	var classFilter=[];
	var langFilter=[];
	for(k=0;k<$(".tagBox.activ").length;k++)
	{
		tags.push($(".tagBox.activ:eq("+k+")").data('tagname'));
	}
	for(k=0;k<$(".classBox.activ").length;k++)
	{
		classFilter.push($(".classBox.activ:eq("+k+")").data('classname'));
	}
	for(k=0;k<$(".langBox.activ").length;k++)
	{
		langFilter.push($(".langBox.activ:eq("+k+")").data('lang'));
	}
	$(".deck").not("#new_Deck,#import_Deck,#deck_oublie,#import_Deck_vierge").hide();

	var AllDecksId=[];
	var DeckFromNameSearch=[];
	var DeckFromSelectedClasses=[];
	var DeckFromSelectedTag=[];
	var DeckFromSelectedLangs=[];
	var DeckFilteredIds=[];
	//avec la liste des filtres, on remplis les arrays avec les decks_id qui valide le filtre.
	for(k in decks)
	{
		AllDecksId.push(decks[k].deck_id);
	}
	for(k1 in classFilter)
	{
		for(k2 in class2id[classFilter[k1]])
		{
			DeckFromSelectedClasses.push(class2id[classFilter[k1]][k2]);
		}
	}
	for(k1 in tags)
	{
		for(k2 in tags2id[tags[k1]])
		{
			DeckFromSelectedTag.push(tags2id[tags[k1]][k2]);
		}
	}
	for(k1 in langFilter)
	{
		for(k2 in lang2id[langFilter[k1]])
		{
			DeckFromSelectedLangs.push(lang2id[langFilter[k1]][k2]);
		}
	}
	var filteredDecksName=decks.filter(filterByName);
	for(k in filteredDecksName)
	{
		DeckFromNameSearch.push(filteredDecksName[k].deck_id);
	}
	//si aucune case n'est cochée, elles le sont toute.
	if(tags.length==0){DeckFromSelectedTag=AllDecksId;}
	if(classFilter.length==0){DeckFromSelectedClasses=AllDecksId;}
	if(langFilter==0){DeckFromSelectedLangs=AllDecksId;}
	createCookie("filtreActif",JSON.stringify(
		{tags:tags,
		 classFilter:classFilter,
		 langFilter:langFilter,
		 searchBox:$('.SearchListInput').val()
	 	}),1/48);
	//Je réalise l'intersection de tous les filtres
		DeckFilteredIds=intersection(DeckFromSelectedTag,DeckFromSelectedClasses);
		DeckFilteredIds=intersection(DeckFilteredIds,DeckFromNameSearch);
		DeckFilteredIds=intersection(DeckFilteredIds,DeckFromSelectedLangs);

	for(k in DeckFilteredIds)
	{$("#deck_"+DeckFilteredIds[k]).show();}

	//console.log(DeckFromSelectedTag,DeckFromSelectedClasses,DeckFilteredIds);

}
function MAJ_list_deck_mobile()
{
	tag=$(".select_tag").val();
	classFilter=$(".select_class").val();
	$(".deck").not("#new_Deck").not("#import_Deck").not("#deck_oublie").hide();
	var DeckFromSelectedClasses=[];
	var DeckFromSelectedTag=[];
	var DeckFilteredIds=[];
		for(k2 in class2id[classFilter])
		{
			DeckFromSelectedClasses.push(class2id[classFilter][k2]);
		}
		for(k2 in tags2id[tag])
		{
			DeckFromSelectedTag.push(tags2id[tag][k2]);
		}
	console.log(tag,classFilter);
	if(tag=="all" && classFilter!="all"){console.log(1);DeckFilteredIds=DeckFromSelectedClasses;}
	if(tag!="all" && classFilter=="all"){console.log(2);DeckFilteredIds=DeckFromSelectedTag;}
	if(tag!="all" && classFilter!="all"){console.log(3);DeckFilteredIds=intersection(DeckFromSelectedTag,DeckFromSelectedClasses);}
	if(tag=="all" && classFilter=="all"){console.log(4);$(".deck").show();}
	else{
		for(k in DeckFilteredIds)
		{$("#deck_"+DeckFilteredIds[k]).show();}
	}
}
//affichage des tuile explicative :

if(class_info.role=="prof")
{
	$.getJSON("ajax.php?action=nbreEleve&class_id="+class_id, function(result)
	{
		if(result.nbreEleve==0)
		 {
			 $(".explicationContainer").append(
		 		`<div class="whiteTile">
					<img src="img/addStudent.png" class="titleIcon"><h3 class="titleTile"><?php echo __("Invitation des élèves");?></h3>
					<div>
					 	<div style="margin:auto;">
							<p><?php echo __("Aucun élève inscrit dans cette classe.");?></p>
							<p><?php echo __("Pour ajouter des élèves, envoyez-leurs le lien suivant par email :");?>
								<div>
										<input type='text' id='inputCodeLink' readonly value='www.exolingo.com/joinClass.php?code=`+code+`'>
								</div>
							</p>
							<p><?php echo __("Vous pouvez également leurs demander de rejoindre la classe avec le code suivant :");?>
								<div class='code'>`+code+`</div>
							</p>
						</div>
					</div>
				</div>`);

		}else{
			$(".colDroiteDeck").append(
			 `<div class="whiteTile">
				 <img src="img/reportStudent.png" class="titleIcon"><h3 class="titleTile"><?php echo __("Rapport de classe");?></h3>
				 <div>
					 <div style="margin:auto;">
						 <p><?php echo __("Consultez le rapport de classe pour vérifier les acquis et la progression de vos élèves.");?></p>
						 <div style="text-align:right;"><button class="btnStd1 ripple"  onClick='windowClass("report");'><?php echo __("Consulter");?></button></div>
					 </div>
				 </div>
			 </div>`);
		}
	});
}
if(userType=="prof")
{
		$.getJSON("ajax.php?action=nbreMyClassProf", function(result)
		{
		if(result.nbreClass==0)
		 {
			 $(".explicationContainer").append(
		 		`<div class="whiteTile Shinytile">
					<img src="img/addCourse.png" class="titleIcon"><h3 class="titleTile"><?php echo __("Création d'une classe");?></h3>
					<div style="">

					 	<div style="margin:auto;">
							<p><?php echo __("Vous n'avez pour le moment aucune classe.");?></p>
							<p><?php echo __("Une classe vous permet de rassembler vos élèves et de leurs donner accées aux listes que vous avez sélectionnées et créées pour eux.");?></p>
							<p><?php echo __("Vos élèves pourront dans un deuxième temps partager (avec votre autorisation) leurs listes avec les membres de la classe.");?></p>
							<div style="text-align:right;"><button class="btnStd1 ripple"  onClick='windowClass("welcome");'><?php echo __("Créer une classe");?></button></div>
						</div>
					</div>
				</div>`);

		}
		});
}
if((class_info.role=="perso" || class_info.role=="explore") && userType=="eleve")
{
	$.getJSON("ajax.php?action=nbreMyClassEleve", function(result)
	{
		if(result.nbreClass==0)
		 {
			 $(".explicationContainer").append(
		 		`<div class="whiteTile Shinytile">
					<img src="img/key.png" class="titleIcon"><h3 class="titleTile"><?php echo __("Rejoindre une classe");?></h3>
					<div style="">

					 	<div style="margin:auto;">
							<p><?php echo __("Vous n'êtes inscrit dans aucune classe.");?></p>
							<p><?php echo __("Demander à votre professeur de créer une classe et de vous donner le code correspondant");?></p>
							<form onsubmit='return false;'>
				        <p><?php echo __("En rejoignant la classe d'un professeur, vous accéderez aux listes de cette classe et serez en relation avec les élèves et les professeurs de cette classe.");?></p>
				        <div style="text-align:right;margin:10px;">
				          <input type='text' size='5' class='inputCodeMobile' placeholder='- - - -' style='text-align:center;padding-left:30px;' onkeyup='if(this.value.length>5){this.value=this.value.substr(0,5);}'>

				          <div class="msgErrorCode"></div>
				          <button type="submit" class="BtnStd1 ripple" onclick="joinClassWithCode2();"><?php echo __("Rejoindre la classe");?></button>
				        </div>
				        </form>

						</div>
					</div>
				</div>`);

		}
	});
}


if(class_info.role!="prof")
{

	$.getJSON("ajax.php?action=nbreCardToReview&class_id="+class_id, function(result)
	{
		console.log(result);
		if(result.nbreCards>1)
		 {
			 $(".colDroiteDeck").append(
		 		`<div class="whiteTile Shinytile">
					<img src="img/alert.png" class="titleIcon"><h3 class="titleTile"><?php echo __("Répétitions espacées");?></h3>
					<div>
					 	<div style="margin:auto;">
							<p><?php echo __("Actuellement ");?><span class='nbreMots'>`+result.nbreCards+`</span><?php echo __(" cartes sont sur le point d'être oubliés.");?></p>
							<p><?php echo __("L'algorithme de ExoLingo prédit quand les cartes doivent être retravaillé pour s'inscrire dans votre mémoire à long terme.");?></p>
							<div style="text-align:right;"><button class="btnStd1 ripple"  onClick='location.href="cards.php?deck_id=-1&class_id=`+class_id+`"'><?php echo __("Réviser");?></button></div>
						</div>
					</div>
				</div>`);

		}
		else if($('.deck:not(#deck_oublie,#import_Deck,#new_Deck):visible').length!=0){

				 $(".colDroiteDeck").append(
		 		 `<div class="whiteTile" id="whiteTileNoCardToReview">
		 			 <img src="img/check3.png" class="titleIcon"><h3 class="titleTile"><?php echo __("Répétitions espacées");?></h3>
					 <div>
					 	<div style="margin:auto;">
		 			 		<p><?php echo __("Aucune carte n'est à réviser pour le moment. Choissisez une liste et apprenez de nouveaux mots en vous amusant.");?></p>
							<p><?php echo __("L'algorithme de ExoLingo prédit quand les cartes doivent être retravaillé pour s'inscrire dans votre mémoire à long terme.");?></p>
						</div>
					 </div>
				 </div>`);
		}
	});
}

var mission_edition=new Mission_edition_manager(0,class_id);


updateObjectifView();

function toggleSelected(deck_id)
{
	if(selectedDeck.indexOf(deck_id)!=-1){selectedDeck.splice(selectedDeck.indexOf(deck_id),1);}else{selectedDeck.push(deck_id);}
	console.log(selectedDeck);
	if(selectedDeck.length>0){$(".btnImport").addClass("activBtnImport").removeClass("unactivBtnImport").prop("disabled",false);}else{$(".btnImport").addClass("unactivBtnImport").removeClass('activBtnImport').prop("disabled",true);}
}
var selectedDeck=[];
function importListInClass()
	{	class_id=class_info.class_id;
		$(".fenetreSombre").fadeOut(200,function() { $(this).remove(); });
		$.getJSON("ajax.php?action=addDecksToClass&class_id="+class_id+"&decks="+selectedDeck, function(decks)
	{//location.reload();

	console.log(decks);
	getList("myClass");
	});
}
var deckAvailable;
var allDecksData;

function showAvailableListtoImport()
{
	selectedDeck=[];
	$('.fenetreSombre').remove();
	$('body').append(`<div class='fenetreSombre' onclick='$(this).fadeOut(200,function() { $(this).remove(); });'>
	<div class='fenetreClaire fenetreAction' style="overflow:hidden;" onclick='event.stopPropagation();'>

	<div style="background-color:white;width:100%;display:inline-block;">
		<img src='img/close.png' class='closeWindowIcon' onclick='$(".fenetreSombre").fadeOut(200,function() { $(this).remove(); });'>
		<h3 style='text-align:left;margin:10px;padding-left:20px;display:inline-block;'><img src='img/arrow_left.png' class='backFenetre' onclick='$(\".fenetreSombre\").fadeOut(200,function() { $(this).remove(); });'><?php echo __("Choisissez des listes à importer dans ");?>`+class_info.class_name+`</h3>
		<div style="text-align:center;">
			<button class='btnImport unactivBtnImport' disabled onclick='importListInClass();'><?php echo __("Importer dans ");?>`+class_info.class_name+`</button>
		</div>
		<div id='tab_container'></div>
		<div>
			<input type="text" class='SearchListInput importBoxSearch typeahead' placeholder="<?php echo __("métier, ...");?>" style="vertical-align:middle;">
			<div class="filterTooStrict"><?php echo __("Aucune liste n'a été trouvé");?></div>
		</div>
	</div>

		<div id="deckToImport" style="overflow:auto;max-height:68vh;position:relative;top:10px;">

		</div>
	</div>
	</div>`);
	$("#deckToImport").append(`<div id='import_Deck_vierge' onclick="location.href='edit_deck.php?deck_id=0';" class='deck' style='display:inline-flex;flex-direction:column;'>
		<div style='margin:auto;'><div class='plusImport'>+</div><br><?php echo __("Créer une nouvelle liste vierge");?></div></div>`);
	$("#deckToImport").show();
	//$("#tab_container").append('<div class="tab activeT" id="tabMarks" onclick="">Tab1</div>');
	//$("#tab_container").append('<div class="tab" id="tabMarks" onclick="">Tab2</div>');
	//$("#tab_container").append('<div class="tab" id="tabMarks" onclick="">Tab2</div>');
		if(typeof deckAvailable!="undefined"){
			drawAvailableDeck(deckAvailable);
		}
		else{
			$.getJSON("ajax.php?action=getAvailableDecks", function(decks_data)
			{	deckAvailable=decks_data;
				drawAvailableDeck(deckAvailable);
					// Constructing the suggestion engine
					var listeDesTagsB = new Bloodhound({
							datumTokenizer: Bloodhound.tokenizers.whitespace,
							queryTokenizer: Bloodhound.tokenizers.whitespace,
							local: listeDesTags
					});
					var listeDesNomsB = new Bloodhound({
							//datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
							datumTokenizer: Bloodhound.tokenizers.whitespace,
							queryTokenizer: Bloodhound.tokenizers.whitespace,
							local: listeDesNoms
					});

					// Initializing the typeahead
					$('.typeahead').typeahead({
							hint: true,
							highlight: true, /* Enable substring highlighting */
							minLength: 0 /* Specify minimum characters required for showing suggestions */
					},
					{
							name: 'Noms',
							source: listeDesNomsB,
							templates:{
    						header: '<h3 class="category-name">Noms</h3>',
  						}
					},{
							name: 'Attributs',
							source: listeDesTagsB,
							templates:{
    						header: '<h3 class="category-name">Attribut</h3>'
  						}
					});
					$('.typeahead').on("input",filterAvailableDeck);
					$('.typeahead').bind('typeahead:selected', function(obj, datum, name) {
						//console.log(obj.data());
						//console.log(obj,datum,name);
		        //console.log(JSON.stringify(obj)); // object
		        // outputs, e.g., {"type":"typeahead:selected","timeStamp":1371822938628,"jQuery19105037956037711017":true,"isTrigger":true,"namespace":"","namespace_re":null,"target":{"jQuery19105037956037711017":46},"delegateTarget":{"jQuery19105037956037711017":46},"currentTarget":
		        //console.log(JSON.stringify(datum)); // contains datum value, tokens and custom fields
		        // outputs, e.g., {"redirect_url":"http://localhost/test/topic/test_topic","image_url":"http://localhost/test/upload/images/t_FWnYhhqd.jpg","description":"A test description","value":"A test value","tokens":["A","test","value"]}
		        // in this case I created custom fields called 'redirect_url', 'image_url', 'description'
		        //console.log(JSON.stringify(name)); // contains dataset name
		        // outputs, e.g., "my_dataset"
					});
			});
		}
}

function filterAvailableDeck()
{
	$("#deckToImport").find(".deck").not("#import_Deck_vierge").hide();

	var AllDecksId=[];
	var DeckFromNameSearch=[];
	var DeckFromSelectedTag=[];
	var DeckFilteredIds=[];
	query=$(".tt-input").val();
	//avec la liste des filtres, on remplis les arrays avec les decks_id qui valide le filtre.
	for(k in deckAvailable.decks)
	{
		AllDecksId.push(deckAvailable.decks[k].deck_id);
	}
	for(k in tags2id[query])
	{
		DeckFromSelectedTag.push(tags2id[query][k]);
	}
	var filteredDecksName=deckAvailable.decks.filter(function(rk){return RegExp(query,"i").test(rk.deck_name);});

	for(k in filteredDecksName)
	{
		DeckFromNameSearch.push(filteredDecksName[k].deck_id);
	}

	//si aucune case n'est cochée, elles le sont toute.
	if(DeckFromSelectedTag.length+DeckFromNameSearch.length!=0){
		if(DeckFromSelectedTag.length==0){DeckFromSelectedTag=AllDecksId;}
		if(DeckFromNameSearch.length==0){DeckFromNameSearch=AllDecksId;}
	}
	createCookie("filtreActif",JSON.stringify(
		{tags:[],
		 classFilter:[],
		 langFilter:[],
		 searchBox:query
	 	}),1/48);
	//Je réalise l'intersection de tous les filtres
		console.log(DeckFromSelectedTag,DeckFromNameSearch,AllDecksId);
		DeckFilteredIds=intersection(DeckFromSelectedTag,DeckFromNameSearch);
		DeckFilteredIds=intersection(DeckFilteredIds, AllDecksId);
		if(DeckFilteredIds.length==0){$(".filterTooStrict").show();}
		else{$(".filterTooStrict").hide();}

		for(k in DeckFilteredIds)
		{$("#deckToImport").find("#deck_"+DeckFilteredIds[k]).show();}
}
function filterDecks()
{
	$("#listsContainer").find(".deck").not("#import_Deck,#fav_deck,#new_Deck").hide();

	var AllDecksId=[];
	var DeckFromNameSearch=[];
	var DeckFromSelectedTag=[];
	var DeckFilteredIds=[];
	query=$(".tt-input").val();
	//avec la liste des filtres, on remplis les arrays avec les decks_id qui valide le filtre.
	for(k in allDecksData.decks)
	{
		AllDecksId.push(allDecksData.decks[k].deck_id);
	}
	for(k in tags2id[query])
	{
		DeckFromSelectedTag.push(tags2id[query][k]);
	}
	var filteredDecksName=allDecksData.decks.filter(function(rk){return RegExp(query,"i").test(rk.deck_name);});

	for(k in filteredDecksName)
	{
		DeckFromNameSearch.push(filteredDecksName[k].deck_id);
	}

	//si aucune case n'est cochée, elles le sont toute.
	if(DeckFromSelectedTag.length+DeckFromNameSearch.length!=0){
		if(DeckFromSelectedTag.length==0){DeckFromSelectedTag=AllDecksId;}
		if(DeckFromNameSearch.length==0){DeckFromNameSearch=AllDecksId;}
	}
	createCookie("filtreActif",JSON.stringify(
		{tags:[],
		 classFilter:[],
		 langFilter:[],
		 searchBox:query
	 	}),1/48);
	//Je réalise l'intersection de tous les filtres
		console.log(DeckFromSelectedTag,DeckFromNameSearch,AllDecksId);
		DeckFilteredIds=intersection(DeckFromSelectedTag,DeckFromNameSearch);
		DeckFilteredIds=intersection(DeckFilteredIds, AllDecksId);
		if(DeckFilteredIds.length==0){$(".filterTooStrict").show();}
		else{$(".filterTooStrict").hide();}

		for(k in DeckFilteredIds)
		{$("#listsContainer").find("#deck_"+DeckFilteredIds[k]).show();}
}
function drawAvailableDeck(decks_data)
{
	drawDecks(decks_data,"#deckToImport");
	$("#deckToImport > .deck").toggleClass("selectedDeck");//.toggleClass("NetB");
	$("#deckToImport > .deck").off();
	$("#deckToImport > .deck:not(#import_Deck_vierge)").attr('onclick','');
	$("#deckToImport > .deck").on("click",function(){
		deck_id=$(this).attr('id');
		deck_id=deck_id.substr(5,deck_id.length);
		console.log(deck_id);
		toggleSelected(deck_id);
		$(this).toggleClass("selectedDeck").toggleClass("selectedAvailableDeck");//.toggleClass("NetB");
	});
}
function deleteDeck(deck_id)
{
	event.stopPropagation();
	confDelete=confirm("<?php echo __("Etes-vous sure de vouloir supprimer cette liste ? La totalité des cartes et des mémorisations associées seront définitivement supprimées.");?>")
	if(confDelete){
		$.getJSON("ajax.php?action=deleteDeck&deck_id="+deck_id, function(result){
			$("#deck_"+deck_id).remove();
		});
	}
}
function duplikAndTranslate(deck_id,lang_fin)
{
	event.stopPropagation();
	$.getJSON("ajax.php?action=duplicDeck&class_id="+class_id+"&deck_id="+deck_id, function(newDeck_id)
		{	console.log(newDeck_id);
			lang_ini=<?php echo $_SESSION["target_lang"];?>;
			$.getJSON("ajax.php?action=translateDeck&deck_id="+newDeck_id+"&lang_ini="+lang_ini+"&lang_fin="+lang_fin, function(result){
				//alert("done");
				$("#lang_item_"+lang_fin).css("filter","grayscale(1)");
				//$(".fenetreSombre").remove();
			});

		});
}


function showWindow(){
	$('.fenetreSombre').remove();
	$('body').append("<div class='fenetreSombre' onclick='$(this).remove();'><div style='text-align:center;' class='fenetreClaire' onclick='event.stopPropagation();'>"
	+"</div></div>");
}
function translateDeckMenu(deck_id){
	showWindow();
	//$(".fenetreClaire").append("<h3>Langue source</h3><select class='select_lang_ini'><option value='1' selected>Français</option><option value='en' selected>English</option></select>")
	$(".fenetreClaire").append("<h3>Langue cible</h3>"+
	"<div class='select_lang_fin'></div>");

	$.getJSON("ajax.php?action=getTargetTranslateLang&deck_id="+deck_id, function(result)
	{
		langs=result.langs;
		langsUsed=result.langsUsed;
		for(langRk=0;langRk<langs.length;langRk++)
		{
			lang_id=langs[langRk].lang_id;
			lang_name=langs[langRk].lang_name;
			$(".select_lang_fin").append("<div class='lang_item' id='lang_item_"+lang_id+"' onclick='duplikAndTranslate("+deck_id+",\""+lang_id+"\")'><div class='flagStd flag_"+langs[langRk].lang_code2+"'></div><div>"+langs[langRk].lang_name+"</div></div>");
		}
		for(langRk=0;langRk<langsUsed.length;langRk++)
		{
			lang_id=langsUsed[langRk].lang_id;
			$("#lang_item_"+lang_id).css("filter","grayscale(1)");
		}
	});
	//$(".fenetreClaire").append("<br><button onclick='duplikAndTranslate("+deck_id+");'>Traduire</button><br>");
}

function duplik(deck_id)
{
	event.stopPropagation();
	$.getJSON("ajax.php?action=duplicDeck&deck_id="+deck_id, function(data)
		{	console.log(data);
			newDeck_id=data.newDeck_id;
			location.href="edit_deck.php?deck_id="+newDeck_id;
		});
}
function removeDeck(deck_id)
{
	$("#deck_"+deck_id).remove();
	event.stopPropagation();
	$.getJSON("ajax.php?action=removeDeckFromClass&class_id="+class_id+"&deck_id="+deck_id, function(result)
	{});
}

function toggleVisi(deck_id)
{
  console.log("toggleActivity");
  event.stopPropagation();
  if($("#deck_"+deck_id).find(".visibilityIcon").hasClass("novisi"))
  {visi=1;$("#deck_"+deck_id).find(".visibilityIcon").removeClass("novisi");$("#deck_"+deck_id).removeClass("deckInvisible");}
  else{visi=0;$("#deck_"+deck_id).find(".visibilityIcon").addClass("novisi");$("#deck_"+deck_id).addClass("deckInvisible");}
  $.getJSON("ajax.php?action=SetVisi&class_id="+class_id+"&deck_id="+deck_id+"&visi="+visi, function(result){});
}

function capitalizeFirstLetterEachWordSplitBySpace(string){
	var words = string.split(" ");
	var output = "";
	for (i = 0 ; i < words.length; i ++){
	lowerWord = words[i].toLowerCase();
	lowerWord = lowerWord.trim();
	capitalizedWord = lowerWord.slice(0,1).toUpperCase() + lowerWord.slice(1);
	output += capitalizedWord;
	if (i != words.length-1){
	output+=" ";
	}
	}//for
	output[output.length-1] = '';
	return output;
}

function showTutorial()
{
	showWindow();
	$('.fenetreClaire').remove();
	$('.fenetreSombre').append(`
		<iframe width="70%" height="70%" style="left:15%;top:10%;position:relative;" src="https://www.youtube.com/embed/OUEUUn-i61U" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	`);
}
if(!readCookie("tuto-mainPage")){
	if(lang_interface=="fr")
	{
	showTutorial();
	createCookie("tuto-mainPage",1,150);
	}
}
if(lang_interface!="fr"){
	$(".button--help").hide();
}
</script>

</body>
</html>
