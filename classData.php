<style>
.sectionPageCourse{margin:auto;}
.containerDescrip,.containerBtnCourse{display:inline-block;width:300px;max-width:95%;vertical-align: middle;}
.containerDescrip{text-align:left;}
.containerDescrip p{color:grey; text-align: justify;}
.containerBtnCourse{text-align:center;}

.hrSeparateur{width:60%;}
.sectionBox{display:inline-block;/*box-shadow:0 0 20px #e0e0e0;*/padding:15px;border-bottom:1px solid #dadada;}
.backButton{width:20px;vertical-align:middle;position:absolute;top:10px;left:10px;}
.ClassContent{position:relative;}

.buttonJoin,.buttonOpen{cursor:hand;margin:10px;padding:10px;color:white;}
.buttonJoin{background-color:var(--mycolor5);}
.buttonOpen{background-color:var(--mycolor2bis);}
#public_class_list>.classItem>.keyPrivateClass{display:none;}
#private_class_list>.classItem>.keyPrivateClass,#archive_class_list>.classItem>.keyPrivateClass{
  background-image:url(img/key.png);
  background-size:contain;
  background-repeat:no-repeat;
  background-position:top right;
  width:30px;
  height:30px;
  position:absolute;
  top:30px;
  right:30px;
  filter:grayscale(1);
}
.img_role_class{position:absolute;top:-18px;left:-3px;transform:rotate(-20deg);}
.img_role_class>img{width:60px;filter: invert(0.3);}
#list_class{margin-top: 30px;}
#ArchiveSlideBtn{display:none;margin-bottom:30px;}
</style>
<script>
var usersData;
var quizsData;
function windowClasses(tabname)
{console.log("settingClass");
  $('.fenetreSombre').remove();
  $('body').append(`<div class='fenetreSombre' onclick='$(this).remove();'><div style='text-align:center;' class='fenetreClaire fenetreAction' onclick='event.stopPropagation();'>
  <img src='img/close.png' class='closeWindowIcon' onclick='$(\".fenetreSombre\").remove();'>
  <h2 style='text-align:left;margin:0 0 10px 10px;padding-left:20px;'><?php echo __("Cours");?></h2>
  <div id='tab_container'></div>
  <div id="page_actionCourse" class="pageCourse"></div>
  <div id="page_myCourses" class="pageCourse" style="dislay:none;"></div>
  <div id="page_exoCourse" class="pageCourse" style="dislay:none;"></div>
  </div></div>`);

  $("#page_actionCourse").html(`
    <div class="sectionPageCourse">
      <div class="sectionBox">
        <div class="containerDescrip">
          <h2>Créer un nouveau cours</h2>
          <p>Un cours est un espace regroupant vos élèves, des collègues et des listes de vocabulaire.</p>
        </div>
        <div class="containerBtnCourse">
          <button class="BtnStd1" onclick="createClass();">Créer un cours</button>
        </div>
      </div>
    </div>

    <div class="sectionPageCourse">
      <div class="sectionBox">
      <form onsubmit='return false;'>
        <div class="containerDescrip">
          <h2>Rejoindre le cours d'un collègue ou d'un prof</h2>
          <p>En rejoignant un cours tiers, vous accéderez aux listes de ce cours et serez en relation avec les élèves et les professeurs de ce cours.</p>
        </div>
        <div class="containerBtnCourse">
          <input type='text' size='5' class='inputCodeMobile' placeholder='- - - -' style='text-align:center;padding-left:30px;' onkeyup='if(this.value.length>5){this.value=this.value.substr(0,5);}'>
          <br><br>
          <div class="msgErrorCode"></div>
          <button type="submit" class="BtnStd1" onclick="joinClassWithCode2();">Rejoindre le cours</button>
        </div>
        </form>
      </div>
    </div>

    <div class="sectionPageCourse">
      <div class="sectionBox">
        <div class="containerDescrip">
          <h2>Ma bibliothèque</h2>
          <p>Retrouvez toutes vos listes de vocabulaire, celle que vous avez importé, ainsi que toutes celle avec lesquelles vous avez joué.</p>
        </div>
        <div class="containerBtnCourse">
          <button  class="BtnStd1" onclick='location.href="decks.php?categorie=myDecks"'>Aller à ma bibliothèque</button>
        </div>
      </div>
    </div>
    `);

    $("#page_myCourses").append(`
      <div id="my_class_list" style="display:inline;"><h2><?php echo __("Mes cours");?></h2></div>
      <div id="ArchiveSlideBtn"><a href="#" onclick="$('#archive_class_list').slideToggle('slow');"><?php echo __("Afficher/Cacher les classes archivées");?></a></div>
      <div id="archive_class_list" style="display:none;"></div>`);
    $("#page_exoCourse").append(`
    <div id="public_class_list" style="display:inline;"><h2><?php echo __("Cours ExoLingo");?></h2></div>`);

    //récupération et affichage des classes.
    $.getJSON("ajax.php?action=getMyClass", function(classes){
      $("#ArchiveSlideBtn").hide();
      console.log("classes",classes);
  		for(pos in classes)
        {
          classLang=classes[pos].lang;
        	status=classes[pos].status;
        	class_id=	classes[pos].class_id;
        	class_name=classes[pos].class_name;
        	promo=classes[pos].promo;
        	enroll=classes[pos].enroll;
        	role=classes[pos].role;
        	if(status=="public")
        	{dest_list="#public_class_list";}
        	else if(status=="ok")
        	{dest_list="#my_class_list";}
        	else
        	{dest_list="#archive_class_list";
          $("#ArchiveSlideBtn").show();}
          if(status=="public" && enroll)
          {dest_list="#public_class_list #my_class_list";}
        			$(dest_list).append('<div class="classItem" id="classItem_'+class_id+'">'
        			+'<div id="class_'+class_id+'" class="panoClass"></div><div class="keyPrivateClass" title="<?php echo __("Cours privé");?>"></div></div>');
        			$('#class_'+class_id).append('<div class="ClassContent"></div>');
        			$('#class_'+class_id+' > .ClassContent').append('<div class="name InfoClass">'+class_name+'</div><div class="promo InfoClass">'+promo+'</div>');
        			if(enroll==0){$('#class_'+class_id+' > .ClassContent').append('<div class="buttonJoin"  onclick="joinClass('+class_id+');"><?php echo __("Rejoindre");?></div>');}
        			else{$('#class_'+class_id+' > .ClassContent').append('<div class="buttonOpen"  onclick="openClass('+class_id+');"><?php echo __("Ouvrir");?></div>');}
              //affichage couronne ou chapeau eleve
        			if(role=="eleve"){$('#class_'+class_id).append('<div class="img_role_class"><img src="img/icon_eleve.png"></div>');}
        			else if(role=="prof"){$('#class_'+class_id).append('<div class="img_role_class"><img src="img/icon_prof.png"></div>');}
        	}

        if($("#public_class_list .classItem").length==0){$("#public_class_list").hide();$("#tab_exoCourse").hide();}else{$("#public_class_list").show();$("#tab_exoCourse").show();}
      	if($("#archive_class_list .classItem").length==0){$("#archiveSlideBtn").hide();}else{$("#archiveSlideBtn").show();}
        if($("#archive_class_list .classItem").length+$("#my_class_list .classItem").length==0){$("#tab_myCourse").hide();}
      	if($("#my_class_list .classItem").length==0){$("#private_class_list").hide();}else{$("#private_class_list").show();}
  	});

  $("#tab_container").append(`<div class="tab" id="tab_actionCourse" id="tabMarks" onclick="showTab('actionCourse');"><?php echo __("Accueil");?></div>`);
  $("#tab_container").append(`<div class="tab" id="tab_myCourses" id="tabDecks" onclick="showTab('myCourses');"><?php echo __("Mes cours");?></div>`);
  $("#tab_container").append(`<div class="tab" id="tab_exoCourse" id="tabCode" onclick="showTab('exoCourse');"><?php echo __("Cours ExoLingo");?></div>`);
  $(".fenetreAction").focus();
  showTab(tabname);
}
function showTab(tabname){
  $(".tab").removeClass("activeT");
  $("#tab_"+tabname).addClass("activeT");
  $(".pageCourse").hide();
  $("#page_"+tabname).show();
}
function joinClass(class_id){
	$.getJSON("ajax.php?action=joinPublicClass&class_id="+class_id, function(result){
			window.location.href="decks.php?categorie=myClass&class_id="+class_id;
	});
}

