
<style>
.iconContainer_Mission{top:10px;right:10px;position:absolute;}
.missionHasClass{display:none;}
.mission_item{position:relative;
    padding: 10px;
    margin: 20px 0px;
    box-shadow: 0px 2px 6px grey;}
.sub_mission_deck_item
{
  padding: 10px;
  margin: 20px 0px;
  box-shadow: 0px 2px 6px grey;
}
.more_icon{float:right;}
.btn--sub-mission{color:var(--mycolor2);}
</style>
<script>

class Mission_edition_manager{
  constructor(objectif_id,class_id) {
    //définit par défault pour la création d'un nouvel objectif
    this.mission=[];
    this.mission[0]={ objectif_id:objectif_id,
                      name:"",
                      decription:"",
                      class_ids:[class_id],
                      deck_ids:[],
                      exo_ids:[],
                      deadline:"",
                      starting_date:"0000-00-00",
                      starting_today:0,
                    };
    this.allDecks=[];
    this.currentIndex=0;

    if(class_info.role=="prof" || class_info.role=="eleve")
    {this.initMissionView();console.log('INITMISSIONVIEW');}
    if(class_info.role=="prof")
    {this.initMissionEditor(class_id);console.log('INITMISSIONEDITOR');}
  }
  createMission()
  {
    var _this=this;
    this.mission[this.currentIndex].class_ids=$("#mission_class_input").val();
    this.mission[this.currentIndex].deck_ids=$("#mission_deck_input").val();
    this.mission[this.currentIndex].exo_ids=$("#mission_exo_input").val();
    this.mission[this.currentIndex].starting_today=$(".mission_starting_input").is(':checked');
    this.mission[this.currentIndex].deadline=$(".mission_deadline_input").val();

    var params = JSON.parse(JSON.stringify(this.mission[this.currentIndex]));
    //console.log("param:",params);
    $.ajax({
        url: 'missionsAPI.php?action=createMission',
        type: "POST",
        data: params,
        dataType: 'json',
        success: function (result) {
          //console.log(result,result.msg_error,result.status);
             if(result.status=='error')
             {$(".js-msg-error-mission").html(result.msg_error);}
          _this.updateMissionView();
        }
    });
  }
  deleteMission(mission_id)
  {if(confirm("<?php echo __("Voulez-vous vraiment supprimer cette mission ?");?>"))
    {
      var _this=this;
      var url='missionsAPI.php?action=deleteMission&mission_id='+mission_id;
      $.getJSON(url, function(data)
      {console.log(data);
      $(".mission_item_"+mission_id).remove();
      //_this.updateMissionView();
      });
    }
  }
  showEditionMission(mission_id)
  {

  }
  updateMissionEditionView()
  {
    if($('#mission_class_input').val().length==0){$('.missionHasClass').hide();}
    else{
      $('.missionHasClass').show();
      if($('#mission_deck_input').val().length==0){$('.missionHasDeck').hide();}
      else{
        $('.missionHasDeck').show();
        if($('#mission_exo_input').val().length==0){$('.missionHasExo').hide();}
        else{$('.missionHasExo').show();}
      }
    }
  }
  initMissionView(){

    $(".colDroiteDeck").append(`<div class="tileDroite whiteTile" id="missionTile">
      <h3 style="padding-top:20px;">
        <span class="">Missions</span>
        <!--<span style="float:right;color:var(--mycolor2);cursor:hand;text-transform:uppercase;"  onclick="showMissionCreation();return false;"><?php echo __("Résultats");?></span>-->
      </h3>
      <div>
        <div class="missionContainer">
          <div class="missionDisplay"></div>
          <div class="missionEdition" style="position:relative;">
            <hr>
            <div class="missionEditionForm" style="display:none;">
            </div>
            <div class="iconContainerTopRight" style="">
              <span class='edit_icon miniIcon' onclick="$('.missionEditionForm').toggle();"></span>
            </div>
          </div>
        </div>
      </div>
    </div>`);
    this.updateMissionView()
  }
  updateMissionView()
  {
    var _this=this;
    var url='missionsAPI.php?action=getCurrentMissionByClass&class_id='+class_info.class_id;
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
          {$(".missionDisplay").append(`<div class="mission_item mission_item_`+mission_id+`">
            <div>mission `+mission_id+` par `+creator_name+`</div>
            <div style="display:none;">Commence `+starting_date+`</div>
            <div>A finir avant `+deadline+`</div>
            <div class='iconContainer_Mission'>
              <!--<span class='edit_icon miniIcon' onclick="mission_edition.showEditionMission(`+mission_id+`)"></span>-->
              <span class='close_icon miniIcon close_mission' style="display:none;" onclick="mission_edition.deleteMission(`+mission_id+`)"></span>

            </div>
          </div>`);}
          if($(".mission_item_"+mission_id+" .sub_mission_item_deck_"+deck_id).length==0)
          {$(".mission_item_"+mission_id).append(`<div class="sub_mission_deck_item sub_mission_item_deck_`+deck_id+`">
            <div>
              <img src="deck_img/deck_`+hasImage+`.png" style="width:50px;height:50px;">
              <span>`+deck_name+`</span>
            </div>
          </div>`);}

          $(".mission_item_"+mission_id+" .sub_mission_item_deck_"+deck_id).append(`<div class="sub_mission_item sub_mission_item_`+sub_mission_id+`">
              <div>`+exo_name+`:</div>
              <div class="sub_mission_result_container sub_mission_result_container_`+sub_mission_id+`">
                <div class="progressbar--thin">
                  <div class="progressbar_fluid--thin js-progressbar_fluid_mission_`+sub_mission_id+`"></div>
                </div>
                <div>
                  <span class="score_sub_mission score_sub_mission_`+sub_mission_id+`">0</span> sur `+quantity+`
                  <div>
                    <a href="cards.php?deck_id=`+deck_id+`&exo_id=`+exo_id+`" class="btn--sub-mission"><?php echo __("Faire la mission");?></a>
                  </div>
                </div>
              </div>
            </div>`);

          if(creator_id==user_id){$(".mission_item_"+mission_id+" .close_mission").show();}
          else{$(".mission_item_"+mission_id+" .close_mission").remove();}
        }

