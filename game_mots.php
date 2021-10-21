<script>
function ini_mot()
{
  $(".buttonRetourList").hide();
  $(".buttonRetourCards").show();
  $("#game_container").removeClass('shift250');
  $("#navRight").hide();

  if(selected_card.length==0){selectAll();}
  if(selected_card.length==0){alert("Vous connaissez déjà toutes les cartes. Revenez plus tard.");}
else{
  $(".onglet").removeClass('active_onglet');
  $(".onglet_jeux").addClass('active_onglet');
$("body").off();
$(".btnBack").remove();
$(".footerFixed").prepend("<div class='btnBack' onclick='ini_memory();'>Retour</div>");
$("#game_container").html('');
$("#game_container").append('<div id="question_order"></div>');
$("#game_container").append('<div id="reponse_phrase"></div><br>');
$("#game_container").append('<div id="mot_container"></div><br>');
$("#game_container").append('<div class="next" title="Passer à la carte suivante"></div>');
$("#game_container").append('<div id="mots_restants"></div>');
$(".consigne").html("Remettre les éléments de la phrase dans l'ordre");
$(".precedent").off();
$(".precedent").on("click",function(){init_memory();});
$('body').on('click',function(){$('#input_order').focus();});
$('#input_order').on('focus click',function(){
var v=$(this).val();
$(this).val('').val(v);
});

selected_card_done=[];

  update_mots_restant();
  id_a_travailler_restant=new Array();
  for(i in selected_card)
  {
    if(cardsById[selected_card[i]].sentences.length!=0){id_a_travailler_restant[i]=selected_card[i];}else{selected_card_done.push(selected_card[i]);}
  }

  pile_glissante=id_a_travailler_restant.slice(0, 8);
  id_a_travailler_restant=id_a_travailler_restant.slice(pile_glissante.length, id_a_travailler_restant.length);
  next_mot(-1);
}
}



function afficher_mot(card_id)
{

    mot=cardsById[card_id].mot.trim();
    mot_trad=cardsById[card_id].mot_trad;
    hasImage=cardsById[card_id]["hasImage"];
    hasAudio=cardsById[card_id]["hasAudio"];
    phrase=cardsById[card_id]["phrase"];
    phrase_trad=cardsById[card_id]["phrase_trad"];
    sentences=cardsById[card_id]["sentences"];
    sentence=rand_parmi(sentences);

  html_fliping_card='<div id="card_'+card_id+'" class="card flip-container">'
  +'<div class="flipper">'
  +'<div class="front">';
  if(mot_trad!=""){html_fliping_card+='<span class="mot_trad_card">'+mot_trad+'</span>';}
  html_fliping_card+='</div>'
  +'<div class="back"><span class="mot_card">'+mot+'</span>'
  +'<div class="icons_card_container"><a target="_blank" class="icon_back google" href="http://www.google.com?q='+mot+'"></a>'
  +'<div class="state"></div>';
  if(hasAudio==1){html_fliping_card+='<div class="icon_back icon_audio" onclick="play_audio('+card_id+')"></div>';}
  //if(phrase!=""){html_fliping_card+='<div class="icon_back icon_phrase" title="'+phrase+'\r\n'+phrase_trad+'"></div>';}
  html_fliping_card+='</div>'//icon_card container
  +'</div>'//back
  +'</div>'//flipper
  +'</div>';//card_#
  $("#question_order").html(html_fliping_card);
  $("#reponse_phrase").html("");
  if(hasImage==1){
  $("#card_"+card_id+" > .flipper > .front").css("background-image","url(card_img/card_"+card_id+".png)");
  }
  else {
  $("#card_"+card_id+" > .flipper > .front").css("background-image","url(img/default_card.png)");
  }

  $('#reponse_phrase').removeClass("good_rep");
  $("#mot_container").slideDown('slow');
  $("#reponse_phrase").show('');
  $("#mot_container").html('');
  $(".next").html('Suivant');
  $(".next").on("click",function(){next_mot(card_id);});
  //premiere lettre
  sentence=sentence.replace("*","").replace("*","")
  list_mots=sentence.split(",").join(" ,").split(".").join(" .").split(" ");

  console.log(list_mots);
  lettre_html='<div class="mot_item" id='+0+' style="font-size: 1.8em;">'+list_mots[0]+'</div>';
  $("#mot_container").append(lettre_html);
  //les autres lettres
  for (var i = 1; i < list_mots.length; i++) {
  //$('.reponse').val($('.reponse').text()+'-');
  mot_html='<div class="mot_item" id='+i+' style="font-size: 2em;">'+list_mots[i]+'</div>';
  var alea=Math.floor(Math.random()*i);
  if(Math.random()*2<1){$("#"+alea).after(mot_html);}
  else{$("#"+alea).before(mot_html);}
  }
  var rg_mot_a_trouver=0;
  //init variable nouvelles question.
  var faux_mot=0;

  //-------------------------------------------GESTION CLICK SUR CARTE/LETTRE APPARENT
  $(".mot_item").off();
  $(".mot_item").on("click", function() {
    var id_mot=$(this).attr('id');
    if(list_mots[id_mot]==list_mots[rg_mot_a_trouver])//SI LA LETTRE TAPE EST BONNE
      {
        console.log("bon mot "+list_mots[id_mot]+" "+list_mots[rg_mot_a_trouver]);
          bon_mot(card_id,id_mot,"mot",rg_mot_a_trouver);
          rg_mot_a_trouver++;
      }
    else//SI LA LETTRE EST MAUVAISE
      {
        console.log("mauvais mot "+list_mots[id_mot]+" "+list_mots[rg_mot_a_trouver]);
        $("#"+id_mot).addClass('bad');
        $("#"+id_mot).on('animationend', function(e) {
        $("#"+id_mot).removeClass("bad");
        });
          faux_mot++;
      }
      if(faux_mot>=3)
        {
        $('.mot_item').removeClass('next_letter');
        $('#'+rg_mot_a_trouver).addClass('next_letter');
        }

});
$("#reponse_phrase").off();


}

