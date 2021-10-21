<style>
.card_lvl{position:absolute;top:10px;right:10px;}
.alertDeck{display:inline-block;position:relative;background-position:center left;padding-left:40px;background-size:25px 25px;margin:30px auto;}
.fav_icon:hover{transform:scale(1.2);}
.fav_icon{position:absolute;top:0;left:0;width:30px;height:30px;background-image:url(img/favVide.png);background-size:cover;}
.fav_active{background-image:url(img/fav.png);}
.favCard{position:relative;}
.favCard:before{
  left:0;
  top:0;
  content:"";
  position:absolute;
  width:100%;
  height:100%;
  -webkit-box-shadow: 0px 0px 0px 5px gold;
  box-shadow: 0px 0px 0px 5px gold;
  }
</style>
<script>
//event to remove Jouer si on voit le block
function elementInView(elem){
  //console.log($(window).height(),$(window).scrollTop(),$(elem).offset().top,$(elem).height());
  return (innerHeight + $(window).scrollTop()) > $(elem).offset().top ;//- $(elem).height();
};





function toggleEntrainement()
{$('.subMenuEntrainement').slideToggle();
}
function closeSubMenu()
{$(".submenuEntr").removeClass("openSubMenu").addClass("closeSubMenu");}
function openSubMenu()
{$(".submenuEntr").removeClass("closeSubMenu").addClass("openSubMenu");}

