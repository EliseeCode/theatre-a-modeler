<!DOCTYPE html>
<?php
include_once ("db.php");
session_start();
include_once ("local_lang.php");
if(!isset($_SESSION['user_id'])){header("location:checkLoginCookie.php");exit();}
    // Makes it easier to read
	  $user_id = $_SESSION['user_id'];
    $first_name = $_SESSION['first_name'];
    $last_name = $_SESSION['last_name'];
    $email = $_SESSION['email'];
    $active = $_SESSION['active'];
		$type = $_SESSION['type'];
		$classe = $_SESSION['classe'];
		$deck_id=(int)$_GET['deck_id'];
    $expire=time()+60*60*3;//session de 3h
    $sql = "INSERT INTO quiz (expire,prof_id)"
            . "VALUES (".$expire.",".$user_id.")";
    $mysqli->query($sql);
    $quiz_id=$mysqli->insert_id;
    $game_id=($quiz_id*298879)%1000000;

    $mysqli->query("UPDATE quiz SET status='over' WHERE game_id=".$game_id." OR prof_id=".$user_id);
    $mysqli->query("UPDATE quiz SET  status='waiting', deck_id=".$deck_id.", game_id=".$game_id." WHERE quiz_id=".$quiz_id);
		$_SESSION['quiz_id']=(int)$quiz_id;

		echo "<script>game_id=".json_encode($game_id).";</script>";
		echo "<script>quiz_id=".json_encode($quiz_id).";</script>";
		echo "<script>deck_id=".json_encode($deck_id).";</script>";

    $result = $mysqli->query('SELECT deck_name ,hasImage FROM decks WHERE deck_id='.$deck_id);
	  $myresult = $result->fetch_assoc();
		$deckInfo=$myresult;
		$result->free();
    echo "<script>deckInfo=".json_encode($deckInfo).";</script>";

		$sql="SELECT user_class.role,classes.class_id,classes.class_name,classes.promo,classes.status,user_class.position
		FROM classes LEFT JOIN  user_class ON user_class.class_id=classes.class_id WHERE classes.status='ok' AND user_class.user_id=".$user_id;
		$result = $mysqli->query($sql);
		$virgule="";
		$classes_id_list="";
		while($row = $result->fetch_assoc())
		{
		  $virgule=" ,";
		  $classes[$row["class_id"]]=array("class_id"=>$row["class_id"],"class_name"=>$row["class_name"],"promo"=>$row["promo"],"status"=>$row["status"],"role"=>$row["role"],"listProf"=>array());
		}
		$result->free();
		echo "<script>myClasses=".json_encode($classes).";</script>";

    $result = $mysqli->query('SELECT distinct cards.card_id,cards.mot,cards.mot_trad,cards.hasImage,cards.hasAudio,card_sentence.sentence FROM cards LEFT JOIN card_sentence ON card_sentence.card_id=cards.card_id WHERE cards.active=1 AND deck_id='.$deck_id.' ORDER BY cards.card_id ASC');
    $cardIdold=-1;
    $deckData=array();
    while ($card = $result->fetch_assoc())
    {
        if($card['card_id']!=$cardIdold)
        {
          $cardIdold=$card['card_id'];
          $sentenceByDefault="*".$card['mot']."*";
          if($card['sentence']!=null)
          {
           $nbreetoile=substr_count($card['sentence'],"*");
            if($nbreetoile==2){$sentence=$card['sentence'];}else{$sentence=$card['sentence']."<br>".$sentenceByDefault;}
          }
          else {
            $sentence=$sentenceByDefault;
          }
          $deckData[$card['card_id']]=$card;
          $deckData[$card['card_id']]['sentence']=array(0=>$sentence);
        }
        else
        {
          if($card['sentence']!=null)
          {
           $nbreetoile=substr_count($card['sentence'],"*");
            if($nbreetoile==2){$sentence=$card['sentence'];}else{$sentence=$card['sentence']."<br>".$sentenceByDefault;}
            array_push($deckData[$card['card_id']]['sentence'],$sentence);
          }
        }
		}
		$result->free();
    echo "<script>var deckData=".json_encode($deckData).";</script>";
?>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta name="viewport" content="width=device-width, user-scalable=no,initial-scale=1">
    <title>Quiz Eleve</title>
    <link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16" />
  <link rel="stylesheet" href="css/myStyle.css">
  <link href="css/navStyle.css" rel="stylesheet">
  <link href="css/styleEntete.css" rel="stylesheet">
  <link href="css/main.css" rel="stylesheet">
  <link href="css/quiz.css?ver=<?php echo filemtime('css/quiz.css');?>" rel="stylesheet">
	<!--<link href="css/print.min.css" rel="stylesheet">-->
  <script src='js/cookiesManager.js'></script>
	<script src='js/jquery-3.3.1.min.js'></script>
	<!--<script src='js/print.min.js'></script>-->
	<script src="/socket.io/socket.io.js"></script>
   </head>
<body class="fond">

  <nav id="navbar">
        <div class="menu">
          <ul class="desktop" onclick="$('.mobile').removeClass('open');">
            <!--<li style="float:left;"><a id="back_btn" href="https://www.exolingo.com/decks.php">Retour</a></li>-->
            <li style="float:left;"><a id="back_btn" href="decks.php"><?php echo __("Retour aux listes");?></a></li>
            <li style="float:left"><a href="#"><span id="mini_code"></span></a></li>

						<li><a href="#"><?php echo __("Quiz en classe");?></a></li>
            <li style="float:right;"><a id="audio_btn" href="#" onClick="toogleVolume();"><img src="img/haut_parleur_mute.png" style="width:30px;"></a></li>
						<li style="float:right;"><a id="print_btn" href="#" onclick="window.print();" title="<?php echo __("Imprimer l'interro");?>"><img src="img/printer.png" style="width:30px;"></a></li>
					</ul>
          <ul class="mobile"  onclick="$('.mobile').removeClass('open');">
            <li><a href="https://www.exolingo.com"><?php echo __("Retour");?></a></li>
          </ul>
        </div>
        <div id="openMenu">ExoLingo - MENU </div>
        <div class="progressbarDeck" style="top:50px;"></div>
      </nav>
      <div class="overlay"></div>

      <script type="text/javascript" src="js/menu-breaker.js"></script>
      <script>

      $('.desktop').menuBreaker();
  $(window).on('load resize', function () {
  $('.mobile').height($(window).height() - $('nav').height());
  });

