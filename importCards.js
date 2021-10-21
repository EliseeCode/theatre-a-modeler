function showImportDeckWindow()
{
    $('.fenetreSombre').remove();
    selectedDeckToImport=[];
  	showWindow();
    $(".fenetreClaire").addClass("fenetreAction");
    $(".fenetreClaire").append(`
  		<h2 class="titreFenetreAction titreActionClass" style="text-align:left;margin:0 0 10px 10px;padding-left:20px;">
        <img src="img/arrow_left.png" class="backFenetre" onclick="$('.fenetreSombre').fadeOut(200,function() { $(this).remove(); });">
        Importation
      </h2>
  		<h2 class="titreFenetreAction titreActionDeckImport" style="display:none;text-align:left;margin:0 0 10px 10px;padding-left:20px;">
        <img src="img/arrow_left.png" class="backFenetre" onclick="showImportDeckWindow();">
        <span class="nomClassImport"></span>
      </h2>
      <div class="page_container">
        <div class="myClassesContainer"></div>
        <div class="myDecksContainer" style="display:none;"></div>
        <div class="footerImport" style="display:none;">
			     <button class="btnImport unactivBtnImport BtnStd1" style="width:auto;" disabled="" onclick="importSelectedDeck();">Importer les cartes</button>
		    </div>
      </div>
  		`);
    $.getJSON("ajax.php?action=getMyClasses", function(result)
    {
      myClasses=result;
      console.log(myClasses);
      for(idC in myClasses)
      {
        myclass_name=myClasses[idC]["class_name"];
        myclass_promo=myClasses[idC]["promo"];
        myclass_role=myClasses[idC]["role"];
        myclass_status=myClasses[idC]["status"];
        myclass_enroll=myClasses[idC]["enroll"];
        myclass_lang_code2=myClasses[idC]["lang_code2"];
        if(myclass_status=="perso")
        {myclass_name='Ma bibliothèque';}
          $(".myClassesContainer").append(`<div class='class_item_import class_`+idC+`' onclick="showDecksFromClass(`+idC+`,'`+myclass_name+`');">
              <img src='img/icon_`+myclass_role+`.png' title='`+myclass_role+`' style='width:20px;margin:0 5%;vertical-align:middle;'>
              <div style='display:inline-block;vertical-align:middle;'>
                <span>`+myclass_name+`</span><br><span style="font-size:0.8em;color:grey;">`+myclass_promo+`</span>
              </div>
          </li>`);
  	   }
     });
}
function showDecksFromClass(class_id,class_name)
{
  url="ajax.php?action=getClassDecks&class_id="+class_id;
  $(".nomClassImport").html(class_name);
  $(".titreActionClass").hide();
  $(".titreActionDeckImport, .footerImport").show();

  $.getJSON(url, function(decks_data)
  {$(".myClassesContainer").hide();
  $(".myDecksContainer").show();
  $(".footerImport").show();
  $(".myDecksContainer").html('');
  decks=decks_data.decks;
  for(rg in decks)
	{
		deck_status=decks[rg]["deck_status"];
		creator_id=decks[rg]["user_id"];
		var deck_id=decks[rg]["deck_id"];
		nbreMots=decks[rg]["nbreMots"];
		deck_class_status=decks[rg]["deck_class_status"];
		creator_type=decks[rg]["creator_type"];
		toSubmit=decks[rg]["toSubmit"];
		likes=decks[rg]["likes"];
		droit=decks[rg]["droit"];
		lang=decks[rg]["lang"];
		position=decks[rg]["position"];
		royalties=decks[rg]["royalties"];
		nbreKnown=0;
		creatorIcon="";
		tag_name=decks[rg]["tag_name"];
		class_name=decks[rg]["class_name"];
		deck_name=decks[rg]["deck_name"];
    creatorName="par "+toTitleCase(decks[rg]["first_name"]+" "+decks[rg]["last_name"]);
		if(creatorName=="par Null Null"){creatorName="";}
    if($("#deck_"+deck_id).length==0)
    {
    $(".myDecksContainer").append(`
      <div id="deck_`+deck_id+`" class="deck_item" onclick="ToggleDeckToImport(`+deck_id+`);">
        <div id='img_deck_`+deck_id+`' class='img_deck'></div>
        <div class='infoDeck'>
          <span class='deck_name'>`+deck_name+`</span>
          <span class='creatorName' style='font-size:0.8em;color:grey;'>`+creatorName+`</span>
        </div>
      </div>`);
      if(decks[rg]["hasImage"]>0){
			$("#img_deck_"+deck_id).css("background-image","url(deck_img/deck_"+decks[rg]["hasImage"]+".png)");
			}
			else {
			$("#img_deck_"+deck_id).css("background-image","url(img/default_deck.png)");
			}
    }
  }
});
}
selectedDeckToImport=[];
function ToggleDeckToImport(deck_id)
{
  $("#deck_"+deck_id).toggleClass("selectedDeckToImport");
  if(selectedDeckToImport.indexOf(deck_id)==-1)
  {selectedDeckToImport.push(deck_id);}
  else
  {selectedDeckToImport.splice(selectedDeckToImport.indexOf(deck_id),1);}
  console.log(selectedDeckToImport);
  if(selectedDeckToImport.length>0){
    $(".btnImport").removeClass('unactivBtnImport').attr("disabled",false);
  }
  else {
    $(".btnImport").addClass('unactivBtnImport').attr("disabled",true);
  }
}

