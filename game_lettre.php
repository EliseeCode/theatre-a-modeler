<script>
function ini_lettre()
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
$("#game_container").append('<div id="question_order"></div><div id="definition"></div>');
$("#game_container").append('<div id="bonne_reponse_order" style="display:none;">Bonne Réponse</div>');
$("#game_container").append('<input type="text" id="input_order" value=""><br>');
$("#game_container").append('<div id="lettre_container"></div><br>');
$("#game_container").append('<div class="next" title="Passer à la carte suivante"></div>');
$("#game_container").append('<div id="mots_restants"></div>');
$(".consigne").html("Remettre les lettres dans l'ordre");
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
  {id_a_travailler_restant[i]=selected_card[i];}
  pile_glissante=id_a_travailler_restant.slice(0, 8);
  id_a_travailler_restant=id_a_travailler_restant.slice(pile_glissante.length, id_a_travailler_restant.length);
  next_lettre(-1);
}
}



function afficher_lettre(card_id)
{

    mot=cardsById[card_id].mot.trim();
    mot_trad=cardsById[card_id].mot_trad;
    hasImage=cardsById[card_id]["hasImage"];
    hasAudio=cardsById[card_id]["hasAudio"];
    phrase=cardsById[card_id]["phrase"];
    phrase_trad=cardsById[card_id]["phrase_trad"];

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
  if(hasImage==1){
  $("#card_"+card_id+" > .flipper > .front").css("background-image","url(card_img/card_"+card_id+".png)");
  }
  else {
  $("#card_"+card_id+" > .flipper > .front").css("background-image","url(img/default_card.png)");
  }
  $("#input_order").prop("readonly", true);
  $('#input_order').removeClass("good_rep");
  $("#lettre_container").slideDown('slow');
  $("#input_order").show('');
  $('#input_order').val('');
  $("#lettre_container").html('');
  $(".next").html('Suivant');
  $(".next").on("click",function(){next_lettre(card_id);});
  //premiere lettre
  lettre_html='<div class="lettre" id='+0+' style="font-size: 1.8em;">'+mot[0]+'</div>';
  $("#lettre_container").append(lettre_html);
  //les autres lettres
  for (var i = 1; i < mot.length; i++) {
  //$('.reponse').val($('.reponse').text()+'-');
  lettre_html='<div class="lettre" id='+i+' style="font-size: 2em;">'+mot[i]+'</div>';
  var alea=Math.floor(Math.random()*i);
  if(Math.random()*2<1){$("#"+alea).after(lettre_html);}
  else{$("#"+alea).before(lettre_html);}
  }
  var rg_lettre_a_trouver=0;
  //init variable nouvelles question.
  var fausse_lettre=0;
  $('#input_order').focus();
  //-------------------------------------------GESTION CLICK SUR CARTE/LETTRE APPARENT
  $(".lettre").off();
  $(".lettre").on("click", function() {
    var id_lettre=$(this).attr('id');
    if(mot[id_lettre].toLowerCase()==mot[rg_lettre_a_trouver].toLowerCase())//SI LA LETTRE TAPE EST BONNE
      {
          bonne_lettre(card_id,id_lettre,"lettre",rg_lettre_a_trouver);
          rg_lettre_a_trouver++;
      }
    else//SI LA LETTRE EST MAUVAISE
      {
        $("#"+id_lettre).addClass('bad');
        $("#"+id_lettre).on('animationend', function(e) {
        $("#"+id_lettre).removeClass("bad");
        });
          fausse_lettre++;
      }
      if(fausse_lettre>=3)
        {
        $('.lettre').removeClass('next_letter');
        $('#'+rg_lettre_a_trouver).addClass('next_letter');
        }
      $('#input_order').focus();
});
$("#input_order").off();


$("#input_order").on("keypress",function(e){//si on a tapé une lettre au clavier
lettre_tape=String.fromCharCode(e.which);
//current_reponse=$("#input_order").val();
//Si la touche ne change pas le contenu de la reponse
//if(current_reponse.length==rg_lettre_a_trouver){return;}
//lettre_tape=current_reponse.substr(-1, 1);
console.log("lettre tapé : "+lettre_tape);
if(lettre_lt(lettre_tape).toLowerCase()==lettre_lt(mot[rg_lettre_a_trouver]).toLowerCase())//SI LA LETTRE TAPE EST BONNE
    {
      id_lettre=$(".lettre:contains('"+mot[rg_lettre_a_trouver]+"'):not(.good):first").attr('id');;
      //$("#"+id_lettre).addClass('good');
      bonne_lettre(card_id,id_lettre,"clavier",rg_lettre_a_trouver);
      rg_lettre_a_trouver++;
    }
else//SI LA LETTRE EST MAUVAISE
    {
      $('#input_order').addClass('bad');
      $('#input_order').on('animationend', function(e) {
      $('#input_order').removeClass("bad");
      });
        fausse_lettre++;
    }
    if(fausse_lettre>=3)
      {
      $('.lettre').removeClass('next_letter');
      $('#'+rg_lettre_a_trouver).addClass('next_letter');
      }

});
}

function bonne_lettre(card_id,id_lettre,type,rg_lettre)
{
  console.log(rg_lettre);
  //on écrit le mot correctement
  $('#input_order').val('');
    for (var i = 0; i < mot.length; i++) {
    if(i<rg_lettre+1){$('#input_order').val($('#input_order').val()+mot[i]);}
    }

  rg_lettre++;//on augmente le nombre de lettre d'1
  $("#"+id_lettre).addClass('good');//la lettre devient verte
    if(mot.length==rg_lettre)
    {//SI LE MOT EST TROUVE
      $("#input_order").prop("readonly", true).addClass('good_rep');
      $(".card").addClass("selected");
      play_audio(card_id);
      if(fausse_lettre==0)//si pas de fausse lettre
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
      $("#lettre_container").slideUp('slow');
      //$("#input_order").slideUp('slow');
      $("#input_order").val('');
      $("#input_order").hide("slow");
      console.log("mot trouvé");
      setTimeout(function(){next_lettre(card_id);},2500);
    }
}


function next_lettre(card_id)
{
console.log('new card'+pile_glissante);

len=selected_card_done.length;
nbre_mot_total=selected_card.length;
if(len==nbre_mot_total){console.log("fin order");fin_lettre();}
else{
card_id_tmp = rand_parmi(pile_glissante);
test_boucle=0;
while(card_id_tmp==card_id && test_boucle<50)//ici id_a_trouver est l'id a trouver de la question precedente
{card_id_tmp=rand_parmi(pile_glissante);
test_boucle++;}
card_id=card_id_tmp;
console.log("la nouvelle carte est "+card_id);
if(len!=nbre_mot_total){afficher_lettre(card_id);}
}
}

function fin_lettre()
{
  $("#microphone").html("");
  $("#question_order").html("<img src='img/check2.png' width='200px'>");
  $("#definition").html("");
  $("body").on("click", function(){ini_memory();})
  $("#reponse_order").append("");
  $('.consigne').html('Bravo ! Continue à jouer ou valide ses mots.');
}
</script>
