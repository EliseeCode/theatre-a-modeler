<script>
function displayStudentList()
{
  $('.report_page').hide();
  $('.report_page_studentList').show();

  $('.tab2').removeClass("activeT");
  $('.tab2_studentlist').addClass("activeT");


  $(".report_page_studentlist").html(`
    <h3><?php echo __("Liste des élèves");?></h3>
    <select class="rangeDays">
      <option value="1" >1 jours</option>
      <option value="7" >1 semaine</option>
      <option value="31" >1 mois</option>
      <option value="62" selected>1 trimestre</option>
      <option value="365"  >1 ans</option>
    </select>
    <div><?php echo __("Cliquez sur le nom d'un élève pour faire apparaître le rapport");?></div>
    <div class="reportPageContainer">
      <div class="listeEleves">
        <table class="tableScoreClass" style="margin:auto;">
        </table>
      </div>
      <div class="reportSection"></div>
    </div>`);

    displayStudentTableReport();
}

function displayStudentTableReport()
{
  $(".tableScoreClass").html("<tr class='ReportTableHeader'><th></th><th class='user_name user_name_head'>Nom</th><th>Niveau</th><th>Fortune</th></tr>");
  //$(".tableScoreClass").append("<tr class='classLineItem activeUserLine' id='allClassLine' onclick='showClassStat();'><td class='borderCell user_name class_name_cell'><?php echo __("Toute la classe");?></td></tr>");
  $.getJSON("ajax.php?action=getUsers&class_id="+class_id, function(usersData){
    for(i in usersData)
      {
        userData=usersData[i];
        if($('#report_user_'+userData.user_id).length==0 && userData.role!="prof")
        {
        $(".tableScoreClass").append(`
          <tr class='user_name_cell userLineItem userTableScoreRole_`+userData.role+`' data-position='`+userData.position+`' id='report_user_`+userData.user_id+`' onclick='showStudentStat(`+userData.user_id+`);'>
            <td class='borderCell user_avatar'><img src="avatar/avatar_`+userData.avatar_id+`_XS.png" class="avatar_S avatarFame"></td>
            <td class='borderCell user_name'>`+userData.user_name+`</td>
            <td class='borderCell user_lvl' style='padding:0 20px;'>`+getLvlFromXp(userData.nbreCoins)+`</td>
            <td class='borderCell user_ruby'>`+userData.ruby+` <span class="ruby ruby_inline"></span></td>
          </tr>`);
        }
      }
  });
}

function showClassStat()
{
  $(".activeUserLine").removeClass("activeUserLine");
  $("#allClassLine").addClass("activeUserLine");
  nbre_jour=$("#page_report").find(".rangeDays").val();
  $(".rangeDays").off();
  $(".rangeDays").on("change",function(){showClassStat();});
  if(typeof classStat=="undefined"){
    $.getJSON("ajax.php?action=getAllClassStat&class_id="+class_info.class_id, function(result)
      {
      classStat=result;
      displayStat(".reportSection",classStat,nbre_jour);
    });
  }else{displayStat(".reportSection",classStat,nbre_jour);}
}

function showStudentStat(eleve_id)
{
  $(".activeUserLine").removeClass("activeUserLine");
  $("#user_"+eleve_id).addClass("activeUserLine");
  nbre_jour=$("#page_report").find(".rangeDays").val();
  $(".rangeDays").off();
  $(".rangeDays").on("change",function(){showStudentStat(eleve_id);});
  if(typeof StudentStat[eleve_id]=="undefined"){
    $.getJSON("ajax.php?action=getThisClassUserStat&user_id="+eleve_id+"&class_id="+class_info.class_id, function(result)
      {
      StudentStat[eleve_id]=result;
      displayStat(".reportSection",StudentStat[eleve_id],nbre_jour);
      $(".kickOutBtn").remove();
      $(".reportSection").append("<div class='BtnStd1 kickOutBtn'  onclick='kickout("+eleve_id+");'><?php echo __("Sortir cet élève de la classe");?> "+class_info.class_name+"</div>");
	  $(".reportSection").append("<div class='BtnStd1 upgradeBtn' style='margin:auto;'  onclick='upgradeProf("+eleve_id+");'><?php echo __("Cette personne est professeur de la classe");?> "+class_info.class_name+"</div>");
	  
    });
  }else{displayStat(".reportSection",StudentStat[eleve_id],nbre_jour);}
  $(".kickOutBtn").remove();
  $(".reportSection").append("<div class='BtnStd1 kickOutBtn' onclick='kickout("+eleve_id+");'><?php echo __("Sortir cet élève de la classe");?> "+class_info.class_name+"</div>");
}

function kickout(user_id){
  class_id=class_info.class_id;
  if(confirm("<?php echo __("Voulez-vous sortir cet élève de la classe ?");?>"))
    {
      $("#user_"+user_id).remove();
      showClassStat();
      $.getJSON("ajax.php?action=kickOutUser&class_id="+class_id+"&user_id="+user_id, function(result){});
    }
}
function upgradeProf(user_id){
  class_id=class_info.class_id;
  if(confirm("<?php echo __("Voulez-vous donner à cet utilisateur le rôle de prof ?");?>"))
  {
    $("#user_"+user_id).remove();
    $.getJSON("ajax.php?action=upgradeProf&class_id="+class_id+"&user_id="+user_id, function(result){});
  }
  }


</script>