function importSelectedDeck()
{
  //alert(deck_id+'importDeck'+selectedDeckToImport.join(","));
  url="ajax.php?action=ImportCardsFromDeck";
  $.getJSON(url,{deck_id:deck_id,importDecks:selectedDeckToImport},function(result){
    console.log(result);
    location.reload();
  })
}




function importCSVWindow(){
	showWindow();
	$(".fenetreClaire").append(`
		<h3>Importation</h3>
		<form>
			<div>
				<div class="col2 colImport">
					<p>Entre le terme et la définition</p>
					<label class="choixImport">
						<input name="wordDelim" type="radio" value="Tab" checked="true">
						<span>Tabulation</span>
					</label>
					<label class="choixImport">
						<input name="wordDelim" type="radio" value="Comma" checked="">
						<span>Virgule</span>
					</label>
					<label class="choixImport">
						<input name="wordDelim" type="radio" value="Union" checked="">
						<span>Trait d'union</span>
					</label>
					<label class="choixImport">
						<input name="wordDelim" type="radio" value="semiCol" checked="">
						<span>Point virgule</span>
					</label>
					<label class="choixImport">
						<input name="wordDelim" type="text" value="">
						<span title="Séparation personnalisée entre les colonnes.">Autre</span>
					</label>
				</div>

				<div class="col2 colImport">
					<p>Entre les rangées</p>
					<label class="choixImport">
						<input name="lineDelim" type="radio" value="Tab" checked="">
						<span>Tab</span>
					</label>
					<label class="choixImport">
						<input name="lineDelim" type="radio" value="Comma" checked="">
						<span>Virgule</span>
					</label>
					<label class="choixImport">
						<input name="lineDelim" type="radio" value="Union" checked="">
						<span>Trait d'union</span>
					</label>
					<label class="choixImport">
						<input name="lineDelim" type="radio" value="semiCol" checked="">
						<span>Point virgule</span>
					</label>
					<label class="choixImport">
						<input name="lineDelim" type="radio" value="br" checked="">
						<span>Saut de ligne</span>
					</label>
					<label class="choixImport">
						<input name="wordDelim" type="text" value="">
						<span title="Séparation personnalisée entre les rangées.">Autre</span>
					</label>
				</div>
				<div style="text-align:center;margin-top:20px;">
					<button class="BtnStd1" onclick="importTextArea();">Importation</button>
				</div>
			</div>
		</form>
		<textarea class="textBrutAImporter"></textarea>
		`);
}

function importTextArea(){

}
