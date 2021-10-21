<script>
function ini_validation()
{
  $(".buttonRetourList").hide();
  $(".buttonRetourCards").show();
  $("#game_container").removeClass('shift250');
  $("#navRight").hide();

  if(selected_card.length==0){selectAll();}
  if(selected_card.length==0){alert("Vous connaissez déjà toutes les cartes. Revenez plus tard.");}
  else{
$("body").off();
$(".onglet").removeClass('active_onglet');
$(".onglet_validation").addClass('active_onglet');
$(".btnBack").remove();
$(".footerFixed").prepend("<div class='btnBack' onclick='ini_memory();'>Retour</div>");
$("#game_container").html('');
$("#game_container").append('<div id="question_order"></div><div id="definition"></div><div id="duree"></div>');
$("#game_container").append('<div id="bonne_reponse_order" style="display:none;">Bonne Réponse</div>');
$("#game_container").append('<div id="sentenceQuestion"></div><br><div class="boutton_validation_rep">Envoyer</div><br>');
$("#game_container").append('<div id="SpeechResult"></div>');
$("#game_container").append('<div class="skip">Corriger, j\'avais juste.</div>');
$("#game_container").append('<div class="next">Suivant</div>');
$("#game_container").append('<div id="mots_restants"></div>');
$(".consigne").html("Ecrire le mot !");
$('body').on('click',function(){$('#input_order').focus();});
$('#SpeechResult').html("");
selected_card_done=[];
selected_card_validated=[];
update_mots_restant();
  id_a_travailler_restant=new Array();
  for(i in selected_card)
  {id_a_travailler_restant[i]=selected_card[i];}
  pile_glissante=id_a_travailler_restant.slice(0, 8);
  id_a_travailler_restant=id_a_travailler_restant.slice(pile_glissante.length, id_a_travailler_restant.length);
  next_validation(-1);
    }
}

