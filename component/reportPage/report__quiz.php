<script>
var REPORT_usersData;
var REPORT_quizsData;
function displayQuiz(){
  $('.report_page').hide();
  $('.report_page_quiz').show();
  $('.tab2').removeClass("activeT");
  $('.tab2_quiz').addClass("activeT");

  $(".report_page_quiz").html(`
    <h3><?php echo __("Rapports des quiz");?></h3>
    <div class="report_ filter_container">
    <select class="rangeDays selectPeriodeClass" onchange="range=$(this).val();resizeClassReport(range);">
      <option value="7"><?php echo __("cette semaine");?></option>
      <option value="31" selected><?php echo __("ce mois");?></option>
      <option value="92"><?php echo __("ce trimestre");?></option>
      <option value="365"><?php echo __("cette année");?></option>
    </select>
    </div>
    <div class="reportPageContainer">
      <div class="listeEleves">
        <table class="tableScoreClass" style="margin:auto;">
        </table>
      </div>
      <div class="reportSection"></div>
    </div>`);
    displayQuizTableReport();
}

function displayQuizTableReport()
{

  $(".tableScoreClass").html("<tr class='ReportTableHeader'><th></th><th class='user_name user_name_head'>Nom</th></tr>");
  $(".tableScoreClass .ReportTableHeader").after("<tr class='iconDeckLine'><td></td><td></td><td class='MarksCell'></td></tr>");
  $(".tableScoreClass .ReportTableHeader").append("<th class='MarksCell borderCell'><?php echo __("Moyenne sur 100");?></th>");
  /*$(window).scroll(function(){
    console.log("scroll");
    $('.user_nameClone').css({
    'left': $(this).scrollLeft()
    });
  });*/
  $.getJSON("ajax.php?action=getUsers&class_id="+class_id, function(usersData){
    REPORT_usersData=usersData;
    for(i in usersData)
      {
        userData=usersData[i];
        if($('#report_user_'+userData.user_id).length==0 && userData.role!="prof")
        {
        $(".tableScoreClass").append(`
          <tr class='user_name_cell userLineItem userTableScoreRole_`+userData.role+`' data-position='`+userData.position+`' id='report_user_`+userData.user_id+`'>
            <td class='borderCell user_avatar'><img src="avatar/avatar_`+userData.avatar_id+`_XS.png" class="avatar_S avatarFame"></td>
            <td class='borderCell user_name'>`+userData.user_name+`</td>
            <td class='MarksCell' id='moyenne_`+userData.user_id+`'>-</td>
          </tr>`);
        }
      }

      getQuizData(usersData);
  });
}

function getQuizData(usersData)
{
  $.getJSON("ajax.php?action=getClassQuizs&class_id="+class_id, function(result){
      quizsData=result.quizs;
      REPORT_quizsData=quizsData
      marks=result.marks;
      for(i in quizsData)
        { quiz_id=quizsData[i].quiz_id;
          expire=quizsData[i].expire;
          day = moment.unix(expire).format("D/MM");
          noteMax=quizsData[i].noteMax;
          deck_id=quizsData[i].deck_id;
          hasImage=quizsData[i].hasImage;
          if(hasImage>1){url_img_deck='deck_img/deck_'+hasImage+'.png';}else{url_img_deck='img/default_deck.png';}
          deck_name=result.quizs[i].deck_name;
          $(".tableScoreClass .ReportTableHeader").append(`<th class='borderCell MarksCell deck_name quiz_`+quiz_id+`' id='entete_quiz_`+quiz_id+`' style='position:relative;'>
            <br>`+deck_name+`<br>
            fait le `+day+`<br>
            sur `+noteMax+`<br>
            <img src='img/del.png' title='<?php echo __("supprimer ce quiz definitivement");?>' class='delQuizIcon' onclick='delQuiz(`+quiz_id+`);' width='20px'>
          </th>`);
          $(".tableScoreClass .iconDeckLine").append(`<td class='borderCell MarksCell quiz_`+quiz_id+`'><img class='iconDeckImg' src='`+url_img_deck+`' onclick=""><input title='<?php echo __("selectionner ce quiz pour le calcul de la moyenne");?>' type='checkbox' checked onclick='calculMoyenneQuiz();' id='selectedQuiz_`+quiz_id+`'></td>`);
          for(k in usersData)
          {
            classUserId=usersData[k].user_id;
            $('#report_user_'+classUserId).append("<td class='borderCell MarksCell quiz_"+quiz_id+"' id='user_"+classUserId+"_quiz_"+quiz_id+"' style=''><input type='number' min='0' max='"+noteMax+"' placeholder='Note' class='note_input' style='background-color:#ffffff00;' onchange='changeMarks("+classUserId+","+quiz_id+");'>/"+noteMax+"</td>");
          }
        }
        for(i in result.marks){
          note=result.marks[i].note;
          user_id=result.marks[i].user_id;
          quiz_id=result.marks[i].quiz_id;
          $("#user_"+user_id+"_quiz_"+quiz_id).find(".note_input").val(note);
        }
        calculMoyenneQuiz();
      });
}


function calculMoyenneQuiz(){
  for(i in REPORT_usersData){
    var user_id=REPORT_usersData[i].user_id
    var totalNote=0;
    var totalNoteMax=0;
    $(".unactiveQuiz").removeClass("unactiveQuiz");
    for(q in REPORT_quizsData){
      quiz_id=REPORT_quizsData[q].quiz_id;
      noteMax=REPORT_quizsData[q].noteMax;
      if($("#selectedQuiz_"+quiz_id).is(':checked'))
      { note=$("#user_"+user_id+"_quiz_"+quiz_id).find(".note_input").val();
        if(note!=""){
        totalNoteMax+=parseInt(noteMax);
        totalNote+=parseInt(note);
        }
      }
      else{
        $(".quiz_"+quiz_id).addClass("unactiveQuiz");
      }
    }
    moy='';
    if(totalNoteMax!=0){moy=Math.round(totalNote*100/totalNoteMax);}
    $('#moyenne_'+user_id).html(moy+"/100");
  }
}
function delQuiz(quiz_id)
{
  if(confirm("<?php echo __("Voulez-vous supprimer ce quiz définitivement ?");?>"))
  {
  $(".quiz_"+quiz_id).remove();
  $.getJSON("ajax.php?action=delQuiz&quiz_id="+quiz_id, function(result){});
  }
}

</script>