function createClass(){
  $('body').append(`<div class='fenetreSombre' onclick='$(this).remove();'><div style='text-align:center;' class='fenetreClaire' onclick='event.stopPropagation();'><img src='img/close.png' class='closeWindowIcon' onclick='$(\".fenetreSombre\").remove();'>
  <h3><?php echo __("Nouveau cours");?></h3>
  <p>	<div class='addClassContent newClassPage'>
      <input class='inputInfoClass' id='class_name' type='text' style='display:inline-block;' autocomplete='off' name='class_name' placeholder='<?php echo __("Nom du cours");?>'/>
      <input class='inputInfoClass' id='promo' type='text' autocomplete='on' name='promo' value='2019-2020' placeholder='2019-2020'/>
  <span><?php echo __("Langue enseignée");?> :<?php echo $_SESSION['target_lang_name']?></span>
  <br>
     <button class='BtnStd1' onClick='newClass();'><?php echo __("Créer");?></button>
    </div></p>
  </div></div>`);
  //importation des langues de l'utilisateur dans le menu déroulant
  // $.getJSON("ajax.php?action=getUserTargetLang", function(result)
  // {
  //   for(langRk=0;langRk<result.length;langRk++)
  //   {
  //     $(".select_lang_creaClass").append("<option value='"+result[langRk].lang_id+"'>"+result[langRk].lang_name+"</option>");
  //   }
  //   if(result.length>0){
  //     $(".select_lang_creaClass").val(result[0].lang_id);
  //   }
  // });
}

function newClass(){
	class_name=$("#class_name").val();
	promo=$("#promo").val();
	$(".fenetreSombre").remove();
	$.getJSON("ajax.php?action=addClass&class_name="+class_name+"&promo="+promo, function(result)
	{
    console.log(result);
    if(result.status=="ok")
		{
		class_id=result.class_id;
    window.location.href='decks.php?categorie=myClass&class_id='+class_id;
    }
    else if(result.status=="limit")
    {alert("Nombre de classes maximum atteint.");}
	})
}





function settingClass(class_id)
{console.log("settingClass");
$.getJSON("ajax.php?action=getThisClass&class_id="+class_id, function(result)
{
  console.log(result);
  $('.fenetreSombre').remove();
  $('body').append(`<div class='fenetreSombre' onclick='$(this).remove();'><div style='text-align:center;' class='fenetreClaire fenetreAction' onclick='event.stopPropagation();'><img src='img/close.png' class='closeWindowIcon' onclick='$(\".fenetreSombre\").remove();'>
  <h3 style='text-align:left;margin:10px;padding-left:20px;'><?php echo __("Rapports & actions");?></h3>
  <div id='tab_container'></div>
  <div class="dataTables_container pageRapport">
  <select class="periodeData" onchange="changePeriodeData();">
  </select>
   <table id="statTable" class="sortable">
  			<thead></thead><tbody></tbody>
   </table>
  </div>
  <div class="pageCode pageRapport" style="dislay:none;"></div>
  <div class="pageAction pageRapport" style="dislay:none;"></div>
  <div class="pageProf pageRapport" style="dislay:none;"></div>
  </div></div>`);
  // if(result.role=="prof"){
  //   $('.periodeData').append("<option value='1jour'><?php //echo __("1 jour");?></option>");
  //   $('.periodeData').append("<option value='1semaine'><?php //echo __("1 semaine");?></option>");
  //   $('.periodeData').append("<option value='1mois'><?php //echo __("1 mois");?></option>");
  //   $('.periodeData').append("<option value='6mois'><?php //echo __("1 semestre");?></option>");
  //   $('.periodeData').append("<option value='all'><?php //echo __("depuis le début");?></option>");
  //$("#tab_container").append('<div class="tab" id="tabBadges" onclick="showBadges('+class_id+');" >Badges</div>');
  $("#tab_container").append('<div class="tab activeT" id="tabMarks" onclick="showMarks('+class_id+');"><?php echo __("Notes");?></div>');
  $("#tab_container").append('<div class="tab" id="tabDecks" onclick="showDecks('+class_id+');"><?php echo __("Listes");?></div>');
  $("#tab_container").append('<div class="tab" id="tabCode" onclick="showCode('+class_id+');"><?php echo __("Invitation");?></div>');


  $("#tab_container").append('<div class="tab" id="tabAction" onclick="showAction('+class_id+');"><?php echo __("Actions");?></div>');
  $("#tab_container").append('<div class="tab" id="tabProf" onclick="showProf('+class_id+');"><?php echo __("Maîtres");?></div>');

  if(result.role=="prof"){getStat(class_id);showCode(class_id);}else{showProf(class_id);}
  $(".fenetreAction").focus();
});
}
function changePeriodeData()
{

}