//var app=app || {};
if((typeof window!=='undefined')&&(typeof window.onload!=='undefined')){
   //socket=io.connect(window.location.protocol+'//'+window.location.hostname+":3030");
	 socket=io.connect('https://www.exolingo.com/socketLink');
 }
      </script>




      <audio class="music_fond" loop preload="auto">
        <source src="audio/VocaSionMusic.ogg" type="audio/ogg">
        <source src="audio/VocaSionMusic.mp3" type="audio/mpeg">
      Your browser does not support the audio element.
      </audio>
<style>
#printIntero{display:none;}
#navbar{}
#listPlayerContainer{
  float:left;
  width:300px;
  background-color:white;
  box-shadow:0 0 4px grey;
  padding:10px;
  position:relative;
  padding-top:30px;
	max-width:350px;
}
#noStudentInfo{color:grey;text-align:center;}
.online{color:black;}
.offline{color:grey;}
.out{text-decoration: line-through;}
.infoSurDeck{background-color:white;padding:10px;display:inline-block;box-shadow:0 0 4px grey;width:100%;max-width:350px;}
.infoSurDeck > img{width:100px;display:inline-block;float:left;vertical-align:middle;}
.titreDeck{margin-left:20px;display:inline-flex;height:100px;}
.titreDeck > *{margin:auto;}
#goodAnswer{background-color:white;padding:10px; min-width:200px;border-bottom:grey solid 3px;display:inline-block;height: 2em;}
#sentence{font-size:2em;margin:20px 0 40px 0;}
.badUser{color:red;}
.goodUser{color:lime;}

.class_item{box-shadow: 1px 1px 3px grey;
    display: inline-block;
    margin: 10px;
    padding: 10px 20px;
    text-align:center;
    position:relative;
		background-color:white;
		border-radius:3px;
		}
.class_item:hover{box-shadow:1px 1px 5px grey;top:-2px; left:-2px;}
#chart_container{flex: 1;
    order: 3;}
