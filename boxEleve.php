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
	.box,.item{cursor: grab;position:relative;vertical-align:middle;text-align:center;z-index:1;display:inline-block;padding:10px; margin:10px;box-shadow:0 0 2px black;}
	.box:hover,.item:hover{top:3px;left:3px;box-shadow:0 0 5px black;}
	.item{display:inline-flex;min-width:180px;min-height:180px;background-size:cover;background-color:white;}
	.box{background-color:#362a12;min-width:400px;min-height:300px;padding-top:30px;
		border: 2px solid black;
		border-image:url(img/box3.png);
		border-image-slice: 12%;
		border-width:15px;

	}
	.highLight{background-color:yellow;}
	.label{position:absolute;top:-20px;left:-20px;padding:10px;font-size:1.3em;background-color:#ffe090;transform:rotate(-10deg);}
	.delIcon{position:absolute;bottom:10px;right:10px;padding:20px;background-image:url(img/del.png);background-size:cover;}
	.delIcon:hover{background-color:red;}
	.mot{margin:auto;background-color:#ffffff80;width:100%;padding:10px;}
	.ColonneBox{
    vertical-align: middle;}
	.badPlaced{box-shadow:0 0 0 8px red;}
	.goodPlaced{box-shadow:0 0 0 8px lime;}
	</style>
  <nav id="navbar">
        <div class="menu">
          <ul class="desktop" onclick="$('.mobile').removeClass('open');">
            <li style="float:left;"><a href="decks.php"><?php echo __("retour aux listes");?></a></li>
            <li><a href="#">Box Them All</a></li>
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
#navbar{border-bottom:gold;}

</style>


<div class="center" style="margin-top:100px;">
  <div id="formCode" class="page">
    <div class="consigneCarte"><?php echo __("Entrer le code de la session");?></div>
    <img src="img/box.png" class="imgfusee" style="margin-top:30px;width:50%; max-width:200px;">
    <div id="inputCodeEleve"><input type="number" class="repCodeEleve" placeholder="Code"><br>
      <div class="envoyerCode" onclick='sendCode();'><?php echo __("Valider");?></div><br>


    </div>
  </div>

  <div id="attente" class="page">
    <h2 style="margin:10vh;" id="messageAttente"><?php echo __("En attente ...");?></h2>
    <img src="img/box.png" class="imgfusee" style="width:50%; max-width:400px;">
    <br>
    <!--<div class="codeAttente"></div>-->
  </div>

  <div id="boxPage" class="page">
    <div>
      <div class="consigneCarte" style="margin:10px 0;"><?php echo __("Mettre les objets dans les bonnes boîtes");?></div>
			<div id="rootBox"></div>
		</div>
  </div>

  </div>

<script>
data=[];
correctionData=[];
status="";
justDraged=false;
$(".repCodeEleve").on("keypress", function(e){if(e.which==13){sendCode();}})


window.addEventListener("blur",function(){socket.emit('playerAway',{user_id:user_id});});
window.addEventListener("focus",function(){socket.emit('playerInAgain',{user_id:user_id});});
socket.on('reconnect', function() {
	$('.message').hide();
	socket.emit('game', myQuizId ,function(result){if(result=="game_done"){
	    socket.emit('playerJoin',{user_id:user_id, quiz_id:myQuizId});

	    }
	    else {
	    alert("probleme avec la connection au groupe : "+result);
	    }
	  }
	);
})
socket.on('connected', function() {
  $(".message").html("Connected");
  $('.message').hide();
});
socket.on('disconnect', function() {
  $(".message").html("Disconnected");
  $('.message').show();
});

socket.on('whoIsThere', function(){
  socket.emit('playerJoin', {user_id:user_id,quiz_id:quiz_id});
});


socket.on('startBox', function(data) {
    if(status=="")
		{
		status="start";
    startBox(data);
		}
});

function startBox(data)
{
  console.log("startQuestionData",data);
	cards=[];
  //on ajoute les cartes qu'il n'a pas encore recu (cas du quiz ou les cartes arrivent les une apres les autres)
  for(k in data.boxs)
  {
    box_id=data.boxs[k].box_id;
		box_name=data.boxs[k].box_name;
		if($("#box_"+box_id).length==0)
    {
			$("#rootBox").append('<div class="box" id="box_'+box_id+'"><span class="label">'+box_name+'</span></div>');
		}
  }
	for(k in data.items)
  {
    item_id=data.items[k].card_id;
		item_mot=data.items[k].mot;
		item_hasImage=data.items[k].hasImage;
		if($("#item_"+item_id).length==0)
    {
			$("#rootBox").append('<div class="item" id="item_'+item_id+'"><span class="mot">'+item_mot+'</span></div>');
			if(item_hasImage>0){$("#item_"+item_id).css("background-image","url(card_img/card_"+item_hasImage+".png)");}
			else{$("#item_"+item_id).css("background-image","url(img/default_card.png)");}
		}
  }
  getPage("boxPage");
	initDrag();
}

socket.on('newBox', function(data){
  console.log("newBox",data);
	box_id=data.box_id;
	box_name=data.box_name;
	$("#rootBox").append('<div class="box" id="box_'+box_id+'"><span class="label">'+box_name+'</span></div>');
	initDrag();
})
socket.on('updateBoxName', function(data){
  console.log("updateBox",data);
	box_id=data.box_id;
	box_name=data.box_name;
	$('#box_'+box_id+'>.label').html(box_name);
})
socket.on('removeBox', function(data){
  console.log("removeBox",data);
	box_id=data.box_id;
	$("#box_"+box_id+">.item").insertAfter("#box_"+box_id);
	$("#box_"+box_id+">.box").insertAfter("#box_"+box_id);
	$("#box_"+box_id).remove();
})

socket.on('checkBox', function(data){
	correctionData=data;
  console.log("checkBox",data);
	correction();
})

function correction()
{
	for(i in correctionData){
		box_id=correctionData[i].box_id;
		$("#box_"+box_id+">.item").addClass("badPlaced");
		$("#box_"+box_id+">.box").addClass("badPlaced");
		for(k in correctionData[i].inside_ids){
			$("#"+correctionData[i].inside_ids[k]).addClass("badPlaced");
			if($("#box_"+box_id+" > #"+correctionData[i].inside_ids[k]).length!=0){$("#"+correctionData[i].inside_ids[k]).addClass("goodPlaced").removeClass("badPlaced");}
		}
	}
	goodBoxs=[];
	badBoxs=[];
	$(".badPlaced").each(function(){ if(this.id!=""){badBoxs.push(this.id);} });
	$(".goodPlaced").each(function(){ if(this.id!=""){goodBoxs.push(this.id);	} });
	console.log("correctionBack",goodBoxs,badBoxs);
	socket.emit('correctionBack',{player_id:user_id,goodBoxs:goodBoxs,badBoxs:badBoxs});
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


function getPage(pageName)
{
console.log('formCode');
$('.page').hide();
$('#'+pageName).show();
if(pageName=="form"){$(".repEleve").focus();}
}

function sendCode()
{
quiz_id=$('.repCodeEleve').val();
if(quiz_id!=-1){connectToQuiz(quiz_id);}
}
myQuizId=0;
function connectToQuiz(quiz_id){
myQuizId=quiz_id;
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
			if(!justDraged){
			justDraged=true;
			event.stopPropagation();
	 	//controle de tous les ids
		container_id=event.target.id;
		elem_id=ui.draggable.attr("id");
		console.log(elem_id,container_id,$(this).attr("id"));

	  $(this).addClass("highLight");
	  setTimeout(function(){justDraged=false;$('.highLight').removeClass("highLight");},400);
	  target=$(this);
	  ui.draggable.detach().appendTo(target);
	  ui.draggable.css("left",0);
	  ui.draggable.css("top",0);
		$("*").removeClass("badPlaced");
		$("*").removeClass("goodPlaced");
		correction();
		}
	}});
}

getPage('formCode');


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