function showAction(class_id)
{
  $.getJSON("ajax.php?action=getThisClass&class_id="+class_id, function(result)
  {
  $(".pageRapport").hide();
  $(".pageAction").show();
  $(".tab").removeClass("activeT");
  $("#tabAction").addClass("activeT");
    $(".pageAction").html('');
    if(result.role=="prof"){
      $(".pageAction").append("<br><div class='BtnStd1'  onclick='renameClass("+class_id+");'><?php echo __("Editer le cours");?></div>");
    }
    $(".pageAction").append("<br><div class='BtnStd1' style='background-color:orange;' onclick='goOutClass("+class_id+");'><?php echo __("Quitter ce cours");?></div>");
    if(result.role=="prof"){
      $(".pageAction").append("<br><div class='BtnStd1'  onclick='addToArchive("+class_id+");'><?php echo __("Archiver ce cours");?></div>");
      //$(".pageAction").append("<br><div class='btn btnAction'  onclick='RemoveFromArchive("+class_info.class_id+");'>Sortir des archives cette classe</div>");
      $(".pageAction").append("<br><div class='BtnStd1' style='background-color:red;margin-top:40px;'  onclick='delClass("+class_id+");'><?php echo __("Supprimer ce cours");?></div>");
    }
  })
}
function showProf(class_id)
{
  $(".pageRapport").hide();
  $(".pageProf").show();
  $(".tab").removeClass("activeT");
  $("#tabProf").addClass("activeT");
  $(".pageProf").html('');
  $(".pageProf").append('<h3 class=""><?php echo __("Liste des maitres");?></h3><div class="listProf" style="margin-top:30px;"></div>');
  $.getJSON("ajax.php?action=getProfFromClass&class_id="+class_id, function(result)
	{
    for(k in result){
    $(".listProf").append('<div class="prof_name_item">'+toTitleCase(result[k].first_name+' '+result[k].last_name)+'</div>');
  }
  });



}
function showCode(This_class_id)
{
  $(".pageRapport").hide();
  $(".pageCode").show();
  $(".tab").removeClass("activeT");
  $("#tabCode").addClass("activeT");

  $.getJSON("ajax.php?action=getClassInfo&class_id="+This_class_id, function(result)
	{
		code=result.code;
		class_name=result.class_name;
		promo=result.promo;

		$('.pageCode').html("<h3 style='text-align:center;margin: 50px 0;'><?php echo __('Inviter des participants à rejoindre le cours');?><br>"+class_name+" <br><span style='font-size:0.8em;color:grey;'>"+promo+"</span></h3><?php echo __("Le code pour rejoindre le cours est");?> :<div style='text-align:center;margin:30px 0;'><div class='code'>"+code+"</div></div>"
		+"<br><div><?php echo __("Vous pouvez également envoyer le lien suivant par mail");?> :<div style='text-align:center;margin:30px 0;'><input type='text' id='inputCodeLink' readonly value='www.exolingo.com/joinClass.php?code="+code+"'><div class='copyLink' onclick='copyLink();'><?php echo __("Copier");?></div></div></div>");
	});
}
function showBadges(class_id)
{
  $(".pageRapport").hide();
  $(".dataTables_container").show();
  $(".tab").removeClass("activeT");
  $("#tabBadges").addClass("activeT");
  $(".BadgesCell").show();
  $(".MarksCell").hide();
  $(".DecksCell").hide();

}
function showMarks(class_id)
{
  $(".pageRapport").hide();
  $(".dataTables_container").show();
  $(".tab").removeClass("activeT");
  $("#tabMarks").addClass("activeT");
  $(".BadgesCell").hide();
  $(".MarksCell").show();
  $(".DecksCell").hide();
}
function showDecks(class_id)
{
  $(".pageRapport").hide();
  $(".dataTables_container").show();
  $(".tab").removeClass("activeT");
  $("#tabDecks").addClass("activeT");
  $(".BadgesCell").hide();
  $(".MarksCell").hide();
  $(".DecksCell").show();
}

function renameClass(class_id)
{
  $('body').append(`<div class='fenetreSombre fenetreRenameClass' onclick='$(this).remove();'><div style='text-align:center;' class='fenetreClaire' onclick='event.stopPropagation();'>
  <h3><?php echo __("Renommer le cours");?></h3>
  <p>	<div class='addClassContent newClassPage'>
      <input class='inputInfoClass' id='class_name' type='text' style='display:inline-block;' autocomplete='off' name='class_name' placeholder='<?php echo __("Nom du cours");?>' value='`+class_info.class_name+`'/>
      <input class='inputInfoClass' id='promo' type='text' autocomplete='on' name='promo' value='`+class_info.promo+`' placeholder='2019-2020'/>
  <span><?php echo __("Langue à apprendre");?></span><select class='select_lang_fin select_lang select_lang_updateClass'>
  </select><br>
     <button class='BtnStd1' class='button' onclick='SaveClassNameChange();'><?php echo __("Valider");?></button>
     <div style='color:red' class='' onClick='$(\'.fenetreRenameClass\').remove();'><?php echo __("Annuler");?></div>
    </div></p>
  </div></div>`);

  //importation des langues de l'utilisateur dans le menu déroulant
  $.getJSON("ajax.php?action=getUserTargetLang", function(result)
  {
    for(langRk=0;langRk<result.length;langRk++)
    {
      $(".select_lang_updateClass").append("<option value='"+result[langRk].lang_id+"'>"+result[langRk].lang_name+"</option>");
    }
    if(result.length>0){
      $(".select_lang_updateClass").val(result[0].lang_id);
    }
  });
}
function SaveClassNameChange(class_id)
{
  class_id=class_info.class_id;
  class_name=$('#class_name').val();
  promo=$('#promo').val();
  $(".class_name").text(class_name);
  lang=$(".select_lang_fin").val();
	$.getJSON("ajax.php?action=ChangeClassName&class_id="+class_id+"&name="+class_name+"&promo="+promo+"&lang="+lang, function(result)
	{
	//window.location.reload();
	});
  $('.fenetreRenameClass').remove();
}
function goOutClass(class_id)
{ event.stopPropagation();
	outConfirm=confirm("<?php echo __("Etes-vous sur de vouloir quitter ce cours ?");?>");
if(outConfirm){
	$.getJSON("ajax.php?action=goOutClass&class_id="+class_id, function(result)
	{
	console.log("out done");
	window.location.reload();
	});
}
}
function delClass(class_id)
{
	event.stopPropagation();
	del=confirm("<?php echo __("Etes-vous sur de vouloir supprimer ce cours ?");?>");
if(del){
	$(".fenetreSombre").remove();
	$.getJSON("ajax.php?action=delClass&class_id="+class_id, function(result)
	{
		console.log("delete done");
		//window.location.reload();
	})
}
}
function addToArchive(class_id)
{
	$(".fenetreSombre").remove();
	$.getJSON("ajax.php?action=addToArchive&class_id="+class_id, function(result)
	{
		console.log("archive done");
	  window.location.reload();
	})
}
function RemoveFromArchive(class_id)
{
	$.getJSON("ajax.php?action=removeFromArchive&class_id="+class_id, function(result)
	{
	window.location.href='decks.php?class_id='+class_id;
	})
}