#listPlayerContainer{flex:1;}
.container_global{display:flex;flex-direction:row;filter:blur(5px);padding-top: 60px;height:100vh;}
#StartingPoint,#question,#fin,#waitForStudentPage{flex:4;}
#print_questionContainer tr td{border:1px 0 lightgrey solid;border: 1px lightgrey solid; border-left: none; border-right: none;}
.col2{width:45%;min-width:200px;display:inline-block;}
.BtnStd1{cursor:hand;padding:15px 20px;background-color:var(--mycolor2);color:white;border:none;width:200px;box-shadow: 2px 2px 2px #dadada;}
.BtnStd1:hover{background-color:var(--mycolor2bis);}
.lienConsigne{font-size:2em;color:var(--mycolor2);;padding:10px;display:block;margin:30px auto;}
.codeQuiz{font-size:3em;color:var(--mycolor2);padding:10px;display:inline-block;margin:20px auto;border:5px solid var(--mycolor2);}
.exoLingoTitle{color:white;text-shadow:0 0 3px grey;font-size:3em;font-family:exolingoTitre;}
#mini_code{position:relative;top:-5px;display:none;color:var(--mycolor2);border:2px var(--mycolor2) solid;font-size:1.5em;padding:0 20px;}

</style>

<div class="center container_global">
  <div id="listPlayerContainer" style="display:none;">
    <!--<div style="background-color:grey;padding:10px;" onclick="ResendTO=setTimeout(function(){socket.emit('answeredReceived',user_idAnswerReceived);},500);">Renvoyer les réponses</div>-->
    <h2><?php echo __("Liste des apprenants");?></h2>
    <div id="noStudentInfo"><?php echo __("Aucun élève inscrit");?></div>
    <div id="listPlayer"></div>
    <div id="nbrePlayer" style="color:grey;margin:20px;"></div>
  </div>

	<div id="chart_container">
		<div class="colonneQuiz1">
      <!--<div class="btn_start btn_quiz" onclick="mode='quiz';start();"><?php echo __("Commencer le carte par carte");?></div>
      <div class="btn_start btn_Interro" onclick="mode='interro';start();"><?php echo __("Commencer l'intérro ");?></div>
      <div class="clock"><img src="img/clock.png" class="clock_img"><input type="number" class="rebours_ini" onchange="changeRebours(1);" value="15" placeholder="nombre de secondes"><?php echo __("secondes par question");?></div>-->
			<div style="display:none;" class="btn_showAnswer btn_start" onclick="showCorrection();"><?php echo __("Montrer les réponses");?></div>
			<div style="display:none;" class="btn_fin btn_quiz2" onclick="fin_quiz();"><?php echo __("Arrêter le quiz");?></div>
		</div>
		<div class="colonneQuiz2">
	  <figure class="chart" data-percent="100">
	       <div class="rebours_num">15</div>
	       <div class="rebours_pause"><?php echo __("pause");?></div>
	      <svg width="190" height="190">
	        <circle class="rebours outer" cx="95" cy="95" r="85" transform="rotate(-90, 95, 95)"/>
	      </svg>
	  </figure>
	</div>
	</div>

  <div id="StartingPoint">
    <!-- <div class="colonneQuiz1">
			<div class="btn_print btn_start" onclick="window.print();"><?php echo __("Imprimer l'interro");?></div>
    </div> -->
    <div class="colonneQuiz2">
      <!--<div class="flexContaine">-->
        <div>
					<h1 class="exoLingoTitle">ExoLingo</h1>
		      <div class="infoSurDeck" style="color:black;"><?php echo __("Pas d'info");?></div>
		      <br>
		      <div class="consigneQuiz">
						<div class="col2">
							<h4><?php echo __("Quiz avec un code");?></h4>
							<div class="BtnStd1" onclick="InitialiseCodeQuiz();" style="display:inline-block;"><?php echo __("Lancer");?></div>
						</div>
						<div class="col2 classSelector_block" style="border-left:thin grey solid;">
							<h4><?php echo __("Quiz par classe");?></h4>
							<div id="classSelector_container"></div>
						</div>
		      </div>
		    <!--<img src="../img/classroom.png" class="imgfusee" style='max-width:40%;'>-->
		    <!--	<p class="codeLine"><span class="code"></span><span onclick="newSession();" style="position:absolute;bottom:0;"><img src="img/flip.png" width="40px"></span></p>-->
  			</div>
			<!--</div>-->
    </div>
  </div>

	<div id="waitForStudentPage" style="display:none;">
    <div class="colonneQuiz1">
			<!-- <div class="btn_print btn_start" onclick="window.print();"><?php echo __("Imprimer l'interro");?></div> -->
      <div class="btn_start btn_quiz" onclick="mode='quiz';start();"><?php echo __("Commencer le carte par carte");?></div>
      <div class="btn_start btn_Interro" onclick="mode='interro';start();"><?php echo __("Commencer l'intérro ");?></div>
      <div class="clock"><img src="img/clock.png" class="clock_img"><input type="number" class="rebours_ini" onchange="changeRebours(1);" value="15" placeholder="nombre de secondes"><?php echo __("secondes par question");?></div>
    </div>
    <div class="colonneQuiz2">
      <div class="flexContaine">
        <div>
		      <div class="infoSurDeck" style="color:black;"><?php echo __("Pas d'info");?></div>
		      <br>
		      <div class="consigneQuiz">
						<div class="quizClassConsigne" style="display:none;">
						<?php echo __("Elèves, Connectez-vous sur ExoLingo et rejoignez le quiz");?>
						</div>
						<div class="quizCodeConsigne" style="display:none;font-size:0.8em;">
						<?php echo __("Elèves, Connectez-vous sur");?>

						<div class="lienConsigne">exolingo.com/quiz</div>

						<?php echo __("Avec le code :");?><br><span class="codeQuiz"></span>
						</div>
		      </div>
		    <!--<img src="../img/classroom.png" class="imgfusee" style='max-width:40%;'>-->
		    <!--	<p class="codeLine"><span class="code"></span><span onclick="newSession();" style="position:absolute;bottom:0;"><img src="img/flip.png" width="40px"></span></p>-->
  			</div>
			</div>
    </div>
  </div>


  <div id="question">
		<div class="colonneQuiz1">
			<div class="btn_quiz_container" style="">
				<div class="ProgressNbreCards"></div>
				<div class="btn_showAnswer btn_start" onclick="showCorrection();"><?php echo __("Montrer les réponses");?></div>
				<div class="btn_suivant btn_start" onclick="if(mode=='quiz'){sendNextQuestion();}else{showNextCorrection();}"><?php echo __("Carte suivante");?></div>
				<div class="btn_fin btn_quiz2" onclick="if(confirm()){fin_quiz()};"><?php echo __("Arrêter le quiz");?></div>
				<figure class="chart" data-percent="100">
						<div class="rebours_num">15</div>
						<div class="rebours_pause"><?php echo __("pause");?></div>
					 <svg width="190" height="190">
						 <circle class="rebours outer" cx="95" cy="95" r="85" transform="rotate(-90, 95, 95)"/>
					 </svg>
			 </figure>
			</div>
    </div>
		<div class="colonneQuiz2">
			<div id="nbreQuestion"><?php echo __("0 question");?></div>
			<div class="flexContaine">
			  <div>
					<h2 class="infoSurDeck" style="color:black;"><?php echo __("Pas d'info");?></h2>
			    <div id="card"></div>
			    <div id="sentence"></div>

			    <div id="answers"></div>
			  </div>
				<div id="resultat"></div>
			</div>
		</div>
	 </div>

  <div id="fin">
    <h1><?php echo __("Classement final :");?></h1>
		<div class="colonneQuiz1">
			<div class="btn_quiz_container" style="">
				<div id="download_results" style="display:none;" class="btn_quiz2"><?php echo __("Télécharger les scores");?></div>
				<div class="btn_fin2 btn_quiz2"  style="display:none;" onclick="window.location='decks.php';"><?php echo __("Retour aux listes");?></div>
			</div>
		</div>
    <div class="colonneQuiz2">
      <img src="img/podium.png" style="width:30%;max-width:300px;">
        <div id="classement"></div>
    </div>
  </div>

</div>
<div id="disconnected"><span style="margin:auto"><?php echo __("Veuillez attendre quelques instants...");?></span></div>
<!--Gestion de l'impression quiz-->
<div id="printIntero" style="">
	<div style="display:block;">
		<span style="width:25%;display:inline-block;"><?php echo __("Prénom Nom :");?></span>
		<span style="width:15%;display:inline-block;"><?php echo __("Num :");?></span>
		<span style="width:20%;display:inline-block;"><?php echo __("Classe :");?></span>
		<span style="width:20%;display:inline-block;"><?php echo __("Date :");?></span>
	</div>
	<h2 class="infoSurDeck" style="color:black;zoom:0.7;"><?php echo __("Pas d'info");?></h2>
	<div id="print_questionContainer" style="">




	</div>
</div>
<script>
//new_game_id=game_id;

var mode='';//interro ou quiz
var fin=false;
var answerShown=false;
var interro=false;
var state="rassemblement";
var card_id;
//$("#back_btn").attr("href","https://www.exolingo.com/cards.php?deck_id="+deck_id);
$("#back_btn").attr("href","cards.php?deck_id="+deck_id);
$('#question').hide();
//$('.colonneQuiz1').hide();
//$(".code").html(game_id);
var classeSelected=false;
var cards=[];
var rang=0;
var players=[];
var nbrePlayer=0;
var nbreReponse=0;
var answers=[];
var answersEff=[];
var user_idAnswerReceived=[];
var selectedIdToWork;
//var socket = io.connect();
var ResendTO;
var quiz_class_id;
for(k in myClasses){

	thisClass=myClasses[k];
	class_name=thisClass.class_name;
	class_id=thisClass.class_id;
	promo=thisClass.promo;
	role=thisClass.role;
	if(role=="prof"){$('#classSelector_container').append("<div class='class_item' onclick='selectClass("+class_id+");'><span style='font-size:1em; color:black;'>"+class_name+"</span><br><span style='font-size:0.8em;'>"+promo+"</span></div>");}
}
if($('#classSelector_container>.class_item').length==0){
	$('.classSelector_block').hide();
	//window.location.href="cards.php?deck_id="+deck_id;
}
//======================================Socket
socket.on('reconnect_error', function () {
    console.log('attempt to reconnect has failed');
  });

	window.onbeforeunload = function() {
		if(classeSelected){
	 $.getJSON("ajax.php?action=overClassQuiz", function(result){
			 socket.emit('closeQuizforClass',quiz_class_id);
	 });
	 }
	   return;
	};




socket.on('reconnect', function() {
	  console.log("ReConnected");
		$(".container_global").css({"filter":"none"});
		$("#disconnected").hide();
		socket.emit('game', quiz_id, function(result){
	    if(result=="game_done"){
	      socket.emit('whoIsThere', quiz_id);
	    }
	    else {
	    alert("probleme avec la connection au groupe : "+result);
	    }
	  });
	});

socket.on('connected', function() {
	console.log("connected");

	$(".container_global").css({"filter":"none"});
	$("#disconnected").hide();
	socket.emit('game', quiz_id, function(result){
    if(result=="game_done"){
      socket.emit('whoIsThere', quiz_id);
    }
    else {
    alert("probleme avec la connection au groupe : "+result);
    }
  });
});

socket.on('disconnect', function() {
	//socket.emit('ProfOut', quiz_id);
	$(".container_global").css({"filter":"blur(5px)"});
	$("#disconnected").show();
  $(".message").html("disconnected");
});
$(".container_global").css({"filter":"blur(5px)"});
$("#disconnected").show();
//DeckInfo !!!!
  $(".infoSurDeck").html("<div class='titreDeck'><span>"+deckInfo.deck_name+"</span></div>");
  if(deckInfo.hasImage>0){$(".infoSurDeck").prepend("<img src='deck_img/deck_"+deckInfo.hasImage+".png'>");}
  else{$(".infoSurDeck").prepend("<img src='img/default_deck.png'>");}

//Lorsqu'un joueur se connect, on affiche le nom et prenom
//On met a jour le nbre de joueur et la variable Player

socket.on('playerJoin', function(user_id) {
  console.log("playerJoin:",user_id);
	player_id=user_id;

	if($("#player_"+player_id+":not(.out)").length==0)
	{
		if(state=="questionSentInterro")
		{socket.emit('state', {dest:player_id,state:"questionSentInterro",data:cards,deck_name:deckInfo.deck_name});}
		else if(state=="questionSentQuiz")
		{socket.emit('state', {dest:player_id,state:"questionSentQuiz",data:[card_to_send],deck_name:deckInfo.deck_name});}
		else if(state=="correction")
		{ socket.emit('pause');
			sendGoodUser(card_id);
		}
		else
		{socket.emit('state', {dest:player_id,state:state,deck_name:deckInfo.deck_name});}

		if($("#player_"+player_id).length==0)
		{$("#listPlayer").append("<div id='player_"+player_id+"' class='player_item online'></div>");
		players["player_"+player_id]={id:player_id,pseudo:"no name",connecte:1,cartes:[]};
		recupererPseudoJoueur(player_id);
		}
		else{
			$("#player_"+player_id).removeClass("offline out").addClass("online");
			recupererPseudoJoueur(player_id);
		}
		nbrePlayer=len(players);
    console.log(nbrePlayer);
		$("#nbrePlayer").show();
    if(nbrePlayer<2){$("#nbrePlayer").html(nbrePlayer+' <?php echo __("participant");?>');}
    else{$("#nbrePlayer").html(nbrePlayer+' <?php echo __("participants");?>');}


  $("#noStudentInfo").hide();
	}
});

socket.on('playerLeave', function(result) {
  player_id=result.user_id;
  if(players["player_"+player_id]!=undefined)
  {players["player_"+player_id].connecte=0;}
  $("#player_"+player_id).removeClass("online").addClass("offline out");
  nbrePlayer=len(players);
  if(nbrePlayer<2){$("#nbrePlayer").html(nbrePlayer+' <?php echo __("participant");?>');}
  else{$("#nbrePlayer").html(nbrePlayer+' <?php echo __("participants");?>');}
  if(nbrePlayer==0){$("#noStudentInfo").show();$("#nbrePlayer").hide();}
});
socket.on('playerAway', function(result) {
  player_id=result.user_id;
	$("#player_"+player_id).removeClass("online").addClass("offline");
});
socket.on('playerInAgain', function(result) {
  player_id=result.user_id;
  $("#player_"+player_id).addClass("online").removeClass("offline");
});
function InitialiseCodeQuiz(){
	console.log("InitialiseCodeQuiz");
	$("#listPlayerContainer").show();
	$('#StartingPoint').hide();
	$('#waitForStudentPage').show();
	$('#waitForStudentPage').find(".consigneQuiz .quizCodeConsigne").show();
	$('.codeQuiz').html(game_id);
	$("#mini_code").html(game_id);
	$('#mini_code').show();
	$.getJSON("ajax.php?action=setCodeQuiz", function(result){console.log("updateCodeQuiz done");
  console.log("newQuizOpen");
	});
}
function selectClass(class_id){
	classeSelected=true;
	quiz_class_id=class_id;
	$("#listPlayerContainer").show();
	$('#StartingPoint').hide();
	$('#waitForStudentPage').show();
	$('#waitForStudentPage').find(".consigneQuiz .quizClassConsigne").show();
	$.getJSON("ajax.php?action=setClassQuiz&class_id="+class_id, function(result){console.log("updateClass done");
  console.log("newQuizOpen");
	socket.emit('newQuizOpen');
	socket.emit('newQuizforClass',class_id);
	});
}
function recupererPseudoJoueur(player_id){
$.getJSON("ajax.php?action=getName&user_id="+player_id,function(data){
	pseudo="no name";
	pseudo=data.first_name+" "+data.last_name;
	pseudo=capitalizeFirstLetterEachWordSplitBySpace(pseudo);
	$("#player_"+data.user_id).html(pseudo)
	players["player_"+data.user_id].pseudo=pseudo;
});
}
/*function newSession()
{
  socket.emit('NewSession',function(result){
    game_id=result;
    createCookie('game_id',game_id,1/24);
    $("#mini_code").html(game_id);
    $(".code").html(game_id);
    $("#noStudentInfo").show();
    $("#nbrePlayer").hide();
    $(".player_item").remove();
    players=Array();
  });
}*/
function len(obj)
{
  //socket.emit('nbrePlayer',quiz_id,function(result){console.log("nbre de joueur",result-1);});
  nbre=0;
  for(k in obj)
  { if(true)//obj[k].connecte)
    {nbre++;}
  }
  return nbre;
}
var reboursInterval;
function start()
{
	$(".infoSurDeck").hide();
	$(".btn_print").hide();
	$(".btn_showAnswer").show();
	$(".btn_suivant").show();
	$(".btn_fin").show();
	$('.btn_quiz_container').show();
  $('#StartingPoint,#waitForStudentPage').hide();
	$('#question').show();
	if(mode=='interro'){
		$('#question').hide();
		changeRebours(cards.length);
		$('#chart_container').show();
	}
	else{$('#chart_container').hide();}
  sendNextQuestion();

}

function filterInSelect(rk)
{
  //console.log(selectedIdToWork,rk.card_id);
  return selectedIdToWork.indexOf(parseInt(rk.card_id))!=-1;
}
function filterToBeSent(rk)
{
  return rk.status=="toBeSent";
}

function filterCorrected(rk)
{
  return rk.status=="corrected" || rk.status=="correcting";
}
function getCards()
{
  cardIdToWork=[];
  //console.log("getCards",cards,deckData);
    	for(k in deckData){
        var thisCards=deckData[k];
        sentence=rand_parmi(deckData[k].sentence);
        thisCards.sentence=sentence;
        thisCards.answers=Array();
        thisCards.status="toBeSent";
        cards.push(thisCards);
      }
      if(readCookie("selectedCards_"+deck_id)){
        json_str=readCookie("selectedCards_"+deck_id);
        selectedIdToWork=JSON.parse(json_str);
        cards=cards.filter(filterInSelect);
      }

      //console.log("fin getCards",cards);
}
var card_to_send;
function sendNextQuestion()
{
  //netoyage de la derniere newCorrection
  UniqAnswers=[];
  for(k in cards)
  {
    if(cards[k].status=="correcting")
    {cards[k].status="corrected";}
  }
if(mode=='interro'){$('#chart_container').show();}else{$('#chart_container').hide();}
kToCorrect=-1;
$('.player_item').removeClass("repReceived").removeClass("goodUser").removeClass("badUser");
user_idAnswerReceived=[];
music_fond=$('.music_fond')[0].play();
answerShown=false;
restartAnimation();
$('.rebours').removeClass('outer').addClass('outer');
nbrePlayer=len(players);
nbreReponse=0;
nbreQuestionRepondu=cards.length-cards.filter(filterToBeSent).length;
pcentProgress=Math.floor(100*nbreQuestionRepondu/cards.length)+"%";
$(".progressbarDeck").css("width",pcentProgress);
//$("#nbreQuestion").html(nbreQuestionRepondu+'/'+cards.length+" questions répondus");
$('#nbreQuestion').hide();
$("#answers").html('');
$("#answers").hide();
$(".btn_showAnswer").show();
$(".btn_suivant").hide();
if(cards.filter(filterToBeSent).length!=0)
{
if(mode=='quiz')
  {
    card_to_send=rand_parmi(cards.filter(filterToBeSent));
    card_to_send.status="pending";
    socket.emit('start',[card_to_send]);
    //Affichage de la question pour le quiz
    card_id=card_to_send.card_id;
    showCard(card_id);
    $('.chart').css({zoom:1});
    state="questionSentQuiz";
  }
  else if(mode=="interro")
  { $('.chart').css({zoom:2});
		$('#question').hide();
    $('#card').hide();
    $('#sentence').hide();
    $('.btn_quiz').hide();
    $('.answers').hide();
    for(k in cards){cards[k].status="pending";}
    socket.emit('start',cards);
    state="questionSentInterro";
  }
}
else {
  fin_quiz();
  }
}

function showCard(card_id)
{
	$('#question').show();
  for(rk in cards)
  {if(cards[rk].card_id==card_id){myCard=cards[rk];}}
  console.log("show card",card_id,myCard);
  mot_trad=myCard.mot_trad;
  hasImage=myCard.hasImage;
  hasAudio=myCard.hasAudio;
  sentenceQuestion=myCard.sentence;
  repCloze=sentenceQuestion.match(/\*(.*?)\*/)[0];
  repCloze=repCloze.replace('*','');
  repCloze=repCloze.replace('*','');
  sentenceQuestion=sentenceQuestion.replace("*"+repCloze+"*","<span id='goodAnswer'><span style='display:none;height:1em;'>"+repCloze+"</span></span>");
  $("#sentence").html(sentenceQuestion);
  if(mot_trad!=""){
  $("#card").html("<span class='mot_trad_card'>"+mot_trad+"</span>");
  }else {
    $("#card").html("");
  }
  if(hasImage>0){
  $("#card").css("background-image","url(card_img/card_"+hasImage+".png)");
  }
  else {
  $("#card").css("background-image","url(img/default_card.png)");
  }
  $('#card').show();
  $('#sentence').show();
  $('.btn_quiz').show();
}



socket.on('oneAnswer', function(result) {
  console.log("oneAnswer",result);
  answer=result.answer;
  user_id=result.user_id;
  $("#player_"+user_id).addClass("repReceived");
  if(user_idAnswerReceived.indexOf(user_id)==-1)
  {
    user_idAnswerReceived.push(user_id);
    //answer contient les réponse d'un élèves à 1 ou plusieurs cartes.
    for(k in answer)
    {
      //bascule de toutes les réponses de l'élève dans l'objet cards du prof
      myCard={};
      var flagCarteTrouve=false;
      for(rk in cards)
      {
        if(cards[rk].card_id==answer[k].card_id)
          {myCard=cards[rk];flagCarteTrouve=true;}
      }
      if(flagCarteTrouve){
      myCard.answers.push({answer:answer[k].repEleve,user_id:user_id,point:-1});
      }
    }
    nbreReponse=user_idAnswerReceived.length;
    nbrePlayer=len(players);
		//Quand est-ce qu'on déclenche l'étape de correction ?
  if(state!="correction" && state!="fin" && nbreReponse>=nbrePlayer){showCorrection();}
		//On met à jour les réponses des élèves si on est en correction
	if(state=="correction"){
		showStudentAnswer(myCard);
		sendGoodUser(card_id);
	}
  //if(nbreReponse<nbrePlayer){
  //  clearTimeout(ResendTO);
  //  ResendTO=setTimeout(function(){socket.emit('answeredReceived',user_idAnswerReceived);console.log("on envoi une demande de réponse",user_idAnswerReceived);},2000);
  //  }
  }
});


var kToCorrect=-1;
var UniqAnswers=[];
function showNextCorrection()
{
	cards[kToCorrect].status="corrected";
	kToCorrect=-1;
	showCorrection();
}
function showCorrection()
{
	clearRebours();
  state='correction';
  socket.emit('pause');
  $("#answers").html("");
  //on trouve quelle carte on va corriger (pending dans cardIdToWork)
  //si on est pas en train de corriger une carte on en choisi une
    if(kToCorrect==-1){
      for(k in cards)
      {
        if(cards[k].status=="pending")
        {kToCorrect=k;}
      }
      for(k in cards)
      {
        if(cards[k].status=="correcting")
        {kToCorrect=k;}
      }
    }
    //il n'y a plus de carte en pending => fin du quiz
    if(kToCorrect==-1 || state=="fin"){fin_quiz();}
    else {
      cards[kToCorrect].status="correcting";
			//progress bar
      nbreQuestionCorrige=cards.filter(filterCorrected).length;
      pcentProgress=Math.floor(100*nbreQuestionCorrige/cards.length)+"%";
			$(".ProgressNbreCards").html(nbreQuestionCorrige+'/'+cards.length);
      $(".progressbarDeck").css("width",pcentProgress);
			//affichage de la carte
      myCard=cards[kToCorrect];
      card_id=myCard.card_id;
      showCard(card_id);
      $("#goodAnswer > span").show();
			//récupération de la réponse à la phrase.
      console.log("myCardAnswer",myCard.answers);
      showStudentAnswer(myCard);
      sendGoodUser(card_id);

      music_fond=$('.music_fond')[0].pause();
      $(".btn_showAnswer").hide();
      $(".btn_suivant").show();
      $("#answers").show();
      $("#chart_container").hide();
    }

}

function showStudentAnswer(myCard)
{
  console.log("showStudentAnswer",myCard);
  card_id=myCard.card_id;

  repCloze=myCard.sentence.match(/\*(.*?)\*/)[0];
  repCloze=repCloze.replace('*','');
  repCloze=repCloze.replace('*','');

  for(k in myCard.answers)
  {//Si la réponse n'a pas déja été donné (pas d'antécédant dans UniqAnswer=>écriture+inscription dans uniqAnswer)
    var thisAnswerData=myCard.answers[k];
    var thisAnswer=myCard.answers[k].answer.toLowerCase().trim();
    if(UniqAnswers.indexOf(thisAnswer)==-1)
    {UniqAnswers.push(thisAnswer);}
    var ThisIndexAns=UniqAnswers.indexOf(thisAnswer);
    console.log(ThisIndexAns);
    if(thisAnswerData.point==-1){
      if(thisAnswer==repCloze.toLowerCase().trim()){classPoint="goodAnswerQuiz";thisAnswerData.point=1;}else{classPoint="badAnswerQuiz";thisAnswerData.point=0;}
    }
    if(!$(".answer_"+ThisIndexAns).length && thisAnswer!=""){
      if(thisAnswerData.point==1){$("#answers").append("<div class='answer_item answer_"+ThisIndexAns+" goodAnswerQuiz' onclick='correction("+ThisIndexAns+","+card_id+");'><span id='answer_"+ThisIndexAns+"'>"+thisAnswer+"</span></div>");}
      else if(thisAnswerData.point==0){$("#answers").append("<div class='answer_item answer_"+ThisIndexAns+" badAnswerQuiz' onclick='correction("+ThisIndexAns+","+card_id+");'><span id='answer_"+ThisIndexAns+"'>"+thisAnswer+"</span></div>");}
    }
  }
}
function sendGoodUser(card_id){
  console.log("sendGoodUser");
  for(k in cards){if(cards[k].card_id==card_id){myCard=cards[k];}}
  goodUsers=[];
  for(k in myCard.answers)
  {
    user_id=myCard.answers[k].user_id;
    if(myCard.answers[k].point==1){goodUsers.push(user_id);$("#player_"+user_id).removeClass("badUser").addClass("goodUser");}else{$("#player_"+user_id).addClass("badUser").removeClass("goodUser");}
  }
  console.log(goodUsers)
  socket.emit('goodUser',{goodUsers:goodUsers,card_id:card_id});
}
function correction(answer_id,card_id)
{
console.log("correction");

for(k in cards){if(cards[k].card_id==card_id){myCard=cards[k];}}
answer2correct=UniqAnswers[answer_id];
//la réponse était bad et devient good.
//repérer tous les user de cette carte et changer point puis sendGoodUser()
if($("#answer_"+answer_id).parent().hasClass("badAnswerQuiz"))
{$("#answer_"+answer_id).parent().removeClass("badAnswerQuiz").addClass("goodAnswerQuiz");
for(k in myCard.answers){if(myCard.answers[k].answer.toLowerCase()==answer2correct){myCard.answers[k].point=1;}}
}
else {
$("#answer_"+answer_id).parent().removeClass("goodAnswerQuiz").addClass("badAnswerQuiz");
for(k in myCard.answers){if(myCard.answers[k].answer.toLowerCase()==answer2correct){myCard.answers[k].point=0;}}
}
sendGoodUser(card_id);
}

function fin_quiz()
{
	console.log("finquiz");
	clearRebours();
  $('#chart_container').hide();
if(state!='fin'){
  state="fin";
  music_fond=$('.music_fond')[0].pause();
  $(".victory")[0].play();
  $("#question").hide();
	$(".btn_showAnswer").hide();
	$(".btn_suivant").hide();
	$(".btn_fin").hide();
	$(".btn_fin2").show();
	$("#download_results").show();

  ranking();
  $("#fin").show();
  socket.emit('fin');
  }
}


function ranking()
{
  $(".player_item").removeClass("goodUser").removeClass("badUser");
  PlayersScore=[];
  rankPlayers=[];
	//je mets 0 à tous les joueurs
    for(card_rk in cards)
    {
      for(ans_rk in cards[card_rk].answers)
      {
				thisPoint=parseInt(cards[card_rk].answers[ans_rk].point);
				thisUser_id=cards[card_rk].answers[ans_rk].user_id;
				console.log("player"+thisUser_id+" has "+thisPoint+" for "+cards[card_rk].answers[ans_rk].answer);
				if(PlayersScore[thisUser_id]==undefined){PlayersScore[thisUser_id]=0;}
				PlayersScore[thisUser_id]+=Math.max(0,parseInt(thisPoint));
      }
    }
		console.log(PlayersScore);
    for(k in PlayersScore)
    {player_id=k;
			console.log("A regarder :",players,PlayersScore,player_id);
      //players["player_"+player_id]={id:player_id,pseudo:pseudo,connecte:1,cartes:[]};
			if(players["player_"+player_id]!=undefined)
			{
     	rankPlayers.push({
       pseudo:players["player_"+player_id].pseudo,
       player_id:player_id,
       score:PlayersScore[player_id]});
		 	}
    }

  rankPlayers.sort(fonctionComparaison)
  rg_classement=0;
  scoreOld=-1;

  $('#classement').html("<table id='classementTable'></table>");
  csv_data="<?php echo __("nom");?>"+'\t'+"<?php echo __("score");?>"+'\t'+"<?php echo __("nbre de question");?>"+'\r\n';
  victoryArray=[];
	nbreQuestionCorrige=cards.filter(filterCorrected).length;
  for(k in rankPlayers)
  { id=rankPlayers[k].player_id;
    pseudo=rankPlayers[k].pseudo;
    score=rankPlayers[k].score;
    csv_data+=pseudo+'\t'+score+'\t'+nbreQuestionCorrige+'\r\n';
    if(scoreOld!=score)
    {rg_classement++;scoreOld=score;}
    if(score<2){score+="pt";}else{score+="pts";}
    if(rg_classement<4){
      if(rg_classement==1){
        //$.getJSON("../../ajax.php?action=addQuizTrophy&user_id="+id, function(result){});
        victoryArray.push(parseInt(id));
      }
			if($(".pseudo_"+rg_classement).length==0){
			$('#classementTable').append("<tr><td class='rgclas' style='font-size:2em;'>"+rg_classement+"</td><td class='pseudo_"+rg_classement+"'></td><td class='score_"+rg_classement+"'></td></tr>");
			}
			$('.pseudo_'+rg_classement).append("<div>"+pseudo+"</div>");
    	$('.score_'+rg_classement).html(score);
    }
  }

	$.getJSON("ajax.php?action=setMarksQuiz&noteMax="+nbreQuestionCorrige,{marks:rankPlayers}, function(result){console.log("Marks done");});
  socket.emit('victory',victoryArray);
  eraseCookie("quiz_id");

  $("#download_results").click(function() {
  var downloadLink = document.createElement("a");
  var blob = new Blob(["\ufeff", csv_data]);
  var url = URL.createObjectURL(blob);
  downloadLink.href = url;
  downloadLink.download = "resultat quiz.xls";
  document.body.appendChild(downloadLink);
  downloadLink.click();
  document.body.removeChild(downloadLink);
  });
}
function fonctionComparaison(a,b)
{
if(a.score<b.score){return 1;}
else{return -1;}
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
var reboursCountIni=15;
var reboursCount=15;

function restartAnimation(){
  $(".rebours").css("animation-play-state","running");
  $(".rebours_num").css("color","var(--mycolor2)");
  $(".rebours").css("stroke","var(--mycolor2)");
	$(".rebours").each(function() {
  	rebours=$( this );
		newrebours=rebours.clone(true);
		rebours.before(newrebours);
		rebours.remove();
		//$("." + rebours.attr("class") + ":last").remove();
	});
	reboursInterval=setInterval(updateRebours,1000);
  $(".chart").off();
  $(".chart").on("mouseover",function(){$(".rebours_pause").html("pause");});
  $(".chart").on("click",function(){
		console.log("chart has been clicked");
    if($(".rebours").css("animation-play-state")=="paused")
    {$(".rebours").css("animation-play-state","running");
    reboursInterval=setInterval(updateRebours,1000);
    }
    else {$(".rebours").css("animation-play-state","paused");
    clearInterval(reboursInterval);}
  });
  console.log("rebour : "+reboursCountIni);
  reboursCount=reboursCountIni;
  if(mode=="interro"){$("#chart_container").show();}else{$("#chart_container").hide();}
  $(".rebours_num").html(reboursCount);
}
function clearRebours(){
	$(".rebours").css("animation-play-state","paused");
	$('.chart').hide();
	clearInterval(reboursInterval);
}

function updateRebours(){
  reboursCount--;
  $(".rebours_num").html(reboursCount);
	$('.chart').show();
  if(reboursCount==-1){state='newCorrection';showCorrection();}
  if(reboursCount==3){$(".rebours_num").css("color","red");$(".rebours").css("stroke","red");}
  if(reboursCount==7){$(".rebours_num").css("color","orange");$(".rebours").css("stroke","orange");}
}
function changeRebours(nbreCarte)
{
  reboursCountIni=$(".rebours_ini").val();
  reboursCountIni*=nbreCarte;
	console.log("changeRebours",reboursCountIni);
  $(".outer").css("animation","show100 linear "+reboursCountIni+"s");
}

function toogleVolume()
{
  music_fond=$('.music_fond')[0];
  music_fond[music_fond.paused ? 'play' : 'pause']();
  if(music_fond.paused){$("#audio_btn > img").prop("src","img/haut_parleur.png");}
  else{$("#audio_btn > img").prop("src","img/haut_parleur_mute.png");}
}

function rand_parmi(liste)
{
nbre_elem=liste.length;
r=Math.floor(nbre_elem*Math.random());
return liste[r];
}
function removeElem(elem,liste)
{
	var index = liste.indexOf(elem);
	if(index > -1){
	liste.splice(index,1);
	}
}
function printIntero()
{
	$(".printQuestionItem").remove();
	for(i in cards){
		hasImage=cards[i].hasImage;
		card_id=cards[i].card_id;
		sentence=cards[i].sentence;
		repCloze=sentence.match(/\*(.*?)\*/)[0];
	  repCloze=repCloze.replace('*','');
	  repCloze=repCloze.replace('*','');
	  sentence=sentence.replace("*"+repCloze+"*","<span class='blankSpace'></span>");

		mot_trad=cards[i].mot_trad;
	if(hasImage>0){url="card_img/card_"+hasImage+".png"}else{url="img/default_card.png"}
	htmlQuestionPrint="<div class='printQuestionItem' id='questionPrint_"+i+"' style='text-align:left;'>"
	+"<table><tr><td class='tdImage'><div style='width:150px;text-align:center;' class='imgContainerPrint' id='questionPrintCell1_"+i+"'>"
	+"<img style='display:inline-block;box-shadow:1px 1px 3px grey;' src='"+url+"'>"
	+"<div>"+mot_trad+"</div>"
	+"</div></td>"
	+"<td style='text-align:left;'><div class='printSentence'>"+sentence+"</div></td></tr></table>"
	+"</div>";

	$("#print_questionContainer").append(htmlQuestionPrint);

	}
}
var cards=[];
getCards();
printIntero();

</script>
<style type="text/css" media="print">
.fond,#printIntero,#print_questionContainer{float: none !important;}
.printQuestionItem{
display:block;
page-break-inside: avoid;
margin:-4px;
}
.tdImage{width:80px;}
.printQuestionItem table{width:100%;}
.blankSpace{padding:0 80px;width:160px;border-bottom:1px grey solid;}
#printIntero{text-align:center;display:block;}
.infoSurDeck{display:inline-block;}
.imgContainerPrint{display: inline-block;width:80px;vertical-align:middle;}
.imgContainerPrint img{object-fit: cover;
  width:60px;
  height:60px;}
/*img{page-break-inside: avoid;}*/
/*.imgContainerPrint{page-break-inside: avoid;}*/
.container_global{display:none;}
#navbar{display:none;}
.printSentence{display:inline-block;vertical-align:middle;}

</style>
<audio class="nextAudio" preload="auto">
    <source src="audio/next.mp3" type="audio/mpeg">
    <source src="audio/next.ogg" type="audio/ogg">
</audio>
<audio class="victory" preload="auto">
    <source src="audio/victory.mp3" type="audio/mpeg">
    <source src="audio/victory.ogg" type="audio/ogg">
</audio>
</body>
</html>