function forgetThisWord(card_id)
{
  forgetConfirm=confirm("<?php echo __("Etes-vous sûr de vouloir effacer ce mot de votre mémoire ? Tous les progrés enregistrés pour cette carte seront effacés.");?>");
  if(forgetConfirm){
    $.getJSON("ajax.php?action=forgetCard&card_id="+card_id, function(results){});
  }
}
function UpdateGlobalContentIcon(){
  $('#GcontentContainer').hide();
	if(GContent.youtube_id!=""){$('#GcontentContainer').append('<div style="width: 100%;max-width: 640px;display:inline-block;"><div class="video-container"><iframe class="GlobalContent" allowFullScreen="allowFullScreen" src="https://www.youtube.com/embed/'+GContent.youtube_id+'" width="640" height="352" frameborder="0"></iframe></div></div><br>');}
	if(GContent.hasAudio==1){cacheBreaker=new Date().getTime();$('#GcontentContainer').append("<audio class='GlobalContent' controls='controls'><source src='deck_audio/deck_"+deck_id+".wav'></source></audio><br>");}
	if(GContent.hasPoster==1){$('#GcontentContainer').append("<img class='posterPreview GlobalContent' src='deck_poster/deck_"+deck_id+".png'><br>");}
	if(GContent.texte!=""){$('#GcontentContainer').append('<div class="GlobalContent GlobalContentTextPreview">'+GContent.texte+'</div>');}
  if($('#GcontentContainer').html()!=""){$('#GcontentContainer').show();}
}
function ini_memory()
{
  selected_card=[];
  $(".progressbarDeck").hide();
  $("#XPcontainer").hide();
  $("#navRight").show();
  //$("#jouerLink").show();
  // $(window).scroll(function(){
  //   if (elementInView($('#navRight')))
  //       {$('#jouerLink').hide();}else{$('#jouerLink').show();}
  // });
  // setTimeout(function(){
  //   if (elementInView($('#navRight')))
  //     {$('#jouerLink').hide();}else{$('#jouerLink').show();}
  //   },20);
  $(".onglet").removeClass('active_onglet');
  $(".onglet_selection").addClass('active_onglet');
  $("body").off();

  $(".buttonRetourList").show();
  $(".buttonRetourCards").hide();
  $("#game_container").addClass('shift250');
  $("#XPcontainer").addClass('shift250');

  $(".card_back_btn").html('<a href="decks.php?categorie=last"></a>');
  $(".progressbarContainer").hide();
  $("#game_container").html('<div class="card_back_btn"></div><div class="memoryHeader"><h2 id="deck_name" class="decalageTitreDroite"></h2><h3 class="consigne decalageTitreDroite"></h3></div><div id="GcontentContainer"></div><div id="selectionOptionContainer"></div><div id="cardToReview"></div>');
  $("#selectionOptionContainer").html("<div class='selectionOption' id='selectionOptionAll' onclick='selectAll();'>Tout sélectionner</div>");
  $("#selectionOptionContainer").append("<div class='selectionOption' id='selectionOptionNo' onclick='selectMax(0);'>Tout desélectionner</div>");
  $("#selectionOptionContainer").append("<div class='selectionOption' id='selectionOptionSome' onclick='selectMax(parseInt(selected_card.length)+10);'>ajouter 10 cartes à la selection</div>");
  $('#deck_name').html(deck_name);
  if(deck_id!=-1){
  $("#game_container").append('<a href="#" onclick="toggleAlertDeck(deck_id);" title="<?php echo __("Signaler un problème sur la liste");?>" class="alertDeck"></a>');
  }//'<a id="flipAllCard" onclick="selectAll();"><span>Retourner toutes les cartes</span></a>')
  //$("#deck_name").append('<a href="#" onclick="editDeck();" class="editDeck" title="<?php echo __("Editer la liste");?>"></a>');
  $(".memoryHeader").prepend('<ul id="actionsOnDeckContainer"></ul>');
  //$("#actionsOnDeckContainer").append('<li class="buttonActionDeck SelectAll"><a onclick="selectAll();"><img src="img/select all.png" width="40px"></a></li>');
  $("#actionsOnDeckContainer").append("<li class='buttonActionDeck buttonEditDeck'><a href='edit_deck.php?deck_id="+deck_id+"'><img src='img/stylo.png' width='40px' title='Editer la liste'></a></li>");
  //$("#actionsOnDeckContainer").append('<li class="buttonActionDeck " onclick="toggleAlertDeck(deck_id);"><a href="#" class="alertDeck" title="Un problème sur la liste ?"></a></li>');
  $("#actionsOnDeckContainer").show();

  UpdateGlobalContentIcon()

  if(creator_id==user_id || droit=="modif" || droit=="admin" || deck_status_coop>0){$("#BtnEdit,.editDeck").show();}
  else{$(".buttonEditDeck,#BtnEdit,.editDeck").hide();}
  if(deck_id!=-1){
  if(alertDeck=="1"){alertClass="alertDeckON";}else{alertClass="alertDeckOFF";}
  $(".alertDeck").addClass(alertClass);
  }

  $("#deroulant").hide();

  $(".consigne").html("<?php echo __("1) Selectionnez les cartes à réviser");?><br>");

  $(".suivant").off();
  var d = new Date();
  var current_tps = Math.round(d.getTime()/1000);
  var allCardsId=[];
  for(rg in cardsById)
  {
    alert=cardsById[rg]["alert"];
    if(alert=="1"){alertClass="alertON_icon";}else{alertClass="alertOFF_icon";}
    if(alert=="1"){alertClassCard="alertON_card";}else{alertClassCard="";}
  	card_id=cardsById[rg]["card_id"];
    allCardsId.push(card_id);
  	mot=cardsById[rg].mot;
    verbe=cardsById[rg].verbe;
    puissance=cardsById[rg].puissance;
  	mot_trad=cardsById[rg].mot_trad;
  	hasImage=cardsById[rg]["hasImage"];
  	hasAudio=cardsById[rg]["hasAudio"];
  	phrase=cardsById[rg]["phrase"];
  	phrase_trad=cardsById[rg]["phrase_trad"];
    LastRD=cardsById[rg]["LastRD"];
    OptimalRD=cardsById[rg]["OptimalRD"];
    sentences=cardsById[card_id]["sentences"];
    fav=parseInt(cardsById[card_id]["fav"]);
    Current_TimeStamp=Math.floor(Date.now() / 1000);
    Delta=OptimalRD-Current_TimeStamp;
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
//DeltaText+="\r\n"+Delta+"\r\n"+DeltaJ+"j"+DeltaS+"s"+DeltaM+"m"+DeltaY+"y";
//console.log(DeltaText);
//ontouchstart=\'this.classList.toggle("hover");\'
  	html_fliping_card=`
    <div id="card_`+card_id+`" data-puissance=`+puissance+` class="unselected card--full `+alertClassCard+`" onclick="select_card(`+card_id+`);"  >
    	<div>
    	  <div class="card_top">
          <div class="card_lvl" ><?php echo file_get_contents("img/memoryLvlBase.svg");?></div>`;
      if(mot_trad!=""){html_fliping_card+='<span class="mot_trad_card">'+mot_trad+'</span>';}
      //+'<div title="Conjuguer" class="icon_back verbeIcon" onclick="ConjugateThisVerbe(\''+mot+'\');"></div>
    	html_fliping_card+=`
        </div>
    	  <div class="card_bottom">
          <span class="mot_card forgetica">`+mot+`</span>
          <span class="icon_back fav_icon" title="<?php echo __("Carte favorite");?>" onclick="toggleFav(`+card_id+`);"></span>
          <div class="icons_card_container">
            <a title="<?php echo __("Ne plus travailler ce mot");?>" class="icon_back forget" onclick="forgetThisWord(`+card_id+`);" href="#"></a>
            <span class="icon_back sablier_icon" title="`+DeltaText+`"></span>
            <div class="icon_back phraseIcon" title="Phrases" onclick="showSentences(`+card_id+`);"></div>
            <a target="_blank" class="icon_back google" title="<?php echo __("Rechercher les images sur internet");?>" href="https://www.ecosia.org/images?q=`+mot+`"></a>
            <span class="icon_back `+alertClass+` alert_icon_`+card_id+`" title="<?php echo __("Signaler un problème sur cette carte");?>" onclick="toggleAlert(`+card_id+`,\'`+mot+`\');event.stopPropagation();"></span>
            <div class="state"></div>`;
    	if(hasAudio==1){html_fliping_card+='<div class="icon_back icon_audio" onclick="play_audio('+card_id+')";event.stopPropagation();></div>';}
    	//if(phrase!=""){html_fliping_card+='<div class="icon_back icon_phrase" title="'+phrase+'\r\n'+phrase_trad+'"></div>';}
    	html_fliping_card+=`
          </div>
    	  </div>
    	</div>
    </div>`;
    $("#cardToReview").append(html_fliping_card);

    if(fav){$("#card_"+card_id).find(".fav_icon").addClass("fav_active");
    $("#card_"+card_id).addClass("favCard");
      }
    if(current_tps>OptimalRD){$("#card_"+card_id).find(".sablier_icon").hide();}

    if(OptimalRD==null || deck_id!=-1){$("#card_"+card_id).find(".forget").hide();}
    if(OptimalRD==null ){$("#card_"+card_id).find(".card_lvl").hide();}
    if(verbe==null){$("#card_"+card_id).find(".verbeIcon").hide();}
    if(sentences.length==0){$("#card_"+card_id).find(".phraseIcon").hide();}

    card_nbre_barre=getBarreNumber(OptimalRD-LastRD);
    card_color_barre=getColorMemoryBarre(puissance/100);
    $("#card_"+card_id).find(".barre"+card_nbre_barre).css("fill",card_color_barre);

    if(hasImage>0){
  	$("#card_"+card_id+"  .card_top").css("background-image","url(card_img/card_"+hasImage+".png)");
  	}
  	else {
  	$("#card_"+card_id+"  .card_top").css("background-image","url(img/default_card.png)");
  	}
    //if(selected_card.indexOf(parseInt(card_id))!=-1){$("#card_"+card_id).removeClass("unselected").addClass('selected');}
    if(selected_card_validated.indexOf(parseInt(card_id))!=-1){
      //$("#card_"+card_id).appendTo("#cardKnown");
      $("#card_"+card_id).removeClass("selectedCard").addClass('unselected');
      $("#deroulant").slideDown();
    }

  }
  $('#cardToReview .card').sort(function(a,b){return parseInt($(a).data("puissance"))-parseInt($(b).data("puissance"));}).appendTo("#cardToReview");

  $(".unselected").on('mouseenter',function(){$(this).find('.flipper').css('transform','rotateY(180deg)');});
  $(".unselected").on('mouseleave',function(){$(this).find('.flipper').css('transform','rotateY(0deg)');});

  if(readCookie("selectedCards_"+deck_id)){
    console.log("ON A UN COOKIE:",readCookie("selectedCards_"+deck_id));
    selected_card=JSON.parse(readCookie("selectedCards_"+deck_id));
    for(k in selected_card){
      if($("#card_"+selected_card[k]).length==0){delete selected_card[k];}
      $("#card_"+selected_card[k]).removeClass("unselected").addClass("selectedCard");
    }
  }else {
    selectMax(50);
  }
  $(".icon_audio").click(function(event){
      event.stopPropagation();
  });
}
function toggleFav(card_id)
{
  event.stopPropagation();
  $("#card_"+card_id).find(".fav_icon").toggleClass("fav_active");
  $("#card_"+card_id).toggleClass("favCard");
  if($("#card_"+card_id).find(".fav_icon").hasClass("fav_active")){fav=1;}else{fav=0;};
  $.getJSON("ajax.php?action=updateFav&card_id="+card_id+"&fav="+fav, function(result)
	{
    console.log(result);
  });
}
function getColorMemoryBarre(value){
    //value from 0 to 1
    var hue=(-30+(value)*150).toString(10);
    return ["hsl(",hue,",100%,50%)"].join("");
}
function getBarreNumber(value){
    var nbreBarre=3;
    if(value<60*60*24*7){nbreBarre=1;}
    else if(value<60*60*24*21){nbreBarre=2}
    else if(value<60*60*24*90){nbreBarre=3}
    return nbreBarre;
}