function changeMarks(user_id,quiz_id)
{
  note=$("#user_"+user_id+"_quiz_"+quiz_id).find(".note_input").val();
  $.getJSON("ajax.php?action=changeMark&user_id="+user_id+"&quiz_id="+quiz_id+"&note="+note, function(result){
  calculMoyenneQuiz();});
}
function getStat(class_id)
{

  //$('.className').html(class_info.class_name);
  $('#statTable > thead').html("");
  $('#statTable > tbody').html("");
  $('#statTable > tfoot').html("");
  //$('#BadgeTable').html("");
  $.getJSON("ajax.php?action=getClassStatToday&class_id="+class_id, function(result){
    usersData=result.users;
    quizsData=result.quizs;
    console.log(result);
    nbreDecks=result.decks.length;
    htmlHeader='<tr class="enteteTable"><th class="user_name">Prénom Nom</th><th class="MarksCell"><?php echo __("Nbre de mot en mémoire");?></th><th class="BadgesCell"><span class="nom_badge"><?php echo __("Créateur");?></span></th><th  class="BadgesCell"><span class="nom_badge"><?php echo __("Nombres de mots");?></span></th><th  class="BadgesCell"><span class="nom_badge"><?php echo __("Champion en quiz");?></span></th><th  class="BadgesCell"><span class="nom_perf"><?php echo __("Note de Perf");?></span></th></tr>';
    $('#statTable > thead').append(htmlHeader);
    //$('#statTable > tfoot').append(htmlHeader);

    //$('#BadgeTable').append('<tr id="liste_badge"><td>Prénom Nom</td><td><span class="nom_deck">Créateur</span></td><td><span class="nom_deck">Nombres de mots</span></td><td><span class="nom_deck">Persévérance</span></td><td><span class="nom_deck">Champion en quiz</span></td><td><span class="nom_perf">Note de Perf</span></td></tr>')
    for(i in result.decks)
      {var deck=result.decks[i];
        if(deck.status=="studentDeck"){classColor="purple";}else{classColor="black";}
        $(".enteteTable").append("<th class='DecksCell'><a href='cards.php?deck_id="+deck.deck_id+"' target='_blank' class='nom_deck' style='color:"+classColor+";' >"+deck.deck_name+"</a></th>");
      }
      $(".enteteTable").append("<th class='MarksCell'><?php echo __("Moyenne sur 100");?></th>");
    for(i in result.quizs)
      { quiz_id=result.quizs[i].quiz_id;
        expire=result.quizs[i].expire;
        day = moment.unix(expire).format("D/MM");
        noteMax=result.quizs[i].noteMax;
        deck_id=result.quizs[i].deck_id;
        hasImage=result.quizs[i].hasImage;
        if(hasImage>1){url_img_deck='deck_img/deck_'+hasImage+'.png';}else{url_img_deck='img/default_deck.png';}
        deck_name=result.quizs[i].deck_name;
        $(".enteteTable").append("<th class='MarksCell quiz_"+quiz_id+"' id='entete_quiz_"+quiz_id+"' style='position:relative;'><img class='iconDeckImg' src='"+url_img_deck+"'><br>"+deck_name+"<br>fait le "+day+"<br>sur "+noteMax+"<br><input title='<?php echo __("selectionner ce quiz pour le calcul de la moyenne");?>' type='checkbox' checked onclick='calculMoyenneQuiz();' id='selectedQuiz_"+quiz_id+"'><img src='img/del.png' title='<?php echo __("supprimer ce quiz definitivement");?>' class='delQuizIcon' onclick='delQuiz("+quiz_id+");' width='20px'></th>");
      }

      NbreEleve=0;
    for(k in result.users)
      {var user=result.users[k];
        if(user.role=="eleve")
        { NbreEleve++;
          motsEnMemoir=result.mots[user.user_id];
        $('#statTable > tbody').append("<tr id='user_"+user.user_id+"'><td class='user_name' onclick='showStudentData("+user.user_id+");getPersonalStat("+user.user_id+","+motsEnMemoir+",\""+user.first_name+" "+user.last_name+"\");'><span class='autocorrection' title='autocorrection="+user.nbreCorrection+"'></span>"+user.first_name+" "+user.last_name+"</td><td class='nbreMotsMemo MarksCell'></td><td class='badge_crea BadgesCell'></td><td class='badge_nbreMots BadgesCell'></td><td class='badge_quiz BadgesCell'></td><td class='badge_perf BadgesCell'></td></tr>");
        //$('#BadgeTable').append("<tr id='user_"+user.user_id+"'><th class='user_name' onclick='getPersonalStat("+user.user_id+","+motsEnMemoir+",\""+user.first_name+" "+user.last_name+"\");'>"+user.first_name+" "+user.last_name+"</th><th class='badge_crea'></th><th class='badge_nbreMots'></th><th class='badge_jours'></th><th class='badge_quiz'></th><th class='badge_perf'></th></tr>");
        for(i in result.decks)
          {deck=result.decks[i];
          $('#user_'+user.user_id).append("<td class='DecksCell' id=user_"+user.user_id+"_deck_"+deck.deck_id+" style='background-color:white;'></td>");
          }
          $('#user_'+user.user_id).append("<td class='MarksCell' id='moyenne_"+user.user_id+"'></td>");
        for(i in result.quizs)
          {quiz_id=result.quizs[i].quiz_id;
          noteMax=result.quizs[i].noteMax;
          $('#user_'+user.user_id).append("<td class='MarksCell quiz_"+quiz_id+"' id='user_"+user.user_id+"_quiz_"+quiz_id+"' style=''><input type='number' min='0' max='"+noteMax+"' placeholder='Note' class='note_input' style='background-color:#ffffff00;' onchange='changeMarks("+user.user_id+","+quiz_id+");'></td>");
          }

        }
      }
      imgBadge={"crea1":"<img class='badge_icon badgecrea1'  title='<?php echo __("Création d'une liste validée par le prof");?>' src='img/badge/badgeCrea1.png'>",
                "crea2":"<img class='badge_icon badgecrea2'  title='<?php echo __("Création de 3 listes validées par le prof");?>' src='img/badge/badgeCrea2.png'>",
                "crea3":"<img class='badge_icon badgecrea3'  title='<?php echo __("Création de 10 listes validées par le prof");?>' src='img/badge/badgeCrea3.png'>",
                "quiz1":"<img class='badge_icon badgequiz1'  title='<?php echo __("1 victoire en quiz de classe");?>' src='img/badge/badgeQuiz1.png'>",
                "quiz2":"<img class='badge_icon badgequiz2'  title='<?php echo __("3 victoires en quiz de classe");?>' src='img/badge/badgeQuiz2.png'>",
                "quiz3":"<img class='badge_icon badgequiz3'  title='<?php echo __("10 victoires en quiz de classe");?>' src='img/badge/badgeQuiz3.png'>",
                "mot1":"<img class='badge_icon badgemot1'  title='<?php echo __("100 mots en mémoire");?>' src='img/badge/badgeMots1.png'>",
                "mot2":"<img class='badge_icon badgemot2'  title='<?php echo __("250 mots en mémoire");?>' src='img/badge/badgeMots2.png'>",
                "mot3":"<img class='badge_icon badgemot3'  title='<?php echo __("500 mots en mémoire");?>' src='img/badge/badgeMots3.png'>",
                "mot4":"<img class='badge_icon badgemot4'  title='<?php echo __("1000 mots en mémoire");?>' src='img/badge/badgeMots4.png'>",
                "mot5":"<img class='badge_icon badgemot5'  title='<?php echo __("1500 mots en mémoire");?>' src='img/badge/badgeMots5.png'>"
                }
      for(i in result.trophy)
        {
          var perf=$('#user_'+i+' > .badge_perf').html();
          if(perf==""){perf=0;}
          perf=parseInt(perf);
          trophyUser=result.trophy[i];
          for(t in trophyUser)
            {
          nbre=trophyUser[t];
          type=t;
          switch (type)
          {
          case "crea":
          if(nbre >=1 && $("#user_"+i).find(".badgecrea1").length==0){perf+=1;$('#user_'+i+' > .badge_crea').html(imgBadge['crea1']);}
          if(nbre >=3 && $("#user_"+i).find(".badgecrea2").length==0){perf+=2;$('#user_'+i+' > .badge_crea').html(imgBadge['crea1']+imgBadge['crea2']);}
          if(nbre >=10 && $("#user_"+i).find(".badgecrea3").length==0){perf+=3;$('#user_'+i+' > .badge_crea').html(imgBadge['crea1']+imgBadge['crea2']+imgBadge['crea3']);}
          break;
          case "quiz":
          if(nbre >=1 && $("#user_"+i).find(".badgequiz1").length==0){perf+=1;$('#user_'+i+' > .badge_quiz').html(imgBadge['quiz1']);}
          if(nbre >=3 && $("#user_"+i).find(".badgequiz2").length==0){perf+=2;$('#user_'+i+' > .badge_quiz').html(imgBadge['quiz1']+imgBadge['quiz2']);}
          if(nbre >=10 && $("#user_"+i).find(".badgequiz3").length==0){perf+=3;$('#user_'+i+' > .badge_quiz').html(imgBadge['quiz1']+imgBadge['quiz2']+imgBadge['quiz3']);}
          break;
          /*case "jours":
          if(nbre >=1){perf+=1;$('#user_'+i+' > .badge_jours').append("<img class='badge_icon' title='Objectif quotidien atteint 1 fois' src='img/badge/badgeTime1.png'>");}
          if(nbre >=3){perf+=2;$('#user_'+i+' > .badge_jours').append("<img class='badge_icon' title='Objectif quotidien atteint 3 fois' src='img/badge/badgeTime2.png'>");}
          if(nbre >=7){perf+=3;$('#user_'+i+' > .badge_jours').append("<img class='badge_icon' title='Objectif quotidien atteint 7 fois' src='img/badge/badgeTime3.png'>");}
          if(nbre >=20){perf+=4;$('#user_'+i+' > .badge_jours').append("<img class='badge_icon' title='Objectif quotidien atteint 20 fois' src='img/badge/badgeTime4.png'>");}
          if(nbre >=30){perf+=5;$('#user_'+i+' > .badge_jours').append("<img class='badge_icon' title='Objectif quotidien atteint 30 fois' src='img/badge/badgeTime5.png'>");}
          if(nbre >=100){perf+=6;$('#user_'+i+' > .badge_jours').append("<img class='badge_icon' title='Objectif quotidien atteint 100 fois' src='img/badge/badgeTime6.png'>");}
          break;*/
          case "100mots":
          if($("#user_"+i).find(".badgemot1").length==0)
          {perf+=1;$('#user_'+i+' > .badge_nbreMots').html(imgBadge['mot1']);}
          break;
          case "250mots":
          if($("#user_"+i).find(".badgemot2").length==0)
          {perf+=2;$('#user_'+i+' > .badge_nbreMots').html(imgBadge['mot1']+imgBadge['mot2']);}
          break;
          case "500mots":
          if($("#user_"+i).find(".badgemot3").length==0)
          {perf+=3;$('#user_'+i+' > .badge_nbreMots').html(imgBadge['mot1']+imgBadge['mot2']+imgBadge['mot3']);}
          break;
          case "1000mots":
          if($("#user_"+i).find(".badgemot4").length==0)
          {perf+=4;$('#user_'+i+' > .badge_nbreMots').html(imgBadge['mot1']+imgBadge['mot2']+imgBadge['mot3']+imgBadge['mot4']);}
          break;
          case "1500mots":
          if($("#user_"+i).find(".badgemot5").length==0)
          {perf+=5;$('#user_'+i+' > .badge_nbreMots').html(imgBadge['mot1']+imgBadge['mot2']+imgBadge['mot3']+imgBadge['mot4']+imgBadge['mot5']);}
          break;
          }
          if(perf>100){perf=100;}
          $('#user_'+i+' > .badge_perf').html(perf);
        }
        }

    for(n in result.stats)
      {stat=result.stats[n];
        if(stat.nbreMots=="0")
        {pourcent=0;}
        else {pourcent=Math.round(stat.nbreMotsEnMemoire*100/stat.nbreMots);}
        if(pourcent<1){couleur="white";textPcent="";}
        else if(pourcent>90){couleur="lightgreen";textPcent=pourcent+"%";}
        else if(pourcent>25){couleur="#ffcf16";textPcent=pourcent+"%";}
        else{couleur="white";textPcent=pourcent+"%";}
        $("#user_"+stat.user_id+"_deck_"+stat.deck_id).html(textPcent);
        $("#user_"+stat.user_id+"_deck_"+stat.deck_id).css("background-color",couleur);
      }
      //On regarde l'ire sous la courbe et on ajoute à perf le resultat.
    for(id in result.jour)
      {
        date_ini = new Date(result.jour[id]["jour_ini"]);
        date_fin = new Date();
        deltaJour = Math.floor((date_fin - date_ini)/(1000*60*60*24))
        objectif=10+deltaJour*10;
        NbreMotsPerf=result.jour[id]["nbreMots"];

        var perf=$('#user_'+id+' > .badge_perf').html();
        if(perf==""){perf=0;}
        perf=parseInt(perf);
        perf+=100*NbreMotsPerf/(objectif*deltaJour/2);
        //console.log(NbreMotsPerf+"/"+objectif*deltaJour/2);
        perf=Math.round(perf);
        if(perf>100){perf=100;}
        $('#user_'+id+' > .badge_perf').html(perf);

        motsEnMemoir=result.mots[id];
        //if(result.mots[id]!=undefined)


        $("#user_"+id+" .nbreMotsMemo").html(motsEnMemoir);
        $("#user_"+id+" .user_name").attr("title",motsEnMemoir+"/"+objectif);
        //if(motsEnMemoir<objectif){$("#user_"+id+" .user_name").css("background-color","red");}
        //else{$("#user_"+id+" .user_name").css("background-color","lime");}
      }
      for(i in result.marks){
        note=result.marks[i].note;
        user_id=result.marks[i].user_id;
        quiz_id=result.marks[i].quiz_id;
        $("#user_"+user_id+"_quiz_"+quiz_id).find(".note_input").val(note);
      }
      calculMoyenneQuiz();
      if(NbreEleve==0){$('#StatsAndBadges').html("Aucun élève inscrit");}
      $(".sortable").sortable({
        items: "tr:not(:first)",
        axis:'y',
        update: function (event, ui) {
        var data = $(this).sortable('serialize');
        console.log(data);
        $.ajax({
              data: data,
              type: 'POST',
              url: 'ajax.php?action=setOrderStat'
          });
          }
      });
      $('#statTable').DataTable({"info":false,"paging":false,"searching":false,"ordering":  false});
      //showMarks();
    });

    /*$(window).scroll(function(){
      console.log("scroll");
      $('.user_nameClone').css({
      'left': $(this).scrollLeft()
      });
    });*/
}