function bon_mot(card_id,id_mot,type,rg_mot)
{
  console.log(rg_mot);
  //on écrit le mot correctement
  $('#reponse_phrase').html('');
    for (var i = 0; i < list_mots.length; i++) {
    if(i<rg_mot+1){$('#reponse_phrase').text($('#reponse_phrase').text()+list_mots[i]+" ");}
    }

  rg_mot++;//on augmente le nombre de lettre d'1
  $("#"+id_mot).addClass('good');//la lettre devient verte
    if(list_mots.length<=rg_mot)
    {//SI LE MOT EST TROUVE
      $("#reponse_phrase").prop("readonly", true).addClass('good_rep');
      $(".card").addClass("selected");
      play_audio(card_id);
      if(faux_mot==0)//si pas de fausse lettre
      {
        console.log("remove :"+card_id+" from "+pile_glissante);
          removeElem(card_id,pile_glissante);
          //removeElem(card_id,selected_card);
          selected_card_done.push(card_id);
          update_mots_restant();
        //s'il reste des id dans la grande pile, on en prend une au hasard que l'on met dans la pile glissante
        if(id_a_travailler_restant.length!=0)
          {
          id_rand_grande_pile = rand_parmi(id_a_travailler_restant);
          pile_glissante.push(id_rand_grande_pile);
          removeElem(id_rand_grande_pile,id_a_travailler_restant);
          }

      }
      $("#mot_container").slideUp('slow');
      //$("#input_order").slideUp('slow');
      $("#reponse_phrase").html('');
      $("#reponse_phrase").hide("slow");
      console.log("mot trouvé");
      setTimeout(function(){next_mot(card_id);},2500);
    }
}


function next_mot(card_id)
{
console.log('new card'+pile_glissante);

len=selected_card_done.length;
nbre_mot_total=selected_card.length;
console.log(len+"=="+nbre_mot_total);
if(len==nbre_mot_total){console.log("fin order");fin_mot();}
else{
card_id_tmp = rand_parmi(pile_glissante);
test_boucle=0;
while(card_id_tmp==card_id && test_boucle<50)//ici id_a_trouver est l'id a trouver de la question precedente
{card_id_tmp=rand_parmi(pile_glissante);
test_boucle++;}
card_id=card_id_tmp;
console.log("la nouvelle carte est "+card_id);
if(len!=nbre_mot_total){afficher_mot(card_id);}
}
}

function fin_mot()
{
  $("#microphone").html("");
  $("#question_order").html("<img src='img/check2.png' width='200px'>");
  $("#definition").html("");
  $("body").on("click", function(){ini_memory();})
  $("#reponse_order").append("");
  $("#reponse_phrase").html('');
  $('.consigne').html('Bravo ! Continue à jouer ou valide ses mots.');
}
</script>
