<script>
function afficherKnown(){
  if($("#cardKnown").is(':visible'))
  {
  $("#cardKnown").slideUp();
  $("#deroulant").html('Afficher les cartes connues');
  }
  else
  {
  $("#cardKnown").slideDown();
  $("#deroulant").html('Cacher les cartes connues');
  }
}
function init_memory()
{
  $("body").off();
  $("#game_container").html('<div id="cardToReview"></div><div id="deroulant" onclick="afficherKnown();">Afficher les cartes connues</div><div id="cardKnown"></div>');
  $("#deroulant").hide();
  $("#cardKnown").hide();
  $(".consigne").html("Selectionnez les mots que vous pensez connaitre");
  $(".suivant").off();
  $(".precedent").off();
  $(".precedent").on('click',function(){window.location='decks.php';})
  update_button_memory();
  var d = new Date();
  var current_tps = Math.round(d.getTime()/1000);
  for(rg in cards)
  {
  	card_id=cards[rg]["card_id"];
  	mot=cards[rg].mot;
  	mot_trad=cards[rg].mot_trad;
  	hasImage=cards[rg]["hasImage"];
  	hasAudio=cards[rg]["hasAudio"];
  	phrase=cards[rg]["phrase"];
  	phrase_trad=cards[rg]["phrase_trad"];
    LastRD=cards[rg]["LastRD"];
    OptimalRD=cards[rg]["OptimalRD"];

  	html_fliping_card='<div id="card_'+card_id+'" class="unselected card flip-container" onclick="select_card('+card_id+');" ontouchstart=\'this.classList.toggle("hover");\'>'
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
    if(current_tps>OptimalRD)
    {$("#cardToReview").append(html_fliping_card);}
    else
    {
    $("#cardKnown").append(html_fliping_card);
    //$("#card_"+card_id).addClass("golden");
    $("#deroulant").slideDown();
    }
  	if(hasImage==1){
  	$("#card_"+card_id+" > .flipper > .front").css("background-image","url(card_img/card_"+card_id+".png)");
  	}
  	else {
  	$("#card_"+card_id+" > .flipper > .front").css("background-image","url(img/default_card.png)");
  	}
    if(selected_card.indexOf(parseInt(card_id))!=-1){$("#card_"+card_id).removeClass("unselected").addClass('selected');}
  }

  $(".icon_audio").click(function(event){
      event.stopPropagation();
  });


}
function select_card(card_id)
{
	$('#card_'+card_id).toggleClass("selected");
	$('#card_'+card_id).toggleClass("unselected");
	if($('#card_'+card_id).hasClass("selected"))
	{
		selected_card.push(card_id);
	}
	else {
		removeElem(card_id,selected_card);
	}
  update_button_memory();
}

function update_button_memory()
{
len=selected_card.length;
if(len)
  {
    if(len==1){$(".suivant").html("Suivant ("+len+" carte)");}
    else{$(".suivant").html("Suivant ("+len+" cartes)");}
  $(".suivant").removeClass('inactif').addClass("actif");
  $(".suivant").off();
  $(".suivant").on("click",function(){init_order();})
  }
else
  {
    $(".suivant").off();
    $(".suivant").html("Suivant");
    $(".suivant").removeClass('actif').addClass("inactif");
  }
}

</script>
