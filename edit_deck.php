<?php
include_once ("db.php");
session_start();

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
		$classe = $_SESSION['classe'];
		$target_lang_id = $_SESSION['target_lang'];
		$deck_id=(int)$_GET['deck_id'];

		if($deck_id<=0)
		{
			$sql = "INSERT INTO decks (deck_name, visible, classe, hasImage, nbreMots,user_id,status,lang_id)"
	            . "VALUES ('Liste sans nom','1','myDecks','0', '0','".$user_id."','".$type."',".$target_lang_id.")";
	    $mysqli->query($sql);
			$deck_id=$mysqli->insert_id;
			$today=date("Y-m-d");
			$sql = "INSERT INTO `user_deck_droit`(`user_id`, `deck_id`, `jour`, `droit`)
			VALUES (".$user_id.",".$deck_id.",'".$today."','admin')";
			$mysqli->query($sql);
			//récupération class_perso
			$result = $mysqli->query('SELECT class_id FROM classes WHERE creator_id = '.$user_id.' AND status="perso"');
			$row = $result->fetch_assoc();
			$class_perso_id=(int)$row["class_id"];
			$result->free();
			if($class_perso_id!=0)
			{$sql = "INSERT INTO deck_class (deck_id, class_id,visible,position,status)"
	            . "VALUES (".$deck_id.",".$class_perso_id.",1,0, 'ok')";
	    $mysqli->query($sql);}

			// if(isset($_GET["class_id"])){
			// 				$class_id=(int)$_GET["class_id"];
			// 				$result = $mysqli->query('SELECT classes.lang FROM classes WHERE classes.class_id = ' . $class_id);
			// 				$row = $result->fetch_assoc();
			// 				$class_lang=$row["lang"];
			// 				$result->free();
			// 				$sql = "UPDATE decks SET lang='".$class_lang."' WHERE deck_id=".$deck_id;
			// 				$mysqli->query($sql);
			// 			}
			header("location: edit_deck.php?deck_id=".$deck_id);
			exit();
		}

		//recupération infos sur le deck, et les classs du deck :
		$myDeck=array();
		$myClass=array();
		$visible=array();
		$result = $mysqli->query('SELECT decks.lang_id,lang.lang_code2,lang.lang_code2_2,lang.lang_code3,decks.deck_id,decks.deck_name,decks.hasImage,decks.visible,decks.nbreMots, decks.position, decks.user_id, decks.status,deck_class.class_id,deck_class.visible,decks.texte,decks.link,decks.hasPoster,decks.hasAudio,decks.youtube_id FROM decks
			LEFT JOIN deck_class ON decks.deck_id=deck_class.deck_id
			LEFT JOIN classes ON classes.class_id=deck_class.class_id
			LEFT JOIN lang ON decks.lang_id=lang.lang_id
			WHERE decks.deck_id = ' . $deck_id);
		while ($row = $result->fetch_assoc()) {
	  $myDeck = $row;
		array_push($myClass,$row["class_id"]);
		$visible[$row["class_id"]]=$row["visible"];
		}
		$creator_id=$myDeck['user_id'];
		$myDeck['class_id']=$myClass;
		$myDeck['visible']=$visible;
		$result->free();
		//récupération des info sur le créateur
		$result = $mysqli->query('SELECT users.type,users.classe, users.first_name, users.last_name FROM users WHERE user_id = ' . (int)$myDeck["user_id"]);
	  $myresult = $result->fetch_assoc();
		$creator_type=$myresult["type"];
		$creator_name=$myresult["first_name"]." ".$myresult["last_name"];
		$result->free();
		$cards=array();
		$result = $mysqli->query('SELECT cards.alert,cards.card_id,cards.deck_id,mot,mot_trad,hasImage,hasAudio,comment FROM cards
			LEFT JOIN alerte ON alerte.card_id=cards.card_id
			WHERE cards.active=1 AND deck_id = ' . $deck_id.' ORDER BY cards.alert ASC, cards.card_id ASC');
		while ($row = $result->fetch_assoc()) {
						array_push($cards,$row);
				}
		$result->free();

		//recupération des phrases:
		$sentences=array();
		$result = $mysqli->query('SELECT cards.card_id,card_sentence.sentence_id,card_sentence.sentence FROM cards
			LEFT JOIN card_sentence ON cards.card_id=card_sentence.card_id WHERE cards.active=1 AND cards.deck_id = ' . $deck_id);
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

		//récupération des droits de l'utilisateur
		$result = $mysqli->query('SELECT user_deck_droit.droit FROM user_deck_droit Where deck_id = ' . $deck_id.' AND user_id='.$user_id);
		$row = $result->fetch_assoc();
			$droit=$row["droit"];
		$result->free();
		//récupération des classes
		$isInCoopMod=0;
		$classes=array();
		$result = $mysqli->query('SELECT IF(deck_class.deck_id='.$deck_id.',deck_class.status,"build") as status, classes.class_id,lang.lang_id,lang.lang_code3,lang.lang_code2_2,lang.lang_code2, class_name,promo,deck_class.deck_id, classes.status as classStatus,user_class.role
		FROM classes
		LEFT JOIN lang ON lang.lang_id=classes.lang_id
		LEFT JOIN user_class ON user_class.class_id=classes.class_id
		LEFT JOIN deck_class ON deck_class.class_id=classes.class_id
		WHERE classes.active=1 AND classes.status!="archive" AND user_class.user_id = ' . $user_id .'  ORDER BY classes.class_id ASC');
		$class_id_old=-1;
		while ($row = $result->fetch_assoc()) {
						if($class_id_old!=$row["class_id"])
						{
						$classes[$row["class_id"]]=$row;
						$status="build";
						if($row["deck_id"]==$deck_id){
							$status=$row["status"];
							$class_id_old=$row["class_id"];
						}
						if($status=="coop"){$isInCoopMod=1;}
						$classes[$row["class_id"]]["status"]=$status;
						//array("class_name" => $row["class_name"],"promo" => $row["promo"],"status" => $status,"classStatus"=>$row["classStatus"],"role"=>$row["role"],"lang"=>$row["lang"]);
						}
				}
		$result->free();
		//check if we kick out the user??
		//out if (he is not the creator_id && (he is not prof || creatortype is not prof) && !$isInCoopMod
		if($droit!="admin" && $droit!="modif" && $isInCoopMod==0){
			$_SESSION['message'] = "Vous n'avez pas les droits pour éditer cette liste."
			."<br>creator_id=".$creator_id
			."<br>creator_type=".$creator_type
			."<br>type=".$type
			."<br>isInCoopMod=".$isInCoopMod;
			echo "<script>console.log('Vous n avez pas les droits pour éditer cette liste."
			.",creator_id=".$creator_id
			.",creator_type=".$creator_type
			.",type=".$type
			.",isInCoopMod=".$isInCoopMod."');</script>;";
	    header("location: cards.php?deck_id=".$deck_id);
			exit();
		}

		echo "<script>classes=".json_encode($classes).";console.log(classes);</script>";
		echo "<script>fullUserName='".$first_name." ".$last_name."';</script>";
		echo "<script>type='".$type."';</script>";
		echo "<script>target_lang_id='".$target_lang_id."';</script>";
		echo "<script>droit='".$droit."';</script>";
		echo "<script>user_id=".$user_id.";</script>";
		echo "<script>myDeck=".json_encode($myDeck).";</script>";
		echo "<script>deck_id=".json_encode($myDeck['deck_id']).";</script>";
		echo "<script>creator_type=".json_encode($creator_type).";</script>";
		echo "<script>creator_name=".json_encode($creator_name).";</script>";
		echo "<script>cards=".json_encode($cards).";</script>";
		echo "<script>sentences=".json_encode($sentences).";</script>";
		echo "<script>lang_interface=".json_encode($_SESSION['local_lang']).";</script>";
?>
<!DOCTYPE html>
<html >
 <head>


    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edition des listes</title>
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
  	<script src="js/cookiesManager.js"></script>
		<script src="importCards.js?ver=<?php echo filemtime('importCards.css');?>"></script>
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
			.addContentIcon:hover,.addContentIconCard:hover, .crea:hover,.creaMagic:hover{
				background-color:var(--mycolor2bis);
			}
			#addContentIconText{background-image:url(img/text.png);}
			#addContentIconImage{background-image:url(img/image.png);}
			.addContentIconImage{background-image:url(img/image.png);}
			#addContentIconVideo{background-image:url(img/play.png);}
			#addContentIconLink{background-image:url(img/link.png);}
			#addContentIconAudio, .addContentIconAudio{background-image:url(img/haut_parleur.png);}
			.addContentIconPhrase{background-image:url(img/sentence.png); margin: 20px;}
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
		.bodycontent{filter:blur(10px);}
		.slideUp{display:none;}
		.accordeonsTop .slideUpTop:after{content:"";background-image:url(img/arrow_down.png);background-size:cover;width:20px;height:20px;padding-left:15px;margin-left:15px;float:right;}
		.accordeonsTop label:not(.slideUpTop):after{content:"";background-image:url(img/arrow_down.png);background-size:cover;width:20px;height:20px;padding-left:15px;margin-left:15px;float:right;transform:rotate(180deg);}
		.accordeonsTop label:after{transition:0.5s;}
		.page_container {
    max-height: 85vh;
    overflow: auto;}

		</style>
</head>

<body class="fond">
	<?php include "entete.php";?>
<script>
// $(".buttonHome").hide();
// $(".buttonMesClasses").hide();
// $(".buttonMyDecks").hide();
// $(".buttonMyClass").hide();
// $(".settingClass").hide();
$(".enteteBtn:not(.enteteEdit)").hide();
langUpdateButton();




</script>

	<!--   ============FIN ENTETE=================-->



<div class="center bodyContent" style="display:flex;flex-direction:column;padding-top:40px;">

	<div id="deck_info" class="block_edit">
		<div id="actionsOnDeckContainer" style="position:relative;float:right;right:0;">
			<div class="buttonActionDeck del_button" id="delDeckIcon" onClick="delete_deck();">
				<a href='#' class="delete_icon icon_back"></a>
			</div>
		</div>
		<label class="label_edition" style="clear: both;">
			<?php echo __("Couverture de la liste");?>
		</label>
		<div class='deck' style="line-height: normal;top:2px; left:2px;box-shadow:0px 0px 3px grey;min-width:300px">
			<div style='display:inline-block;min-width:100%;'>
				<div id='' class='img_deck'>
					<div id="deck_preview_manager"><img src="img/default_deck.png" class="img_deck_preview" >
						<div class="addContentIconCard addContentIconImage" id="addContentIconImageDeck"></div>
					</div>
					<!--<div class="button_icon del_deck_img" onclick='del_deck_img();'>Supprimer</div>
					<div class="button_icon add_deck_img" onclick='showOnTopDeck();'>ajouter</div>-->

					<input style="display:inline-block;visibility:hidden;" type="file" onchange="upload(this);" class="browse_deck_img" name="image_indice_input" id="image_indice_input" accept="image/*">
				</div>
				<div class='infoDeck'>
					<span class='deck_name'><input type="text" style="max-width:60%;" class="titre_deck_edition" value="" onBlur="MAJ_deck();"></span>
				</div>
			</div>
		</div>
</div>

<div>
	<div class="block_edit classManagementBlock">
		<div class="accordeonsTop" onclick="retract(this);">
				<label class="label_edition slideUpTop">
					<?php echo __("Langue de la liste");?>
				</label>
		</div>
		<div class="retractable">
				<div id="assoLangDeck"></div>
		</div>
	</div>
	<div class="block_edit classManagementBlock">
		<div class="accordeonsTop" onclick="retract(this);">
				<label class="label_edition slideUpTop">
					<?php echo __("Diffusion de la liste");?>
				</label>
		</div>
		<div class="retractable">
				<div class="noClassInfo"><?php echo __("Aucune classe dans cette langue.");?></div>
				<div id="assoClass"></div>
		</div>
	</div>

	<div class="block_edit classManagementBlock">
			<div class="accordeonsTop" onclick="retract(this);">
				<label class="label_edition slideUpTop">
				<?php echo __("Thèmes");?>
				</label>
			</div>
			<div class="retractable slideUp">
				<div id="list_tag">
					<br>
					<select class="select_tag" name="theme" onchange="MAJ_tag();">
						 <option value="" selected><?php echo __("Selectionner un thème");?></option>
						 <option value="add_tag"><?php echo __("Ajouter un thème");?></option>
					</select>
				</div>
			</div>
	</div>

</div>
<div>

				<div class="block_edit globalContentBlock">
					<div class="accordeonsTop" onclick="retract(this);">
							<label style="" class="label_edition slideUpTop">
								<?php echo __("Contenu global de la liste");?>
							</label>
					</div>
					<div class="retractable slideUp">
						<div style="margin:0 40px 0 40px;">
							<div class="addContentIcon" id="addContentIconVideo" title='<?php echo __("Ajouter une vidéo Youtube");?>'></div>
							<div class="addContentIcon" id="addContentIconAudio" title='<?php echo __("Ajouter un fichier audio");?>'></div>
							<div class="addContentIcon" id="addContentIconImage" title='<?php echo __("Ajouter une image");?>'></div>
							<div class="addContentIcon" id="addContentIconText" title='<?php echo __("Ajouter un texte");?>' ></div>
						</div>
						<div id="windowGlobalContainer">

								<div class="addContentContainer" id="contentContainerImage"><div class="backIcon" onclick="$('.fenetreSombre').remove();"></div><div class="addContentTitre"><?php echo __("Image");?></div><div class="addContent"><input type="file" accept="image/*" onchange="uploadGC(this,'Image');"></div><img src="" class="previewPoster" style="display:none;"><div style="text-align:center;">
									<div class="annuler"><?php echo __("Annuler");?></div><div class="valider"><?php echo __("Valider");?></div></div>
								</div>
								<div class="addContentContainer" id="contentContainerVideo">
									<div class="backIcon" onclick="$('.fenetreSombre').remove();"></div>
									<div class="addContentTitre"><?php echo __('Vidéo (youtube)');?></div>
									<p><?php echo __("indiquer l'URL de la vidéo youtube");?></p>
									<div class="addContent"><input type="text" class="videoInput"></div>
									<div class="previewVideo" style="display:none;"></div>
									<div style="text-align:center;"><div class="annuler"><?php echo __("Annuler");?></div><div class="valider"><?php echo __("Valider");?></div></div>
								</div>
								<div class="addContentContainer" id="contentContainerAudio">
									<div class="backIcon" onclick="$('.fenetreSombre').remove();"></div>
									<div class="addContentTitre"><?php echo __("Audio");?></div>
									<div class="addContent">
										<p><?php echo __("Importer un fichier");?><i>.mp3</i> ou <i>.wav</i></p>
										<input type="file" accept=".mp3,.wav" onchange="uploadGC(this,'Audio');">
									</div>
									<div class="previewAudio" style="display:none;"></div>
									<div style="text-align:center;"><div class="annuler"><?php echo __("Annuler");?></div><div class="valider"><?php echo __("Valider");?></div></div>
								</div>
						</div>
				<br>
						<div id="previewArea"></div>
				</div>
			</div>

			<div>
				<div id="new_card" class="block_edit">
					<label class="label_edition">
						<?php echo __("Ajouter une nouvelle carte");?>
					</label>
						<form onsubmit="return false;" style="margin:0;">
							<input id="new_mot" class="input_text" type="text" value="" placeholder="<?php echo __("Mot sur la carte");?>">
							<input class="crea" type="submit" onclick='AddNewCard();' value="<?php echo __("Nouveau mot");?>">
						<!--<div class="creaMagic" type="submit" onclick='AddMagicNewCard();'><img src="img/magicStick.png"></div>-->
						<!--	<input class="importCSV" type="button" style="float:right;" onclick='importCSVWindow();' value="<?php echo __("Importer");?>">-->
							<input class="ImportDeckWindow" type="button" style="float:right;" onclick='showImportDeckWindow();' value="<?php echo __("Importer des cartes d'autres listes");?>">

						</form>
				</div>
			</div>
		</div>
<div>
	<div id="list_card" class="block_edit">
		<div id='table_card'>
		</div>
	</div>
</div>
</div><!--center-->

<div class="button--help" onclick="showTutorial();"><div>?</div></div>


<div id="onTopBottom" tabindex="100"></div>
<input style="position: absolute;left: -9999px;" type="file" onchange="upload_card_img();" class="btn btn-primary browse_deck_img" name="image_indice_input" id="input_image_card" accept="image/*">
<div id="disconnected"><span style="margin:auto"><?php echo __("Veuillez attendre quelques instants... nous essayons de vous reconnecter.");?></span></div>
<style>
.labelClass{width:110px;position:relative;display:inline-block;vertical-align:middle;text-align: left;margin-left: 10px;}
.class_item:hover{box-shadow:2px 2px 5px grey;top:-2px;left:-2px;}
.class_item{display: inline-flex;
    margin: 3px;
    min-height: 60px;
		height:60px;
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
.selectedLangFlag{box-shadow:0 0 0 5px var(--mycolor2bis);}
#assoLangDeck .tinyFlag{margin:20px;transform:scale(1.3);}
#assoLangDeck .tinyFlag:hover{margin:20px;transform:scale(1.5);}

.cutted_sentence{text-align:left;margin:20px 0;padding:10px 20px;}
.cutted_sentence:hover{border-left:3px solid var(--mycolor2bis);}
.selectedWord{background-color:var(--mycolor2bis);color:white;border-radius:3px;padding:4px;}
.word_item:hover{background-color:var(--mycolor2bis);color:white;border-radius:3px;padding:4px;}
.word_item{padding:0 4px;margin:0 -4px;}
.selectedWord:hover{background-color:#ff3030;color:white;border-radius:3px;padding:4px;}
.choixImport{display:block;text-align:left;margin:10px;}
.colImport{min-width:300px;width:300px;text-align:left;}
.textBrutAImporter{width:80%;height:20%;}
.deck_item{position:relative;margin:10px; display:inline-block;box-shadow:0 0 5px grey;}
.selectedDeckToImport{
		webkit-box-shadow: 0px 0px 0px 8px var(--mycolor2bis);
    box-shadow: 0px 0px 0px 8px var(--mycolor2bis);
    border-radius: 0;}
.selectedDeckToImport:after{
	content: "";
	position: absolute;
	top: 0;
	left: 0;
	background-image: url(img/check2.png);
	background-repeat: no-repeat;
	background-position: center center;
	background-size: 40px 40px;
	width: 100px;
	height: 100px;
	display: block;
	opacity: 1;}
.myDecksContainer{position:relative;margin-bottom:100px;}
.class_item{border-bottom:1px solid grey;padding:5px;}
.class_item_import{display:inline-block;padding:10px;width:300px;text-align:left;margin:10px;box-shadow:0 0 10px grey;}
.class_item_import:hover{box-shadow:0 0 3px grey;}
.footerImport{text-align: center;
    position: absolute;
    left: 0;
    width: 100%;
    bottom: 0;
    background-color: white;
    box-shadow: 0 0 10px grey;
    z-index: 1;
    padding: 5px;}
	.progress{width:300px;height:10px;box-shadow: 0 0 3px lightgrey;margin:auto;}
	.progress-bar{height:10px;background-color:var(--mycolor2);}
</style>

<script>
jQuery.fn.highlightWord = function () {
    return this.each(function () {
        $(this).contents().filter(function() {
            return this.nodeType == 3;// && regex.test(this.nodeValue);
        }).replaceWith(function() {
            //var nodeValueArray=(this.nodeValue || "").split(" ");
           return "<span class='word_item'>"+(this.nodeValue || "").replace(/([\.|\xA0| |,|:|;|!|\?|-|_]+)/gm,"</span>$1<span class='word_item'>")+"</span>";
        });
    });
};
var ReverseWordList=[];

function cutTextInSentence()
{
$(".traiterText").hide();

text2cut=$(".jqte_editor").text();
text2cut+=".";
//text2cut=$(".jqteClone").text();
console.log(text2cut);
cuttedText="<div class='cutted_sentence'>"+text2cut.replace(/([\.|;|!|\?]+)/gm,"$1</div><div class='cutted_sentence'>");
$(".processedtext").html(cuttedText);
if($(".cutted_sentence").last().text().length<=1){$(".cutted_sentence").last().remove();}
$(".cutted_sentence").highlightWord();

$(".word_item").each(function(index){
	$(this).addClass("word_"+index).attr("id","word_"+index).on("click",function(){createCardFromText(index);});
	if(!(ReverseWordList[$(this).text()]!=undefined)){ReverseWordList[$(this).text()]=[];};ReverseWordList[$(this).text()].push(index);
});
updateSelectedWord();
}
function updateSelectedWord()
{
	console.log("updateSelectedWord",ReverseWordList);
	for(k in cards){
		if(typeof ReverseWordList[cards[k].mot]!="undefined"){
			for(k2 in ReverseWordList[cards[k].mot]){
					index=ReverseWordList[cards[k].mot][k2];
					$(".word_"+index).addClass("selectedWord");
					$(".word_"+index).off();
					$(".word_"+index).attr("onclick","");
					$(".word_"+index).attr("onclick","deleteCardFromText("+cards[k].card_id+","+index+");");
				}
			}
		}
}
function deleteCardFromText(card_id,index){
	console.log("deleteCardFromText");
	suppression_ligne(card_id);
	$(".word_"+index).removeClass("selectedWord");
	$(".word_"+index).attr("onclick","");
	$(".word_"+index).attr("onclick","createCardFromText("+index+");");

}
function createCardFromText(index){
	console.log("createCardFromText");
	$(".word_"+index).addClass("selectedWord");
	$(".word_"+index).off();
	$(".word_"+index).attr("onclick","");
	mot=$(".word_"+index).text();
	sentence=$(".word_"+index).closest('.cutted_sentence').text();
	sentence=sentence.replace(mot,"*"+mot+"*");
	console.log(mot,sentence);
	AddNewCardWithSentence(index,mot,sentence);
}

function retract(that)
	{$(that).parent().find('.retractable').slideToggle();
	 $(that).find("label").toggleClass("slideUpTop");
	}

for(k in cards){
	cards[k].sentences=[];
	card_id=cards[k].card_id;
	for(j in sentences[card_id])
		{
			cards[k].sentences.push({sentence_id:sentences[card_id][j].sentence_id,sentence:sentences[card_id][j].sentence});
		}
}
//si on est pas le createur:
if(droit!="admin"){$(".classManagementBlock").remove();$("#delDeckIcon").remove();$("#partageDeckIcon").remove();}

// $.getJSON("ajax.php?action=getUserTargetLang", function(result)
// {
// 	for(langRk=0;langRk<result.length;langRk++)
// 	{
// 		$(".select_lang").append("<option value='"+result[langRk].lang_id+"'>"+result[langRk].lang_name+"</option>");
// 	}
// 	if(result.length>0){
// 		$(".select_lang").val(result[0].lang_id);
// 	}
// });

socket.on('changeEditorWrite', function() {
	$.getJSON("ajax.php?action=checkEditionRight&deck_id="+myDeck.deck_id, function(result){
		console.log("changeEditorWrite",result);
		if(result=="out"){
			window.location.href='decks.php?categorie=last';
		}
	});
});
socket.on('disconnect', function() {
  console.log("DisConnected");
	$(".bodyContent").css({"filter":"blur(10px)"});
	$("#disconnected").show();
});
socket.on('connected', function() {
  console.log("Connected");
	socket.emit('join_edit', deck_id, function(result){
    console.log(result);
		$(".bodyContent").css({"filter":"none"});
		$("#disconnected").hide();
  });
});
socket.on('reconnect', function() {
  console.log("ReConnected");
	socket.emit('join_edit', deck_id, function(result){
    console.log(result);
		$(".bodyContent").css({"filter":"none"});
		$("#disconnected").hide();
  });
});

socket.on('cardUpdate', function(data){
	updatedStuf=data.updatedStuf;
	dataCard=data.dataCard;
	card_id=data.dataCard.card_id;
	if($("#ligne_carte_"+card_id).length==0){
		//création de la cartes
			cards[nbre_ligne]=dataCard;
			console.log("New dans card_update");
			creer_ligne(nbre_ligne);
	}
	else{//la carte existe déja et c'est une modif
		cardExist=false;
		rkCard=cards.length;
		for(k in cards){if(cards[k].card_id==card_id){cardExist=true;rkCard=k;}}
		if(!cardExist){cards.push(dataCard);}
		console.log("updatedStuf",updatedStuf,"dataCard",dataCard);
		cards[rkCard]=dataCard;
		console.log("rkCard",rkCard);
		switch (updatedStuf) {
  		case 'mot':
			$("#ligne_carte_"+card_id).find(".mot_trad_card").val(dataCard.mot_trad);
			$("#ligne_carte_"+card_id).find(".mot_card").val(dataCard.mot);
    	break;
			case 'image':
				if(dataCard.hasImage>0){
					cacheBreaker=new Date().getTime();
					$('#ligne_carte_'+card_id).find(".edit_front").css("background-image","url(card_img/card_"+dataCard.hasImage+".png?v="+cacheBreaker+")");
					$('#ligne_carte_'+card_id).find(".addContentIconImage").removeClass("addBubbleIcon").addClass("delBubbleIcon").off().attr("onclick",'del_card_img('+card_id+');');
				}
				else{
					$('#ligne_carte_'+card_id).find(".edit_front").css("background-image","url(img/default_card.png)");
					$('#ligne_carte_'+card_id).find(".addContentIconImage").removeClass("delBubbleIcon").addClass("addBubbleIcon").off().attr("onclick",'showOnTop('+card_id+');');
				}
    	break;
			case 'audio':
				if(dataCard.hasAudio=="1"){
					$('#ligne_carte_'+card_id).find(".icon_audio").show();
					$('#ligne_carte_'+card_id).find(".addContentIconAudio").show();
					$('#ligne_carte_'+card_id).find(".addContentIconAudio").addClass("delBubbleIcon").off().attr('onclick','del_audio('+card_id+');');
				}else{
					$('#ligne_carte_'+card_id).find(".addContentIconAudio").hide();
					$('#ligne_carte_'+card_id).find(".icon_audio").hide();
					$('#ligne_carte_'+card_id).find(".addContentIconAudio").addClass("addBubbleIcon");}
    	break;
			case 'updateSentence':
			sentence_id=data.sentence_id;
			for(k in dataCard.sentences)
			{		if(dataCard.sentences[k].sentence_id==sentence_id){
					sentence=dataCard.sentences[k].sentence;
					sentence=sentence.replace("*","<span style='color:orange;'>",1);
					sentence=sentence.replace("*","</span>",1);
					showSentenceInCard(card_id,sentence_id,sentence);}
			}
    	break;
			case 'deleteSentence':
			sentence_id=data.sentence_id;
			$("#sent_"+sentence_id).remove();

    	break;
			case 'alert':
				if(dataCard.alert=="1"){
					$('#ligne_carte_'+card_id).find(".msgAlertcard").show();
		      $("#ligne_carte_"+card_id).find(".alert_button_ligne").addClass("alertON_icon").removeClass("alertOFF_icon");
					$("#ligne_carte_"+card_id).addClass("alertEditON");
					$("#ligne_carte_"+card_id).find(".msgAlertcard").html(dataCard.comment);
				}
				else{
					$("#ligne_carte_"+card_id).find(".alert_button_ligne").removeClass("alertON_icon").addClass("alertOFF_icon");
					$("#ligne_carte_"+card_id).removeClass("alertEditON");
					$("#ligne_carte_"+card_id).find(".alert_button_ligne").html("");
					$('#ligne_carte_'+card_id).find(".msgAlertcard").hide();
				}
    	break;
		}
	}
	$('#ligne_carte_'+card_id).addClass('Updatedcard');
	$(".Updatedcard").on("animationend",function(){$(this).removeClass("Updatedcard");})
});
//gestion des contenus globale
socket.on('GCUpdate', function(data){
	GContent=data.GContent;
	UpdateGlobalContentIcon();
});
socket.on('deckUpdate', function(data){
	//on met a jours le titre et l'image du deck
	//data contient les nouvelles valeurs
	$('.titre_deck_edition').val(data.myDeck.deck_name);
	console.log(data.myDeck)
	//cas 1:il y avait une image, on l'a effacé:
	if(data.myDeck.hasImage==0 && myDeck.hasImage>0){
		$('.img_deck_preview').attr('src','img/default_deck.png');
			$('#addContentIconImageDeck').removeClass("delBubbleIcon").addClass("addBubbleIcon").off().attr("onclick",'showOnTopDeck();');
	}
	//cas 2:il n'y avait pas d'image, on en a ajouté une.
	if(data.myDeck.hasImage>0 && myDeck.hasImage==0){
		cacheBreaker=new Date().getTime();
		$('.img_deck_preview').attr('src','deck_img/deck_'+data.myDeck.hasImage+'.png?v='+cacheBreaker);
		$('#addContentIconImageDeck').removeClass("addBubbleIcon").addClass("delBubbleIcon").off().attr("onclick",'del_deck_img();');
	}
	myDeck=data.myDeck;
});

GContent={"Audio":myDeck.hasAudio,"Image":myDeck.hasPoster,"Link":myDeck.link,"Text":myDeck.texte,"Video":myDeck.youtube_id,}

function UpdateGlobalContentIcon(){
	$('#previewArea').hide();
	$('#previewArea').html('');
	$("#addContentIcon").off();
	$("#addContentIconVideo").off();
	$("#addContentIconAudio").off();
	$("#addContentIconImage").off();
	$("#addContentIconText").off();
	if(GContent.Video==""){$("#addContentIconVideo").removeClass("delBubbleIcon").addClass("addBubbleIcon");
		$("#addContentIconVideo").on("click",function(){showGlobalWindow('Video');});}
	else{$("#addContentIconVideo").removeClass("addBubbleIcon").addClass("delBubbleIcon");
		$("#addContentIconVideo").on("click",function(){removeGC('Video');});
		$('#previewArea').append('<div class="video-container"><div class="AllGlobalContent"><iframe allowFullScreen="allowFullScreen" src="https://www.youtube.com/embed/'+GContent.Video+'" width="640" height="352" frameborder="0"></iframe></div></div>');
	}
	if(GContent.Audio==0){$("#addContentIconAudio").removeClass("delBubbleIcon").addClass("addBubbleIcon");
		$("#addContentIconAudio").on("click",function(){showGlobalWindow('Audio');});
	}
	else{$("#addContentIconAudio").removeClass("addBubbleIcon").addClass("delBubbleIcon");
		$("#addContentIconAudio").on("click",function(){removeGC('Audio');});
		cacheBreaker=new Date().getTime();
		$('#previewArea').append("<div class='AllGlobalContent'><audio controls='controls'><source src='deck_audio/deck_"+deck_id+".wav?v="+cacheBreaker+"'></source></audio></div>");
	}
	if(GContent.Image==0){$("#addContentIconImage").removeClass("delBubbleIcon").addClass("addBubbleIcon");
		$("#addContentIconImage").on("click",function(){showGlobalWindow('Image');});}
	else{$("#addContentIconImage").removeClass("addBubbleIcon").addClass("delBubbleIcon");
		$("#addContentIconImage").on("click",function(){removeGC('Image');});
		cacheBreaker=new Date().getTime();
		$('#previewArea').append("<div class='AllGlobalContent'><img class='posterPreview' src='deck_poster/deck_"+deck_id+".png?v="+cacheBreaker+"'></div>");
	}
	if(GContent.Text==""){$("#addContentIconText").removeClass("delBubbleIcon").addClass("addBubbleIcon");
		$("#addContentIconText").on("click",function(){showGlobalWindow('Text');});}
	else{$("#addContentIconText").removeClass("addBubbleIcon").addClass("delBubbleIcon");
	$("#addContentIconText").on("click",function(){showGlobalWindow('Text');});
		$('#previewArea').append(`<div class="AllGlobalContent" onclick="showGlobalWindow('Text');">`+GContent.Text+`</div>`);
	}
	if($('#previewArea').html()!=""){
		$('#previewArea').show();
		$(".globalContentBlock").find('.retractable').slideDown();;
		$(".globalContentBlock").find("label").removeClass("slideUpTop");
	}

}
UpdateGlobalContentIcon();
//creer fonction pour ouverture de la fenetre avec le bon content loader
//créer une fonction pour gerer l'upload
function showLangDeckFlag()
{
	$.getJSON("ajax.php?action=getUserTargetLang", function(result){
		console.log(result);
		for(k in result)
		{
			lang_code2=result[k].lang_code2;
			lang_id=result[k].lang_id;
			lang_name=result[k].lang_name;
			$("#assoLangDeck").append(`<span title='`+lang_name+`' onclick='changeLanguageDeck(`+lang_id+`);' class='lang_flag_deck lang_flag_`+lang_id+` tinyFlag flag_`+lang_code2+`'></span>`);
		}
		$("#assoLangDeck .flag_"+myDeck.lang_code2).addClass("selectedLangFlag");
		$("#assoLangDeck").append(`<a href='#' title='<?php echo __("plus de langues");?>' onclick='getMoreLanguageDeck();return false;' class='more_lang_flag_deck'><?php echo __("plus");?></a>`);
		showClasses();
	});
}
function getMoreLanguageDeck(){

  $.getJSON("ajax.php?action=getTargetLang", function(result){
    for(k in result)
    {
      lang_code2=result[k].lang_code2;
      lang_id=result[k].lang_id;
      lang_name=result[k].lang_name;
      if($('.lang_flag_'+lang_id).length==0){
      $("#assoLangDeck").append(`<span onclick="changeLanguageDeck(`+lang_id+`);" title='`+lang_name+`' class='lang_flag_deck lang_flag_`+lang_id+` tinyFlag flag_`+lang_code2+`'></span>`);
      }
    }
		$(".more_lang_flag_deck").remove();
	});
}
function changeLanguageDeck(lang_id){
	$.getJSON("ajax.php?action=updateDeckLang&lang_id="+lang_id+"&deck_id="+myDeck.deck_id, function(result)
  {
		target_lang_id=lang_id;
		console.log(result);
  $("#assoLangDeck .selectedLangFlag").removeClass("selectedLangFlag");
	$("#assoLangDeck .lang_flag_"+lang_id).addClass("selectedLangFlag");
	myDeck.lang_id=result.langData.lang_id;
	myDeck.lang_code2=result.langData.lang_code2;
	myDeck.lang_code2_2=result.langData.lang_code2_2;
	myDeck.lang_code3=result.langData.lang_code3;
	showClasses();
  });
}
showLangDeckFlag();
function showWindow(){
	$('.fenetreSombre').remove();
	$('body').append("<div class='fenetreSombre' onclick='$(this).remove();'><div style='text-align:center;' class='fenetreClaire' onclick='event.stopPropagation();'>"
	+"</div></div>");
}
function partage_deck()
	{showWindow();
	$('.fenetreClaire').css("min-height","600px");
	$('.fenetreClaire').append("<h3><?php echo __("Partage de la liste");?></h3>");
	$('.fenetreClaire').append(`<p>
			<input type='email' placeholder='<?php echo __("Prénom ou mail");?>' class='emailInputPartage'>
			<select class='droitPartage'></select>
			<button class='btnStd1' onclick='lookForUser();'>
				<?php echo __("Rechercher");?>
			</button>
			<div style="position:relative;height:1;">
				<div class="lookForUser_result"></div>
			</div>
			<div class="askForUser_feedback"></div>
		</p>`);
	$('.droitPartage').append("<option value='lecture'><?php echo __("Consultation");?></option>");
	$('.droitPartage').append("<option value='modif'><?php echo __("Edition");?></option>");
	$('.droitPartage').append("<option value='admin'><?php echo __("Propriété");?></option>");
	$('.fenetreClaire').append("<div class='messagePartage' style='display:none;color:red;'></div>");
	$('.fenetreClaire').append("<p class='user_partage'></p>");
	getUser();
	}
function lookForUser(){
	input=$(".emailInputPartage").val();
  $.getJSON("ajax.php?action=getUserFromMailName&input="+input, function(usersFound)
    {
      $(".lookForUser_result").show();
      $(".lookForUser_result").html(`<div class='close_icon' style="float:right;width:20px;height:20px;" onclick='$(this).parent().html("");'></div><br>`);
      $(".userFound_item").remove();
      var usersFoundUseful=0;
      for(var rk in usersFound)
      {
        friend_id=usersFound[rk].user_id;
        friend_name=usersFound[rk].first_name+" "+usersFound[rk].last_name;
        friend_avatar_id=usersFound[rk].avatar_id;
        if($("#userFound_item_"+friend_id).length==0)
          { usersFoundUseful++;
            $(".lookForUser_result").append(
              `<div id='userFound_item_`+friend_id+`' class='userFound_item'>
                <div class='avatar'>
                  <img src='avatar/avatar_`+friend_avatar_id+`_XS.png' class='avatar_img avatar_XS'>
                </div>
                <div class='user_name_menu'>`+friend_name+`</div>
                <button style="justify-self:end;" class='btnStd2' onclick='partageWithMail(`+friend_id+`);'><?php echo __("Partager");?></button>
              </div>`);
          }
      }
      if(usersFoundUseful==0){$(".lookForUser_result").append("<?php echo __("Introuvable sur ExoLingo");?>");}
    });
}
function getUser(){
	$.getJSON("ajax.php?action=getUserWithDroit&deck_id="+myDeck.deck_id, function(result){
		$('.user_partage').html();
		intro='<?php echo __("Déjà partagé avec ");?>';
		for(k in result){
			if(result[k].user_id!=user_id)
			{	$('.user_partage').append(intro+toTitleCase(result[k].first_name+" "+result[k].last_name)+" en "+result[k].droit+"<br>");
				intro='';
			}
		}
	});
}
function partageWithMail(user_id)
{
	droit=$('.droitPartage').val();
	$.getJSON("ajax.php?action=setUserWithDroit&deck_id="+myDeck.deck_id+"&sharedUser_id="+user_id+"&droit="+droit, function(result){
		if(result=="ok"){
			$(".messagePartage").hide();
			getUser();
		}
		else{
			$(".messagePartage").html(result);
			$(".messagePartage").show();
		}
	});
}
function showGlobalWindow(typeContent)
{
	showWindow();
	$('#contentContainer'+typeContent).clone().appendTo('.fenetreClaire');
	if(typeContent=="Text"){
		$(".fenetreClaire").html(`<div class="addContentContainer" id="contentContainerText">
			<div class="backIcon" onclick="$('.fenetreSombre').remove();"></div>
			<div class="addContentTitre"><?php echo __("Texte");?></div>
			<div class="addContent">
				<textarea class="editor" name="textGlobalContent"></textarea>
			</div>
			<div style="text-align:center;">

				<div class="traiterText BtnStd1" onclick="cutTextInSentence();">Découper en phrase</div>
				<div class='processedtext'></div>
				<div class="annuler"><?php echo __("Annuler");?></div>
				<div class="valider"><?php echo __("Valider");?></div>
			</div>
		</div>`);
		$('.fenetreClaire').find(".editor").addClass("activEditor");
		$(".activEditor").jqte({"status":true,outdent: false,indent:false,strike:false,sup:false,sub:false,source:false,remove:false,rule:false,format:false,change: function(){$('.fenetreClaire').find(".valider,.annuler,.traiterText").show();}});
		$(".activEditor").jqteVal(GContent.Text);
	}
	else if(typeContent=="Video"){
	$(".videoInput").on("focus",function(){$('.fenetreClaire').find(".valider,.annuler").show();});
	}
	else{}
	$('.fenetreClaire').find(".valider").hide().on("click",function(){validerGC(typeContent);});
	$('.fenetreClaire').find(".annuler").hide().on("click",closeWindow);
}
function removeGC(typeContent)
{
	if(typeContent=="Text"){
				UpdateGlobalContentIcon();
				socket.emit("GCUpdate",{GContent:GContent});
		}
	else if(typeContent=="Audio"){
		$.ajax({url:"ajax.php?action=deckHasNoAudio&deck_id="+deck_id,success:function(){
			GContent.Audio=0;
			UpdateGlobalContentIcon();
			socket.emit("GCUpdate",{GContent:GContent});
		}});
	}
	else if(typeContent=="Video"){
		$.ajax({url:"ajax.php?action=deckHasNoVideo&deck_id="+deck_id,success:function(){
			GContent.Video="";
			UpdateGlobalContentIcon();
			socket.emit("GCUpdate",{GContent:GContent});
		}});
	}
	else if(typeContent=="Image"){
		$.ajax({url:"ajax.php?action=deckHasNoPoster&deck_id="+deck_id,success:function(){
			GContent.Image=0;
			UpdateGlobalContentIcon();
			socket.emit("GCUpdate",{GContent:GContent});
		}});
	}

}
function validerGC(typeContent){
	console.log("validerGC");
	if(typeContent=="Text")
	{console.log("text");
	GContent.Text=$(".activEditor").val();
	socket.emit("GCUpdate",{GContent:GContent});
	$.post("ajax.php?action=deckHasText&deck_id="+deck_id,{texte:GContent.Text},closeWindow);
	UpdateGlobalContentIcon();
	}
	else if(typeContent=="Image")
	{
	console.log("Image");
	var formData = new FormData();
	formData.append("type", "deck_poster");
	formData.append("id", deck_id);
	formData.append("fileToUpload", file);
	$(".fenetreClaire").find(".previewPoster").after('<div class="progress">'+
			'<div class="upload_progress progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: 10%"></div>'+
	'</div>');
	var request = new XMLHttpRequest();
	request.upload.onprogress = function (evt) {
  var percentComplete = parseInt(evt.loaded *100/ evt.total);
  			   $('.upload_progress').css("width",percentComplete+'%');
	}
	request.onreadystatechange = function() {
    if (request.readyState == XMLHttpRequest.DONE) {
        console.log(request.responseText);
				$.getJSON("ajax.php?action=deckHasPoster&deck_id="+myDeck.deck_id, function(result){});
				GContent.Image=1;
				socket.emit("GCUpdate",{GContent:GContent});
				UpdateGlobalContentIcon();
				closeWindow();
		}
	}
	request.open("POST", "upload.php");
	request.send(formData);
	}
	else if(typeContent=="Video")
	{
	console.log("Video");
	GContent.Video=$('.fenetreClaire').find(".videoInput").val();
	socket.emit("GCUpdate",{GContent:GContent});
	$.getJSON("ajax.php?action=deckHasVideo&deck_id="+deck_id+"&youtube_id="+GContent.Video,function(result){if(result.status=="done"){console.log(result);closeWindow();GContent.Video=result.youtube_id;UpdateGlobalContentIcon();}else{alert(result.status);}});

	}
	else if(typeContent=="Audio")
	{
		console.log("Audio",deck_id);
		var formData = new FormData();
		formData.append("type", "deck_audio");
		formData.append("id", deck_id);
		formData.append("fileToUpload", file);
		$(".fenetreClaire").find(".previewAudio").after('<div class="progress">'+
				'<div class="upload_progress progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: 10%"></div>'+
		'</div>');
		var request = new XMLHttpRequest();
		request.upload.onprogress = function (evt) {
	  var percentComplete = parseInt(evt.loaded *100/ evt.total);
	  			   $('.upload_progress').css("width",percentComplete+'%');
		}
		request.onreadystatechange = function() {
	    if (request.readyState == XMLHttpRequest.DONE) {
	        console.log(request.responseText);
					$.getJSON("ajax.php?action=deckHasAudio&deck_id="+myDeck.deck_id, function(result){});
					GContent.Audio=1;
					UpdateGlobalContentIcon();
					socket.emit("GCUpdate",{GContent:GContent});
					closeWindow();
			}
		}
		request.open("POST", "upload.php");
		request.send(formData);
	}


}

function closeWindow()
	{$(".fenetreSombre").remove();}


var cacheBreaker=new Date().getTime();
var record_flag=false;
var nbre_ligne=0;
var img_card_id=0;
$("#actionsOnDeckContainer").append('<div class="preview_button buttonActionDeck"><a class="jumelle_icon icon_back" title="<?php echo __("visualiser la liste");?>" href="cards.php?deck_id='+deck_id+'"></a></div>');
//if(user_id==7){$("#actionsOnDeckContainer").append('<div class="translate_button buttonActionDeck"><a class="translate_icon icon_back" title="<?php //echo __("traduire la liste");?>" href="#" onclick="translateDeckMenu();"></a></div>');}

//Alert
alertDeck=myDeck.alertDeck;
if(alertDeck=="1"){alertClass="alertDeckON";}else{alertClass="alertDeckOFF";}
$("#actionsOnDeckContainer").append('<div class="buttonActionDeck"><a href="#" class="'+alertClass+' alertDeck" title="<?php echo __("Signaler un problème sur la liste");?>" onclick="toggleAlertDeck('+myDeck.deck_id+');event.stopPropagation();"></a></div>');
//partage
if(droit=="admin")
{$("#actionsOnDeckContainer").append('<div class="buttonActionDeck partage_button" id="partageDeckIcon" title="<?php echo __("Partager la liste");?>" onclick="partage_deck();"><a href="#" class="partage_icon icon_back"></a></div>');}
//name, visibilité, image
$('.titre_deck_edition').val(myDeck.deck_name);

if(myDeck.hasImage>0){
	$('.img_deck_preview').attr('src','deck_img/deck_'+myDeck.hasImage+'.png?v='+cacheBreaker);
	//$('.del_deck_img').show();
	//$('.browse_deck_img').hide();
	$('#addContentIconImageDeck').removeClass("addBubbleIcon").addClass("delBubbleIcon").off().attr("onclick",'del_deck_img();');

}
else{
	$('.img_deck_preview').attr('src','img/default_deck.png');
	$('#addContentIconImageDeck').removeClass("delBubbleIcon").addClass("addBubbleIcon").off().attr("onclick",'showOnTopDeck();');

}

//CARDS
for(rg in cards)
{
	//if(rg%2==0){parite_ligne="ligne_pair";}else{parite_ligne="ligne_impair";}
	creer_ligne(rg)
}

//==============FUNCTIONS===========
//get all class from $school
//add classes to "#assoClass"
function showClasses()
{
//$(".class_item").remove();
$(".class_status_build").remove();
classEtat=[];
for(class_id in classes){
	check_value="";
	visibilityIcon="";
	hiddenClass="hidden";
	class_name=classes[class_id]["class_name"];
	role=classes[class_id]["role"];
	classLang=classes[class_id]["lang_code2"];
	promo=classes[class_id]["promo"];
	deck_status=classes[class_id]["status"];
	classStatus=classes[class_id]["classStatus"];
	visiClass="";
	if(classStatus!="perso" && classStatus!="explore" && classLang==myDeck.lang_code2)
	{
		if(myDeck.class_id.indexOf(class_id)!=-1){//le deck est dans la class
			hiddenClass="";
			check_value="checked";
			if(myDeck.visible[class_id]==0){visiClass="novisi";}
			visibilityIcon="";
		}
		//$("#assoClass").append("<div class='class_item' id='class_item_"+class_id+"'>"
		//+"<input type='checkbox' onchange='toggleClass("+class_id+");' value='"+class_id+"' "+check_value+">"
		//+"<div class='labelClass'>"+class_name+"<br><span class='promo'>"+promo+"</span>"
		//+"<div class='visibilityIcon "+visiClass+" "+hiddenClass+"' onclick='toggleVisi("+class_id+");'>"
		//+"</div></div></div>");
		//console.log(class_name,deck_status);

		if(typeof(deck_status)=="undefined"){deck_status="build";}
		classEtat[class_id]=deck_status;
		//$("#assoClass").append("<div onclick='changeStatus("+class_id+",\""+role+"\");' class='class_item' id='class_item_"+class_id+"'>"
		//	+"<div class='labelClass'><span class='status_txt'></span><span class='class_nameSpan'>"+class_name+"</span><br><span class='promo'>"+promo+"</span>"
		//	+"</div></div>");
		if($("#class_item_"+class_id).length==0)
		{
		$("#assoClass").append(`<div class='class_item class_lang_`+classLang+`' id='class_item_`+class_id+`'>
			<img src='img/icon_`+role+`.png' style='width:30px;filter:invert(1);object-fit: contain;'>
			<span class='tinyFlag flag_`+classLang+`' style="margin:auto 5px;"></span>
			<div class='labelClass'><span class='status_txt'></span><span class='class_nameSpan'>`+class_name+`</span><br><span class='promo'>`+promo+`</span></div>
			<img class='scaleOver' src="img/partage.png" width="25px" height="25px" onclick="proposePartageProf(`+class_id+`);">
			<div class='selectField'><select class='statusState' onchange='changeStatus(`+class_id+`);'></select></div>
			</div>`);
			if(role=="prof"){
				etats=[{status:"build",class_status_txt:"<?php echo __("Liste cachée");?>"},
							 {status:"ok",class_status_txt:"<?php echo __("Liste visible");?>"},
							 //{status:"waiting",class_status_txt:"Liste en attente de validation par un maître"},
							 {status:"coop",class_status_txt:"<?php echo __("Liste en construction coopérative");?>"}];
				}
			else{
				etats=[{status:"build",class_status_txt:"<?php echo __("Liste cachée");?>"},
						 {status:"waiting",class_status_txt:"<?php echo __("Liste en attente de validation par un maître");?>"}];}
			for(k in etats)
			{$("#class_item_"+class_id).find(".statusState").append("<option value='"+etats[k].status+"'>"+etats[k].class_status_txt+"</option>");}
			afficherClassStatus(class_id,deck_status);
		}
	}
}
if($(".class_item:visible").length==0){$(".noClassInfo").show();}else{$(".noClassInfo").hide();}
}
//créer la fonction avec Ajax pour associer deck et class
function afficherClassStatus(class_id,status)
{
	class_status_txt="";
	//if(status=="build"){class_status_txt="Aucune action sur cette classe";}
	//else if(status=="ok"){class_status_txt="Liste validée";}
	//else if(status=="waiting"){class_status_txt="Liste en attente de validation";}
	//else if(status=="coop"){class_status_txt="Liste en construction coopérative";}
	class_status="class_status_"+status;
	$("#class_item_"+class_id).removeClass("class_status_build class_status_ok class_status_waiting class_status_coop");
	$("#class_item_"+class_id).addClass(class_status);
	//$("#class_item_"+class_id).find(".status_txt").html(class_status_txt);
	$("#class_item_"+class_id).find(".statusState").val(status);
}

function changeStatus(class_id,role)
{
	//if(role=="prof"){etats=["build","ok","waiting","coop"];}
	//else{etats=["build","waiting"];}
	//status=classEtat[class_id];
	//newStatus=etats[(etats.indexOf(status)+1)%etats.length];
	newStatus=$("#class_item_"+class_id).find(".statusState").val();
	classEtat[class_id]=newStatus;
	console.log(newStatus)
	if(newStatus=="build"){
		$.getJSON("ajax.php?action=removeDeckFromClass&class_id="+class_id+"&deck_id="+myDeck.deck_id, function(result){
			socket.emit('changeEditorWrite');
			console.log('changeEditorWrite');
		});

	}
	else if(newStatus=="ok"){
		$.getJSON("ajax.php?action=addDeckToClass&class_id="+class_id+"&deck_id="+myDeck.deck_id, function(result){
			socket.emit('changeEditorWrite');
			console.log('changeEditorWrite');
		});
	}
	else if(newStatus=="waiting"){
		$.getJSON("ajax.php?action=ProposeDeckToClass&class_id="+class_id+"&deck_id="+myDeck.deck_id, function(result){});
		proposePartageProf(class_id);
	}
	else if(newStatus=="coop"){
		$.getJSON("ajax.php?action=ProposeDeckCoopToClass&class_id="+class_id+"&deck_id="+myDeck.deck_id, function(result){});
	}
	afficherClassStatus(class_id,newStatus);
}
function proposePartageProf(class_id){
	showWindow();
	$(".fenetreClaire").append("<h3><?php echo __("Droit de partage avec les maîtres de cette classe");?></h3>");
	$(".fenetreClaire").append("<p><select class='droitPartageProf'><option value='lecture' selected><?php echo __("Consultation");?></option><option value='modif'><?php echo __("Modification");?></option><option value='admin' selected><?php echo __("Propriété");?></option></select><div class='btnStd1' style='float:right;' onclick='giveDroitToProf("+class_id+");'><?php echo __("Partager");?></div></p>");

}
function giveDroitToProf(class_id){
	droit=$(".droitPartageProf").val();
	if(droit!='lecture')
	{$.getJSON("ajax.php?action=SetDroitToProf&class_id="+class_id+"&deck_id="+myDeck.deck_id+"&droit="+droit, function(result){});
	}
	$(".fenetreSombre").remove();
}

// function translateDeckMenu(){
// 	showWindow();
// 	$(".fenetreClaire").append("<h3>Langue source</h3><select class='select_lang_ini'><option value='fr' selected>Français</option><option value='en' selected>English</option></select>")
// 	$(".fenetreClaire").append("<h3>Langue cible</h3>"+
// 	"<select class='select_lang_fin'>"+
// 	"	<option value='fr' >Français</option>"+
// 	"	<option value='en' selected>English</option>"+
// 	"	<option value='ru' >Russe</option>"+
// 	"	<option value='lt' >Lituanian</option>"+
// 	"	<option value='ar' >Arabe</option>"+
// 	"	<option value='eo' >Esperanto</option>"+
// 	"	<option value='zh-CN ' >Chinois</option>"+
// 	"	<option value='ru' >Russe</option>"+
// 	"	<option value='de' >Allemand</option>"+
// 	"	<option value='el' >Grec</option>"+
// 	"	<option value='it' >Italian</option>"+
// 	"	<option value='ga' >Irish</option>"+
// 	"	<option value='es' >Espagnol</option>"+
// 	"</select>");
// 	$(".fenetreClaire").append("<br><button onclick='translateDeck();'>Traduire</button>");
// }
// function translateDeck()
// {
// 	lang_fin=$('.select_lang_fin').val();
// 	lang_ini=$('.select_lang_ini').val();
// 	$.getJSON("ajax.php?action=translateDeck&deck_id="+myDeck.deck_id+"&lang_ini="+lang_ini+"&lang_fin="+lang_fin, function(result){location.reload();});
// }
function toggleVisi(class_id)
{
	event.stopPropagation();
	visiIcon=$("#class_item_"+class_id+" > .labelClass >.visibilityIcon");
	if(visiIcon.hasClass("novisi"))
	{visi=1;visiIcon.removeClass("novisi");}
	else{visi=0;visiIcon.addClass("novisi");}

	$.getJSON("ajax.php?action=SetVisi&class_id="+class_id+"&deck_id="+myDeck.deck_id+"&visi="+visi, function(result){});
}
function toggleClass(class_id)
{
	event.stopPropagation();
	classInput=$("#class_item_"+class_id+" > input");
	visiIcon=$("#class_item_"+class_id+" > .labelClass >.visibilityIcon");
	if(!classInput.is(':checked'))
	{visiIcon.hide();$.getJSON("ajax.php?action=removeDeckFromClass&class_id="+class_id+"&deck_id="+myDeck.deck_id, function(result){});}
	else{visiIcon.show();visiIcon.addClass("novisi");$.getJSON("ajax.php?action=addDeckToClass&class_id="+class_id+"&deck_id="+myDeck.deck_id, function(result){});}
}

function delete_deck()
{
	if(confirm("<?php echo __("Etes-vous sur de vouloir supprimer cette liste de vocabulaire ?");?>"))
	{
	$.getJSON("ajax.php?action=deleteDeck&deck_id="+myDeck.deck_id, function(result){
		console.log(result);
		if(result.status=="ok")
		{
			window.location="decks.php?categorie=last";
		}
	});
	}
}
function MAJ_deck(){
	new_deck_name=$('.titre_deck_edition').val();
	myDeck.deck_name=new_deck_name;
	new_deck_classe=$('.select_class').val();
	if(new_deck_classe==null){new_deck_classe="myDecks";}
	socket.emit('deckUpdate',{myDeck:myDeck});
	$.getJSON("ajax.php?action=updateDeck&deck_id="+myDeck.deck_id+"&deck_name="+new_deck_name+"&deck_classe="+new_deck_classe, function(result){
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
			socket.emit('cardUpdate',{dataCard:cards[k],updatedStuf:"mot"});
		}
	}
}

function del_deck_img()
{
	deck_id=myDeck.deck_id;
	$.getJSON("ajax.php?action=deckHasNoImage&deck_id="+myDeck.deck_id, function(result){});
	$('.img_deck_preview').attr('src','img/default_deck.png');
	$('#addContentIconImageDeck').removeClass("delBubbleIcon").addClass("addBubbleIcon").off().attr("onclick",'showOnTopDeck();');

	//$('.del_deck_img').hide();
	//$('.browse_deck_img').show();
	$('.browse_deck_img').val('');
	myDeck.hasImage=0;
	socket.emit('deckUpdate',{myDeck:myDeck});
}

function upload_card_img()
{
	$("#onTopBottom").html('');
	card_id=img_card_id;
	file=document.getElementById("input_image_card").files[0];
	var formData = new FormData();
	formData.append("type", "card_img");
	formData.append("id", card_id);
	formData.append("fileToUpload", file);
	var request = new XMLHttpRequest();
	request.onreadystatechange = function() {
    if (request.readyState == XMLHttpRequest.DONE) {
        console.log(request.responseText);
				$.getJSON("ajax.php?action=cardHasImage&card_id="+card_id, function(result){
					for(k in cards){
						if(cards[k].card_id==card_id){
							cards[k].hasImage=card_id;
							socket.emit('cardUpdate',{dataCard:cards[k],updatedStuf:"image"});
						}
					}
				});
				cacheBreaker=new Date().getTime();
				console.log("carte affiché : "+card_id);
				$('#ligne_carte_'+card_id).find(".edit_front").css("background-image","url(card_img/card_"+card_id+".png?v="+cacheBreaker+")");
				$('#ligne_carte_'+card_id).find(".addContentIconImage").removeClass("addBubbleIcon").addClass("delBubbleIcon").off().attr("onclick",'del_card_img('+card_id+');');
				//$("#del_img_"+card_id).html('<img class="del_img icon_ecriture" onclick="del_card_img('+card_id+');" src="img/del_image.png" width="40px">');
		}
	}
	request.open("POST", "upload.php");
	request.send(formData);
}
function del_card_img(card_id)
{$.getJSON("ajax.php?action=cardHasNoImage&card_id="+card_id, function(result){
	for(k in cards){
		if(cards[k].card_id==card_id){
			cards[k].hasImage=0;
			socket.emit('cardUpdate',{dataCard:cards[k],updatedStuf:"image"});
		}
	}
	});
	$('#ligne_carte_'+card_id).find(".edit_front").css("background-image","url(img/default_card.png)");
	$('#ligne_carte_'+card_id).find(".addContentIconImage").removeClass("delBubbleIcon").addClass("addBubbleIcon").off().attr("onclick",'showOnTop('+card_id+');');
}
function upload(that){
	deck_id=myDeck.deck_id;
  file=that.files[0];
	$("#deck_preview_manager").show();
	$("#deck_preview_manager").html('<div class="progress">'+
			'<div class="upload_progress progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: 10%"></div>'+
	'<div class="addContentIconCard addContentIconImage" id="addContentIconImageDeck"></div></div>');

    if (file.size > 50*1024*1024) {
        alert('max upload size is 50M')
    }
	var formData = new FormData();
	formData.append("type", "deck_img");
	formData.append("id", deck_id);
	formData.append("fileToUpload", file);
	var request = new XMLHttpRequest();
	request.upload.onprogress = function (evt) {
               var percentComplete = parseInt(evt.loaded *100/ evt.total);
  			   $('.upload_progress').css("width",percentComplete+'%');
	}

	request.onreadystatechange = function() {
    if (request.readyState == XMLHttpRequest.DONE) {
        console.log(request.responseText);
				$.getJSON("ajax.php?action=deckHasImage&deck_id="+myDeck.deck_id, function(result){
					myDeck.hasImage=myDeck.deck_id;
					socket.emit('deckUpdate',{myDeck:myDeck});
				});
				cacheBreaker=new Date().getTime();
				$("#deck_preview_manager").html('<img reload src="deck_img/deck_'+deck_id+'.png?v='+cacheBreaker+'" class="img_deck_preview" ><div class="addContentIconCard addContentIconImage" id="addContentIconImageDeck"></div>');
				$('#addContentIconImageDeck').removeClass("addBubbleIcon").addClass("delBubbleIcon").off().attr("onclick",'del_deck_img();');
				$("#onTopBottom").html("");
				//$('.del_deck_img').show();
				//$('.browse_deck_img').hide();
		}
	}
	request.open("POST", "upload.php");
	request.send(formData);

}
var file;
function uploadGC(that,typeContent){
  file=that.files[0];
	if(that.files && file)
	{
		var reader=new FileReader();
		if(typeContent=="Image"){
			reader.onload=function(e){$('.fenetreClaire').find(".previewPoster").attr('src',e.target.result);}
		}
		else if(typeContent=="Audio"){
			reader.onload=function(e){$('.fenetreClaire').find(".previewAudio").append("<audio controls='controls'><source src='"+e.target.result+"'></source></audio>");}
		}
		reader.readAsDataURL(file);
	}
	$('.fenetreClaire').find(".previewPoster").show();
	$('.fenetreClaire').find(".previewAudio").show();
	$('.fenetreClaire').find(".valider,.annuler").show();
    if (file.size > 50*1024*1024) {
        alert('max upload size is 50M')
    }
}
function tradDef()
{console.log("à définir");}

function AddNewCardWithSentence(index,mot,sentence){
	mot=mot.trim();
	mot_trad="";
	flagConf=true;
	lang_id=$(".select_lang").val();
	if(cards.filter(function(c){return c.mot==mot}).length!=0){flagConf=confirm("<?php echo __("Une carte similaire existe déjà. Voulez-vous tout de même créer cette carte ?");?>");}
	if(mot!="" && flagConf)
	{
	$.getJSON("ajax.php?action=AddNewCard",{deck_id:myDeck.deck_id,mot:mot,mot_trad:mot_trad,lang_id:lang_id}, function(result){
		console.log(result);
		if(result.status=="ok")
		{
		console.log(result.card_id);
		card_id=result.card_id;
		newCard={alert:"0",comment:"",card_id:card_id,mot:mot,mot_trad:mot_trad, hasImage:"0",hasAudio:"0",sentences:[sentence]};
		cards.push(newCard);
		console.log("emitNew");
		socket.emit('cardUpdate',{dataCard:newCard,updatedStuf:"new"});
		console.log("addNewCard");
		creer_ligne(cards.length-1);
		$.getJSON("ajax.php?action=addOneSentenceToCard&card_id=" + card_id+"&sentence="+sentence, function(result){
			sentence_id=result;
			$(".word_"+index).attr("onclick","");
			$(".word_"+index).attr("onclick","deleteCardFromText("+card_id+","+index+");");

			showSentenceInCard(card_id,sentence_id,sentence);
			changeSentToEditCard(sentence_id);
				});
		}
	});
	}
}

function AddNewCard(){
	mot=$('#new_mot').val().trim();
	mot_trad="";
	flagConf=true;
	//lang_id=$(".select_lang").val();
	lang_id=myDeck.lang_id
	if(cards.filter(function(c){return c.mot==mot}).length!=0){flagConf=confirm("<?php echo __("Une carte similaire existe déjà. Voulez-vous tout de même créer cette carte ?");?>");}
	if(mot!="" && flagConf)
	{
		console.log("addnewCard",lang_id);
	$.getJSON("ajax.php?action=AddNewCard",{deck_id:myDeck.deck_id,mot:mot,mot_trad:mot_trad,lang_id:lang_id}, function(result){
		console.log(result);
		if(result.status=="ok")
		{
		console.log(result.card_id);
		card_id=result.card_id;
		$('#new_mot').val("");
		$('#new_mot').focus();
		newCard={alert:"0",comment:"",card_id:card_id,mot:mot,mot_trad:mot_trad,hasImage:"0",hasAudio:"0",sentences:[]};
		cards.push(newCard);
		console.log("emitNew");
		socket.emit('cardUpdate',{dataCard:newCard,updatedStuf:"new"});
		console.log("addNewCard");
		creer_ligne(cards.length-1);
		}
	});
	}
}

socket.on("cardDelete",function(card_id){
	for(k in cards){
		if(cards[k].card_id==card_id){
			cards.splice(k,1);
			nbre_ligne=cards.length-1;
			$('#ligne_carte_'+card_id).remove();
		}
	}
});
function suppression_ligne(card_id)
{	$.getJSON("ajax.php?action=deleteCard&card_id="+card_id, function(result){
	for(k in cards){if(cards[k].card_id==card_id){cards.splice(k,1);}}
	socket.emit('cardDelete',card_id);
	console.log(result);
	//updateSelectedWord();
	});
	$('#ligne_carte_'+card_id).remove();
}
function save_img(id,url)
{
	console.log(id+" "+url);
	//$("#preview_img_"+id).html('<img src="'+url+'" class="img_deck_preview" >');
	$('#ligne_carte_'+id).find(".edit_front").css("background-image",'url('+url+')');
	$('#ligne_carte_'+id).find(".addContentIconImage").addClass("delBubbleIcon").off().attr("onclick",'del_card_img('+id+');');
	//$("#del_img_"+id).html('<img class="del_img icon_ecriture" onclick="del_card_img('+id+');" src="img/del_image.png" width="40px">');
	$.getJSON("ajax.php?action=save_img&id="+id+"&link="+url, function(result){
		console.log(result);
		for(k in cards){
			if(cards[k].card_id==id){
				cards[k].hasImage=cards[k].card_id;
				socket.emit('cardUpdate',{dataCard:cards[k],updatedStuf:"image"});
			}
		}
	});
		$("#onTopBottom").html("");
}
function save_deck_img(url)
{
	console.log(url);
	//$("#preview_img_"+id).html('<img src="'+url+'" class="img_deck_preview" >');
	$(".img_deck_preview").attr("src",url);
	$('#addContentIconImageDeck').removeClass("addBubbleIcon").addClass("delBubbleIcon").off().attr("onclick",'del_deck_img();');

	//$('.del_deck_img').show();
	//$('.add_deck_img').hide();
	//$("#del_img_"+id).html('<img class="del_img icon_ecriture" onclick="del_card_img('+id+');" src="img/del_image.png" width="40px">');
	$.getJSON("ajax.php?action=save_deck_img&deck_id="+myDeck.deck_id+"&link="+url, function(result){
		console.log(result);
		myDeck.hasImage=myDeck.deck_id;
		socket.emit('deckUpdate',{myDeck:myDeck});
	});
		$("#onTopBottom").html("");
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
  $('#type_img_pixabay').append("<div class='buttonTypePixabay' onclick='loadPixabayAPI("+id+",\"all\");'><?php echo __("Recherche");?></div><br><div>Powered by <a href='https://pixabay.com'>Pixabay</a></div>");
	//$('#type_img_pixabay').append("<div class='buttonTypePixabay' onclick='loadPixabayAPI("+id+",\"photo\");'>Recherche de photos</div><br>");
	//$('#type_img_pixabay').append("<div class='buttonTypePixabay' onclick='loadPixabayAPI("+id+",\"illustration\");'>Recherche d'illustration</div><br>");
	//$('#type_img_pixabay').append("<div class='buttonTypePixabay' onclick='loadPixabayAPI("+id+",\"vector\");'>Recherche d'image vectorisée</div><br>");
	$('#type_img_pixabay').append("<div class='buttonImportFile' onclick='$(\"#input_image_card\").click();'><?php echo __("Importer un fichier");?></div>");
	$('#type_img_pixabay').append("<div class='buttonImportFile' onclick='url=prompt();save_img("+id+",url)'><?php echo __("Depuis url");?></div><br>");
	loadPixabayAPI(id,"all");
}
function showOnTopDeck()
{

	titre=myDeck.deck_name.replace("'"," ");
	$('#onTopBottom').html("");
	$('#onTopBottom').prepend("<div style='position:absolute; right:10px;top:0;color:red;font-size:0.8em;' onclick='emptyOnTopBottom();'><img src='img/close.png' width='30px'></div>");
	$('#onTopBottom').append("<div id='type_img_pixabay' style='margin:20px;vertical-align:middle;display:inline-block;margin-left:20px;text-align:center;'><div>");
	$('#onTopBottom').append("<div id='resultat_pixabay' style='margin-20px;vertical-align:middle;display:inline-block;margin-left:20px;'><div>");
	$('#type_img_pixabay').append("<input type='text' id='inputRechercheImage' value='"+titre+"'><br>");
  $('#type_img_pixabay').append("<div class='buttonTypePixabay' onclick='loadPixabayAPIdeck(\"all\");'><?php echo __("Recherche");?></div><br>");
	//$('#type_img_pixabay').append("<div class='buttonTypePixabay' onclick='loadPixabayAPI("+id+",\"photo\");'>Recherche de photos</div><br>");
	//$('#type_img_pixabay').append("<div class='buttonTypePixabay' onclick='loadPixabayAPI("+id+",\"illustration\");'>Recherche d'illustration</div><br>");
	//$('#type_img_pixabay').append("<div class='buttonTypePixabay' onclick='loadPixabayAPI("+id+",\"vector\");'>Recherche d'image vectorisée</div><br>");
	$('#type_img_pixabay').append("<div class='buttonImportFile' onclick='$(\"#image_indice_input\").click();'><?php echo __("Importer un fichier");?></div>");
	$('#type_img_pixabay').append("<div class='buttonImportFile' onclick='url=prompt();save_deck_img(url)'><?php echo __("Depuis url");?></div><br>");
	loadPixabayAPIdeck("all");
}

function getImgFromURL(id,url)
{}
function loadPixabayAPI(id,type)
{	console.log("loadPixabayAPI("+id+","+type+")");
  $('#resultat_pixabay').html('');
	q=$("#inputRechercheImage").val();
	$.getJSON("ajax.php?action=getPixImage&q="+q+"&image_type="+type, function(result){
	result=JSON.parse(result);
	for(i in result.hits)
	{urlPreviewImage=result.hits[i].previewURL;
		console.log(urlPreviewImage);
		$('#resultat_pixabay').prepend("<img class='imgPix' onclick='save_img("+id+",\""+result.hits[i].webformatURL+"\");' src='"+urlPreviewImage+"'>");
	}
	$("#resultat_pixabay").off();
	$("#resultat_pixabay").focus();
	});
}
function loadPixabayAPIdeck(type)
{
  $('#resultat_pixabay').html('');
	q=$("#inputRechercheImage").val();
	$.getJSON("ajax.php?action=getPixImage&q="+q+"&image_type="+type, function(result){
		result=JSON.parse(result);
	console.log(result);
	for(i in result.hits)
	{urlPreviewImage=result.hits[i].previewURL;
		$('#resultat_pixabay').prepend("<img class='imgPix' onclick='save_deck_img(\""+result.hits[i].webformatURL+"\");' src='"+urlPreviewImage+"'>");
	}
	$("#resultat_pixabay").off();
	$("#resultat_pixabay").focus();
});
}
function emptyOnTopBottom(){
	$("#onTopBottom").html('');
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
	sentencesCard=sentences[id];
	if(alertCard=="1"){alertClass="alertEditON";alertCloche="alertON_icon";}else{alertClass="";alertCloche="alertOFF_icon";}
		html_ligne='';
		html_ligne+='<div id="ligne_carte_'+id+'" class="ligneEditCard '+alertClass+'" height="40px;">';

		html_ligne+='<div class="alertCardContainer">';
		html_ligne+='<div class="del_button_ligne" title="<?php echo __("supprimer la carte");?>" onclick="suppression_ligne('+id+');"></div>';
		html_ligne+='<div class="alert_button_ligne icon_back '+alertCloche+'" onclick="toggleAlert('+id+');"></div><div class="msgAlertcard">'+alertComment+'</div>';
		html_ligne+='</div>';

		html_ligne+='<div class="blockCard"><div class="edit_front card" style="background-image:url(\'card_img/card_'+hasImage+'.png?v='+cacheBreaker+'\')"><input class="mot_trad_card" type="text" value="'+mot_trad+'" onkeyup="MAJ_auto('+id+');">'
		+'<div class="icons_card_container">'
		+'<div class="addContentIconCard addContentIconImage"></div>'
		+'</div></div>';
		html_ligne+='<div class="edit_back card"><input class="mot_card" style="font-family: caviar;" type="text" value="'+mot+'" onkeyup="MAJ_auto('+id+');"><div class="icons_card_container">'
		+'<div class="addContentIconCard addContentIconAudio delBubbleIcon" onclick="del_audio('+id+');"></div>'
			+'<button class="icon_back recording_icon" onclick="if(record_flag){stopRecording(this,'+id+');record_flag=false;}else{startRecording(this);record_flag=true;}"></button>'
		+'<div class="icon_back icon_audio" onclick="play_audio('+id+')";event.stopPropagation();></div>'
		+'</div></div></div>';
		html_ligne+='<div class="sentences_container"><div class="sentence_container_block"></div><div class="addSentenceYourself" title="<?php echo __("Ajouter une phrase manuellement");?>" onclick="addSentenceYourself('+id+');"><input type="text" class="modifiedSent" readonly placeholder="<?php echo __("Nouvelle phrase avec le mot entre * ou bien sa définition");?>"></div><div class="rechercheSentence addContentIconCard addContentIconPhrase searchBubbleIcon" onclick="showOnTopSentence('+id+');" title="<?php echo __("Rechercher des phrases avec :");?> '+mot+'."></div></div>';
		html_ligne+='</div>';

		if($('#table_card #ligne_carte_'+id).length==0){
			$('#table_card').prepend(html_ligne);
		}

		if(alertComment==''){	$('#ligne_carte_'+id).find(".msgAlertcard").hide();}
		if(hasImage>0){
			$('#ligne_carte_'+id).find(".addContentIconImage").addClass("delBubbleIcon").off().attr('onclick','del_card_img('+id+');');
		}else{
			$('#ligne_carte_'+id).find(".edit_front").css("background-image","url(img/default_card.png)");
			$('#ligne_carte_'+id).find(".addContentIconImage").addClass("addBubbleIcon").off().attr('onclick','showOnTop('+id+');');}
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
					$('#ligne_carte_'+id).find(".sentence_container_block").append("<div class='sentenceBlock' id='sent_"+sentence_id+"'><div class='delSentenceIcon icon_back' onclick='delSentenceFound("+sentence_id+","+id+");'></div><div id='sentence_"+sentence_id+"' onclick='changeSentToEditCard("+sentence_id+");' class='one_sentence'>"+sentence+"</div><input style='display:none;' type='text' class='modifiedSent' value='' onblur='console.log(2);goBackOneSentenceCard("+sentence_id+");'></div>");
			}

		//html_ligne+='<br><br><span class="del_button_ligne" onclick="del_card_img('+id+');">supprimer image</span></TD>';
		//html_ligne+='<TD id="preview_img_'+id+'"><img src="card_img/card_'+id+'.png?v='+cacheBreaker+'" width="70px"></TD>';

	//html_ligne+='<TD style="color:lightgrey;">'+id+'</TD>';


}
sentence_found=[];
function rechercheSent(id)
{
	//console.log(id);
	mot=$("#inputRechercheSentence").val();
	mot=mot.replace("'"," ");
	$.getJSON("ajax.php?action=getPhrasePlus&mot="+mot+"&target_lang_id="+target_lang_id, function(result){
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
				socket.emit('cardUpdate',{dataCard:cards[k],updatedStuf:"updateSentence",sentence_id:sentence_id});
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
				socket.emit('cardUpdate',{dataCard:cards[k],updatedStuf:"updateSentence",sentence_id:sentence_id});}
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
				socket.emit('cardUpdate',{dataCard:cards[k],updatedStuf:"updateSentence",sentence_id:sentence_id});}
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
							socket.emit('cardUpdate',{dataCard:cards[k],updatedStuf:"deleteSentence",sentence_id:sentence_id});}
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
				socket.emit('cardUpdate',{dataCard:cards[k],updatedStuf:"alert"});}
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
						socket.emit('cardUpdate',{dataCard:cards[k],updatedStuf:"alert"});
					}
				}
			});
    }
  }
}
function getTradMot(mot,id)
{
	$.getJSON("ajax.php?action=translate&phrase=" + mot+"&lang_ini=fr&lang_fin=en", function(result){
		console.log(result);
		//$('#mot_trad_'+id).val(result);
			});
}


	function del_audio(card_id)
{
	console.log("remove sound");
	$.getJSON("ajax.php?action=cardHasNoAudio&card_id=" + card_id, function(result){
		for(k in cards){
			if(cards[k].card_id==card_id){
				cards[k].hasAudio=0;
				socket.emit('cardUpdate',{dataCard:cards[k],updatedStuf:"audio"});

			}
		}
		$('#ligne_carte_'+card_id).find(".addContentIconAudio").hide();
		$('#ligne_carte_'+card_id).find(".icon_audio").hide();
		$('#ligne_carte_'+card_id).find(".addContentIconAudio").addClass("addBubbleIcon");

	});
			//$('#play_son_'+card_id+' > audio').remove();
			//$('#suppr_son_'+card_id).html('');
}
//========================RECORD=========================
function MAJ_tag()
{
	console.log("MAJ_tag");
	tag_value=$('.select_tag').val();
	if(tag_value=="add_tag"){
		addNewTag();
		}
	else if(tag_value!=""){
		setTag(tag_value);
		}
}

