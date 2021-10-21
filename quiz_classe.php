<?php
include_once ("db.php");
session_start();


function getNiveau($classe)
{
	if (strpos($classe, 'haz') !== false) {return 'haz';}
	elseif(strpos($classe, '9') !== false) {return '9';}
	elseif(strpos($classe, '10') !== false) {return '10';}
	elseif(strpos($classe, '11') !== false) {return '11';}
	elseif(strpos($classe, '12') !== false) {return '12';}
	elseif($classe=='prof'){return 'prof';}
	else {return "haz";};
}
if(!isset($_SESSION['user_id'])){header("location:checkLoginCookie.php");exit();}
    // Makes it easier to read
	  $user_id = $_SESSION['user_id'];
    $first_name = $_SESSION['first_name'];
    $last_name = $_SESSION['last_name'];
    $email = $_SESSION['email'];
    $active = $_SESSION['active'];
		$type = $_SESSION['type'];
		$classe = $_SESSION['classe'];


		if(!isset($_GET['niveau'])){
			if(isset($_SESSION['niveau']))
			{$niveau=htmlspecialchars($_SESSION['niveau']);}
			else
			{$niveau="haz";}
		header("location:decks.php?niveau=haz");
		}
		else
    {
		$niveau = htmlspecialchars($_GET['niveau']);
		$_SESSION['niveau'] = $niveau;
	  }
		echo "<script>niveau='".$niveau."';</script>";
?>
<!DOCTYPE html>
<html >
 <head>


    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mes listes</title>
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

		<style>
      .navbar{
        margin-bottom:0;
        border-radius:0;
      }
    </style>
</head>

<body class="fond">
	<nav id="navbar">
				<div class="menu">
					<ul class="desktop" onclick="$('.mobile').removeClass('open');">

						<li style="float:left;"><a href="index.php" style="text-transform: none;margin-right:30px;">VocaSion</a></li>

						<li><a class="onglet_niveaux" href="#">Niveaux</a>
							<ul class="submenu">
								<li><a class="onglet_haz" href="decks.php?niveau=haz">Haz</a></li>
								<li><a class="onglet_9" href="decks.php?niveau=9">9e</a></li>
								<li><a class="onglet_10" href="decks.php?niveau=10">10e</a></li>
								<li><a class="onglet_11" href="decks.php?niveau=11">11e</a></li>
								<li><a class="onglet_12" href="decks.php?niveau=12">12e</a></li>
								<li><a class="onglet_divers" href="decks.php?niveau=divers">Hors niveaux</a></li>
							</ul>
						</li>
						<li><a class="onglet_myDecks" href="decks.php?niveau=myDecks">Mes listes</a></li>
						<li><a class="onglet_quizEleve" href="quiz_classe.php">Quiz en classe</a></li>
						<?php
						if($classe=="prof")
							{
						echo '<li><a class="onglet_StudentDecks" href="decks.php?niveau=StudentDecks" style="margin-left:100px;">Listes des élèves</a></li>';
						echo '<li><a href="stat.php">Stats</a></li>';

						//		echo '<a href="decks.php?niveau=StudentDecks" class="onglet float-left"><span id="onglet_studentDecks">Listes des élèves</span></a>';
						//		echo '<a href="stat.php" class="onglet float-left"><span id="onglet_prof">Stats</span></a>';
							}
						?>
					<li style="float:right;"><a href="profile.php"><img src="img/userBlanc.png" width="40px"  style="position:relative;top:-10px;"></a></li>


					</ul>
					<ul class="mobile" onclick="$('.mobile').removeClass('open');">
						<li><a href="#">Niveaux</a>
							<ul class="submenu">
								<li><a href="decks.php?niveau=haz">Haz</a></li>
								<li><a href="decks.php?niveau=9">9e</a></li>
								<li><a href="decks.php?niveau=10">10e</a></li>
								<li><a href="decks.php?niveau=11">11e</a></li>
								<li><a href="decks.php?niveau=12">12e</a></li>
								<li><a href="decks.php?niveau=divers">Hors niveaux</a></li>
							</ul>
						</li>
						<li><a href="decks.php?niveau=myDecks">Mes listes</a></li>
						<li><a class="onglet_quizEleve" href="quiz_classe.php">Quiz en classe</a></li>
						<?php
						if($classe=="prof")
							{
						echo '<li><a href="decks.php?niveau=StudentDecks">Listes des élèves</a></li>';
						echo '<li><a href="stat.php">Resultats des élèves</a></li>';
						//		echo '<a href="decks.php?niveau=StudentDecks" class="onglet float-left"><span id="onglet_studentDecks">Listes des élèves</span></a>';
						//		echo '<a href="stat.php" class="onglet float-left"><span id="onglet_prof">Stats</span></a>';
							}
						?>

						<li><a href="profile.php">mon profil</a></li>
						<li><a href="logout.php">Deconnexion</a></li>
					</ul>
				</div>
				<div id="openMenu">VocaSion - MENU</div>
			</nav>
			<div class="overlay"></div>

			<script type="text/javascript" src="js/menu-breaker.js"></script>
			<script>

			$('.onglet_quizEleve').addClass("active_onglet");
			$('.onglet_quizEleve').css("background-color","var(--mycolor2fonce)");
			$('.desktop').menuBreaker();
