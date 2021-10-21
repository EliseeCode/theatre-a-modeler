<!DOCTYPE html>
<?php
include_once ("db.php");
session_start();
include_once ("local_lang.php");
		$user_id=-1;
		$pseudo="";
		$new_user=true;
		if(isset($_SESSION["user_id"]) && isset($_SESSION['active'])){
			$new_user=false;
			$user_id = $_SESSION['user_id'];
			$pseudo = $_SESSION['first_name']." ".$_SESSION['last_name'];
		}
		else{
			//création d'un user anonymus
			$avatar_id=rand(0,719);
			$sql = "INSERT INTO users (status,avatar_id) "
		          . "VALUES ('anonyme',".$avatar_id.")";
		  $mysqli->query($sql);
		  $user_id=$mysqli->insert_id;

			$sql = "INSERT INTO `user_avatar`(`user_id`, `avatar_id`, `status`)
			VALUES (".$user_id.",".$avatar_id.",'selected')";
			$mysqli->query($sql);

			$_SESSION["user_id"]=$user_id;

		}
		$quiz_id=-1;
		if(isset($_GET["quiz_id"])){
			$quiz_id=(int)$_GET["quiz_id"];
		}

		//$lang_interface="en";
		if(isset($_GET["target_lang"])){$_SESSION["target_lang"]=(int)$_GET["target_lang"];}

		echo "<script>lang_interface=".json_encode($lang_interface).";</script>";

		echo "<script>new_user=".json_encode($new_user).";</script>";
		echo "<script>quiz_id=".json_encode($quiz_id).";</script>";
    echo "<script>user_id=".json_encode($user_id).";</script>";
		echo "<script>pseudo=".json_encode($pseudo).";</script>";
    ?>

<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta name="viewport" content="width=device-width, user-scalable=no,initial-scale=1">
    <title><?php echo __("Quiz en classe");?></title>
    <link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16" />
  <link rel="stylesheet" href="css/myStyle.css">
  <link href="css/navStyle.css" rel="stylesheet">
  <link href="css/styleEntete.css" rel="stylesheet">
  <link href="css/main.css?ver=<?php echo filemtime('css/main.css');?>" rel="stylesheet">
  <link href="css/quiz.css?ver=<?php echo filemtime('css/quiz.css');?>" rel="stylesheet">

	<script src='js/jquery-3.3.1.min.js'></script>

  <script src="socket.io/socket.io.js"></script>

  <script src="js/cookiesManager.js"></script>
   </head>
<body class="fond">


      <div class="progressbarDeck"></div>


      <script type="text/javascript" src="js/menu-breaker.js"></script>
      <script>

      $('.desktop').menuBreaker();
  $(window).on('load resize', function () {
  $('.mobile').height($(window).height() - $('nav').height());
  });

  if((typeof window!=='undefined')&&(typeof window.onload!=='undefined')){
     //socket=io.connect(window.location.protocol+'//'+window.location.hostname+":3030");
		 socket=io.connect('https://www.exolingo.com/socketLink');
   }
      </script>


<style>
.message{position:fixed;background-color:red; color:white;bottom:30px;padding:10px;}
#openMenu li {display:inline-block;}

/*#formCode{padding-top:100px;}*/
#navbar{border-bottom:gold;}
.container_global_eleve{filter:blur(5px);}
.quizClass_item{box-shadow: 0 0 4px grey;
    display: inline-block;
    margin: 10px;
		font-size:1.5em;
    padding: 30px 30px;
    text-align:center;
    position:relative;
		background-color:white;
		border-radius:6px;
		border-bottom:6px grey solid;}
		.quizClass_item:before {
			content: " ";
			position: absolute;
			z-index: -1;
			top: -5px;
			left: -5px;
			right: -5px;
			bottom: -13px;
			animation: blinkBorder 2s ease Infinite;
			border-radius:6px;
		}
