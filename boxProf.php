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
    echo "<script>user_id=".json_encode($user_id).";</script>";
		$deck_id=(int)$_GET['deck_id'];
		echo "<script>deck_id=".json_encode($deck_id).";</script>";
		$expire=time()+60*60*3;//session de 3h
		$sql = "INSERT INTO quiz (expire,prof_id)"
						. "VALUES (".$expire.",".$user_id.")";
		$mysqli->query($sql);
		$quiz_id=$mysqli->insert_id;
		$game_id=($quiz_id*987)%10000;
		$mysqli->query("UPDATE quiz SET status='over' WHERE game_id=".$game_id." OR prof_id=".$user_id);
		$mysqli->query("UPDATE quiz SET status='open',deck_id=".$deck_id.", game_id=".$game_id." WHERE quiz_id=".$quiz_id);
		echo "<script>game_id=".json_encode($game_id).";</script>";

		$result = $mysqli->query('SELECT deck_name ,hasImage FROM decks WHERE deck_id='.$deck_id);
	  $myresult = $result->fetch_assoc();
		$deckInfo=$myresult;
		$result->free();
    echo "<script>deckInfo=".json_encode($deckInfo).";</script>";

		$result = $mysqli->query('SELECT distinct cards.card_id,cards.mot,cards.hasImage FROM cards WHERE deck_id='.$deck_id.' ORDER BY cards.card_id ASC');
    $cardIdold=-1;
    $itemsData=array();
    while ($card = $result->fetch_assoc())
    {
          array_push($itemsData,$card);
		}
		$result->free();
    echo "<script>var itemsData=".json_encode($itemsData).";</script>";
    ?>

<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta name="viewport" content="width=device-width, user-scalable=no,initial-scale=1">
    <title>Box Them All</title>
    <link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16" />
  <link rel="stylesheet" href="css/myStyle.css">
  <link href="css/navStyle.css" rel="stylesheet">
  <link href="css/styleEntete.css" rel="stylesheet">
  <link href="css/main.css" rel="stylesheet">
  <link href="css/quiz.css" rel="stylesheet">

	<script src='js/jquery-3.3.1.min.js'></script>
	<script src="js/jquery-ui.js"></script>
  	<script src="socket.io/socket.io.js"></script>

  <script src="js/cookiesManager.js"></script>
   </head>