function showSentences(card_id)
{
  $('.fenetreSombre').remove();
  $('body').append("<div class='fenetreSombre' onclick='$(this).remove();'><div style='text-align:center;background-color:#f0f0f0;' class='fenetreClaire' onclick='event.stopPropagation();'>"
  +"<h2 style='text-align:center;margin:0 0 20px 0;'>Exemple(s) de phrase(s) avec <span style='color:var(--mycolor2);'>"+cardsById[card_id].mot+"</span></h2>"
  +"</div></div>");
  for(k in cardsById[card_id].sentences)
  { sentence=cardsById[card_id].sentences[k];
    sentence=sentence.replace("*","<span style='color:orange;'>",1);
    sentence=sentence.replace("*","</span>",1);
    $('.fenetreClaire').append("<div class='one_sentence'>"+sentence+"</div>");}
}



function ConjugateThisVerbe(verb)
{
	$.getJSON("ajax.php?action=getConjugaison&verbe="+verb, function(result)
	{
    var old_height = $(document).height();  //store document height before modifications
    var old_scroll = $(window).scrollTop(); //remember the scroll position


    $('.fenetreSombre').remove();
    $('body').append("<div class='fenetreSombre' onclick='$(this).remove();'><div style='text-align:center;background-color:#f0f0f0;' class='fenetreClaire' onclick='event.stopPropagation();'>"
    +"<h1 style='text-align:center;margin:0 0 0px 0;'>Conjugaison du verbe : <span style='color:var(--mycolor2);'>"+verb+"</span></h1><div id='affichageConjug'></div>"
    +"</div></div>");


	console.log(result);
	conjug=result.conjug;
	prono=(result.categorie=="pronominal");
	if(conjug!="")
	{longConj=result.verbe_type.length-result.verbe_type.indexOf(":")-1;
	console.log(longConj);

	racine=verb.substring(0,verb.length-longConj);
	if(prono){racine=racine.replace(/s'|se /g, "");}
	console.log(racine);
	pronom=["Je ","Tu ","Il ","Nous ","Vous ","Ils "];
	pronoPrefixLong=["me ","te ","se ","nous ","vous ","se "];
	pronoPrefixCourt=["m'","t'","s'","nous ","vous ","s'"];
	NoPronoPrefix=["","","","","",""];
  modename={indicative:"Indicatif",conditional:"Conditionnel",imperative:"Impératif",subjunctive:"Subjonctif",participle:"Participe"};
	tempsname={present:"Présent",'imperative-present':"Présent",future:"Future",imperfect:"Imparfait",'simple-past':"Passé simple",'past-participle':"Passé",'present-participe':"Présent"};
	$("#affichageConjug").html("");
		for(mode in conjug)
		{	if(mode!="@attributes" && mode!="participle" && mode!="infinitive")
			{$("#affichageConjug").append("<div id='"+mode+"' class='conjugModes'><h1>"+modename[mode]+"</h1></div>");
			for(temps in conjug[mode])
				{
				$("#"+mode).append("<div id='"+mode+"_"+temps+"' class='conjugTemps'><h2>"+tempsname[temps]+"</h2></div>")
				Conjugaisons=conjug[mode][temps].p;
				for(k in Conjugaisons)
					{
						if(Conjugaisons[k].i!=undefined){
						pronom=["Je ","Tu ","Il ","Nous ","Vous ","Ils "];
						if(prono){firstPersVerb=racine+Conjugaisons[0].i;if(firstPersVerb[0].replace(/[aeéèihouhy]/g, "")==""){pronoPrefix=pronoPrefixCourt;}else{pronoPrefix=pronoPrefixLong;}}else{pronoPrefix=NoPronoPrefix;}
						if(k==0){firstPersVerb=pronoPrefix[0]+racine+Conjugaisons[0].i;if(firstPersVerb[0].replace(/[aeéèihouhy]/g, "")==""){pronom[0]="J'";}else{pronom[0]="Je ";}}
						if(mode=='subjunctive'){ if(k==2||k==5){pronom[k]="Qu'"+pronom[k].toLowerCase();}else{pronom[k]="Que "+pronom[k].toLowerCase();}}
						pronoSufix=["","","","","",""];
						if(mode=="imperative"){pronom[k]="";pronoPrefix[k]="";pronoSufix=["-toi","-nous","-vous"];}
						$("#"+mode+"_"+temps).append(pronom[k]+pronoPrefix[k]+racine+"<span class='endings'>"+Conjugaisons[k].i+"</span>"+pronoSufix[k]+"<br>");
						}
						else
						{$("#"+mode+"_"+temps).append("-<br>");}
					}
				}
			}
		}
		//gestion du participe:
		auxiliaire=result.auxiliaire;
		if(auxiliaire=="avoir"){
			ConjugPresentAux=["J'ai ","Tu as ","Il a ","Nous avons ","Vous avez ","Ils ont "];
			ConjugPasseAux=["J'avais ","Tu avais ","Il avait ","Nous avions ","Vous aviez ","Ils avaient "];
			ConjugFutureAux=["J'aurai ","Tu auras ","Il aura ","Nous aurons ","Vous aurez ","Ils auront "];}
		if(auxiliaire=="être"){
				if(prono)
				{
					ConjugPresentAux=["Je me suis ","Tu t'es ","Il s'est ","Nous nous sommes ","Vous vous êtes ","Ils se sont "];
					ConjugPasseAux=["Je m'étais ","Tu t'étais ","Il s'était ","Nous nous étions ","Vous vous étiez ","Ils s'étaient "];
				  ConjugFutureAux=["Je me serai ","Tu te seras ","Il se sera ","Nous nous serons ","Vous vous serez ","Ils se seront "];
				}
				else {
					ConjugPresentAux=["Je suis ","Tu es ","Il est ","Nous sommes ","Vous êtes ","Ils sont "];
					ConjugPasseAux=["J'étais ","Tu étais ","Il était ","Nous étions ","Vous étiez ","Ils étaient "];
				  ConjugFutureAux=["Je serai ","Tu seras ","Il sera ","Nous serons ","Vous serez ","Ils seront "];
				}
			}
		//Passé composé
			ParticipeEndingSg=conjug["participle"]["past-participle"].p[0].i;
			ParticipeEndingPl=conjug["participle"]["past-participle"].p[1].i;
			$("#indicative").append("<div id='indicative_passeCompose' class='conjugTemps'><h2>Passé composé</h2></div>");
			for(k in conjug["indicative"]["present"].p)
			{
				if(conjug["indicative"]["present"].p[k].i!=undefined)
				{
					if(k>2 && auxiliaire=="être"){ParticipeEnding=ParticipeEndingPl;}else{ParticipeEnding=ParticipeEndingSg;}
				$("#indicative_passeCompose").append(ConjugPresentAux[k]+racine+"<span class='endings'>"+ParticipeEnding+"</span><br>");
				}
				else {
				$("#indicative_passeCompose").append("-<br>");
				}
			}
	}
	else {
		$("#affichageConjug").html("Ce verbe n'a pas été reconnu.");
	}
	$("#affichageConjug").show();
	$("body").animate({scrollTop: $('#affichageConjug').prop("scrollHeight")}, 500);
	});

  $(document).scrollTop(old_scroll + $(document).height() - old_height);
}





function selectAll()
{
  //var d = new Date();
  //var current_tps = Math.round(d.getTime()/1000);
  for(rg in cardsById)
  {
    //OptimalRD=cardsById[rg]["OptimalRD"];
    puissance=cardsById[rg]["puissance"];
    if(puissance<75)
    {
      if(selected_card.indexOf(parseInt(cardsById[rg]["card_id"]))==-1)
      {selected_card.push(parseInt(cardsById[rg]["card_id"]));}
    }
  }
  if(selected_card.length==0){
    for(rg in cardsById)
    {
      if(selected_card.indexOf(parseInt(cardsById[rg]["card_id"]))==-1)
      {selected_card.push(parseInt(cardsById[rg]["card_id"]));}
    }
  }
  json_str=JSON.stringify(selected_card);
  createCookie("selectedCards_"+deck_id,json_str,1/48);
  for(k in selected_card)
  {$("#card_"+selected_card[k]).addClass("selectedCard").removeClass("unselected");}
  getNbreCardsPerEx();
}
function selectOther(numMax)
{
  selected_card_tmp=[];
    for(rg in cardsById){
      puissance=cardsById[rg]["puissance"];
      card_id=parseInt(cardsById[rg]["card_id"]);
      if(puissance<75 && selected_card.indexOf(card_id)==-1)
      {
        if(selected_card_tmp.length<numMax){
          if(selected_card_tmp.indexOf(card_id)==-1)
          {selected_card_tmp.push(card_id);}
        }
      }
    }
    if(selected_card_tmp.length<numMax){
      for(rg in cardsById)
      {
        card_id=parseInt(cardsById[rg]["card_id"]);
        if(selected_card_tmp.length<numMax && selected_card.indexOf(card_id)==-1){
          if(selected_card_tmp.indexOf(card_id)==-1)
          {selected_card_tmp.push(card_id);}
        }
      }
    }
return selected_card_tmp;
}
function selectMax(numMax)
{ //Select the number of cards given in parameters
  selected_card=selected_card.slice(0, numMax);
  if(numMax==0){selected_card=[];}
  else{
    for(rg in cardsById){
      //OptimalRD=cardsById[rg]["OptimalRD"];
      puissance=cardsById[rg]["puissance"];
      if(puissance<75)
      {
        if(selected_card.length<numMax){
          if(selected_card.indexOf(parseInt(cardsById[rg]["card_id"]))==-1)
          {selected_card.push(parseInt(cardsById[rg]["card_id"]));}
        }
      }
    }
    if(selected_card.length<numMax){
      for(rg in cardsById)
      {
        if(selected_card.length<numMax){
          if(selected_card.indexOf(parseInt(cardsById[rg]["card_id"]))==-1)
          {selected_card.push(parseInt(cardsById[rg]["card_id"]));}
        }
      }
    }
  }
  json_str=JSON.stringify(selected_card);
  //createCookie("selected_card",json_str,1/48);
  createCookie("selectedCards_"+deck_id,json_str,1/48);
  $(".selectedCard").addClass("unselected").removeClass("selectedCard");
  for(k in selected_card)
  {$("#card_"+selected_card[k]).addClass("selectedCard").removeClass("unselected");}
  getNbreCardsPerEx();
}

function select_card(card_id)
{
	$('#card_'+card_id).toggleClass("selectedCard");
	$('#card_'+card_id).toggleClass("unselected");
	if($('#card_'+card_id).hasClass("selectedCard"))
	{
    if(selected_card.indexOf(parseInt(card_id))==-1)
    {selected_card.push(parseInt(card_id));
    json_str=JSON.stringify(selected_card);
    createCookie("selectedCards_"+deck_id,json_str,1/48);}
	}
	else {
    removeElem(parseInt(card_id),selected_card);
    json_str=JSON.stringify(selected_card);
    createCookie("selectedCards_"+deck_id,json_str,1/48);
    if(cardsById.length==0){$(".BtnEntr").hide();$("#BtnQuizEnClass").hide();$("#BtnMiseEnBoite").hide();}
    else{$(".BtnEntr").show();$("#BtnQuizEnClass").show();$("#BtnMiseEnBoite").show();}
	}
  $(".nbreCardsSelected").html(selected_card.length);
  getNbreCardsPerEx();
}


function getNbreCardsPerEx()
{
  //compte le nombre d'exercice faisable avec les cartes selectionné en fonction de critere comme la présence d'un audio (necessaire pour la dictée par exemple)
  $(".selectMoreText").hide();
  //par default, on montre tous les boutons et on les cachera en fonction des résultats obtenus.
  $(".BtnEntr,#BtnMiseEnBoite,#BtnQuizEnClass").attr("disabled",false);

  $(".BtnEntr,#BtnMiseEnBoite,#BtnQuizEnClass").show();
  //on écrit par default le nombre de cartes faisables pour chaque type d'exercice (provisoir car les critère n'ont pas été pris en compte)

  //calcul du nombre total de carte dans le deck
  nbreCardTotal=0;
  for(rg in cardsById)
  {nbreCardTotal++;}
  $(".nbreCardsSelected").html(selected_card.length);
  //gestion des bouton de selection des cartes
  if(nbreCardTotal==selected_card.length){$("#selectionOptionAll").hide();$("#selectionOptionSome").hide();}else{$("#selectionOptionAll").show();$("#selectionOptionSome").show();}
  if(selected_card.length==0){$("#selectionOptionNo").hide();}else{$("#selectionOptionNo").show();}
  if(nbreCardTotal-selected_card.length<10){$("#selectionOptionSome").hide();}else{$("#selectionOptionSome").show();}

  if(selected_card.length!=0){
  //nbre de dicté faisable
  nbreDicte=selected_card.filter(function(elem){return cardsById[elem].hasAudio==1}).length;
  if(nbreDicte==0){
    $("#BtnDicte").attr("disabled",true);
    $("#BtnDicte").hide();
  }
  else{
    $("#BtnDicte").attr("disabled",false);
    $("#BtnDicte").slideDown();
  }
  //nbre de bazar de mot faisable
  nbreBzMot=selected_card.filter(function(elem){return cardsById[elem].sentences.length>0}).length;
  if(nbreBzMot==0){
    $("#BtnBazarMot").attr("disabled",true);
    $("#BtnBazarMot").hide();
  }
  else{
    $("#BtnBazarMot").attr("disabled",false);
    $("#BtnBazarMot").slideDown();
  }

  if(selected_card.length<=3 && nbreCardTotal>3){
    $(".BtnEntr").attr("disabled",true);
    $(".BtnEntr").hide();
  }
  if(selected_card.length<3){
    $("#BtnQCM,#BtnQCM2").attr("disabled",true);
    $("#BtnQCM,#BtnQCM2").hide();
  }
  if(selected_card.length<4){
    $("#BtngridLetter,#BtnXWord").attr("disabled",true);
    $("#BtngridLetter,#BtnXWord").hide();
  }
  if(nbreCardTotal==0){
    $(".BtnEntr,#BtnMiseEnBoite,#BtnQuizEnClass").attr("disabled",true);
    $(".BtnEntr,#BtnMiseEnBoite,#BtnQuizEnClass").hide();
  }
  }
  else
  {
    $(".BtnEntr,#BtnMiseEnBoite,#BtnQuizEnClass").hide();
  }


  if($("#BottomContainerNav button:visible").length==0){
    $(".selectMoreText").show();
  }
}
function toggleAlert(card_id,mot)
{
  if($(".alert_icon_"+card_id).hasClass("alertON_icon"))
  {$(".alert_icon_"+card_id).removeClass("alertON_icon").addClass("alertOFF_icon");
  $("#card_"+card_id).removeClass("alertON_card");
  $.getJSON("ajax.php?action=deleteAlert&card_id="+card_id, function(results){});
  }
  else {
    alert_comment=prompt("<?php echo __("Quel est le problème avec cette carte ?");?>");
    if(alert_comment!="" && alert_comment!=null){
      $(".alert_icon_"+card_id).addClass("alertON_icon").removeClass("alertOFF_icon");
      $("#card_"+card_id).addClass("alertON_card");
      $.getJSON("ajax.php?action=addAlert&card_id="+card_id+"&alert_comment="+alert_comment, function(results){});
    }

  }

}

function emptyOnTopBottom(){$('#onTopBottomMnemo').html('');}



</script>