function showStudentData(user_id)
{//$('.fenetreSombre').remove();
$('body').append("<div class='fenetreSombre' onclick='$(this).remove();'><div style='text-align:center;' class='fenetreClaire' onclick='event.stopPropagation();'><img src='img/close.png' class='closeWindowIcon' onclick='$(\".fenetreSombre\").remove();'>"
+"<h1 style='text-align:center;margin:0 0 50px 0;'><div class='user_name_title'></div></h1>"
+'<div class="stat_container" style="display:none;height:23vw; text-align:center;margin:20px;overflow:auto;">'
+'  <div><input class="rangeDays" type="range" name="points" min="30" max="365" value="60" onchange="range=$(\'.rangeDays\').val();resizeGraph(range);"></div>'
+'  <div class="chart-container" style="display:inline-block; position: relative; height:25vh; width:50vw;">'
+'    <canvas id="myChart" width="500" height="200" style="display:inline-block;"></canvas>'
+"  </div>"
+"</div>"
+"<div class='btn' onclick='upgradeProf("+user_id+");' style='border-bottom:6px solid #00000080;width:30%;display:inline-block;margin:0 10px;'><?php echo __("Donner à cet élève le rôle de prof");?></div>"
+"<div class='btn' style='background-color:red;border-bottom:6px solid #00000080;width:30%;display:inline-block;margin:0 10px;' onclick='kickout("+user_id+");'><?php echo __("Sortir cette élève du cours");?></div>"
+"</div></div>");

}
function printBadges(){
  clone=$("#statTable").clone()
  clone.prop('id', 'section-to-print' );
  clone.find(".nom_deck").parent().remove();
  clone.find("td").remove();
  clone.find(".badge_icon").css("width","50px");
  clone.find(".badge_icon").css("height","50px");
  clone.find("th").css("border","1px grey solid");
  $("td").css("border","none");
  $("#statTable").before(clone);
  window.print();
  $("td").css("border","1px grey solid");
  clone.remove();
}