<body class="fond">
	<style>
	@keyframes blinkBorderGreen {
	  0%{border:6px #00ff0080 solid;}
	  100%{border:4px #00ff0000 solid;top:-15px;left:-15px;right:-15px;bottom:-15px;}
	}
	#addBox{vertical-align:middle; transition:0.1s;position:relative;margin:30px;background-image:url(img/box.png);background-size:cover;width:100px;height:100px;display:inline-block;}
	#addBox:hover{margin:25px;width:110px;height:110px;}
	#addBox:after{content: "+";
    position: relative;
    top: 21px;
    left: 25px;
    font-size: 4em;
    color: lime;
    text-shadow: 0 0 3px black;}
	#addBox:before{content: " ";
  position: absolute;
  z-index: -1;
  top: 10px;
  left: 10px;
  right: 10px;
  bottom: 10px;
	border-radius:50%;
  animation: blinkBorderGreen 2s ease Infinite;}
	#CheckAll{vertical-align:middle;transition:0.1s;position:relative;margin:30px;background-image:url(img/check3.png);background-size:cover;width:100px;height:100px;display:inline-block;}
	#CheckAll:hover{margin:25px;width:110px;height:110px;}
	#CheckAll:active:before{content: " ";
  position: absolute;
  z-index: -1;
  top: 10px;
  left: 10px;
  right: 10px;
  bottom: 10px;
	border-radius:50%;
  animation: blinkBorderGreen 2s ease;}

	.box,.item{cursor: grab;position:relative;vertical-align:middle;text-align:center;z-index:1;display:inline-block; margin:10px;box-shadow:0 0 2px black;}
	.box:hover,.item:hover{top:3px;left:3px;box-shadow:0 0 5px black;}
	.item{display:inline-flex;min-width:180px;min-height:180px;background-size:cover;background-color:white;}
	.box{background-color:#362a12;min-width:400px;min-height:300px;padding:40px;
		border: 2px solid black;
		border-image:url(img/box3.png);
		border-image-slice: 14%;
		border-width:30px;
		box-shadow: 0 0 50px black inset;
	}
	.highLight{background-color:yellow;}
	.label{position:absolute;top:-20px;left:-20px;padding:10px;font-size:1.3em;background-color:#ffe090;transform:rotate(-10deg);}
	.delIcon{position:absolute;bottom:5px;right:5px;padding:20px;background-image:url(img/del.png);background-size:cover;}
	.delIcon:hover{background-color:red;}
	.mot{margin:auto;background-color:#ffffff80;width:100%;padding:10px;}
	#rootBox{display:inline-block;}
	.wrongPlaced{box-shadow:0 0 0 8px orange;}
	</style>
  <nav id="navbar">
        <div class="menu">
          <ul class="desktop" onclick="$('.mobile').removeClass('open');">
            <li style="float:left;"><a href="decks.php"><?php echo __("retour aux listes");?></a></li>
            <li><a href="#">Box Them All</a></li>
						<li><a href='#' class="minicode"></a></li>
            <!--<li><a href="#" onclick='getPage("attente");'>attente</a></li>
            <li><a href="#" onclick='getPage("goodAnswerEleve");'>good</a></li>
            <li><a href="#" onclick='getPage("badAnswerEleve");'>bad</a></li>-->

            <!--<li style="float:right;"><a href="#" onclick='getPage("formCode");'>nouveau code</a></li>-->
            <!--<li style="float:right;"><a href="#" onclick='logout();'>changer de session</a></li>-->
            <!--<li><a href="#" onclick='getPage("fin");'>fin</a></li>-->
          </ul>
          <ul class="mobile"  onclick="$('.mobile').removeClass('open');">
          </ul>
        </div>
        <div id="openMenu">ExoLingo - MENU</div>
        <div class="progressbarDeck"></div>
      </nav>
      <div class="message" style="display:none;"></div>

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
/*#formCode{padding-top:100px;}*/
#navbar{border-bottom:8px solid var(--mycolor2fonce);}
#listPlayerContainer{
  float:left;
  width:300px;
  background-color:white;
  box-shadow:0 0 4px grey;
  padding:10px;
  position:relative;
  padding-top:30px;
}
#noStudentInfo{color:grey;text-align:center;}
.online{color:black;}
.offline{color:grey;}
#infoSurDeck{border-bottom:8px solid var(--mycolor2);background-color:white;padding:10px;display:inline-block;box-shadow:0 0 4px grey;}
#infoSurDeck > img{width:100px;display:inline-block;float:left;vertical-align:middle;}
#titreDeck{margin-left:20px;display:inline-flex;height:100px;}
#titreDeck > *{margin:auto;}
#goodAnswer{background-color:white;padding:10px; min-width:200px;border-bottom:grey solid 3px;display:inline-block;height: 2em;}
#sentence{font-size:2em;margin:20px 0 40px 0;}
.badUser{color:red;}
.goodUser{color:lime;}
.msg{position:fixed,left:0;bottom:0;width:50px;height:50px;background-color:lime;color:black;}
.loading{position:fixed;top:0;left:0;right:0;bottom:0;background-color:#ffffffA0;color:black;text-align:center;display:flex;}
.class_item{border: 1px solid grey;
    display: inline-block;
    margin: 5px;
    padding: 10px 10px;
    text-align:center;
    position:relative;}
.class_item:hover{box-shadow:1px 1px 5px grey;top:-2px; left:-2px;}
.ColonneBox{;vertical-align: middle;}
.goodBox{color:lime;}
.badBox{color:red;}
</style>


<div class="center" style="margin-top:70px;">
	<div id="listPlayerContainer">
		<h2><?php echo __("Liste des apprenants");?></h2>
		<div id="noStudentInfo"><?php echo __("Aucun élève inscrit");?></div>
		<div id="listPlayer"></div>
		<div id="nbrePlayer" style="color:grey;margin:20px;"></div>
	</div>

	<div id="StartingPoint">
		<div class="colonneQuiz1">
			<div class="btn_start btn_quiz" onclick="startBox();"><?php echo __("Commencer la mise en boite");?></div>
		</div>

		<div class="ColonneBox" id="pageIntro">
			<div class="flexContaine">
				<div>
					<h2 id="infoSurDeck" style="color:black;"><?php echo __("Pas d'info");?></h2>
					<br>
					<div class="consigneQuiz"><?php echo __("Connectez-vous sur ExoLingo<br> puis cliquez sur <br>'REJOINDRE UNE Mise en Boite'.");?>
					</div>
					<p class="codeLine"><div class="code"></div><p>
				<img src="../img/box.png" class="imgfusee" style='max-width:40%;'>
				<!--	<p class="codeLine"><span class="code"></span><span onclick="newSession();" style="position:absolute;bottom:0;"><img src="img/flip.png" width="40px"></span></p>-->
				</div>
			</div>
		</div>
		<div class="ColonneBox" id="pageMiseEnBoite" style="display:none;">
			<h3><?php echo __("Créer des boîtes et glisser-déposer les cartes à l'intérieur");?></h3>
			<div><div id="addBox" title="<?php echo __('Créer une nouvelle boîte');?>" onclick="newBox();"></div><div id="CheckAll" title="<?php echo __("Envoyer la correction aux élèves");?>" onclick="checkAllBox();"></div></div>
			<div id="rootBox"></div>
		</div>

	</div>
</div>

<script>

data=[];
etatEleve=[];
var players=[];
var nbrePlayer=0;
last_box_id=0;
boxsData=[];
justDraged=false;
$("#infoSurDeck").html("<div id='titreDeck'><span>"+deckInfo.deck_name+"</span></div>");
if(deckInfo.hasImage>0){$("#infoSurDeck").prepend("<img src='deck_img/deck_"+deckInfo.hasImage+".png'>");}
else{$("#infoSurDeck").prepend("<img src='img/default_deck.png'>");}

status="";
$(".code").html(game_id);
$(".minicode").html(game_id);
socket.on('reconnect', function() {
	$('.message').hide();
	socket.emit('game', game_id, function(result){
    if(result=="game_done"){
      socket.emit('whoIsThere', game_id);
    }
    else {
    alert("probleme avec la connection au groupe : "+result);
    }
});
});

socket.on('connected', function() {
	console.log("connected");
	$('.message').hide();
  $('.loading').hide();
	socket.emit('game', game_id, function(result){
    if(result=="game_done"){
      socket.emit('whoIsThere', game_id);
    }
    else {
    alert("probleme avec la connection au groupe : "+result);
    }
  });
});

socket.on('disconnect', function() {
  $(".message").html("Disconnected");
  $('.message').show();
});

data={boxs:[],items:itemsData};



socket.on('playerJoin', function(user_id) {
  console.log("playerJoin:",user_id);
  player_id=user_id;
	etatEleve[player_id]={player_id:player_id,nbreBadBox:"-",nbreGoodBox:"-",goodBoxs:[],badBoxs:[]};
  $.getJSON("ajax.php?action=getName&user_id="+user_id,function(data){

    pseudo=data.first_name+" "+data.last_name;
    pseudo=capitalizeFirstLetterEachWordSplitBySpace(pseudo);
    if($("#player_"+player_id).length==0)
    {$("#listPlayer").append("<div id='player_"+player_id+"' class='player_item online' title='<?php echo __("cliquez pour voir les cartes mal placé");?>' onmouseup='$(\"*\").removeClass(\"wrongPlaced\");' onmousedown='showEleve("+player_id+");'>"+pseudo+" <span class='goodBox'>-</span>/<span class='badBox'>-</span></div>");}
    else{$("#player_"+player_id).removeClass("offline").addClass("online");}

    players["player_"+player_id]={id:player_id,pseudo:pseudo,connecte:1,cartes:[]};
    nbrePlayer=len(players);
    console.log(nbrePlayer);
    $("#nbrePlayer").show();
    if(nbrePlayer<2){$("#nbrePlayer").html(nbrePlayer+' <?php echo __("participant");?>');}
    else{$("#nbrePlayer").html(nbrePlayer+' <?php echo __("participants");?>');}
  });
  $("#noStudentInfo").hide();
	sendData();
});

socket.on('correctionBack', function(data) {
		console.log('correctionBack',data);
		player_id=data.player_id;
		nbreBadBox=data.badBoxs.length;
		nbreGoodBox=data.goodBoxs.length;
		goodBoxs=data.goodBoxs;
		badBoxs=data.badBoxs;
		etatEleve[player_id]=data;
		$("#player_"+player_id+">.goodBox").html(nbreGoodBox);
		$("#player_"+player_id+">.badBox").html(nbreBadBox);
});

function showEleve(player_id){
	for(k in etatEleve[player_id].badBoxs)
	{$("#"+etatEleve[player_id].badBoxs[k]).addClass("wrongPlaced");}
}

function startBox()
{
	$('.colonneQuiz1').hide();
	$('#pageIntro').hide();
	$('#pageMiseEnBoite').show();
  //on ajoute les cartes qu'il n'a pas encore recu (cas du quiz ou les cartes arrivent les une apres les autres)
	for(k in itemsData)
  {
    item_id=itemsData[k].card_id;
		item_mot=itemsData[k].mot;
		item_hasImage=itemsData[k].hasImage;
		if($("#item_"+item_id).length==0)
    {
			$("#rootBox").append('<div class="item" id="item_'+item_id+'"><span class="mot">'+item_mot+'</span></div>');
			if(item_hasImage>0){$("#item_"+item_id).css("background-image","url(card_img/card_"+item_hasImage+".png)");}
			else{$("#item_"+item_id).css("background-image","url(img/default_card.png)");}
		}
  }
	initDrag();
	sendData();
}

function sendData(){
	socket.emit('startBox',{boxs:boxsData,items:itemsData});
}
function newBox(){
	last_box_id++;
	newBoxData={box_id:last_box_id,box_name:"<?php echo __("Attribut");?>",inside_ids:[]};
	boxsData.push(newBoxData);
	$("#rootBox").append('<div class="box" id="box_'+last_box_id+'"><input class="label" value="Attribut" onkeyup="updateBoxName('+last_box_id+')"><div class="delIcon" onclick="removeBox('+last_box_id+');"></div><div class="checkIcon" onclick="checkBox('+last_box_id+');"></div></div>');
	$("#box_"+last_box_id+" >.label ").focus();
	socket.emit('newBox',newBoxData);
	initDrag();
}
function updateBoxName(box_id){
newBoxName=$("#box_"+box_id+">.label").val();
for(k in boxsData){if(boxsData[k].box_id==box_id){boxsData[k].box_name=newBoxName;}}
socket.emit('updateBoxName', {box_id:box_id,box_name:newBoxName});
}
function removeBox(box_id){
	removedBoxData={box_id:box_id};
	//gestion de la inside_ids
	//trouver qui est la parentBox
	parentBox_id=$("#box_"+box_id).parent().attr("id");
	//mettre le inside_ids du supprimé dans parentbox
	k_parent="";
	for(k in boxsData){
		if("box_"+boxsData[k].box_id==parentBox_id){
		k_parent=k;
		}
	}
	for(k in boxsData){
		if(boxsData[k].box_id==box_id){
			if(k_parent!=""){
				for(i in boxsData[k].inside_ids)
				{boxsData[k_parent].inside_ids.push(boxsData[k].inside_ids[i]);}
			}
			boxsData.splice(k,1);
		}
	}
	$("#box_"+box_id+">.item").insertAfter("#box_"+box_id);
	$("#box_"+box_id+">.box").insertAfter("#box_"+box_id);
	$("#box_"+box_id).remove();
	socket.emit('removeBox',removedBoxData);

}
//function checkBox(box_id){

//	for(k in boxsData){
//		if(boxsData[k].box_id==box_id){
//			socket.emit('checkBox',boxsData[k]);
//			console.log("checkboxSent",boxsData[k]);
//		}
//	}
//}
function checkAllBox(){
	socket.emit('checkBox',boxsData);
}

function playAudio(filename)
{console.log("on demande l'écoute de "+filename);
$("."+filename)[0].play();
//if(!$("#audio_"+filename).length){
//	console.log("creation de l'élem Audio");
//  soundFile="../../audio/"+filename+".wav";
//	$("body").append('<audio id="audio_'+filename+'" src="'+soundFile+'">');
//	}
//$("#audio_"+filename).get(0).play();
}

function sendCode()
{
quiz_id=$('.repCodeEleve').val();
if(quiz_id!=-1){connectToQuiz(quiz_id);}
}

function connectToQuiz(quiz_id){
socket.emit('game', quiz_id ,function(result){if(result=="game_done"){
    socket.emit('playerJoin',{user_id:user_id, quiz_id:quiz_id});
    $('.codeAttente').html(quiz_id);
    getPage('attente');
    }
    else {
    alert("probleme avec la connection au groupe : "+result);
    }
  }
);
}
//}

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

function initDrag()
{
	$(".box,.item").draggable({
	  cursor: "move",
	  helper:"clone"
	  //cursorAt: { top: "50", left: "50" }//,
	  //start:function(event,ui){
	  //  ui.helper.css("zIndex",99);
	  //},
	  //stop:function(event,ui){
	  //  ui.helper.css("zIndex",1)
	  //}
	});
	$(".box,#rootBox").droppable({
	  classes: {
	        "ui-droppable-hover": "highLight"
	      },
	  greedy: true,
	  drop:function(event,ui){
			if(!justDraged)
			{
			justDraged=true;
			event.stopPropagation();
			//id du container:
			container_id=event.target.id;
			//id de l'element ajouté
			elem_id=ui.draggable.attr("id");
			console.log(elem_id,container_id,$(this).attr("id"));
			//remove elem de son precedent emplacement
				for(k in boxsData){
					if(boxsData[k].inside_ids.indexOf(elem_id)!=-1)
					{boxsData[k].inside_ids.splice(boxsData[k].inside_ids.indexOf(elem_id),1);
					 console.log(elem_id+" removed from inside_ids");
					}
				}
				for(k in boxsData){
					if("box_"+boxsData[k].box_id==container_id){
						boxsData[k].inside_ids.push(elem_id);
						console.log(elem_id+" added to" +container_id);
					}
				}
	  $(this).addClass("highLight");
	  setTimeout(function(){justDraged=false;$('.highLight').removeClass("highLight");},400);
	  target=$(this);
	  ui.draggable.detach().appendTo(target);
	  ui.draggable.css("left",0);
	  ui.draggable.css("top",0);
		}
	}});

}


function len(obj)
{
  socket.emit('nbrePlayer',game_id,function(result){console.log("nbre de joueur",result-1);});
  nbre=0;
  for(k in obj)
  { if(obj[k].connecte)
    {nbre++;}
  }
  return nbre;
}

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
