<script>
function displayMissionWork(){
  $('.report_page').hide();
  $('.report_page_missions').show();
  $('.tab2').removeClass("activeT");
  $('.tab2_missions').addClass("activeT");

  $(".report_page_missions").html(`
    <h3><?php echo __("Rapports des missions");?></h3>
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
    </div>`);
    displayMissionTableReport();
}

function displayMissionTableReport(){
  $(".tableScoreClass").html("<tr class='ReportTableHeader'><th></th><th class='user_name user_name_head'>Nom</th></tr>");
  $(".tableScoreClass .ReportTableHeader").after("<tr class='sub_missionLine'><td></td><td></td></tr>");
  $.getJSON("ajax.php?action=getUsers&class_id="+class_id, function(usersData){
    for(i in usersData)
      {
        userData=usersData[i];
        if($('#report_user_'+userData.user_id).length==0 && userData.role!="prof")
        {
        $(".tableScoreClass").append(`
          <tr class='userLineItem userTableScoreRole_`+userData.role+`' data-position='`+userData.position+`' id='report_user_`+userData.user_id+`' onclick='showStudentStat(`+userData.user_id+`);'>
            <td class='borderCell user_avatar'><img src="avatar/avatar_`+userData.avatar_id+`_XS.png" class="avatar_S avatarFame"></td>
            <td class='borderCell user_name'>`+userData.user_name+`</td>
            <td class='MarksCell' id='moyenne_`+userData.user_id+`'>-</td>
          </tr>`);
        }
      }

      getMissionData();
  });
}
function getMissionData()
{
  var url='missionsAPI.php?action=getCurrentMissionByClassProf&class_id='+class_info.class_id;
  $.getJSON(url, function(data)
  {
    console.log(data);
    if(data.mission!=null)
    {
      $(".mission_item").remove();
      for(var k in data.mission)
      {
        var mission_id=data.mission[k].mission_id;
        var sub_mission_id=data.mission[k].sub_mission_id;
        var nbre_sub_mission=data.mission[k].nbre_sub_mission;
        var exo_id=data.mission[k].exo_id;
        var exo_name=data.mission[k].exo_name;
        var deck_id=data.mission[k].deck_id;
        var hasImage=data.mission[k].hasImage;
        var deck_name=data.mission[k].deck_name;
        var quantity=data.mission[k].quantity;
        var class_id=data.mission[k].class_id;
        var deadline=data.mission[k].deadline;
        var creator_id=data.mission[k].creator_id;
        var creator_name=data.mission[k].creator_name;
        var starting_date=data.mission[k].starting_date;

        if($(".mission_item_"+mission_id).length==0)
        {
          $(".tableScoreClass .ReportTableHeader").append(`<th class='borderCell MissionCell mission_item_`+mission_id+`' id='entete_mission_`+mission_id+`' style='position:relative;' colspan='`+nbre_sub_mission+`'>
            `+mission_id+`</th>`);
            for(var k_sub in data.mission)
            {
              if(data.mission[k_sub].mission_id==mission_id && $(".mission_cell_"+mission_id+" .sub_mission_"+data.mission[k_sub].sub_mission_id).length==0)
              {
              $(".userLineItem,.sub_missionLine").append("<td class='mission_cell_"+mission_id+" sub_mission_"+data.mission[k_sub].sub_mission_id+"'>-</td>");
              }
            }
          $(".sub_missionLine .sub_mission_"+sub_mission_id).html(`<img src="deck_img/deck_`+hasImage+`.png" class="tiny_deck_image"><br>`+deck_name+`<br>`+exo_name);
        }
      }
    }
  });
}
</script>
