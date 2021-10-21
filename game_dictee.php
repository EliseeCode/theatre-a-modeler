<script>
function ini_dictee()
{
  $(".buttonRetourList").hide();
  $(".buttonRetourCards").show();
  $("#game_container").removeClass('shift250');
  $("#navRight").hide();
  if(selected_card.length==0){selectAll();}
  if(selected_card.length==0){alert("Vous connaissez déjà toutes les cartes. Revenez plus tard.");}
  else{
$("body").off();
$("#actionsOnDeckContainer").hide();
$(".onglet").removeClass('active_onglet');
$(".onglet_quizAudio").addClass('active_onglet');
$(".btnBack").remove();
$(".footerFixed").prepend("<div class='btnBack' onclick='ini_memory();'>Retour</div>");
$("#game_container").html('');
$("#game_container").append('<div id="question_order"></div>');
$("#game_container").append('<input type="text" id="input_order" value=""><br><div class="boutton_dictee_rep">Envoyer</div><br>');
$("#game_container").append('<div class="skip">Corriger, j\'avais juste.</div>');
$("#game_container").append('<div class="next"></div>');
$("#game_container").append('<div id="mots_restants"></div>');
$(".consigne").html("Ecrire le mot !");
$('body').on('click',function(){$('#input_order').focus();});

selected_card_done=[];

update_mots_restant();
  id_a_travailler_restant=new Array();
  for(i in selected_card)
  {
    if(cardsById[selected_card[i]]["hasAudio"]==1)
    {id_a_travailler_restant[i]=selected_card[i];}
    else {
    {selected_card_done.push(selected_card[i]);}
    }
  }
  pile_glissante=id_a_travailler_restant.slice(0, 8);
  id_a_travailler_restant=id_a_travailler_restant.slice(pile_glissante.length, id_a_travailler_restant.length);
  next_dictee(-1);
    }
}

function afficher_dictee(card_id)
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
  if(mot_trad!=""){html_fliping_card+='';}
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
  play_audio(card_id);
  $("#question_order").html(html_fliping_card);
  $("#input_order").slideDown();
  $(".boutton_dictee_rep").slideDown();
  $(".next").html('Suivant');
  $(".skip").show();
  $(".skip").html("Je donne ma langue au chat");
  $(".skip").off();
  $(".skip").on("click",function(){
    $(".card").addClass('recto');
    $(".next").fadeIn();
    $(".skip").hide();
    $("#input_order").slideUp();
    $(".boutton_dictee_rep").slideUp();
    });
  //$(".next").hide();
  $(".next").off();
  $(".next").on("click",function(){next_dictee(card_id);});
  $("#input_order").focus();

  $(".boutton_dictee_rep").off();
  $(".boutton_dictee_rep").on('click',function(){
    envoi_mot_dictee(card_id,mot);
  });
  $("body").off();
  console.log("body keyup13");
  $("body").on("keyup",function(e){
    var code = e.keyCode || e.which;
 if(code == 13) { envoi_mot_dictee(card_id,mot);}
  });
  $("#card_"+card_id+" > .flipper > .front").css("background-image","url(img/haut_parleur_carte.png)");
  $("#card_"+card_id+" > .flipper > .front").off();
  $("#card_"+card_id+" > .flipper > .front").on("click",function(){play_audio(card_id);});
  if(hasImage>0){
  $("#card_"+card_id+" > .flipper > .back").css("background-image","url(card_img/card_"+hasImage+".png)");
  }
  else {
  $("#card_"+card_id+" > .flipper > .back").css("background-image","url(img/default_card.png)");
  }
  $("#input_order").prop("readonly", false);
  $('#input_order').removeClass("good_rep");
  //$("#lettre_container").slideDown('slow');
  $("#input_order").show();
  $('#input_order').val('');
  $("#lettre_container").html('');
  $('#input_order').focus();
}
function envoi_mot_dictee(card_id,mot)
{
  console.log('envoi_mot_dictee('+card_id+','+mot+')');
  rep=$("#input_order").val();
  if(rep.toLowerCase()==mot.toLowerCase())
  {bonne_dictee(card_id);
    $(".skip").hide();
    $("#input_order").slideUp();
    $(".boutton_dictee_rep").slideUp();
  }
  else {
    $("#input_order").addClass('bad');
    $("#input_order").on('animationend', function(e) {
    $("#input_order").removeClass("bad");
    });
    $(".skip").fadeIn("slow");
    $(".skip").off();
    $(".skip").on("click",function(){
      $(".card").addClass('recto');
      $(".next").fadeIn();
      $(".skip").html("Ma réponse est correcte aussi");
      $(".skip").on("click",function(){
      bonne_dictee(card_id);});
      //$("#input_order").slideUp();
      $(".boutton_validation_rep").slideUp();
      });
    }
    console.log("faux");
    $(".next").fadeIn("slow");

}
function bonne_dictee(card_id)
{console.log("bonne dictee"+ card_id);

      $("#input_order").prop("readonly", true).addClass('good_rep');
      $(".card").addClass("recto");
      play_audio(card_id);
      $(".skip").hide();
      $(".next").show();
      $("#input_order").addClass('good_rep');
      $("#input_order").slideUp();
      $(".boutton_validation_rep").slideUp();

          removeElem(card_id,pile_glissante);
          selected_card_done.push(card_id);
          update_mots_restant();

        //s'il reste des id dans la grande pile, on en prend une au hasard que l'on met dans la pile glissante
        if(id_a_travailler_restant.length!=0)
          {
          id_rand_grande_pile = rand_parmi(id_a_travailler_restant);
          pile_glissante.push(id_rand_grande_pile);
          removeElem(id_rand_grande_pile,id_a_travailler_restant);
          }

      console.log("mot trouvé");
}



function next_dictee(card_id)
{
  console.log('new card'+pile_glissante);

  len=selected_card_done.length;
  nbre_mot_total=selected_card.length;
  if(len==nbre_mot_total){fin_dictee();}
  else{
  card_id_tmp = rand_parmi(pile_glissante);
  test_boucle=0;
  while(card_id_tmp==card_id && test_boucle<50)//ici id_a_trouver est l'id a trouver de la question precedente
  {card_id_tmp=rand_parmi(pile_glissante);
  test_boucle++;}
  card_id=card_id_tmp;
  console.log("la nouvelle carte est "+card_id);
  if(len!=nbre_mot_total){afficher_dictee(card_id);}
  }

}

function fin_dictee()
{
  $("#question_order").html("<img src='img/check2.png' width='300px'>");
  $("body").on("click", function(){ini_memory();})
  $("#reponse_order").append("");
  $('.consigne').html('');
  //updateStats();
}


</script>