function calculMoyenneQuiz(){
  for(i in usersData){
    user_id=usersData[i].user_id
    totalNote=0;
    totalNoteMax=0;
    $(".unactiveQuiz").removeClass("unactiveQuiz");
    for(q in quizsData){
      quiz_id=quizsData[q].quiz_id;
      noteMax=quizsData[q].noteMax;
      if($("#selectedQuiz_"+quiz_id).is(':checked'))
      { note=$("#user_"+user_id+"_quiz_"+quiz_id).find(".note_input").val();
        if(note!=""){
        totalNoteMax+=parseInt(noteMax);
        totalNote+=parseInt(note);
        }
      }
      else{
        $(".quiz_"+quiz_id).addClass("unactiveQuiz");
      }
    }
    moy='';
    if(totalNoteMax!=0){moy=Math.round(totalNote*100/totalNoteMax);}
    $('#moyenne_'+user_id).html(moy);
  }
}
function delQuiz(quiz_id)
{
  if(confirm("<?php echo __("Voulez-vous supprimer ce quiz définitivement ?");?>"))
  {
  $(".quiz_"+quiz_id).remove();
  $.getJSON("ajax.php?action=delQuiz&quiz_id="+quiz_id, function(result){});
  }
}

function kickout(user_id){
class_id=class_info.class_id;
if(confirm("<?php echo __("Voulez-vous sortir cet élève du cours ?");?>"))
  {
  $("#user_"+user_id).remove();
  $.getJSON("ajax.php?action=kickOutUser&class_id="+class_id+"&user_id="+user_id, function(result){});
  }
}
function upgradeProf(user_id){
class_id=class_info.class_id;
if(confirm("<?php echo __("Voulez-vous donner à cet utilisateur le rôle de prof ?");?>"))
{
$("#user_"+user_id).remove();
$.getJSON("ajax.php?action=upgradeProf&class_id="+class_id+"&user_id="+user_id, function(result){});
}
}
function getParticipant()
{$('#participantContainer').html("");
class_id=class_info.class_id;
$.getJSON("ajax.php?action=getParticipant&class_id="+class_id, function(result){
users=result;
console.log(result);

$('#participantContainer').append('<h3>Professeurs</h3><table id="tableProf"><tr id="first_line"><th><input id="checkAllProf" type="checkbox" title="Selectionner tous les professeurs"></th><th>Prénom Nom</th><th>Action</th></tr></table>');
$('#participantContainer').append('<h3>Elèves</h3><table id="tableEleve"><tr id="first_line"><th><input id="checkAllEleve" type="checkbox" title="Selectionner tous les élèves"></th><th>Prénom Nom</th><th>Action</th></tr></table>');
//$('#BadgeTable').append('<tr id="liste_badge"><td>Prénom Nom</td><td><span class="nom_deck">Créateur</span></td><td><span class="nom_deck">Nombres de mots</span></td><td><span class="nom_deck">Persévérance</span></td><td><span class="nom_deck">Champion en quiz</span></td><td><span class="nom_perf">Note de Perf</span></td></tr>')
for(i in users)
  {
   type=users[i].type;
   user_id=users[i].user_id;
   nom=  users[i].first_name+" "+users[i].last_name;
   if(type=="eleve"){$('#tableEleve').append("<tr id='user_"+user_id+"'><td><input class='checkEleve' type='checkbox'></td><td width='300px'>"+nom+"</td><td></td></tr>");}
   else{$('#tableProf').append("<tr id='user_"+user_id+"'><td><input class='checkProf' type='checkbox'></td><td width='300px'>"+nom+"</td><td></td></tr>");}
  }
  $("#checkAllEleve").on("click",function(){$(".checkEleve").prop("checked",$("#checkAllEleve").prop("checked"));});
  $("#checkAllProf").on("click",function(){$(".checkProf").prop("checked",$("#checkAllProf").prop("checked"));});
  $(".checkProf").on("click",function(){if(!$(this).prop("checked")){$("#checkAllProf").prop("checked",false);}});
  $(".checkEleve").on("click",function(){if(!$(this).prop("checked")){$("#checkAllEleve").prop("checked",false);}});
  $('#tableProf').append("<tr><td colspan='3' class='invitationBtn'>Inviter un professeur</td></tr>");
  $('#tableEleve').append("<tr><td colspan='3' class='invitationBtn'>Inviter un élève</td></tr>");
});
}

