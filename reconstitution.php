<script>
jQuery.fn.highlight = function () {
    return this.each(function () {
        $(this).contents().filter(function() {
            return this.nodeType == 3;// && regex.test(this.nodeValue);
        }).replaceWith(function() {
            //var nodeValueArray=(this.nodeValue || "").split(" ");
           return "<span class='word_item'>"+(this.nodeValue || "").replace(/([\.| |,|:|;|!|\?|-|_]+)/gm,"</span>$1<span class='word_item'>")+"</span>";
        });
    });
};
var WordList=[];
var ReverseWordList=[];
function PlayReconstitution(){
  $(".buttonRetourList").hide();
  $(".buttonRetourCards").show();
  $("#game_container").removeClass('shift250');
  $("#navRight").hide();
  history.pushState("", '', '/cards.php?deck_id='+deck_id+'&game=reconstitution');
  $("#game_container").html('');
  $("#game_container").append('<div id="consigne"><?php echo __("Ecouter le dialogue puis reconstruisez-le en cliquant sur le microphone");?></div><br><select class="mode"><option value="mots"><?php echo __("mots");?></option><option value="phrases"><?php echo __("phrases");?></option></select><button onclick="resetDialog();"><?php echo __("Remise à zéro");?></button>');
  $("#game_container").append('<div id="question"></div>');
  $("#game_container").append('<div id="reponse"></div>');
  if(GContent.youtube_id!=""){$('#reponse').html('<div style="width: 100%;max-width: 640px;display:inline-block;"><div class="video-container"><iframe class="GlobalContent" allowFullScreen="allowFullScreen" src="https://www.youtube.com/embed/'+GContent.youtube_id+'" width="640" height="352" frameborder="0"></iframe></div></div><br>');}
  if(GContent.hasAudio==1){cacheBreaker=new Date().getTime();$('#reponse').append("<audio class='GlobalContent' controls='controls'><source src='deck_audio/deck_"+deck_id+".wav'></source></audio><br>");}
  dialog=GContent.texte;
  dialog="<span>"+dialog+"</span>";
  dialog=dialog.replace(new RegExp("<a","g"),"<a target='_blank'");
  //dialog='<span>'+dialog.replace(/([!]){1}/g,"</span><span class='dialog_item'>$1</span><span>")+"</span>";
  //dialog=dialog.replace(\[\.]\g,"<span class='dialog_item'>.</span>");
  //dialog=dialog.replace(\[!]\g,"<span class='dialog_item'>!</span>");
  //dialog=dialog.replace(\[\?]\g,"<span class='dialog_item'>?</span>");
  //dialog=dialog.replace(\[:]\g,"<span class='dialog_item'>:</span>");
/*dialog=dialog.replace(new RegExp("\-", 'g'),"<span class='dialog_item'>-</span>");
  dialog=dialog.replace(new RegExp("\_", 'g'),"<span class='dialog_item'>_</span>");
  dialog=dialog.replace(new RegExp("\;", 'g'),"<span class='dialog_item'>;</span>");
*/
  dialog=dialog.replace(/[’]+/g,"'");
  $("#question").html("<div class='dialog'>"+dialog+"</div>");
  //$(".dialog *:not(a *)").css("color","lightgrey");
  $(".dialog *").highlight();
  $(".word_item").each(function(index){$(this).addClass("word_"+index).attr("id","word_"+index);if(!(ReverseWordList[$(this).text()]!=undefined)){ReverseWordList[$(this).text()]=[];}ReverseWordList[$(this).text()].push(index);WordList[index]=$(this).text();});

  if(readCookie('dialog_'+deck_id)!=null){
    ArrIdHighlight=JSON.parse(readCookie('dialog_'+deck_id));
    for(k in ArrIdHighlight){
      $("#"+ArrIdHighlight[k]).addClass("highlight");
    }
  }

  console.log(WordList,ReverseWordList);
  $("#reponse").after('<div id="microphoneVal" style="background-color:var(--mycolor2);padding:10px;width:100px;left:0;"><div id="waves" style="display:none;width:70px;">'
  +'<div style="animation-delay: -350ms;" class="wave"></div>'
  +'<div style="animation-delay: -400ms;" class="wave"></div>'
  +'<div style="animation-delay: -500ms;" class="wave"></div>'
  +'<div style="animation-delay: -200ms;" class="wave"></div>'
  +'<div style="animation-delay: -300ms;" class="wave"></div>'
  +'<div class="wave"></div>'
  +'</div><div id="recorderRec"><img src="img/micro.png" width="30px"></div></div><input type="text" class="transcriptEleve" onkeypress="checkAndReveal(this.value);" style="text-align:center;padding:20px 0;color:grey;display:block;width:100%;margin:10px 0;"></div>');
  $("#microphoneVal").before('<div class="feedBackFound" style="font-size:1.5em;">0%</div>');
  //event
  $("#recorderRec").off();
  //$("body").on('keypress',function(e){if(e.which==32){startMicVal(repCloze,card_id);}});
  $("#recorderRec").on('click',function(){startMic("reconstitution","",0);});
}
function resetDialog(){
  eraseCookie('dialog_'+deck_id);
  $(".highlight").removeClass("highlight");
  $(".feedBackFound").html("0%");
}
</script>