$(window).on('load resize', function () {
  $('.mobile').height($(window).height() - $('nav').height());
});

			</script>


<div class="center">

	<div id="select_tag_deck">
		<select class="select_tag" style="display:inline-block;" onchange="MAJ_list_deck();">
			<option class="tag_opt_deck" value="all" selected>Toutes les listes</option>
		</select>
	</div>
	<div id="list_deck" class="list_deck"></div>

	<?php if($niveau=="myDecks"){
	echo '<a href="edit_deck.php?deck_id=0" class="creation_deck"><span class="plus">+ <span class="Addtext">Nouvelle liste</span></span></a>';
	} ?>
	<div id="objectif"></div>
</div>

<?php



//$result = $mysqli->query('SELECT COUNT(*) AS nbreCardSeen FROM activite LEFT JOIN WHERE ');
//while ($row = $result->fetch_assoc()) {
//				array_push($decks,$row);
//		}
//$result->free();
echo "<script>niveau=".json_encode($niveau).";</script>";
//echo "<script>decks=".json_encode($decks).";</script>";
echo "<script>user_id=".json_encode($user_id).";</script>";
//==============================
?>

<script>
function MAJ_list_deck()
{tag=$(".select_tag").val();
showDecks(tag);
}
function showDecks(tag)
{
	$.getJSON("ajax.php?action=getDecks&tag="+tag, function(decks)
	{
		$(".deck:not(#deck_oublie)").remove();
		for(rg in decks)
		{
			deck_id=decks[rg]["deck_id"];
			nbreMots=decks[rg]["nbreMots"];
		  nbreKnown=decks[rg]["NbreKnown"];
			//ajouter le deck

			$("#list_deck").append("<div id='deck_"+deck_id+"' class='deck' onclick='location.href=\"cards.php?deck_id="+deck_id+"\"'>"
			+"<div id='img_deck_"+deck_id+"' class='img_deck'></div>"
			+"<div class='infoDeck'>"
			+"<span class='deck_name'>"+decks[rg].deck_name+" </span><br>"
			+"<span class='nbreMots'>("+nbreKnown+"/"+nbreMots+")</span><br>"
			<?php if($classe=="prof" || $niveau=="myDecks"){
			echo '+"<a class=\'edit_icon\' href=\'edit_deck.php?deck_id="+deck_id+"\'></a>"';
			}?>
			<?php if($niveau=="StudentDecks"){
			echo '+"<span class=\'creator_name\'>"+decks[rg]["user_classe"]+" : "+decks[rg]["first_name"]+" "+decks[rg]["last_name"]+"</span><br>"';
			}?>
			+"</div></div>");
		if(nbreKnown==nbreMots){$("#deck_"+deck_id+' .img_deck').addClass('golden');
		$("#deck_"+deck_id).css("border-bottom-color","#ffc107");
		}
			if(decks[rg]["hasImage"]>0){
			$("#img_deck_"+deck_id).css("background-image","url(deck_img/deck_"+decks[rg]["hasImage"]+".png)");
			}
			else {
			$("#img_deck_"+deck_id).css("background-image","url(img/default_deck.png)");
			}
		}


});
}
showDecks("all");
$.getJSON("ajax.php?action=getTagsDecksList", function(results)
{
	for(rg in results)
	{
		tag_name=results[rg]["tag_name"];
		nbreDecks=results[rg]["nbreDeck"];
	$(".select_tag").append("<option value='"+tag_name+"'>"+tag_name+" ("+nbreDecks+")</option>");
	}
});
$.getJSON("ajax.php?action=nbreCardToReview&user_id="+user_id, function(result)
{
	console.log(result);
	if(result.nbreCards!=0 && niveau!="myDecks")
	{$("#list_deck").prepend("<div id='deck_oublie' class='deck' onClick='location.href=\"cards.php?deck_id=-1\"'>"
	+"<div id='img_deck_oublie' class='img_deck img_oublie'></div>"
	+"<div class='infoDeck'>"
	+"<span class='deck_name'>Mots oubliés</span><br>"
	+"<span class='nbreMots'>("+result.nbreCards+")</span><br>"
	+"</div></div><br>");
	<?php if($classe!="prof"){
	echo '$(".deck:not(#deck_oublie)").addClass("NetB");';
	echo '$(".deck:not(#deck_oublie)").attr("onclick","");';
	}?>
	}
});

$.getJSON("ajax.php?action=getStatsTodayUser&user_id="+user_id, function(result){
	console.log(result.nbreMotsEnMemoire);
	nbreMotsEnMemoire=result.nbreMotsEnMemoire;
	jour_ini=result.jour_ini;
	date_ini = new Date(jour_ini);
	date_fin = new Date();
	deltaJour = (date_fin - date_ini)/(1000*60*60*24)
	deltaJour = Math.round(deltaJour);
	objectif=10+deltaJour*10;
	$('#objectif').html("<div>Nombre de mots en mémoire :<span class='nbreMotsEnMemoire'>"+nbreMotsEnMemoire+"</span>/<span class='goal' title='Objectif du jour'>"+objectif+"</span>");
	if(nbreMotsEnMemoire<objectif){$(".nbreMotsEnMemoire").addClass("underObjectif");}else{$(".nbreMotsEnMemoire").addClass("overObjectif");}
});

</script>

<script src="js/index.js"></script>

</body>
</html>
