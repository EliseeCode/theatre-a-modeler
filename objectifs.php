<script>
function changeObjectifClass()
{
  var newQuantityObjectif=$(".objectifFormQuantity").val();
  var newDay_numObjectif=$(".objectifFormDay_num").val();

  url='objectifsAPI.php?action=createUpdateObjectif&day_num='+newDay_numObjectif+'&quantity='+newQuantityObjectif+'&class_id='+class_info.class_id;
  $.getJSON(url, function(data)
  {
    //console.log(data);
    updateObjectifView();
  });

}
function updateObjectifView()
{
  var weekdays=["","<?php echo __("dimanche");?>","<?php echo __("lundi");?>","<?php echo __("mardi");?>","<?php echo __("mercredi");?>","<?php echo __("jeudi");?>","<?php echo __("vendredi");?>","<?php echo __("samedi");?>"];
  url='objectifsAPI.php?action=getCurrentObjectifsByClass&class_id='+class_info.class_id;
  $.getJSON(url, function(data)
  {
    console.log("objectif_data",data);
    if(data.objectif!=null)
    {
      quantity=parseInt(data.objectif.quantity);
      day_num=parseInt(data.objectif.day_num);
      objectif_id=parseInt(data.objectif.objectif_id)
      $(".objectifFormQuantity").val(quantity);
      $(".objectifFormDay_num").val(day_num);
      $(".objectifScore").html(`

        <div class="objectifScore_item objectifScore_`+objectif_id+`">
          <div class="objectiveStreak"><span class="streakValue">0</span> <?php echo __("d'affilé");?></div>
          <div class="objectif_status">
            <div class="progressbar--thin">
              <div class="progressbar_fluid--thin js-progressbar_fluid_objectif_`+objectif_id+`"></div>
            </div>
            <div>
              <span class="currentScoreObjectif">0</span>/`+quantity+` avant `+weekdays[day_num]+`
            </div>
          </div>
        </div>`);
      if(data.streak!=null)
      {
        $(".objectifScore_"+objectif_id+" .streakValue").html(data.streak.value);
      }
      if(data.score!=null)
      {
        score=parseInt(data.score.score);
        if(score>=quantity){
          $(".objectifScore_"+objectif_id+" .objectif_status").html(`Objectif validé (`+quantity+`) <br>Prochain objectif `+weekdays[day_num]);
        }else {
          $(".objectifScore_"+objectif_id+" .currentScoreObjectif").html(score);
          var pcentProgressbar=Math.round(100*score/quantity)+"%";
          $(".js-progressbar_fluid_objectif_"+objectif_id).css("width",pcentProgressbar);        
        }
      }

    }


    if(class_info.role=="prof"||class_info.role=="perso")
    {$(".objectifUpdate").show();}
    if(class_info.role=="eleve")
    { if( data.objectif==null ){$("#objectifTile").hide();}
      else if(data.objectif.quantity==0){$("#objectifTile").hide();}
      $(".objectifScore").show();}
  });
//show or not the objective tile depending on role and category
}



</script>