function afficher_validation(card_id)
{

    mot=cardsById[card_id].mot.trim();
    mot_trad=cardsById[card_id].mot_trad;
    hasImage=cardsById[card_id]["hasImage"];
    hasAudio=cardsById[card_id]["hasAudio"];
    phrase=cardsById[card_id]["phrase"];
    phrase_trad=cardsById[card_id]["phrase_trad"];
    sentences=cardsById[card_id]["sentences"];
    sentence=rand_parmi(sentences);
    questionCloze='<input type="text" autocomplete="off" id="input_order" value="" class="">';
    repCloze=mot;
    if(sentence)
    {
      nbreEtoile=0;
      for(k in sentence)
  	   {if(sentence[k]=="*"){nbreEtoile++;}}
    	if(nbreEtoile==1){sentence+="*";}
      if(nbreEtoile==0){sentence+="<br>*"+mot+"*";}
    	repCloze=sentence.match(/\*(.*?)\*/)[0];
      repCloze=repCloze.replace('*','');
      repCloze=repCloze.replace('*','');
    	questionCloze=sentence.replace("*"+repCloze+"*",'<input type="text" autocomplete="off" id="input_order" value="" class="">',1);
    }


  html_fliping_card='<div id="card_'+card_id+'" class="card flip-container">'
  +'<div class="flipper">'
  +'<div class="front">';
  if(mot_trad!=""){html_fliping_card+='<span class="mot_trad_card">'+mot_trad+'</span>';}
  html_fliping_card+='</div>'
  +'<div class="back"><span class="mot_card">'+mot+'</span>'
  +'<div class="icons_card_container"><a target="_blank" class="icon_back google" href="http://www.google.com?q='+mot+'"></a>'
  +'<div class="state"></div>';
  if(hasAudio==1){html_fliping_card+='<div class="icon_back icon_audio" onclick="play_audio('+card_id+')"></div>';}
  if(phrase!=""){html_fliping_card+='<div class="icon_back icon_phrase" title="'+phrase+'\r\n'+phrase_trad+'"></div>';}
  html_fliping_card+='</div>'//icon_card container
  +'</div>'//back
  +'</div>'//flipper
  +'</div>';//card_#
  $("#question_order").html(html_fliping_card);
  $("#definition").html(phrase);
  $("#duree").hide();
  $("#sentenceQuestion").slideDown();
  $("#sentenceQuestion").html(questionCloze);
  $("#input_order").after('<div id="microphoneVal"><div id="waves" style="display:none;width:70px;">'
  +'<div style="animation-delay: -350ms;" class="wave"></div>'
  +'<div style="animation-delay: -400ms;" class="wave"></div>'
  +'<div style="animation-delay: -500ms;" class="wave"></div>'
  +'<div style="animation-delay: -200ms;" class="wave"></div>'
  +'<div style="animation-delay: -300ms;" class="wave"></div>'
  +'<div class="wave"></div>'
  +'</div><div id="recorderVal"><img src="img/micro.png" width="30px"></div></div>');

  $(".boutton_validation_rep").slideDown();
  $(".skip").show();
  $(".skip").html("Je donne ma langue au chat");
  $(".skip").off();
  $(".skip").on("click",function(){
    console.log("skipClicked1");
    $(".card").addClass('recto');
    $(".next").fadeIn();
    $(".skip").hide();
    $("body").off();
    $("body").on("keyup",function(e){
      var code = e.keyCode || e.which;
    if(code == 13) { next_validation(card_id);$(".rewardpage").hide();}
    });
    $("#sentenceQuestion").slideUp();
    $(".boutton_validation_rep").slideUp();
    });
  //$(".next").hide();
  $(".next").off();
  $(".next").on("click",function(){next_validation(card_id);});
  $("#input_order").focus();

  $(".boutton_validation_rep").off();
  $(".boutton_validation_rep").on('click',function(){
    envoi_mot(card_id,repCloze);
  });
  $("body").off();
  console.log("body keyup13");
  $("body").on("keyup",function(e){
    var code = e.keyCode || e.which;
 if(code == 13) { envoi_mot(card_id,mot);}
  });
  if(hasImage==1){
  $("#card_"+card_id+" > .flipper > .front").css("background-image","url(card_img/card_"+card_id+".png)");
  }
  else {
  $("#card_"+card_id+" > .flipper > .front").css("background-image","url(img/default_card.png)");
  }
  $('#SpeechResult').removeClass("good_rep_audio");
  $("#SpeechResult").show('');
  $('#SpeechResult').html('');
  $("#recorderVal").off();
  //$("body").on('keypress',function(e){if(e.which==32){startMicVal(repCloze,card_id);}});
  $("#recorderVal").on('click',function(){startMicVal(repCloze,card_id);});

  $("#input_order").prop("readonly", false);
  $('#input_order').removeClass("good_rep");
  //$("#lettre_container").slideDown('slow');
  $("#sentenceQuestion").show();
  $('#input_order').val('');
  $("#lettre_container").html('');
  $('#input_order').focus();
}
function envoi_mot(card_id,mot)
{
  console.log('envoi_mot('+card_id+','+mot+')');
  rep=$("#input_order").val();
  if(rep.toLowerCase()==mot.toLowerCase())
  {bonne_validation(card_id);
    $(".skip").hide();
    $("#sentenceQuestion").slideUp();
    $(".boutton_validation_rep").slideUp();
  }
  else {
    $("#input_order").addClass('bad');
    $("#input_order").on('animationend', function(e) {
    $("#input_order").removeClass("bad");
    });
    //skip est : je donne ma langue au chat
    $(".skip").fadeIn("slow");
    $(".skip").off();
    $(".skip").on("click",function(){
      console.log("skipClicked2");
      $(".card").addClass('recto');
      $("#input_order").val(mot).addClass("good_rep");
      $(".next").fadeIn();
      //$(".skip").hide();
      $("body").off();
      $("body").on("keyup",function(e){
        var code = e.keyCode || e.which;
      if(code == 13){next_validation(card_id);$(".rewardpage").hide();}
      });
      if(rep.length>=3){
        //$(".skip").html("Ma réponse est correcte aussi");
        $(".skip").html("<div style='zoom:1.4;position:relative;top:-40px;'><div>Notez votre maîtrise de cette cartes : </div><div onmouseover='startOver(1);' onclick='star(1,"+card_id+");' class='starIcon star1 star2 star3 star4 star5'></div><div onmouseover='startOver(2);' onclick='star(2,"+card_id+");' class='starIcon star1 star2 star3 star4'></div><div onmouseover='startOver(3);' onclick='star(3,"+card_id+");' class='starIcon star1 star2 star3'></div><div onmouseover='startOver(4);' onclick='star(4,"+card_id+");' class='starIcon star1 star2'></div><div onmouseover='startOver(5);' onclick='star(5,"+card_id+");' class='starIcon star1 starEnd'></div><div class='affichageStar'>Je le savais presque.</div>");
      }
      else {
        $(".skip").hide();
        star(1,card_id);
      }
      //$("#input_order").slideUp();
      $(".boutton_validation_rep").slideUp();
      });
    }
    console.log("faux");
    $(".next").fadeIn("slow");

}
function startOver(nbreStar)
{
  $(".star1").css("background-image","url('img/starVide.png')");
  if(nbreStar==1){$(".star5").css("background-image","url('img/star.png')");$(".affichageStar").html("J'avais complètement oublié cette carte.");}
  if(nbreStar==2){$(".star4").css("background-image","url('img/star.png')");$(".affichageStar").html("Je l'avais oublié.");}
  if(nbreStar==3){$(".star3").css("background-image","url('img/star.png')");$(".affichageStar").html("Ha oui, ça me dit quelquechose.");}
  if(nbreStar==4){$(".star2").css("background-image","url('img/star.png')");$(".affichageStar").html("Je le savais presque.");}
  if(nbreStar==5){$(".star1").css("background-image","url('img/star.png')");$(".affichageStar").html("Je le savais.");}
}
function star(nbreStar,card_id)
{
  if(nbreStar==5){
    $(".skip").html("Attendez ...");
    setTimeout(function(){$(".skip").html(5);},500);
    setTimeout(function(){$(".skip").html(4);},1500);
    setTimeout(function(){$(".skip").html(3);},2500);
    setTimeout(function(){$(".skip").html(2);},3500);
    setTimeout(function(){$(".skip").html(1);},4500);
    setTimeout(function(){bonne_validation(card_id);AddCorrection();},5500);
  }else {
    $.getJSON("ajax.php?action=cardSetNewDelta&star="+star+"&card_id="+card_id+"&user_id="+user_id, function(result){
      next_validation(card_id);
    });
  }
}
function AddCorrection(){
  $.getJSON("ajax.php?action=AddCorrection&user_id="+user_id, function(result){});
}
function bonne_validation(card_id)
{console.log("bonne validation"+ card_id);

$("body").off();
$("body").on("keyup",function(e){
  var code = e.keyCode || e.which;
if(code == 13) { next_validation(card_id);$(".rewardpage").hide();}
});
      $('#SpeechResult').addClass('good_rep_audio');
      $("#input_order").prop("readonly", true).addClass('good_rep');
      $(".card").addClass("recto");
      play_audio(card_id);
      $(".skip").hide();
      $(".next").show();
      $("#input_order").addClass('good_rep');
      $("#sentenceQuestion").slideUp();
      $(".boutton_validation_rep").slideUp();

          removeElem(card_id,pile_glissante);
          selected_card_done.push(card_id);
          selected_card_validated.push(card_id);
          update_mots_restant();

        //s'il reste des id dans la grande pile, on en prend une au hasard que l'on met dans la pile glissante
        if(id_a_travailler_restant.length!=0)
          {
          id_rand_grande_pile = rand_parmi(id_a_travailler_restant);
          pile_glissante.push(id_rand_grande_pile);
          removeElem(id_rand_grande_pile,id_a_travailler_restant);
          }

          $.getJSON("ajax.php?action=addCoins&card_id="+card_id+"&reward_type=validation", function(result){
            console.log(result);
            if(result.status=="ok"){
              $('.nbreCoins').html(result.nbreCoins);
              for(k=0;k<result.coins2add;k++){
                delay=100*k;
                $('.nbreCoins').parent().append('<div class="animatedCoin" style="animation-delay:'+delay+'ms;"><img src="img/golden_coin.png" width="20px"></div>');
              }
              $(".animatedCoin").on("animationend",function(){$(this).remove();});
            }
          });

          $.getJSON("ajax.php?action=cardLearned&card_id="+card_id+"&user_id="+user_id, function(result){
            console.log("card "+card_id+" by "+user_id);
            console.log(result);
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
            if(DeltaJ==1){DeltaText="En mémoire pendant un peu plus de "+DeltaJ+" jour";}
            if(DeltaJ>1){DeltaText="En mémoire pendant un peu plus de "+DeltaJ+" jours";}
            if(DeltaS==1){DeltaText="En mémoire pendant un peu plus de "+DeltaS+" semaine";}
            if(DeltaS>1){DeltaText="En mémoire pendant un peu plus de "+DeltaS+" semaines";}
            if(DeltaM!=0){DeltaText="En mémoire pendant un peu plus de "+DeltaM+" mois";}
            if(DeltaY!=0){DeltaText="En mémoire pendant un peu plus de "+DeltaY+" ans";}

            $("#duree").html("<img src='img/sablier.png' width='50px'>"+DeltaText);
            $("#duree").show();

            if(result.Newtrophy.length>0)
            {showReward(result.Newtrophy);}
          });
      console.log("mot trouvé");
}



