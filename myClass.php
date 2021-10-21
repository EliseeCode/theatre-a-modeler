<?php
include_once ("db.php");
session_start();

$explodeURI=explode('/',$_SERVER['REQUEST_URI']);
$_SESSION['url']=end($explodeURI);
if(!isset($_SESSION['user_id'])){header("location:checkLoginCookie.php");exit();}
$user_id = $_SESSION['user_id'];
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$email = $_SESSION['email'];
$active = $_SESSION['active'];
$type = $_SESSION['type'];
$classe = $_SESSION['classe'];
echo "<script>fullUserName='".$first_name." ".$last_name."';</script>";
echo "<script>type='".$type."';</script>";
echo "<script>user_id=".$user_id.";</script>";
$_SESSION['url']="";
//get other classes infos
$sql="SELECT classes.class_id,classes.class_name,classes.promo FROM classes JOIN user_class ON user_class.class_id=classes.class_id WHERE classes.status='ok' AND user_class.user_id=".$user_id;
$myClasses=array();
$result = $mysqli->query($sql);
while ($row = $result->fetch_assoc()) {
$myClasses[$row['class_id']]=$row;}
  echo "<script>myClasses=".json_encode($myClasses).";</script>";
$result->free();

//get DeckNumber For studentDeck and getClassDecks
$nbreDeckInClass=0;
$nbreDeckToValidate=0;

  echo "<script>nbreDeckInClass=".json_encode($nbreDeckInClass).";</script>";
  echo "<script>nbreDeckToValidate=".json_encode($nbreDeckToValidate).";</script>";
$result->free();

?>

<!DOCTYPE html>
<html >
 <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ma classe</title>
    <link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16" />

    <!-- Bootstrap -->
	  <link href="css/main.css?ver=<?php echo filemtime('css/main.css');?>" rel="stylesheet">
		<link href="css/styleEntete.css?ver=<?php echo filemtime('css/styleEntete.css');?>" rel="stylesheet">
	  <link href="css/deck.css?ver=<?php echo filemtime('css/deck.css');?>" rel="stylesheet">
	  <link href="css/card.css?ver=<?php echo filemtime('css/card.css');?>" rel="stylesheet">
    <link href="css/printBadges.css?ver=<?php echo filemtime('css/printBadges.css');?>" rel="stylesheet">
		<link href="css/myStyle.css?ver=<?php echo filemtime('css/myStyle.css');?>" rel="stylesheet">
    <link href="css/navStyle.css?ver=<?php echo filemtime('css/navStyle.css');?>" rel="stylesheet">
    <script src="js/jquery-3.3.1.min.js"></script>
		<script src="js/jquery-ui.js"></script>

		<style>
      .navbar{
        margin-bottom:0;
        border-radius:0;
      }
    </style>
</head>

<body class="fond">

  <?php include "entete.php";?>
  <script>
	$(".buttonRetourList").hide();
	$(".buttonMyClass").hide();
  $(".buttonMyDecks").hide();
  $(".buttonBadgeStat").show();
  $(".buttonBadgeStat").addClass("active");
  $(".buttonListes").show();
  $(".subButtonMyClass > a").append(" ("+nbreDeckInClass+")");
  $(".subButtonStudentDecks > a").append(" ("+nbreDeckToValidate+")");

  $(".buttonInvitation").show();
  classesUpdateButton("myClass.php");
	$('.desktop').menuBreaker();
	</script>

	<div style="text-align:center;" class="bodyContent">