// function MAJ_lang()
// {
// 	console.log("MAJ_lang");
// 	tag_value=$('.select_lang').val();
// 	if(tag_value!=""){
// 		setLang(tag_value);
// 		$(".class_status_build:not(.class_lang_"+tag_value+")").hide();
// 		$(".class_lang_"+tag_value).show();
// 		}
// 	if($(".class_item:visible").length==0){$(".noClassInfo").show();}else{$(".noClassInfo").hide();}
//
// }
// function setLang(lang_id)
// {
// 	console.log("setLang");
// 	$.getJSON("ajax.php?action=setLang&deck_id=" + myDeck.deck_id+"&lang="+lang_id, function(result){
// 		console.log("setLang Done");
// 	});
// }
function getTagList()
{
	console.log("getTagList");
	$.getJSON("ajax.php?action=getTagList&deck_id="+myDeck.deck_id, function(results){
		console.log(results);
    $('.tag_opt').remove();
	  for(rg in results)
    {
    tag=results[rg];
    $('.select_tag').append("<option class='tag_opt' value='"+tag["tag_name"]+"'>"+tag["tag_name"]+"</option>");
    }
	});
}
function deleteTag(tag_name)
{
	console.log("deleteTag");
	$.getJSON("ajax.php?action=deleteTag&deck_id=" + myDeck.deck_id+"&tag="+tag_name, function(result){
		console.log("deleteTag Done");
		$('.select_tag').append("<option class='tag_opt' value='"+tag_name+"'>"+tag_name+"</option>");
	});
}
function setTag(tag_name)
{
	console.log("setTag");
	$.getJSON("ajax.php?action=setTag&deck_id=" + myDeck.deck_id+"&tag="+tag_name, function(result){
		console.log("setTag Done");
		$('#list_tag').prepend("<div class='tag_item' id='tag_item_new'>"+tag_name+"<div class='del_tag' onclick='deleteTag(\""+tag_name+"\");$(this).parent().remove();'>x</div></div>");
		$(".tag_opt[value="+tag_name+"]").remove();
	});
}
function addNewTag()
{
	$(".select_tag option[value='']").prop('selected', true);
	console.log("addNewTag");
	new_tag_name=prompt("<?php echo __("nom du nouveau thème :");?>");
	if(new_tag_name.replace("'"," ")!=new_tag_name)
	{
	alert("<?php echo __("Un thème ne peut contenir d'apostrophe.");?>");
	}
	else
	{
	new_tag_name=new_tag_name.replace("'"," ");
	setTag(new_tag_name);
	}
}
function getTag()
{
	console.log("getTag");
	$.getJSON("ajax.php?action=getTag&deck_id=" + myDeck.deck_id, function(results){
		$('.tag_item').remove();
		for(rg in results)
		{
		tag=results[rg];
		$('#list_tag').prepend("<div class='tag_item' id='tag_item_"+tag["tag_id"]+"'>"+tag["tag_name"]+"</div>");
		$('#tag_item_'+tag["tag_id"]).append("<div class='del_tag' onclick='deleteTag(\""+tag["tag_name"]+"\");$(this).parent().remove();'>x</div>");
		}
	});
}
getTag();
$('.select_lang').val(myDeck.lang);
//MAJ_lang();
getTagList();


</script>


		  <script>
/*
			function __log(e, data) {
		  }
		  var audio_context;
		  var recorder;
		  function startUserMedia(stream) {
		    var input = audio_context.createMediaStreamSource(stream);

		    // Uncomment if you want the audio to feedback directly
		    //input.connect(audio_context.destination);
		    //__log('Input connected to audio context destination.');

		    recorder = new Recorder(input);

		  }
		  function startRecording(button) {
		    recorder && recorder.record();

				$(button).addClass('stop_icon');
				$(button).removeClass('recording_icon');
				$('.recording_icon').hide();

		  }
		  function stopRecording(button,card_id) {
		    recorder && recorder.stop();
				$(button).removeClass('stop_icon');
				$(button).addClass('recording_icon');
				$('.recording_icon').show();
		    // create WAV download link using audio data blob
		    createDownloadLink(card_id);

		    recorder.clear();
		  }
		  function createDownloadLink(card_id) {
		    recorder && recorder.exportWAV(function(blob) {
		      var url = URL.createObjectURL(blob);
					$('#play_son_'+card_id+' > audio').remove();
		      //var li = document.getElementById("play_son_"+card_id);
		      var au = document.createElement('audio');
		      au.controls = true;
		      au.src = url;

		      //li.appendChild(au);
					$('#ligne_carte_'+card_id).find(".icon_audio").show();
					$('#ligne_carte_'+card_id).find(".addContentIconAudio").show();
		      //li.appendChild(hf);

					//======================
					var formData = new FormData();
					var fileType = 'audio'; // or "audio"
					var fileName = 'card_'+card_id+'.wav';  // or "wav"
					formData.append("type", "card_audio");
					formData.append("id", card_id);
					formData.append('fileToUpload', blob);
					//formData.append("fileToUpload", url);
					var request = new XMLHttpRequest();
					request.upload.onprogress = function (evt) {
											 var percentComplete = parseInt(evt.loaded *100/ evt.total);
							//		 $('.upload_progress').css("width",percentComplete+'%');
					}

					request.onreadystatechange = function() {
						if (request.readyState == XMLHttpRequest.DONE) {
								console.log(request.responseText);
								$.getJSON("ajax.php?action=cardHasAudio&card_id="+card_id, function(result){console.log(result);});
						}
					}

					request.open("POST", "upload.php");
					request.send(formData);

		    });
		  }
		  window.onload = function init() {
		    try {
		      // webkit shim
		      window.AudioContext = window.AudioContext || window.webkitAudioContext;
		      navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia;
		      window.URL = window.URL || window.webkitURL;

		      audio_context = new AudioContext;
		      __log('navigator.getUserMedia ' + (navigator.getUserMedia ? 'available.' : 'not present!'));
		    } catch (e) {
		      alert('No web audio support in this browser!');
		    }

		    navigator.getUserMedia({audio: true}, startUserMedia, function(e) {
		      __log('No live audio input: ' + e);
		    });
		  };
*/

function toggleAlertDeck(deck_id)
			{
				if($(".alertDeck").hasClass("alertDeckON"))
				{$(".alertDeck").removeClass("alertDeckON").addClass("alertDeckOFF");
				$.getJSON("ajax.php?action=deleteAlertDeck&deck_id="+deck_id, function(results){
				});
				}
				else {
					alert_comment=prompt("<?php echo __("Quel est le problème avec cette liste ?");?>");
					if(alert_comment!="" && alert_comment!=null){
						$(".alertDeck").addClass("alertDeckON").removeClass("alertDeckOFF");
						$.getJSON("ajax.php?action=addAlertDeck&deck_id="+deck_id+"&alert_comment="+alert_comment, function(results){});
					}
				}
}


function showTutorial()
{
	showWindow();
	$('.fenetreClaire').remove();
	$('.fenetreSombre').append(`
		<iframe width="70%" height="70%" style="left:15%;top:10%;position:relative;" src="https://www.youtube.com/embed/_nGwoDTyzY8" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	`);
}

if(!readCookie("tuto-edit")){
	if(lang_interface=="fr")
	{
	showTutorial();
	createCookie("tuto-edit",1,150);
	}
}
if(lang_interface!="fr"){
	$(".button--help").hide();
}
</script>

<script src="js/index.js"></script>

</body>
</html>