        if(data.score_sub_mission!=null)
        {
          for(var k in data.score_sub_mission)
          {
            var sub_mission_id=data.score_sub_mission[k].sub_mission_id;
            var score=parseInt(data.score_sub_mission[k].score);
            var quantity=parseInt(data.score_sub_mission[k].quantity);
            var success=data.score_sub_mission[k].success;
            if(score>=quantity){
              $(".sub_mission_result_container_"+sub_mission_id).html(`<img src="img/check2.png" width="15px">Sous-mission validée.`);
            }else {
              $('.score_sub_mission_'+sub_mission_id).html(score);
              var pcentProgressbar=Math.round(100*score/quantity)+"%";
              $(".js-progressbar_fluid_mission_"+sub_mission_id).css("width",pcentProgressbar);
            }
          }
        }
        if(data.score_mission!=null)
        {
          for(k in data.score_mission)
          {
            var mission_id=data.score_mission[k].mission_id;
            var success=data.score_mission[k].success;
            var completed_date=data.score_mission[k].completed_date;
            if(success==1){
              $(".mission_item_"+mission_id+" .sub_mission_deck_item").hide();
              $(".mission_item_"+mission_id).append(`<img src="img/check2.png" width="15px">Mission validé.`).addClass("mission_success");
              $(".mission_item_"+mission_id).append(`<span onclick="$('.mission_item_`+mission_id+` .sub_mission_deck_item').show();$(this).hide();" class="more_icon">Voir plus</span>`);
            }
          }
        }
      }
      else
      {$(".missionDisplay").html("Aucune mission disponible.");}
      // if(class_info.role=="prof")
      // {$(".objectifUpdate").show();}
      // if(class_info.role=="eleve")
      // { if( data.objectif==null ){$("#objectifTile").hide();}
      //   else if(data.objectif.quantity==0){$("#objectifTile").hide();}
      //   $(".objectifScore").show();}
    });
  //show or not the objective tile depending on role and category
  }

  initMissionEditor(class_id=0)
  {
      var _this=this;
      var d = new Date();
      var monthZero=["01","02","03","04","05","06","07","08","09","10","11","12"];
      var today=d.getFullYear()+"-"+monthZero[d.getMonth()]+"-"+d.getDate();
      var nextWeek=d.getTime()+7*24*60*60*1000;
      nextWeek=new Date(nextWeek);

      nextWeek=nextWeek.getFullYear()+"-"+monthZero[nextWeek.getMonth()]+"-"+nextWeek.getDate();
      $(".missionEditionForm").html(`
        <h3>Nouvelle mission</h3>
          <section>
            <label>Classe(s)</label>
            <select id="mission_class_input" class="mission_class_input" name="class_ids" multiple required></select>
          </section>
          <section class="missionHasClass">
            <label>Liste(s)</label>
            <select id="mission_deck_input" class="mission_deck_input" style="display:none;" name="deck_ids" multiple required></select>
          </section>
          <section class="missionHasDeck missionHasClass">
            <label>Exercices(s)</label>
            <select id="mission_exo_input" class="mission_exo_input" style="display:none;" name="exo_ids" multiple required></select>
          </section>
          <section class="missionHasExo missionHasDeck missionHasClass">
            <input type="checkbox" class="mission_starting_input missionHasExo" name="starting_date" value="true" checked></input>
            <label>commencer à partir de maintenant</label>
            <input type="date" class="mission_deadline_input" name="deadline" min='`+today+`' value='`+nextWeek+`' required></input>

            <button class="btnStd1 createNewMissionBtn" onclick="mission_edition.createMission();">Publier la mission</button>
            <p class="msg-error js-msg-error-mission"></p>
          </section>
        `);

    $.getJSON("ajax.php?action=getClassProf", function(data)
      {
        $('.optionClassMission').remove();
        for(k in data)
        {
          if($(".optionClassMission_"+data[k].class_id).length==0)
          {$('#mission_class_input').append("<option class='optionClassMission optionClassMission_"+data[k].class_id+"' value='"+data[k].class_id+"'>"+data[k].class_name+" - "+data[k].promo+"</option>");
          }
        }
        if(class_id!=0){$('#mission_class_input').val([class_id]);
          _this.mission[_this.currentIndex].class_ids=[class_id];
        }
        var SelectizeClassObj=$('#mission_class_input').selectize({closeAfterSelect: true});
        //console.log(SelectizeClassObj);
        SelectizeClassObj[0].selectize.on('change', function(e, value){
          _this.mission[_this.currentIndex].class_ids=$("#mission_class_input").val();
          _this.updateListsOnClass();
          _this.updateMissionEditionView();
        });

        $.getJSON("ajax.php?action=getDeckProf", function(data)
          {
            _this.allDecks=data;
            //console.log(data);
            //var SelectizeDeckObj=$('.mission_deck_input').selectize();
            var SelectizeDeckObj=$('#mission_deck_input').selectize({
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
            SelectizeDeckObj[0].selectize.on('change', function(e, value){
              _this.mission[_this.currentIndex].deck_ids=$("#mission_deck_input").val();
              _this.updateMissionEditionView();
            });
            _this.updateListsOnClass();
            _this.updateMissionEditionView();
          });

        //_this.updateMissionEditionView();
      });

    $.getJSON("ajax.php?action=getExoMissionable", function(data)
      {
        $('.optionExoMission').remove();
        for(k in data)
        {
          $('#mission_exo_input').append("<option class='optionExoMission' value='"+data[k].exo_id+"'>"+data[k].name+"</option>");
        }
        var SelectizeExoObj=$('#mission_exo_input').selectize({
          valueField: 'exo_id',
          labelField: 'name',
          searchField: ['name'],
          closeAfterSelect: true,
          placeholder: 'Select exercice',
          options: data
        });

        SelectizeExoObj[0].selectize.on('change', function(e, value){
          _this.mission[_this.currentIndex].exo_ids=$("#mission_exo_input").val();
          _this.updateMissionEditionView();
        });
      });
  }
  updateListsOnClass()
  {
    var _this=this;
    var decksAvailableByAllClass=_this.allDecks.filter(function(elem){
      var flag=true;
      for(k in _this.mission[_this.currentIndex].class_ids)
       {
         var class_id=_this.mission[_this.currentIndex].class_ids[k];
          if(elem.class_ids.indexOf(class_id)==-1)
          {
            flag=false;
          }
       }
      return flag;
    });

    //console.log(decksAvailableByAllClass);
    $('#mission_deck_input').selectize()[0].selectize.destroy();
    var SelectizeDeckObj=$('#mission_deck_input').selectize({
      valueField: 'deck_id',
      labelField: 'deck_name',
      searchField: ['deck_name'],
      placeholder: 'Select deck',
      closeAfterSelect: true,
      options: decksAvailableByAllClass,
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
    SelectizeDeckObj[0].selectize.on('change', function(e, value){
      _this.mission[_this.currentIndex].deck_ids=$("#mission_deck_input").val();
      _this.updateMissionEditionView();
    });

    // for(k in decksAvailableByAllClass)
    // {
    //   console.log(decksAvailableByAllClass[k]);
    //
    //   $('.mission_deck_input').append("<option class='mission_deck_option' value='"+decksAvailableByAllClass[k].deck_id+"'>"+decksAvailableByAllClass[k].deck_name+"</option>");
    // }



  }
}



</script>