<br>
<style>
thead{background-color:#F8F8F8;}
#statTable{box-shadow: 0 0 5px grey;margin:10px;max-width:1px;}

.unactivBtnImport{color:grey;}
.activBtnImport{background-color:gold;color:black;}
.BadgesCell .DecksCell{display:none; max-width:100px;}
.note_input{text-align:center;width:100%;}
.iconDeckImg{width:55px;}
.tab{width:115px;padding:10px;display:inline-block;background-color:#f0f0f0;margin:5px -10px -10px -10px;border-radius:10px 10px 0 0;border:1px solid lightgrey;}
#statTable tr td{padding:2px 5px;}
.MarksCell{position:relative;}
.MarksCell input{border:none;padding: 6px 0;}
.MarksCell:hover input{background-color:#f0f0f0;}
.delQuizIcon{position:absolute;top:3px;right:3px;}
.delQuizIcon:hover{background-color:red;}
.showStudentData{position:absolute;top:7px;right:0;display:none;width:20px;height:20px;background-image:url(img/tripoint.png);background-size:cover;}
.user_name:hover .showStudentData{display:inline-block;}
.user_name{position:relative;padding-right:20px;}
.activeT{background-color:white;z-index:10;}
</style>



<!--=============Page Stats et Badges================-->
<div id="StatsAndBadges" class="pageClass">
  <h1 class="className"></h1>

  <!--<div class="stat_container" style="display:none;height:26vw; text-align:center;margin:20px;overflow:auto;">
    <div class="user_name_title"></div>
    <div><input class="rangeDays" type="range" name="points" min="30" max="365" value="60" onchange="range=$('.rangeDays').val();resizeGraph(range);"></div>
    <div class="chart-container" style="display:inline-block; position: relative; height:25vh; width:60vw;">
      <canvas id="myChart" width="500" height="200" style="display:inline-block;"></canvas>
    </div>
  </div>-->
  <br>


    <div>
      <div id="tab_container">
        <div class="tab" id="tabBadges" onclick="showBadges();" >Badges</div>
        <div class="tab activeT" id="tabMarks" onclick="showMarks();">Notes</div>
        <div class="tab" id="tabDecks" onclick="showDecks();">Listes</div>
      </div>
      <div style="display:inline-block;max-width: 100vw;overflow: auto;">
        <table id="statTable" class='sortable'>
          <thead>
          </thead>
          <tbody>
          </tbody>
          <tfoot>
          </tfoot>
				</table>
      </div>
      <!--<a href="#" id="download_results" class="">Télécharger les notes de perf</a>
      <a href="#" id="download_badges" class="" onClick="printBadges();">Télécharger les badges</a>-->
    </div>

</div>

<style>
.badge_crea,.badge_quiz,.badge_nbremots,.badge_jours,.badge_icon{text-align:left;}
</style>
<script>
var usersData;
var quizsData;
if(nbreDeckToValidate!=0){
  $(".subButtonMyClass > a").addClass("BlinkButton");
  //$(".buttonListes").addClass("BlinkButton");
}else {$('#student_listBtn').hide();}

function showBadges()
{
  $(".tab").removeClass("activeT");
  $("#tabBadges").addClass("activeT");
  $(".BadgesCell").show();
  $(".MarksCell").hide();
  $(".DecksCell").hide();

}
function showMarks()
{
  $(".tab").removeClass("activeT");
  $("#tabMarks").addClass("activeT");
  $(".BadgesCell").hide();
  $(".MarksCell").show();
  $(".DecksCell").hide();
}
function showDecks()
{
  $(".tab").removeClass("activeT");
  $("#tabDecks").addClass("activeT");
  $(".BadgesCell").hide();
  $(".MarksCell").hide();
  $(".DecksCell").show();
}
function changeMarks(user_id,quiz_id)
{
  note=$("#user_"+user_id+"_quiz_"+quiz_id).find(".note_input").val();
  $.getJSON("ajax.php?action=changeMark&user_id="+user_id+"&quiz_id="+quiz_id+"&note="+note, function(result){
  calculMoyenneQuiz();});
}
function getStat()
{
  $('.className').html(class_info.class_name);
  $('#statTable > thead').html("");
  $('#statTable > tbody').html("");
  $('#statTable > tfoot').html("");
  //$('#BadgeTable').html("");
  class_id=class_info.class_id;
  $.getJSON("ajax.php?action=getClassStatToday&class_id="+class_id, function(result){
    usersData=result.users;
    quizsData=result.quizs;
    console.log(result);
    nbreDecks=result.decks.length;
    htmlHeader='<tr class="enteteTable"><th class="user_name">Prénom Nom</th><th class="BadgesCell">Nbre de mot en mémoire</th><th class="BadgesCell"><span class="nom_badge">Créateur</span></th><th  class="BadgesCell"><span class="nom_badge">Nombres de mots</span></th><th  class="BadgesCell"><span class="nom_badge">Champion en quiz</span></th><th  class="BadgesCell"><span class="nom_perf">Note de Perf</span></th></tr>';
    $('#statTable > thead').append(htmlHeader);
    //$('#statTable > tfoot').append(htmlHeader);

    //$('#BadgeTable').append('<tr id="liste_badge"><td>Prénom Nom</td><td><span class="nom_deck">Créateur</span></td><td><span class="nom_deck">Nombres de mots</span></td><td><span class="nom_deck">Persévérance</span></td><td><span class="nom_deck">Champion en quiz</span></td><td><span class="nom_perf">Note de Perf</span></td></tr>')
    for(i in result.decks)
      {var deck=result.decks[i];
        if(deck.status=="studentDeck"){classColor="purple";}else{classColor="black";}
        $(".enteteTable").append("<th class='DecksCell'><a href='cards.php?deck_id="+deck.deck_id+"' target='_blank' class='nom_deck' style='color:"+classColor+";' >"+deck.deck_name+"</a></th>");
      }
      $(".enteteTable").append("<th class='MarksCell'>Moyenne sur 100</th>");
    for(i in result.quizs)
      { quiz_id=result.quizs[i].quiz_id;
        expire=result.quizs[i].expire;
        day = moment.unix(expire).format("D/MM");
        noteMax=result.quizs[i].noteMax;
        deck_id=result.quizs[i].deck_id;
        hasImage=result.quizs[i].hasImage;
        if(hasImage>0){url_img_deck='deck_img/deck_'+hasImage+'.png';}else{url_img_deck='img/default_deck.png';}
        deck_name=result.quizs[i].deck_name;
        $(".enteteTable").append("<th class='MarksCell quiz_"+quiz_id+"' id='entete_quiz_"+quiz_id+"' style='position:relative;'><img class='iconDeckImg' src='"+url_img_deck+"'><br>"+deck_name+"<br>fait le "+day+"<br>sur "+noteMax+"<br><input title='selectionner ce quiz pour le calcul de la moyenne' type='checkbox' checked onclick='calculMoyenneQuiz();' id='selectedQuiz_"+quiz_id+"'><img src='img/del.png' title='supprimer ce quiz definitivement' class='delQuizIcon' onclick='delQuiz("+quiz_id+");' width='20px'></th>");
      }

      NbreEleve=0;
    for(k in result.users)
      {var user=result.users[k];
        if(user.type=="eleve")
        { NbreEleve++;
          motsEnMemoir=result.mots[user.user_id];
        $('#statTable > tbody').append("<tr id='user_"+user.user_id+"'><td class='user_name' onclick='showStudentData("+user.user_id+");getPersonalStat("+user.user_id+","+motsEnMemoir+",\""+user.first_name+" "+user.last_name+"\");'><span class='autocorrection' title='autocorrection="+user.nbreCorrection+"'></span>"+user.first_name+" "+user.last_name+"</td><td class='nbreMotsMemo BadgesCell'></td><td class='badge_crea BadgesCell'></td><td class='badge_nbreMots BadgesCell'></td><td class='badge_quiz BadgesCell'></td><td class='badge_perf BadgesCell'></td></tr>");
        //$('#BadgeTable').append("<tr id='user_"+user.user_id+"'><th class='user_name' onclick='getPersonalStat("+user.user_id+","+motsEnMemoir+",\""+user.first_name+" "+user.last_name+"\");'>"+user.first_name+" "+user.last_name+"</th><th class='badge_crea'></th><th class='badge_nbreMots'></th><th class='badge_jours'></th><th class='badge_quiz'></th><th class='badge_perf'></th></tr>");
        for(i in result.decks)
          {deck=result.decks[i];
          $('#user_'+user.user_id).append("<td class='DecksCell' id=user_"+user.user_id+"_deck_"+deck.deck_id+" style='background-color:white;'>0%</td>");
          }
          $('#user_'+user.user_id).append("<td class='MarksCell' id='moyenne_"+user.user_id+"'></td>");
        for(i in result.quizs)
          {quiz_id=result.quizs[i].quiz_id;
          noteMax=result.quizs[i].noteMax;
          $('#user_'+user.user_id).append("<td class='MarksCell quiz_"+quiz_id+"' id='user_"+user.user_id+"_quiz_"+quiz_id+"' style='background-color:white;'><input type='number' min='0' max='"+noteMax+"' placeholder='Note' class='note_input' onchange='changeMarks("+user.user_id+","+quiz_id+");'></td>");
          }

        }
      }
      imgBadge={"crea1":"<img class='badge_icon badgecrea1'  title='Création d'une liste validée par le prof' src='img/badge/badgeCrea1.png'>",
                "crea2":"<img class='badge_icon badgecrea2'  title='Création de 3 listes validées par le prof' src='img/badge/badgeCrea2.png'>",
                "crea3":"<img class='badge_icon badgecrea3'  title='Création de 10 listes validées par le prof' src='img/badge/badgeCrea3.png'>",
                "quiz1":"<img class='badge_icon badgequiz1'  title='1 victoire en quiz de classe' src='img/badge/badgeQuiz1.png'>",
                "quiz2":"<img class='badge_icon badgequiz2'  title='3 victoires en quiz de classe' src='img/badge/badgeQuiz2.png'>",
                "quiz3":"<img class='badge_icon badgequiz3'  title='10 victoires en quiz de classe' src='img/badge/badgeQuiz3.png'>",
                "mot1":"<img class='badge_icon badgemot1'  title='100 mots en mémoire' src='img/badge/badgeMots1.png'>",
                "mot2":"<img class='badge_icon badgemot2'  title='250 mots en mémoire' src='img/badge/badgeMots2.png'>",
                "mot3":"<img class='badge_icon badgemot3'  title='500 mots en mémoire' src='img/badge/badgeMots3.png'>",
                "mot4":"<img class='badge_icon badgemot4'  title='1000 mots en mémoire' src='img/badge/badgeMots4.png'>",
                "mot5":"<img class='badge_icon badgemot5'  title='1500 mots en mémoire' src='img/badge/badgeMots5.png'>"
                }
      for(i in result.trophy)
        {
          var perf=$('#user_'+i+' > .badge_perf').html();
          if(perf==""){perf=0;}
          perf=parseInt(perf);
          trophyUser=result.trophy[i];
          for(t in trophyUser)
            {
          nbre=trophyUser[t];
          type=t;
          switch (type)
          {
          case "crea":
          if(nbre >=1 && $("#user_"+i).find(".badgecrea1").length==0){perf+=1;$('#user_'+i+' > .badge_crea').html(imgBadge['crea1']);}
          if(nbre >=3 && $("#user_"+i).find(".badgecrea2").length==0){perf+=2;$('#user_'+i+' > .badge_crea').html(imgBadge['crea1']+imgBadge['crea2']);}
          if(nbre >=10 && $("#user_"+i).find(".badgecrea3").length==0){perf+=3;$('#user_'+i+' > .badge_crea').html(imgBadge['crea1']+imgBadge['crea2']+imgBadge['crea3']);}
          break;
          case "quiz":
          if(nbre >=1 && $("#user_"+i).find(".badgequiz1").length==0){perf+=1;$('#user_'+i+' > .badge_quiz').html(imgBadge['quiz1']);}
          if(nbre >=3 && $("#user_"+i).find(".badgequiz2").length==0){perf+=2;$('#user_'+i+' > .badge_quiz').html(imgBadge['quiz1']+imgBadge['quiz2']);}
          if(nbre >=10 && $("#user_"+i).find(".badgequiz3").length==0){perf+=3;$('#user_'+i+' > .badge_quiz').html(imgBadge['quiz1']+imgBadge['quiz2']+imgBadge['quiz3']);}
          break;
          /*case "jours":
          if(nbre >=1){perf+=1;$('#user_'+i+' > .badge_jours').append("<img class='badge_icon' title='Objectif quotidien atteint 1 fois' src='img/badge/badgeTime1.png'>");}
          if(nbre >=3){perf+=2;$('#user_'+i+' > .badge_jours').append("<img class='badge_icon' title='Objectif quotidien atteint 3 fois' src='img/badge/badgeTime2.png'>");}
          if(nbre >=7){perf+=3;$('#user_'+i+' > .badge_jours').append("<img class='badge_icon' title='Objectif quotidien atteint 7 fois' src='img/badge/badgeTime3.png'>");}
          if(nbre >=20){perf+=4;$('#user_'+i+' > .badge_jours').append("<img class='badge_icon' title='Objectif quotidien atteint 20 fois' src='img/badge/badgeTime4.png'>");}
          if(nbre >=30){perf+=5;$('#user_'+i+' > .badge_jours').append("<img class='badge_icon' title='Objectif quotidien atteint 30 fois' src='img/badge/badgeTime5.png'>");}
          if(nbre >=100){perf+=6;$('#user_'+i+' > .badge_jours').append("<img class='badge_icon' title='Objectif quotidien atteint 100 fois' src='img/badge/badgeTime6.png'>");}
          break;*/
          case "100mots":
          if($("#user_"+i).find(".badgemot1").length==0)
          {perf+=1;$('#user_'+i+' > .badge_nbreMots').html(imgBadge['mot1']);}
          break;
          case "250mots":
          if($("#user_"+i).find(".badgemot2").length==0)
          {perf+=2;$('#user_'+i+' > .badge_nbreMots').html(imgBadge['mot1']+imgBadge['mot2']);}
          break;
          case "500mots":
          if($("#user_"+i).find(".badgemot3").length==0)
          {perf+=3;$('#user_'+i+' > .badge_nbreMots').html(imgBadge['mot1']+imgBadge['mot2']+imgBadge['mot3']);}
          break;
          case "1000mots":
          if($("#user_"+i).find(".badgemot4").length==0)
          {perf+=4;$('#user_'+i+' > .badge_nbreMots').html(imgBadge['mot1']+imgBadge['mot2']+imgBadge['mot3']+imgBadge['mot4']);}
          break;
          case "1500mots":
          if($("#user_"+i).find(".badgemot5").length==0)
          {perf+=5;$('#user_'+i+' > .badge_nbreMots').html(imgBadge['mot1']+imgBadge['mot2']+imgBadge['mot3']+imgBadge['mot4']+imgBadge['mot5']);}
          break;
          }
          if(perf>100){perf=100;}
          $('#user_'+i+' > .badge_perf').html(perf);
        }
        }

    for(n in result.stats)
      {stat=result.stats[n];
        if(stat.nbreMots=="0")
        {pourcent=0;}
        else {pourcent=Math.round(stat.nbreMotsEnMemoire*100/stat.nbreMots);}
        if(pourcent==0){couleur="white";}
        else if(pourcent>90){couleur="#ffcf16";}
        else if(pourcent>25){couleur="lightgreen";}
        else{couleur="white";}
        $("#user_"+stat.user_id+"_deck_"+stat.deck_id).html(pourcent+"%");
        $("#user_"+stat.user_id+"_deck_"+stat.deck_id).css("background-color",couleur);
      }
      //On regarde l'ire sous la courbe et on ajoute à perf le resultat.
    for(id in result.jour)
      {
        date_ini = new Date(result.jour[id]["jour_ini"]);
        date_fin = new Date();
        deltaJour = Math.floor((date_fin - date_ini)/(1000*60*60*24))
        objectif=10+deltaJour*10;
        NbreMotsPerf=result.jour[id]["nbreMots"];

        var perf=$('#user_'+id+' > .badge_perf').html();
        if(perf==""){perf=0;}
        perf=parseInt(perf);
        perf+=100*NbreMotsPerf/(objectif*deltaJour/2);
        //console.log(NbreMotsPerf+"/"+objectif*deltaJour/2);
        perf=Math.round(perf);
        if(perf>100){perf=100;}
        $('#user_'+id+' > .badge_perf').html(perf);

        motsEnMemoir=result.mots[id];
        //if(result.mots[id]!=undefined)


        $("#user_"+id+" .nbreMotsMemo").html(motsEnMemoir);
        $("#user_"+id+" .user_name").attr("title",motsEnMemoir+"/"+objectif);
        //if(motsEnMemoir<objectif){$("#user_"+id+" .user_name").css("background-color","red");}
        //else{$("#user_"+id+" .user_name").css("background-color","lime");}
      }
      for(i in result.marks){
        note=result.marks[i].note;
        user_id=result.marks[i].user_id;
        quiz_id=result.marks[i].quiz_id;
        $("#user_"+user_id+"_quiz_"+quiz_id).find(".note_input").val(note);
      }
      calculMoyenneQuiz();
      if(NbreEleve==0){$('#StatsAndBadges').html("Aucun élève inscrit");}
      $(".sortable").sortable({
        items: "tr:not(:first)",
        axis:'y',
        update: function (event, ui) {
        var data = $(this).sortable('serialize');
        console.log(data);
        $.ajax({
              data: data,
              type: 'POST',
              url: 'ajax.php?action=setOrderStat'
          });
          }
      });
      $('#statTable').DataTable({"paging":   false,searching:false});
      showMarks();
    });

    /*$(window).scroll(function(){
      console.log("scroll");
      $('.user_nameClone').css({
      'left': $(this).scrollLeft()
      });
    });*/
}
getStat();
function showStudentData(user_id)
{$('.fenetreSombre').remove();
$('body').append("<div class='fenetreSombre' onclick='$(this).remove();'><div style='text-align:center;' class='fenetreClaire' onclick='event.stopPropagation();'>"
+"<h1 style='text-align:center;margin:0 0 50px 0;'><div class='user_name_title'></div></h1>"
+'<div class="stat_container" style="display:none;height:23vw; text-align:center;margin:20px;overflow:auto;">'
+'  <div><input class="rangeDays" type="range" name="points" min="30" max="365" value="60" onchange="range=$(\'.rangeDays\').val();resizeGraph(range);"></div>'
+'  <div class="chart-container" style="display:inline-block; position: relative; height:25vh; width:50vw;">'
+'    <canvas id="myChart" width="500" height="200" style="display:inline-block;"></canvas>'
+"  </div>"
+"</div>"
+"<div class='btn' onclick='upgradeProf("+user_id+");' style='border-bottom:6px solid #00000080;width:30%;display:inline-block;margin:0 10px;'>Donner à cet élève le rôle de prof</div>"
+"<div class='btn' style='background-color:red;border-bottom:6px solid #00000080;width:30%;display:inline-block;margin:0 10px;' onclick='kickout("+user_id+");'>Sortir cette élève de la classe</div>"
+"</div></div>");

}
function printBadges(){
  clone=$("#statTable").clone()
  clone.prop('id', 'section-to-print' );
  clone.find(".nom_deck").parent().remove();
  clone.find("td").remove();
  clone.find(".badge_icon").css("width","50px");
  clone.find(".badge_icon").css("height","50px");
  clone.find("th").css("border","1px grey solid");
  $("td").css("border","none");
  $("#statTable").before(clone);
  window.print();
  $("td").css("border","1px grey solid");
  clone.remove();
}

function calculMoyenneQuiz(){
  for(i in usersData){
    user_id=usersData[i].user_id
    totalNote=0;
    totalNoteMax=0;
    for(q in quizsData){
      quiz_id=quizsData[q].quiz_id;
      noteMax=quizsData[q].noteMax;
      if($("#selectedQuiz_"+quiz_id).is(':checked'))
      { note=$("#user_"+user_id+"_quiz_"+quiz_id).find(".note_input").val();
        if(note!=""){
        totalNoteMax+=parseInt(noteMax);
        totalNote+=parseInt(note);
        }
      }
    }
    moy='';
    if(totalNoteMax!=0){moy=Math.round(totalNote*100/totalNoteMax);}
    $('#moyenne_'+user_id).html(moy);
  }
}
function delQuiz(quiz_id)
{
  if(confirm("Voulez-vous supprimer ce quiz définitivement de la classe ?"))
  {
  $(".quiz_"+quiz_id).remove();
  $.getJSON("ajax.php?action=delQuiz&quiz_id="+quiz_id, function(result){});
  }
}

function kickout(user_id){
class_id=class_info.class_id;
if(confirm("Voulez-vous sortir cet élève de la classe ?"))
{
$("#user_"+user_id).remove();
$.getJSON("ajax.php?action=kickOutUser&class_id="+class_id+"&user_id="+user_id, function(result){});
}
}
function upgradeProf(user_id){
class_id=class_info.class_id;
if(confirm("Voulez-vous donner à cet utilisateur le rôle de prof ?"))
{
$("#user_"+user_id).remove();
$.getJSON("ajax.php?action=upgradeProf&class_id="+class_id+"&user_id="+user_id, function(result){});
}
}
function getParticipant()
{$('#participantContainer').html("");
class_id=class_info.class_id;
$.getJSON("ajax.php?action=getParticipant&class_id="+class_id, function(result){
users=result;
console.log(result);

$('#participantContainer').append('<h3>Professeurs</h3><table id="tableProf"><tr id="first_line"><th><input id="checkAllProf" type="checkbox" title="Selectionner tous les professeurs"></th><th>Prénom Nom</th><th>Action</th></tr></table>');
$('#participantContainer').append('<h3>Elèves</h3><table id="tableEleve"><tr id="first_line"><th><input id="checkAllEleve" type="checkbox" title="Selectionner tous les élèves"></th><th>Prénom Nom</th><th>Action</th></tr></table>');
//$('#BadgeTable').append('<tr id="liste_badge"><td>Prénom Nom</td><td><span class="nom_deck">Créateur</span></td><td><span class="nom_deck">Nombres de mots</span></td><td><span class="nom_deck">Persévérance</span></td><td><span class="nom_deck">Champion en quiz</span></td><td><span class="nom_perf">Note de Perf</span></td></tr>')
for(i in users)
  {
   type=users[i].type;
   user_id=users[i].user_id;
   nom=  users[i].first_name+" "+users[i].last_name;
   if(type=="eleve"){$('#tableEleve').append("<tr id='user_"+user_id+"'><td><input class='checkEleve' type='checkbox'></td><td width='300px'>"+nom+"</td><td></td></tr>");}
   else{$('#tableProf').append("<tr id='user_"+user_id+"'><td><input class='checkProf' type='checkbox'></td><td width='300px'>"+nom+"</td><td></td></tr>");}
  }
  $("#checkAllEleve").on("click",function(){$(".checkEleve").prop("checked",$("#checkAllEleve").prop("checked"));});
  $("#checkAllProf").on("click",function(){$(".checkProf").prop("checked",$("#checkAllProf").prop("checked"));});
  $(".checkProf").on("click",function(){if(!$(this).prop("checked")){$("#checkAllProf").prop("checked",false);}});
  $(".checkEleve").on("click",function(){if(!$(this).prop("checked")){$("#checkAllEleve").prop("checked",false);}});
  $('#tableProf').append("<tr><td colspan='3' class='invitationBtn'>Inviter un professeur</td></tr>");
  $('#tableEleve').append("<tr><td colspan='3' class='invitationBtn'>Inviter un élève</td></tr>");
});
}

var datapointsMotsMemo = [];
var datapointsMotsVus = [];
var datapointsMotsMemoPred = [];
var datapointsMotsVusPred = [];
var datapointsObjectif = [];
var datapointsToday = [];

var config = {
  type: 'line',
  data: {
    labels: [],//'0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
    datasets: [{
      label: 'Nbre de mots en mémoire',
      data: datapointsMotsMemo,
      borderColor: "#3399ff",
      backgroundColor: 'rgba(0, 255, 0, 0.2)',
      fill: false,
      //cubicInterpolationMode: 'monotone'
      lineTension: 0
    },{
      label: 'Nbre de mots vue',
      data: datapointsMotsVus,
      borderColor: "transparent",
      backgroundColor: 'rgba(255, 0, 0, 0.2)',
      fill: '-1',
      //cubicInterpolationMode: 'monotone'
      lineTension: 0
    },{
      label: "Aujourd'hui",
      data: datapointsToday,
      borderColor: "#red",
      backgroundColor: 'rgba(255, 0, 0, 1)',
      fill: false,
      //cubicInterpolationMode: 'monotone'
      lineTension: 0
    },{
      label: 'Prédiction mots en mémoire sans travailler',
      data: datapointsMotsMemoPred,
      borderColor: "#63B9ff",
      backgroundColor: 'rgba(0, 255, 0, 0)',
      borderDash:[3, 3],
      fill: true,
      //cubicInterpolationMode: 'monotone'
      lineTension: 0
    },{
      label: 'Prédiction mots vus sans travailler',
      data: datapointsMotsVusPred,
      borderColor: "transparent",
      backgroundColor: 'rgba(0,0, 255, 0.1)',
      fill: true,
      //cubicInterpolationMode: 'monotone'
      lineTension: 0
    }, {
      label: 'Objectif à 10 nouveau mots/jours',
      data: datapointsObjectif,
      borderColor: "red",
      borderDash:[3, 3],
      backgroundColor: 'rgba(0, 0, 0, 0)',
      fill: false,
      lineTension: 0
    }]
  },
  options: {
    legend:{display:false},
    layout: {
           padding: {
               left: 0,
               right: 0,
               top: 0,
               bottom: 0
           }
       },
    responsive: true,
    title: {
      //display: true,
      //text: 'Chart.js Line Chart - Cubic interpolation mode'
    },
    tooltips: {
      mode: 'index'
    },
    scales: {
      xAxes: [{
        type:"time",
        distribution: 'linear',
        time: {
                displayFormats: {
                    quarter: 'MMM D'
                },
                //min:"2018-06-05",
                unit:"day"
            },
        display: true,
        scaleLabel: {
          display: true
        }
      }],
      yAxes: [{
        min:0,
        maxTicksLimit:4,
        display: true,
        scaleLabel: {
          display: false,
          labelString: 'Nombre de mots'
        },
        ticks: {
          source:'data',
          min:0,
          maxTicksLimit:4
          //suggestedMin: -10,
          //suggestedMax: 200,
        }
      }]
    }
  }
};

function resizeGraph(nbre_jour)
{
	startingDate=moment().subtract(nbre_jour,"days").format("YYYY-MM-DD");
	myLine.options.scales.xAxes[0].time.min=startingDate;
	myLine.update();
}

function getPersonalStat(user_id,nbremotsNow, user_name) {
   datapointsMotsMemo = [];
   datapointsMotsVus = [];
   datapointsMotsMemoPred = [];
   datapointsMotsVusPred = [];
   datapointsObjectif = [];
   datapointsToday = [];

  nbre_jour=240;
  $('.stat_container').slideDown();
  $(".stat_user_selected").removeClass("stat_user_selected");
  $("#user_"+user_id).addClass("stat_user_selected");
    var ctx = document.getElementById('myChart').getContext('2d');
    if(window.myLine==undefined)
    {window.myLine = new Chart(ctx, config);}
    $('.user_name_title').html(user_name);
        $.getJSON("ajax.php?action=getStats&user_id="+user_id, function(result){
          console.log(result.stats);
      stats=result.stats;
      OptimalRDs=result.OptimalRDs;
      datapoints=[];
      if(parseInt(stats.length)!=0)
      {jour_ini=stats[0].jour;
      jour_fin=stats[stats.length-1].jour;
      jour_debut="";
      for(k in stats)
      {
        jour=stats[k].jour;
        nbreMots=parseInt(stats[k].nbreMots);
        nbreMotTotal=parseInt(stats[k].nbreMotTotal);
        if(k==stats.length-1){nbreMotTotal+=parseInt(nbremotsNow)-nbreMots;nbreMots=nbremotsNow;}
        if(nbreMotTotal<nbreMots){nbreMotTotal=nbreMots;}
        //if(k>stats.length-nbre_jour){
          datapointsMotsMemo.push({t:jour,y:nbreMots});
          datapointsMotsVus.push({t:jour,y:nbreMotTotal});
        //}
        if(k>stats.length-12){
          jour=stats[k].jour;
          Objectif=parseInt(stats[k].nbreMotTotal)+100;
          jourFuture=moment(jour).add(10,"days").format("YYYY-MM-DD");
          datapointsObjectif.push({t:jourFuture,y:Objectif});
        }
      }
      date_ini = new Date(jour_ini);
      date_fin = new Date(jour_fin);
      deltaJour1 = (date_fin - date_ini)/(1000*60*60*24);
      $(".rangeDays").attr("max",deltaJour1);
      console.log(deltaJour1);
      //objectif=10+deltaJour1*10;

      date_debut = new Date(jour_debut);
      //deltaJour2 = (date_debut - date_ini)/(1000*60*60*24)
      //objectif_debut=10+deltaJour2*10;

      //myLine.data.datasets[2].data=[{t:jour_debut,y:objectif_debut},{t:jour_fin,y:objectif}];
      //10jours apres:
      today=moment().format("YYYY-MM-DD");
      jourFuture=moment().add(10,"days").format("YYYY-MM-DD");
      objectif10j=parseInt(nbreMotTotal)+100;
      //datapointsObjectif=[{t:today,y:nbreMotTotal},{t:jourFuture,y:objectif10j}];
      datapointsMotsToday=[{t:today,y:0},{t:today,y:objectif10j}];
      //datapointsMotsVusPred=[{t:today,y:nbreMotTotal},{t:jourFuture,y:nbreMotTotal}];

      //prediction mot memoire
      for(k=0;k<11;k++)
      {
        thisJour=moment().add(k,"days").format("YYYY-MM-DD");
        thisDay=+new Date(thisJour)/1000;
        //console.log(OptimalRDs);
        NbreMotsPred = OptimalRDs.filter(rk => rk.OptimalRD > thisDay);
        //console.log(NbreMotsPred);
        NbreMotsPred=NbreMotsPred.length;
        //console.log(NbreMotsPred);
        datapointsMotsMemoPred.push({t:thisJour,y:NbreMotsPred});
      }

      myLine.data.datasets[0].data=datapointsMotsMemo;
      myLine.data.datasets[1].data=datapointsMotsVus;
      //myLine.data.datasets[2].data=datapointsMotsToday;
      myLine.data.datasets[3].data=datapointsMotsMemoPred;
      //myLine.data.datasets[4].data=datapointsMotsVusPred;
      //myLine.data.datasets[5].data=datapointsObjectif;
      myLine.options.legend.display=false;
      myLine.options.tooltips.mode="nearest";
      myLine.options.elements.point.radius="1";
      myLine.options.elements.point.backgroundColor="#fff";
      myLine.options.elements.point.borderWidth="1";
      //myLine.canvas.parentNode.style.height = '128px';
      startingDate=moment().subtract(30,"days").format("YYYY-MM-DD");
      myLine.options.scales.xAxes[0].time.min=startingDate;
      myLine.update();
  			}
  		});
  	};

    $("#download_results").click(function() {
      csv_data="email"+'\t'+"nom"+'\t'+"score"+'\r\n';
      for(k in usersData)
        {var user=usersData[k];
          user_id=user.user_id;
          email=user.email;
          nom=user.first_name+" "+user.last_name;
          type=user.type;
          if(type=="eleve")
          {perf=$('#user_'+user_id+' > .badge_perf').html();
          csv_data+=email+'\t'+nom+'\t'+perf+'\r\n';}
        }

    var downloadLink = document.createElement("a");
    var blob = new Blob(["\ufeff", csv_data]);
    var url = URL.createObjectURL(blob);
    downloadLink.href = url;
    downloadLink.download = "resultat quiz.xls";
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
    });




  function toTitleCase(str) {
    return str.replace(/\w\S*/g, function(txt){
        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });
  }
	</script>

</body>
</html>
