

<script>
var posYini;
var posXini;
var posYfin;
var posXfin;
var flagSelect=false;
function PlayLetterGrid(){
  mode = 0;
}

var freqAlpha=[' ',' ',' ',' ','A','A','A','A','A','B','C','D','E','E','E','E','E','E','E','E','E','E','E','E','E','E','F','G','H','I','I','I','I','I','J','L','M','N','O','O','O','O','P','Q','R','S','T','U','U','U','U','V','Y'];
function createLetterGrid(){
  for(i in selected_card)
  {id_a_travailler_restant[i]=selected_card[i];}
  if (mode === 0){
    document.getElementById("lettergrid").innerHTML = BoardToHtml(" ");
    mode = 1;
  }
  else{
    GetWordsFromInput();

    for(var i = 0, isSuccess=false; i < 10 && !isSuccess; i++){
      CleanVars();
      isSuccess = PopulateBoard();
    }

    document.getElementById("lettergrid").innerHTML =
      (isSuccess) ? BoardToHtml(" ") : "Failed to find crossword." ;
      for(k in $(".square:not(.letter)"))
      {$(".square:not(.letter):eq("+k+")").html(rand_parmi(freqAlpha));}

      $(".square").addClass("letter");
      $(".square").addClass("letter gridLetterSquare");
  }

  $('.square').on("click tap",function(){
    if(flagSelect){
      Words2check=wordsActive.filter(function(elem){return ((posXini==elem.x && posXfin==elem.x && elem.dir==1)||(posYini==elem.y && posYfin==elem.y && elem.dir==0));});
      for(var W in Words2check){
        verificationGridLetter(posXini,posYini,Words2check[W]);
      }
    posYini="";
    posXini="";
    $(".highLightSquare").removeClass("highLightSquare");
    }
    else{
    arr=this.id.split("_");
    posYini=parseInt(arr[1]);
    posXini=parseInt(arr[2]);
    $("#box_"+posYini+"_"+posXini).addClass("highLightSquare");
    }
    flagSelect=!flagSelect;
  });

  $('.square').on("mouseup",function(){Words2check=wordsActive.filter(function(elem){return ((posXini==elem.x && posXfin==elem.x && elem.dir==1)||(posYini==elem.y && posYfin==elem.y && elem.dir==0));});
    console.log("clickuP");
    for(var W in Words2check){
      verificationGridLetter(posXini,posYini,Words2check[W]);
      }
    posYini="";
    posXini="";
    $(".highLightSquare").removeClass("highLightSquare");
    })
  $('.square').on("mousedown",function(){
    $(".highLightSquare").removeClass("highLightSquare");
    arr=this.id.split("_");
    posYini=parseInt(arr[1]);
    posXini=parseInt(arr[2]);
    $("#box_"+posYini+"_"+posXini).addClass("highLightSquare");
  });

  $('.square').on("mousemove",function(){
    arr=this.id.split("_");
    posYfin=parseInt(arr[1]);
    posXfin=parseInt(arr[2]);
    $(".highLightSquare").removeClass("highLightSquare");
    if(posYini==posYfin){
      for(k=Math.min(posXini,posXfin);k<=Math.max(posXini,posXfin);k++){
        $("#box_"+posYini+"_"+k).addClass("highLightSquare");
      }
    }
    if(posXini==posXfin){
      for(k=Math.min(posYini,posYfin);k<=Math.max(posYini,posYfin);k++){
        $("#box_"+k+"_"+posXini).addClass("highLightSquare");
      }
    }
  })
}
function verificationGridLetter(posMouseX,posMouseY,mot)
{
  correctFlag=true;
    var posX=parseInt(mot.x);
    var posY=parseInt(mot.y);
    for(var k in mot.char)
    {
      $("#box_"+posY+"_"+posX).addClass("mot_"+mot.id);
      if(!$("#box_"+posY+"_"+posX).hasClass("highLightSquare")){correctFlag=false;}
      if(mot.dir==0){posX++;}
      if(mot.dir==1){posY++;}
    }

    if(id_a_travailler_restant.indexOf(mot.id)!=-1 && $(".highLightSquare").length==mot.char.length && correctFlag){
      update_mots_restant();
      $(".mot_"+mot.id).addClass('goodLetter');
      $("#clue_item_"+mot.id).css("border","3px lime solid");
      removeElem(mot.id,id_a_travailler_restant);
      selected_card_done.push(card_id);
      $.getJSON("ajax.php?action=addActiviteGlobal&exo_id=3&card_id="+card_id+"&game="+game+"&correctness=1", function(result){});
      $.getJSON("ajax.php?action=cardLearned&exo_id=3&card_id="+card_id+"&puissance=15", function(result){
        console.log(result);
        $('.nbreCoins').html(result.nbreCoins);
        updateXp(result.nbreCoins);
        $('#XPcontainer').append('<div class="animatedXP" style="">+'+result.coins2add+'xp</div>');
        if(result.bankStatus=="limit"){$(".animatedXP").html("<span style='font-size:0.8;'><?php echo __("Limite quotidienne atteinte");?></span>");}
        play_audio_coin();
        $(".animatedXP").on("animationend",function(){$(this).remove();});
        //gestion durée
        // Create a new JavaScript Date object based on the timestamp
        // multiplied by 1000 so that the argument is in milliseconds, not seconds.
        Current_TimeStamp=Math.floor(Date.now() / 1000);
        Delta=result.nextOptimalRD-Current_TimeStamp;
        console.log(Delta,result.nextOptimalRD,Current_TimeStamp);
        DeltaY=Math.floor(Delta/(365*24*60*60));
        DeltaM=Math.floor((Delta-DeltaY*365*24*60*60)/(30*24*60*60));
        DeltaS=Math.floor((Delta-DeltaY*365*24*60*60-DeltaM*30*24*60*60)/(7*24*60*60));
        DeltaJ=Math.floor((Delta-DeltaY*365*24*60*60-DeltaM*30*24*60*60-DeltaS*7*24*60*60)/(24*60*60));
        DeltaText="";
        if(DeltaJ==1){DeltaText="<?php echo __('En mémoire pour un peu plus de ');?>"+DeltaJ+" <?php echo __("jour");?>";}
        if(DeltaJ>1){DeltaText="<?php echo __('En mémoire pour un peu plus de ');?>"+DeltaJ+" <?php echo __("jours");?>";}
        if(DeltaS==1){DeltaText="<?php echo __('En mémoire pour un peu plus de ');?>"+DeltaS+" <?php echo __("semaine");?>";}
        if(DeltaS>1){DeltaText="<?php echo __('En mémoire pour un peu plus de ');?>"+DeltaS+" <?php echo __("semaines");?>";}
        if(DeltaM!=0){DeltaText="<?php echo __('En mémoire pour un peu plus de ');?>"+DeltaM+" <?php echo __("mois");?>";}
        if(DeltaY!=0){DeltaText="<?php echo __('En mémoire pour un peu plus de ');?>"+DeltaY+" <?php echo __("ans");?>";}

        $("#duree").html("<img src='img/sablier.png' width='50px'>"+DeltaText);
        //$("#duree").show();
      });
      if(id_a_travailler_restant.length==0){fin();}
    }
    else
    {
      play_audio_fail();
      $(".mot_"+mot.id).removeClass('goodLetter');
    }
}
</script>