var datapointsMotsMemo = [];
var datapointsMotsVus = [];
var datapointsMotsMemoPred = [];
var datapointsMotsVusPred = [];
var datapointsObjectif = [];
var datapointsToday = [];

var config = {
  type: 'line',
  data: {
    labels: [],//'0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
    datasets: [{
      label: '<?php echo __("Nbre de mots en mémoire");?>',
      data: datapointsMotsMemo,
      borderColor: "#3399ff",
      backgroundColor: 'rgba(0, 255, 0, 0.2)',
      fill: false,
      //cubicInterpolationMode: 'monotone'
      lineTension: 0
    },{
      label: '<?php echo __("Nbre de mots vue");?>',
      data: datapointsMotsVus,
      borderColor: "transparent",
      backgroundColor: 'rgba(255, 0, 0, 0.2)',
      fill: '-1',
      //cubicInterpolationMode: 'monotone'
      lineTension: 0
    },{
      label: "Aujourd'hui",
      data: datapointsToday,
      borderColor: "#red",
      backgroundColor: 'rgba(255, 0, 0, 1)',
      fill: false,
      //cubicInterpolationMode: 'monotone'
      lineTension: 0
    },{
      label: '<?php echo __("Prédiction mots en mémoire sans travailler");?>',
      data: datapointsMotsMemoPred,
      borderColor: "#63B9ff",
      backgroundColor: 'rgba(0, 255, 0, 0)',
      borderDash:[3, 3],
      fill: true,
      //cubicInterpolationMode: 'monotone'
      lineTension: 0
    },{
      label: 'Prédiction mots vus sans travailler',
      data: datapointsMotsVusPred,
      borderColor: "transparent",
      backgroundColor: 'rgba(0,0, 255, 0.1)',
      fill: true,
      //cubicInterpolationMode: 'monotone'
      lineTension: 0
    }, {
      label: 'Objectif à 10 nouveau mots/jours',
      data: datapointsObjectif,
      borderColor: "red",
      borderDash:[3, 3],
      backgroundColor: 'rgba(0, 0, 0, 0)',
      fill: false,
      lineTension: 0
    }]
  },
  options: {
    legend:{display:false},
    layout: {
           padding: {
               left: 0,
               right: 0,
               top: 0,
               bottom: 0
           }
       },
    responsive: true,
    title: {
      //display: true,
      //text: 'Chart.js Line Chart - Cubic interpolation mode'
    },
    tooltips: {
      mode: 'index'
    },
    scales: {
      xAxes: [{
        type:"time",
        distribution: 'linear',
        time: {
                displayFormats: {
                    quarter: 'MMM D'
                },
                //min:"2018-06-05",
                unit:"day"
            },
        display: true,
        scaleLabel: {
          display: true
        }
      }],
      yAxes: [{
        min:0,
        maxTicksLimit:4,
        display: true,
        scaleLabel: {
          display: false,
          labelString: '<?php echo __("Nombre de mots");?>'
        },
        ticks: {
          source:'data',
          min:0,
          maxTicksLimit:4
          //suggestedMin: -10,
          //suggestedMax: 200,
        }
      }]
    }
  }
};