function next_validation(card_id)
{
  console.log('new card'+pile_glissante);

  len=selected_card_done.length;
  nbre_mot_total=selected_card.length;
  if(len==nbre_mot_total){fin_validation();}
  else{
  card_id_tmp = rand_parmi(pile_glissante);
  test_boucle=0;
  while(card_id_tmp==card_id && test_boucle<50)//ici id_a_trouver est l'id a trouver de la question precedente
  {card_id_tmp=rand_parmi(pile_glissante);
  test_boucle++;}
  card_id=card_id_tmp;
  console.log("la nouvelle carte est "+card_id);
  if(len!=nbre_mot_total){afficher_validation(card_id);}
  }

}

function fin_validation()
{
  selected_card=[];
  $("#question_order").html("<img src='img/check2.png' width='300px'>");
  $("#definition").hide();
  $("#duree").hide();
  $("body").on("click", function(){location.reload();})
  $("#reponse_order").append("");
  $('.consigne').html('Bravo !');
  //updateStats();
}

function updateStats()
{
//$.getJSON("ajax.php?action=updateStats&user_id="+user_id, function(result){console.log(result);});
}


var SpeechRecognition = SpeechRecognition || webkitSpeechRecognition;
var SpeechGrammarList = SpeechGrammarList || webkitSpeechGrammarList;
var SpeechRecognitionEvent = SpeechRecognitionEvent || webkitSpeechRecognitionEvent;


