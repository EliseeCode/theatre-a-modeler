<script>
function displayDecksWork()
{
  $('.report_page').hide();
  $('.report_page_decks').show();

  $('.tab2').removeClass("activeT");
  $('.tab2_decks').addClass("activeT");

  $(".report_page_decks").html(`
    <h3><?php echo __("Rapports de Classe");?></h3>
    <div class="report_ filter_container" style="margin:auto;">
      <section class="input_section">
        <select class="report_filter_deck_input" name="deck_id" required></select>
      </section>
      <section class="input_section">
        <select class="report_filter_exo_input" name="exo_id" required value="9"></select>
      </section>
      <section class="input_section">
        <input type="button" class="BtnStd1" value="Check" onclick="getReportDeckExo();"></input>
      </section>
    </div>
    <div class="reportPageContainer">
      <div class="listeEleves">
        <table class="tableScoreClass" style="margin:auto;">
        </table>
      </div>
    </div>`);
  $.getJSON("ajax.php?action=getDeckClass&class_id="+class_id, function(data)
    {
      var SelectizeDeckObj=$('.report_filter_deck_input').selectize({
        valueField: 'deck_id',
        labelField: 'deck_name',
        searchField: ['deck_name'],
        placeholder: 'Select deck',
        closeAfterSelect: true,
        options: data,
        render: {
          option: function(item, escape) {
            var html = `<div style="padding:3px;">
              <img src='deck_img/deck_`+item.hasImage+`.png' width='30px' style="float:left;margin:0 5px;">
              <div class="">`+item.deck_name+`</div>
              </div>`;
            return html;
          }
        }
      });
    });
    $.getJSON("ajax.php?action=getExo", function(data)
      {
        for(k in data)
        {
          $('.report_filter_exo_input').append("<option class='' value='"+data[k].exo_id+"'>"+data[k].name+"</option>");
        }
        var SelectizeExoObj=$('.report_filter_exo_input').selectize({
          valueField: 'exo_id',
          labelField: 'name',
          searchField: ['name'],
          closeAfterSelect: true,
          placeholder: 'Select exercice',
          options: data
        });
    });
    displayUserTableReport();
}

function displayUserTableReport()
{
  $(".tableScoreClass").html("<tr class='ReportTableHeader'><th></th><th class='user_name user_name_head'>Nom</th></tr>");
  //$(".tableScoreClass").append("<tr class='classLineItem activeUserLine' id='allClassLine' onclick='showClassStat();'><td class='borderCell user_name class_name_cell'><?php echo __("Toute la classe");?></td></tr>");
  $.getJSON("ajax.php?action=getUsers&class_id="+class_id, function(usersData){
    for(i in usersData)
      {
        userData=usersData[i];
        if($('#report_user_'+userData.user_id).length==0 && userData.role!="prof")
        {
        $(".tableScoreClass").append(`
          <tr class='user_name_cell userLineItem userTableScoreRole_`+userData.role+`' data-position='`+userData.position+`' id='report_user_`+userData.user_id+`'>
            <td class='borderCell user_avatar'><img src="avatar/avatar_`+userData.avatar_id+`_XS.png" class="avatar_S avatarFame"></td>
            <td class='borderCell user_name'>`+userData.user_name+`</td>
          </tr>`);
        }
      }
  });
}
function getReportDeckExo(){
  reportDeck=$(".report_filter_deck_input").val();
  reportExo=$(".report_filter_exo_input").val();
  if (reportExo!="" && reportDeck!="")
  {
  $.getJSON("ajax.php?action=getReportDeckExo&exo_id="+reportExo+"&deck_id="+reportDeck+"&class_id="+class_id, function(result){
    var quantity=result.deck.quantity;
    if($(`.deck_`+result.deck.deck_id+`exo_`+result.exo.exo_id).length==0)
    {
      $(".ReportTableHeader").append(`<th class="deck_`+result.deck.deck_id+`exo_`+result.exo.exo_id+`">
          <div>
            <img src="deck_img/deck_`+result.deck.hasImage+`.png" class="tiny_deck_image">
            <div>`+result.deck.deck_name+`<br>`+result.exo.name+`</div>
          </div>
        </th>`);
      $(".userLineItem").append(`<td class="deck_`+result.deck.deck_id+`exo_`+result.exo.exo_id+`">-</td>`)
      for(i in result.scoresData){
        var score=result.scoresData[i].score;
        var user_id=result.scoresData[i].user_id;
        if(score==quantity){
        $("#report_user_"+user_id+" .deck_"+result.deck.deck_id+"exo_"+result.exo.exo_id).html("<img src='img/check2.png' width='30px'>");
        }
        else if(score>0){
        $("#report_user_"+user_id+" .deck_"+result.deck.deck_id+"exo_"+result.exo.exo_id).html(score+`/`+quantity);
        }
        else {$("#report_user_"+user_id+" .deck_"+result.deck.deck_id+"exo_"+result.exo.exo_id).html("-");}

      }
    }
  });
  }
}


</script>