function resizeGraph(nbre_jour)
{
	startingDate=moment().subtract(nbre_jour,"days").format("YYYY-MM-DD");
	myLine.options.scales.xAxes[0].time.min=startingDate;
	myLine.update();
}

function getPersonalStat(user_id,nbremotsNow, user_name) {
   datapointsMotsMemo = [];
   datapointsMotsVus = [];
   datapointsMotsMemoPred = [];
   datapointsMotsVusPred = [];
   datapointsObjectif = [];
   datapointsToday = [];

  nbre_jour=240;
  $('.stat_container').slideDown();
  $(".stat_user_selected").removeClass("stat_user_selected");
  $("#user_"+user_id).addClass("stat_user_selected");
    var ctx = document.getElementById('myChart').getContext('2d');
    //if(window.myLine==undefined)
    //{window.myLine = new Chart(ctx, config);}
    window.myLine = new Chart(ctx, config);
    $('.user_name_title').html(user_name);
        $.getJSON("ajax.php?action=getStats&user_id="+user_id, function(result){
          console.log(result.stats);
      stats=result.stats;
      OptimalRDs=result.OptimalRDs;
      datapoints=[];
      if(parseInt(stats.length)!=0)
      {jour_ini=stats[0].jour;
      jour_fin=stats[stats.length-1].jour;
      jour_debut="";
      for(k in stats)
      {
        jour=stats[k].jour;
        nbreMots=parseInt(stats[k].nbreMots);
        nbreMotTotal=parseInt(stats[k].nbreMotTotal);
        if(k==stats.length-1){nbreMotTotal+=parseInt(nbremotsNow)-nbreMots;nbreMots=nbremotsNow;}
        if(nbreMotTotal<nbreMots){nbreMotTotal=nbreMots;}
        //if(k>stats.length-nbre_jour){
          datapointsMotsMemo.push({t:jour,y:nbreMots});
          datapointsMotsVus.push({t:jour,y:nbreMotTotal});
        //}
        if(k>stats.length-12){
          jour=stats[k].jour;
          Objectif=parseInt(stats[k].nbreMotTotal)+100;
          jourFuture=moment(jour).add(10,"days").format("YYYY-MM-DD");
          datapointsObjectif.push({t:jourFuture,y:Objectif});
        }
      }
      date_ini = new Date(jour_ini);
      date_fin = new Date(jour_fin);
      deltaJour1 = (date_fin - date_ini)/(1000*60*60*24);
      $(".rangeDays").attr("max",deltaJour1);
      console.log(deltaJour1);
      //objectif=10+deltaJour1*10;

      date_debut = new Date(jour_debut);
      //deltaJour2 = (date_debut - date_ini)/(1000*60*60*24)
      //objectif_debut=10+deltaJour2*10;

      //myLine.data.datasets[2].data=[{t:jour_debut,y:objectif_debut},{t:jour_fin,y:objectif}];
      //10jours apres:
      today=moment().format("YYYY-MM-DD");
      jourFuture=moment().add(10,"days").format("YYYY-MM-DD");
      objectif10j=parseInt(nbreMotTotal)+100;
      //datapointsObjectif=[{t:today,y:nbreMotTotal},{t:jourFuture,y:objectif10j}];
      datapointsMotsToday=[{t:today,y:0},{t:today,y:objectif10j}];
      //datapointsMotsVusPred=[{t:today,y:nbreMotTotal},{t:jourFuture,y:nbreMotTotal}];

      //prediction mot memoire
      for(k=0;k<11;k++)
      {
        thisJour=moment().add(k,"days").format("YYYY-MM-DD");
        thisDay=+new Date(thisJour)/1000;
        //console.log(OptimalRDs);
        NbreMotsPred = OptimalRDs.filter(rk => rk.OptimalRD > thisDay);
        //console.log(NbreMotsPred);
        NbreMotsPred=NbreMotsPred.length;
        //console.log(NbreMotsPred);
        datapointsMotsMemoPred.push({t:thisJour,y:NbreMotsPred});
      }

      myLine.data.datasets[0].data=datapointsMotsMemo;
      myLine.data.datasets[1].data=datapointsMotsVus;
      //myLine.data.datasets[2].data=datapointsMotsToday;
      myLine.data.datasets[3].data=datapointsMotsMemoPred;
      //myLine.data.datasets[4].data=datapointsMotsVusPred;
      //myLine.data.datasets[5].data=datapointsObjectif;
      myLine.options.legend.display=false;
      myLine.options.tooltips.mode="nearest";
      myLine.options.elements.point.radius="1";
      myLine.options.elements.point.backgroundColor="#fff";
      myLine.options.elements.point.borderWidth="1";
      //myLine.canvas.parentNode.style.height = '128px';
      startingDate=moment().subtract(30,"days").format("YYYY-MM-DD");
      myLine.options.scales.xAxes[0].time.min=startingDate;
      myLine.update();
  			}
  		});
  	};

    $("#download_results").click(function() {
      csv_data="email"+'\t'+"nom"+'\t'+"score"+'\r\n';
      for(k in usersData)
        {var user=usersData[k];
          user_id=user.user_id;
          email=user.email;
          nom=user.first_name+" "+user.last_name;
          type=user.role;
          if(type=="eleve")
          {perf=$('#user_'+user_id+' > .badge_perf').html();
          csv_data+=email+'\t'+nom+'\t'+perf+'\r\n';
          }
        }


    var downloadLink = document.createElement("a");
    var blob = new Blob(["\ufeff", csv_data]);
    var url = URL.createObjectURL(blob);
    downloadLink.href = url;
    downloadLink.download = "resultat quiz.xls";
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
    });
</script>