.quizClass_item:hover{box-shadow:1px -1px 3px grey;top:3px;right:3px;}
.class_name{font-size:1.2em;}
.class_promo{font-size:0.9em;color:grey;}
.prof_name{font-style:italic;}
.prof_name:before{content:'par ';}
#navCard{text-align:center;}
.navCard_item{display:inline-block;background-color:grey;border:2px solid grey;width:15px;height:15px;border-radius:50%;margin:5px;vertical-align:middle;}
.navCard_item.pending{background-color:white;border:2px solid grey;}
.navCard_item.activeNavCard{width: 25px;  height: 25px; margin: 4px;}
.page{display:none;min-height:100vh;padding-top:70px;}
.buttonRetourList>a>img{width:40px;}
.envoyerPseudo,.envoyerCode,.connectLinkContainer{margin:auto;width:350px;max-width:75%;}
.flexBox{min-height:60px;color:black;display:flex !important;flex-direction:column;padding:0 !important;}
.flexBox >div{margin:auto;}
.msgCode,.msgPseudo{text-align:center;color:#ff6060;padding:10px;}

.choixLangue{width: 300px;
    max-width: 100%;
    text-align: left;min-width:100px;position:relative;top:-5px;display:inline-block; border-radius:0 0 30px 30px;text-align:center;z-index:1031;}
    .selectLang ul li{list-style-type: none;}
    .selectLang ul {padding:0;}
.tiretteLang{min-width:100px;transition:1s;z-index:1032;overflow:visible;height:0;right:20px;text-align:center;position:absolute;top:0;}
.lang_item{text-decoration:none;color:black;padding:5px; margin:10px;display:inline-block;border-radius:10px;}

.choixLang_item{display:inline-block;width:90px;box-shadow:0 0 3px grey;margin:3px;padding:10px;background-color:white;border-radius:5px;text-align:left;}
.choixLang_item:hover{transform:scale(1.1);}
.lang_name{padding-left:10px;text-transform: uppercase;}
.exoLingoTitle{color:white;text-shadow:0 0 3px grey;font-size:3em;font-family:exolingoTitre;}
.connectLink{color:var(--mycolor2);font-size:1em;margin-top:20px;}
.connectLinkContainer{margin-top:20px;text-align:left;}
</style>

<div class="tiretteLang">
  <div class="choixLangue" onclick="$('.selectLang').slideToggle();">
    <div class="interfaceLangActive"></div>
    <div class="selectLang" style="display:none;">
      <ul class="interfaceLangChoice">
      <ul>
    </div>
  </div>
</div>

<div class="center container_global_eleve">
  <div id="formPseudo" class="page">
		<h1 class="exoLingoTitle">ExoLingo</h1>
    <div id="inputPseudoEleve"><input type="text" oninput='$(".msgPseudo").html("");' class="repPseudoEleve" placeholder="<?php echo __("Prénom");?>">
		<br>
		<div class="msgPseudo"></div>
    <div class="envoyerPseudo BtnStd1" onclick='sendPseudo();'><?php echo __("Go !");?></div>
		<div class="connectLinkContainer"><a href="loginPage.php" class="connectLink"><?php echo __("Connecte-toi");?></a></div>
    </div>
  </div>

	<div id="QuizDeMaClasse" class="page">
		<h1 class="exoLingoTitle">ExoLingo</h1>
		<div class="quizClass_block">
			<h2 style="" class="consigne"><?php echo __("Plusieurs quiz on été trouvé, Sélectionnez votre classe.");?></h2>
			<div id="quizClass_container"></div>
		</div>
		<div class="quizCode_block">
	    <div id="inputCodeEleve"><input type="number" oninput='$(".msgCode").html("");if(this.value.length>=6){sendCode();}' class="repCodeEleve" placeholder="<?php echo __("Code du quiz");?>"></div>
			<br>
			<div class="msgCode"></div>
	    <div class="envoyerCode BtnStd1" onclick='sendCode();'><?php echo __("Rejoindre");?></div>
		</div>
	</div>

  <div id="attente" class="page">
    <h2 style="margin:10vh;" id="messageAttente"><?php echo __("En attente ...");?></h2>
    <br>
  </div>

  <div id="form" class="page">
		<div id="navCard"></div>
    <div>
      <div id="nbreQuestion"><?php echo __("0 question");?></div>
      <div class="consigneCarte" style="margin:10px 0;"><?php echo __("Compléter");?></div>
      <div id="card" style="margin:10px 0;"></div>
      <div id="sentence" style="margin:10px 0;"></div>
      <div id="btn_next" class="envoyer" onclick='next();'><?php echo __("Valider");?></div>
    </div>
  </div>



  <div id="goodAnswerEleve" class="page">
    <div class="consigneCarte"><?php echo __("Bravo !");?></div>
    <img src="img/check2.png" style="width:80%; max-width:300px;margin-top:20vh;">
    <div class="myGoodAnswer" style="margin-top:20px;"></div>
  </div>

  <div id="badAnswerEleve" class="page">
    <div class="consigneCarte"><?php echo __("C'est pas ça !");?></div>
    <img src="img/fail.png" style="width:80%; max-width:300px;margin-top:20vh;">
    <div class="myBadAnswer" style="margin-top:20px;"></div>
  </div>

  <div id="fin" class="page" style="text-align:center;">
    <h1 class="victory_titre" style="display:none;"><?php echo __("Victoire");?></h1>
    <img class="victory_img" src="img/trophy.png" style="width:60%; max-width:300px;display:none;">
    <h2><?php echo __("Votre score :");?></h2>
    <div id="score"></div>
    <div id="errorList">
      <h2><?php echo __("Liste des mots à réviser");?></h2>
    </div>
    <h2 class="nbreVictory"></h2>
	</div>
	<div id="disconnected"><span style="margin:auto"><?php echo __("Veuillez attendre quelques instants... problème de connection");?></span></div>

</div>
<script>

data=[];
status="wait";
numQuestion=0;
$(".repEleve").on("keypress", function(e){if(e.which==13){next();}});
$(".repCodeEleve").on("keypress", function(e){if(e.which==13){sendCode();}})
$(".repPseudoEleve").on("keypress", function(e){if(e.which==13){sendPseudo();}})

window.addEventListener("blur",function(){socket.emit('playerAway',{user_id:user_id});});
window.addEventListener("focus",function(){socket.emit('playerInAgain',{user_id:user_id});});

socket.on('reconnect', function() {
	console.log("reconnect");
	//quiz_id=readCookie('quiz_id');
	socket.emit('game', quiz_id ,function(result){if(result=="game_done"){
	    socket.emit('playerJoin',{user_id:user_id, quiz_id:quiz_id,pseudo:pseudo});
			$(".container_global_eleve").css({"filter":"none"});
			$("#disconnected").hide();
	    }
	    else {
	    alert("probleme avec la connection au groupe : "+result);
	    }
	  }
	);

});
socket.on('connect', function() {
	console.log("connect");
	$(".container_global_eleve").css({"filter":"none"});
	$("#disconnected").hide();
});
socket.on('connected', function() {
	console.log("connected");
	$(".container_global_eleve").css({"filter":"none"});
	$("#disconnected").hide();
});
socket.on('disconnect', function() {
	console.log("disconnected");
	$(".container_global_eleve").css({"filter":"blur(5px)"});
	$("#disconnected").show();
});
socket.on('whoIsThere', function(){
  socket.emit('playerJoin', {user_id:user_id,quiz_id:quiz_id,pseudo:pseudo});
});
socket.on('state', function(result){
  $('.message').html(result);
	if(result.dest==user_id){
	  console.log("STATE",result);//result.state can be : rassemblement,newSessionProf
	  switch(result.state){

	  case 'rassemblement':
	    getPage("attente");status="wait";
	  break;

	  case 'questionSentQuiz':
	    status="formQuiz";
	    startQuestion(result.data);
	  break;
	  case 'questionSentInterro':
	    status="formInterro";
	    startQuestion(result.data);
	  break;
	  case 'start':
	    status="start";
	    //if(readCookie("cards_"+quiz_id)!=null)
	    //{cards=JSON.parse(readCookie("cards_"+quiz_id));}
	    getPage("form");
	    status="form";
	  break;
	  case 'fin':
	    fin();
	    //getPage("form");
	  break;
	  }
		$(".deck_name").html(result.deck_name);
	}
});

var card_id;
var cards=[];
var allCards=[];
var dataQuestion=[];
var question;

socket.on('start', function(dataQuestion) {
    status="start";
    startQuestion(dataQuestion);
});

if(quiz_id!=-1){
connectToQuiz(quiz_id);
}
function sendCode()
{
	console.log("sendCode");
	game_id=$('.repCodeEleve').val();
	$(".msgCode").html("<span style='color:grey;'>recherche</span>");
	$.getJSON("ajax.php?action=checkQuiz&game_id="+game_id, function(result){
		quiz_id=parseInt(result);
		if(quiz_id>0){
			createCookie("game_id",quiz_id,1/24);
			console.log("checkQuiz passed"+quiz_id);
			socket.emit('game', quiz_id ,function(result){if(result=="game_done"){
			    socket.emit('playerJoin',{user_id:user_id, game_id:game_id,pseudo:pseudo});
			    $('.codeAttente').html(game_id);
			    getPage('attente');
			    createCookie('game_id',game_id,1/24);
			    }
			    else {
			    //alert("probleme avec la connection au groupe : "+result);
					$(".msgCode").html("<?php echo __("probleme avec la connection au groupe");?>");
			    }
			  }
			);
		}
		else{
					$(".msgCode").html("<?php echo __("Quiz introuvable");?>");
		}
	});
}

function startQuestion(data)
{
  console.log("startQuestionData",data,cards);
	cards=[];
  //on ajoute les cartes qu'il n'a pas encore recu (cas du quiz ou les cartes arrivent les une apres les autres)
  for(k in data)
  {
    thisCardId=parseInt(data[k].card_id);
    flag=true;
    for(rk in allCards){
			console.log(allCards[rk].card_id,thisCardId,allCards[rk].card_id==thisCardId);
			if(parseInt(allCards[rk].card_id)==thisCardId){flag=false;}
		}
    if(flag){
			data[k].status="pending";
			data[k].repEleve="";
			data[k].rand=Math.random();
			cards.push(data[k]);
			allCards.push(data[k]);
			}
  }
	console.log("apres remplacement eventuel",cards);
	cards=cards.sort(function(a,b){return a.rand-b.rand});
	$(".navCard_item").remove();
	for(k in cards)
	{
		navCardClass="";
		if(cards[k].status=='pending'){navCardClass="pending";}
		if(cards[k].status=='pending'|| cards[k].status=='answered'){$("#navCard").append("<div id='navCard_item_"+k+"' class='navCard_item "+navCardClass+"' onclick='showQuestionRk("+k+");'></div>");}
	}
	//console.log(cards);

  //on enregistre toutes les cartes avec le status "answered" ou "pending" dans cards_#game_id
  //createCookie("cards_"+quiz_id,JSON.stringify(cards),1/12);
  pcentProgress=Math.floor(100*(cards.length-cards.filter(NotYetAnswerd).length)/cards.length)+"%";
  $(".progressbarDeck").css("width",pcentProgress);
  //$('#nbreQuestion').html(cards.filter(NotYetAnswerd).length+"/"+cards.length+" questions");
  $('#nbreQuestion').hide();
  //choisir une carte au hazard parmi celles qui reste à repondre
  //question=rand_parmi(cards.filter(NotYetAnswerd));
	question=rand_parmi(cards);
  showQuestion(question);
  myAnswer="";
  $(".repEleve").val("").focus();
  getPage("form");
}

var myAnswer='';
function showQuestionRk(k){
	$(".activeNavCard").removeClass("activeNavCard");
	$("#navCard_item_"+k).addClass("activeNavCard");
	showQuestion(cards[k]);
}
function showQuestion(question)
{
  console.log("showQuestion",question);
  if(question!=false)
  {
    status="showQuestion";
    console.log(question);
    question.status='pending';
    card_id=question.card_id;
    sentenceQuestion=question.sentence;
    mot_trad=question.mot_trad;
    hasImage=question.hasImage;
    repCloze=sentenceQuestion.match(/\*(.*?)\*/)[0];
    //console.log(repCloze);
    repCloze=repCloze.replace('*','');
    repCloze=repCloze.replace('*','');
    sentenceQuestion=sentenceQuestion.replace("*"+repCloze+"*","<span id='Answer'><input type='text' class='repEleve'></span>");
    $("#sentence").html(sentenceQuestion);
		$(".repEleve").val(question.repEleve);
    $(".repEleve").off();
    $(".repEleve").on("keypress", function(e){if(e.which==13){next();}});
    //afficher une premiere question
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
  }
  else{
    if(status!="wait"){getPage("attente");status="wait";console.log("Answered Sent",cards);socket.emit('answer', cards);}
  }
}
function next()
{
  //card_id contient le num de la carte en cours.
  console.log(cards,card_id);
	myK=0;
	//je retrouve le rang de la card_id
  for(k in cards){if(cards[k].card_id==card_id){myCard=cards[k];myK=k;}}
  repEleve=$(".repEleve").val();
  myCard.repEleve=repEleve;
	if(repEleve!=""){$("#navCard_item_"+myK).removeClass("pending");}
  myCard.status="answered";
  //question=rand_parmi(cards.filter(NotYetAnswerd));
  //showQuestion(question);
	if(cards.length==1){
		getPage("attente");status="wait";console.log("Answered Sent",cards);socket.emit('answer', cards);
	}
	else{
	showQuestionRk((parseInt(myK)+1+cards.length)%cards.length);
	$(".repEleve").focus();
	}
	updateAllCard();
  //createCookie('cards_'+quiz_id,JSON.stringify(allCards),1/12);
  //createCookie('question'_game_id,JSON.stringify(cards),1/12);
}

socket.on('pause', function() {
	console.log("pause");
  if(status!="wait" && status!="fin"){
    getPage("attente");
    status="wait";
		for(k in cards){if(cards[k].card_id==card_id){myCard=cards[k];}}
	  repEleve=$(".repEleve").val();
	  myCard.repEleve=repEleve;
	  myCard.status="answered";
		console.log("Answered Sent",cards);
    socket.emit('answer', cards);
  }
  //socket.emit('answer', dataQuestion);
});

socket.on('answeredReceived', function(data){
  console.log("answeredReceived",data);
  if(data.indexOf(user_id)==-1){console.log("answeredResent");socket.emit('answer', cards);}
})

//correction
scores=[];
socket.on('goodUser', function(data){
  console.log(data);
  goodUsers(data);
});
function goodUsers(data)
{
  card_id=data.data.card_id;
  for(k in cards){if(cards[k].card_id==card_id){myCard=cards[k];}}
	myCard.status="corrected";
  if(data.data.goodUsers.indexOf(user_id)!=-1)
  {
    if(scores[card_id]!=1){
       scores[card_id]=1;
       myCard.point=1;
			 if(status=="fin"){fin();}
			 else{
				 addCoins(1);
       getPage("goodAnswerEleve");
       $(".myGoodAnswer").html(myCard[repEleve]);
       playAudio("success");}
      }
  }
  else {
    if(scores[card_id]!=0){
      scores[card_id]=0;
      myCard.point=0;
			if(status=="fin"){fin();}
			else{
      getPage("badAnswerEleve");
      $(".myBadAnswer").html(myCard[repEleve]);
      playAudio("success");
			}
      }
  }
	updateAllCard();
  //createCookie('cards_'+quiz_id,JSON.stringify(allCards),1/12);
}

socket.on('victory', function(victoryArray) {
  console.log(victoryArray);
  fin();
  if(victoryArray.indexOf(user_id)!=-1)
  {
  playAudio("victory");
  $(".victory_titre").show();
  $(".victory_img").show();
  $.getJSON("ajax.php?action=getQuizTrophyNumber&user_id="+user_id, function(result){
    //if(result<=1){$('.nbreVictory').html(result+" victoire au total");}
    //if(result>1){$('.nbreVictory').html(result+" victoires au total");}
  });
  }
});


socket.on('fin', function() {
	fin();
});

function fin()
{
	console.log("function fin");
	status="fin";
	getPage("fin");
	$('#errorList').html("");
	score=0;
	errorExist=false;
	for(k in allCards)
	{
	if(allCards[k].point!=undefined)
	{
	score+=allCards[k].point;
	  if(allCards[k].point==0)
	  {
	    sentence=allCards[k].sentence;
	    repCloze=sentence.match(/\*(.*?)\*/)[0];
	    repCloze=repCloze.replace('*','');
	    repCloze=repCloze.replace('*','');
	    sentence=sentence.replace("*"+repCloze+"*","<span style='display:inline-block;vertical-align:middle;'><span style='color:lime;'>"+repCloze+"</span><br><span style='color:red;text-decoration: line-through;'>"+allCards[k].repEleve+"</span></span>");
	    imageHTML="<img style='float:left;' src='img/default_card.png' width='50px' height='50px'>";
	    if(allCards[k].hasImage>0){imageHTML="<img style='float:left;' src='card_img/card_"+allCards[k].hasImage+".png' width='50px' height='50px'>";}

	    $('#errorList').append('<div class="erreur_item">'+imageHTML+sentence+'</div>');
	    errorExist=true;
	  }
	}
	}
	if(!errorExist){$('#errorList').hide();}

	if(score<2){$("#score").html(score+" point");}
	else{$("#score").html(score+" points");}
}

function updateAllCard()
{
		for(k in cards)
		{
			cardExist=false;
			for(j in allCards)
			{
				if(cards[k].card_id==allCards[j].card_id){allCards[j]=cards[k];cardExist=true;}
			}
			if(!cardExist){allCards.push(cards[k]);}
		}
}

function playAudio(filename)
	{console.log("on demande l'écoute de "+filename);
	$("."+filename)[0].play();
	/*if(!$("#audio_"+filename).length){
		console.log("creation de l'élem Audio");
	  soundFile="../../audio/"+filename+".wav";
		$("body").append('<audio id="audio_'+filename+'" src="'+soundFile+'">');
		}
	$("#audio_"+filename).get(0).play();*/
}
function rand_parmi(liste)
{
	nbre_elem=liste.length;
	rang=Math.floor(nbre_elem*Math.random());
	if(nbre_elem!=0){return liste[rang];}else{return false;}
}

function getPage(pageName)
	{$('.page').hide();
	$('#'+pageName).show();
	if(pageName=="form"){$(".repEleve").focus();}
}

function addQuizClassItem(data)
{
	class_name=data.class_name;
	quiz_id=data.quiz_id;
	class_promo=data.promo;
	prof_name=data.prof_name;
	$('#quizClass_container').append('<div class="quizClass_item" onclick="connectToQuiz('+quiz_id+');"><div class="class_name">'+class_name+'</div><div class="class_promo">'+class_promo+'</div><div class="prof_name">'+prof_name+'</div></div>')
}

function init()
{//récupérer tous les quiz me concernant et les mettre dans
	if(new_user!=true)
	{
		$.getJSON("ajax.php?action=getAllGames", function(data){
			//on affiche les QuizClassItem dans le container.
			$('.quizClass_item').remove();
			for(i in data){addQuizClassItem(data[i]);}
				if(data.length==0){$('.quizClass_block > .consigne').html("<?php echo __("Insérer le code du quiz donné par votre professeur.");?>");}
				else{$('.quizClass_block > .consigne').html("<?php echo __("Sélectionnez le quiz de votre professeur");?>");}
	  });
		getPage("QuizDeMaClasse");
	}
	else{
		$(".quizClass_block").remove();
		getPage("formPseudo");
	}

//apres la selection, enlever la mise à jour des quiz et connectToQuiz(quiz_id);
}

quizChosen=false;
pseudo="";
socket.on('newQuizOpen',function(){
	console.log("newQuizOpen");
	if(!quizChosen)
	{init();}
});

init();

function sendPseudo(){
	console.log("send Pseudo");
	pseudo=$(".repPseudoEleve").val();
	pseudoReg=new RegExp("([A-Za-z])+").exec(pseudo);
	if(pseudo==""){
		$(".msgPseudo").html("<?php echo __("Indiquez votre prénom.");?>");
	}
	else if(pseudoReg==null){
		$(".msgPseudo").html("<?php echo __("Votre prénom est composé de lettre n'est-ce pas ?");?>");
	}
	else{
	$.getJSON("ajax.php?action=updateFirstName&pseudo="+pseudo, function(result){
		console.log(result,pseudo);
	});
	getPage("QuizDeMaClasse");
	}
}
function connectToQuiz(quiz_id){
	quizChosen=true;
	socket.emit('game', quiz_id ,function(result){if(result=="game_done"){
    socket.emit('playerJoin',{user_id:user_id, quiz_id:quiz_id,pseudo:pseudo});
    $('.codeAttente').html(quiz_id);
    getPage('attente');
    //createCookie('quiz_id',quiz_id,1/24);
		}
    else {
    alert("<?php echo __("probleme avec la connection au groupe");?> : "+result);
    }
  }
	);
}
//}

function NotYetAnswerd(rk)
{
  return rk.status!="answered";//pending or answered
}
function addCoins(coinsReward){
$.getJSON("ajax.php?action=addCoins&nbre="+coinsReward, function(result){
		$('.nbreCoins').html(result.nbreCoins);
		for(k=0;k<result.coins2add;k++){
			delay=100*k;
			$('.nbreCoins').parent().append('<div class="animatedCoin" style="animation-delay:'+delay+'ms;"><img src="img/golden_coin.png" width="40px"></div>');
		}
		$(".animatedCoin").on("animationend",function(){$(this).remove();play_audio_coin();});
});
}




function FillInLang(){
  console.log("fillinlang")
  $.getJSON("ajax.php?action=getAllLang", function(result)
  {
    console.log(result);
    $(".interfaceLangActive").html("<div class='choixLang_item'><span class='tinyFlag flag_"+lang_interface+"'></span><span class='lang_name'>"+lang_interface+"</span></div>");
    for(k in result)
    {
    lang_code2=result[k].lang_code2;
    lang_interface_active=result[k].lang_interface;
    if(lang_interface_active==1 && lang_interface!=lang_code2){
      lang_name=result[k].lang_name_Origin;
      if($('interfaceLangChoice_'+lang_code2).length==0)
        {$(".interfaceLangChoice").append('<a href="quizEleve.php?lang='+lang_code2+'"><li class="interfaceLangChoice_'+lang_code2+' choixLang_item"><span class="tinyFlag flag_'+lang_code2+'"></span><span class="lang_name">'+lang_code2+'</span></li></a>');
        }
      }
    }
  });
}
FillInLang();

</script>
<audio class="fail" preload="auto">
    <source src="audio/fail.mp3" type="audio/mpeg">
    <source src="audio/fail.ogg" type="audio/ogg">
</audio>
<audio class="success" preload="auto">
    <source src="audio/success.mp3" type="audio/mpeg">
    <source src="audio/success.ogg" type="audio/ogg">
</audio>
<audio class="victory" preload="auto">
    <source src="audio/victory.mp3" type="audio/mpeg">
    <source src="audio/victory.ogg" type="audio/ogg">
</audio>
</body>
</html>