function startMicVal(mot,card_id)
{
$("#recorderVal").hide();
$("#waves").fadeIn('slow');
console.log("mot mic Val :"+mot);
var grammar = '#JSGF V1.0; grammar phrase; public <phrase> = ' + cardsById[card_id]["mot"] +';';
var recognition = new SpeechRecognition();
var speechRecognitionList = new SpeechGrammarList();
speechRecognitionList.addFromString(grammar, 1);
recognition.grammars = speechRecognitionList;
recognition.lang = 'fr-FR';
recognition.interimResults = false;
recognition.maxAlternatives = 1;
recognition.start();

recognition.onresult = function(event) {
  $("#recorderVal").fadeIn("slow");
  $("#waves").hide();
  var speechResult = event.results[0][0].transcript;
  console.log('speechResult:',speechResult);
  $("#SpeechResult").html(speechResult);
  /*mot_modif=mot.replace("un ","");
  mot_modif=mot_modif.replace("une ","");
  mot_modif=mot_modif.replace("le ","");
  mot_modif=mot_modif.replace("la ","");
  mot_modif=mot_modif.replace("les ","");*/


  //if(speechResult.replace(mot_modif.toLowerCase(),"") == speechResult)
  if(speechResult.toLowerCase().replace(mot.toLowerCase(),"") == speechResult.toLowerCase())
  {
    console.log("faux");
  $('#SpeechResult').addClass('bad_rep_audio');
  $(".skip").fadeIn("slow");
  $(".next").fadeIn("slow");
  $('#SpeechResult').on('animationend', function(e) {
  $('#SpeechResult').removeClass("bad_rep_audio");
});
  }
  else
  {
    bonne_validation(card_id);
  }
//$("#microphone").html(parseInt(event.results[0][0].confidence*100)+"%");
}

recognition.onspeechend = function() {
  recognition.stop();
  $("#recorderVal").fadeIn("slow");
  $("#waves").hide();
}

recognition.onerror = function(event) {
  $("#recorderVal").fadeIn("slow");
  $("#waves").hide();
  console.log('error : '+event.error);
  //diagnosticPara.textContent = 'Error occurred in recognition: ' + event.error;
}

recognition.onaudiostart = function(event) {
    //Fired when the user agent has started to capture audio.
    console.log('SpeechRecognition.onaudiostart');
}

recognition.onaudioend = function(event) {
    //Fired when the user agent has finished capturing audio.
    console.log('SpeechRecognition.onaudioend');
}

recognition.onend = function(event) {
    //Fired when the speech recognition service has disconnected.
    console.log('SpeechRecognition.onend');
}

recognition.onnomatch = function(event) {
    //Fired when the speech recognition service returns a final result with no significant recognition. This may involve some degree of recognition, which doesn't meet or exceed the confidence threshold.
    console.log('SpeechRecognition.onnomatch');
}

recognition.onsoundstart = function(event) {
    //Fired when any sound — recognisable speech or not — has been detected.
    console.log('SpeechRecognition.onsoundstart');
}

recognition.onsoundend = function(event) {
    //Fired when any sound — recognisable speech or not — has stopped being detected.
    console.log('SpeechRecognition.onsoundend');
}

recognition.onspeechstart = function (event) {
    //Fired when sound that is recognised by the speech recognition service as speech has been detected.
    console.log('SpeechRecognition.onspeechstart');
}
recognition.onstart = function(event) {
    //Fired when the speech recognition service has begun listening to incoming audio with intent to recognize grammars associated with the current SpeechRecognition.
    console.log('SpeechRecognition.onstart');
}
}
</script>
