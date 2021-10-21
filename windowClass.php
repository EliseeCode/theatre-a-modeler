<style>
.page_item{display:none;}
.tableScore{margin:auto;}
.chart-container{max-width:100%;width:550px;margin:20px auto;display:inline-block;box-shadow:0 0 10px grey;
margin:20px auto;vertical-align:top;}
.tableScore td{text-align:left;}
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
.page_container{max-height: 85vh;
    overflow: auto;}
.footerImport{text-align:center;position: absolute;
left: 0;
width: 100%;
bottom: 0;
background-color: white;
box-shadow: 0 0 10px grey;
z-index: 1;
padding: 5px;}
.userTableScoreRole_prof{display:none;}
.tableScoreClass .user_name{text-align:left;}
.user_name_cell{padding: 6px;
    text-transform: uppercase;}

.tableScoreClass .DecksCell{text-align:center;}
.tableScoreClass .user_nbreData{max-width:100px;}
.classLineItem{border-bottom:2px grey solid;}
.tableScoreClass .userLineItem:hover,.tableScoreClass .classLineItem:hover{background-color:#3ee6a43b;}
.tableScoreClass{padding-right:20px;float:left;border-collapse: collapse;}
.tableScoreClass tr:nth-child(even){background-color: #f2f2f2;}
.user_nbreData{text-align:right;}
.user_name{padding:10px;}

.reportPageContainer{display:grid;grid-template-columns:auto auto;padding-bottom:20px;}
.listeEleves{grid-column:1/2;}
.reportSection{grid-column:2/3;}
.user_nbreData{padding-right:20px;}
.DecksCell{text-align:center;}
.MarksCell{text-align:center;}
.borderCell{border:thin solid #e5e5e5;}
.nom_deck{color:black;}
.rangeDays{border:none;color:var(--mycolor2);}
.lang_flag_newClass{margin:10px;transform:scale(1.2);}
.lang_flag_newClass:hover{transform:scale(1.5);}
.selectedFlagClass{box-shadow:0px 0px 0px 5px var(--mycolor2bis);margin:10px;}
.activeUserLine{background-color:#caffca !important;}
.kickOutBtn{color:#ff6060;border:3px solid #ff6060;background-color:transparent;;margin:auto;width:300px;margin-bottom:20px;}
.kickOutBtn:hover{background-color:#ff6060;color:white;}
.newClass_input_lang{max-width:300px;text-align:left;margin:auto;}
.report_page{display:none;}
</style>
<script>
var usersData;
var quizsData;
function windowClass(tabname)
{console.log("settingClass");
  $('.fenetreSombre').remove();
  $('body').append(`<div class='fenetreSombre' onclick='$(this).remove();'><div style='text-align:center;' class='fenetreClaire fenetreAction' onclick='event.stopPropagation();'>
  <img src='img/close.png' class='closeWindowIcon' onclick='$(\".fenetreSombre\").remove();'>
  <h2 class="titreFenetreAction" style='text-align:left;margin:0 0 10px 10px;padding-left:20px;'><img src='img/arrow_left.png' class='backFenetre' onclick='$(\".fenetreSombre\").fadeOut(200,function() { $(this).remove(); });'>`+class_info.class_name+`</h2>
  <div class='tab_container'></div>
  <div class='page_container'></div>
  </div></div>`);

  //$(".tab_container").append(`<div class="tab_item role_eleve role_prof role_perso" id="tab_welcome" onclick="showTabClass('welcome');"><?php echo __("Accueil");?></div>`);
  $(".page_container").append('<div class="page_item" id="page_welcome"></div>');
  //$(".tab_container").append(`<div class="tab_item role_prof role_eleve role_perso" id="tab_myCourses" onclick="showTabClass('myCourses');"><?php echo __("Mes classes");?></div>`);
  $(".page_container").append('<div class="page_item" id="page_myCourses"></div>');
  //$(".tab_container").append(`<div class="tab_item role_eleve role_autodidact" id="tab_exoCourses" onclick="showTabClass('exoCourses');"><?php echo __("Classes ExoLingo");?></div>`);
  //$(".page_container").append('<div class="page_item" id="page_exoCourses"></div>');


  // $(".tab_container").append(`<div class="tab_item role_prof" id="tab_marks" onclick="showTabClass('marks');"><?php echo __("Notes des quizs");?></div>`);
  // $(".page_container").append('<div class="page_item" id="page_marks"></div>');
  // $(".tab_container").append(`<div class="tab_item role_prof" id="tab_decks" onclick="showTabClass('decks');"><?php echo __("Avancement par liste");?></div>`);
  // $(".page_container").append('<div class="page_item" id="page_decks"></div>');
  $(".tab_container").append(`<div class="tab_item role_prof" id="tab_report" onclick="showTabClass('report');"><?php echo __("Rapport de classe");?></div>`);
  $(".page_container").append('<div class="page_item" id="page_report"></div>');
  $(".tab_container").append(`<div class="tab_item role_prof" id="tab_code" onclick="showTabClass('code');"><?php echo __("Invitation");?></div>`);
  $(".page_container").append('<div class="page_item" id="page_code"></div>');
  $(".tab_container").append(`<div class="tab_item role_eleve role_prof" id="tab_settings" onclick="showTabClass('settings');" style="float:right;"><?php echo __("Paramètres");?></div>`);
  $(".page_container").append('<div class="page_item" id="page_settings"></div>');
  $(".tab_container").append(`<div class="tab_item role_eleve role_perso" id="tab_myStats" onclick="showTabClass('myStats');"><?php echo __("Mes résultats");?></div>`);
  $(".page_container").append('<div class="page_item" id="page_myStats"></div>');
  $(".tab_container").append(`<div class="tab_item role_prof role_perso" id="tab_import" onclick="showTabClass('import');"><?php echo __("Importer des listes");?></div>`);
  $(".page_container").append('<div class="page_item" id="page_import"></div>');
  $(".tab_container").append(`<div class="tab_item role_eleve role_prof role_perso" id="tab_friends" onclick="showTabClass('friends');"><?php echo __("Mes amis");?></div>`);
  $(".page_container").append('<div class="page_item" id="page_friends"></div>');
  $(".tab_container").append(`<div class="tab_item role_eleve role_prof role_perso" id="tab_avatar" onclick="showTabClass('avatar');"><?php echo __("Avatars");?></div>`);
  $(".page_container").append('<div class="page_item" id="page_avatar"></div>');

  $(".tab_item:not(.role_"+class_info.role+")").hide();

  $(".fenetreAction").focus();

  showTabClass(tabname);
}

var classMarks=[];
var classDecksData=[];
var classMyStat=[];
var classInvitData=[];
var classSettings=[];
var myCoursesData=[];
var classReport=[];
function showTabClass(tabname){
  $(".tab_item").removeClass("activeT");
  $("#tab_"+tabname).addClass("activeT");
  $(".page_item").hide();
  $("#page_"+tabname).show();
  console.log("tab :",tabname);
  switch(tabname) {
    case "welcome":
    displayPageWelcome();
  break;
  case "myCourses":
    if(myCoursesData.length==0)
    {$.getJSON("ajax.php?action=getMyClasses", function(result){
      myCoursesData=result;
      displayPageMyCourses(myCoursesData);
    });
    }else{displayPageMyCourses(myCoursesData);}
  break;
  case "report":
    if(classReport.length==0)
    displayPageClassReport(classReport);
  break;
  case "marks":
    if(classMarks.length==0)
    {$.getJSON("ajax.php?action=getThisClassMarks&class_id="+class_info.class_id, function(result)
      {
      classMarks=result;
      displayPageClassQuiz(classMarks);
      });
    }else{displayPageClassQuiz(classMarks);}
  break;
  case "decks":
    if(classMarks.length==0)
    {$.getJSON("ajax.php?action=getThisClassDecksData&class_id="+class_info.class_id, function(result)
      {
      classDecksData=result;
      displayPageClassDecksData(classDecksData);
      });
    }else{displayPageClassDecksData(classDecksData);}
  break;
  case "code":
        displayPageClassInvitData();
  break;
  case "settings":
    if(classSettings.length==0)
    {$.getJSON("ajax.php?action=getProfFromClass&class_id="+class_info.class_id, function(result)
      {
      classSettings=result;
      displayPageAClassSettings(classSettings);
      });
    }else{displayPageAClassSettings(classSettings);}

  break;
  case "myStats":
    if(classMyStat.length==0)
    {$.getJSON("ajax.php?action=getThisClassUserStat&user_id="+user_id+"&class_id="+class_info.class_id, function(result)
      {
      classMyStat=result;
      displayPageMyStat(classMyStat);
      });
    }else{displayPageMyStat(classMyStat);}
  break;
  case "import":
    displayPageImportDeck();
  break;
  case "friends":
    displayPageFriends();
  break;
  case "avatar":
    displayPageAvatar();
  break;
  case "default":
    if(class_info.role=="prof"){showTabClass("marks");}
    else if(class_info.role=="eleve"){showTabClass("myStats");}
    else if(class_info.role=="autodidact"){showTabClass("myStats");}
  break;
  }
  $(".fenetreAction").focus();
}
function displayPageMyStat(classMyStat)
{
  $("#page_myStats").append(`<div><?php echo __("Période du rapport");?> :
    <select class="rangeDays" onchange="range=$(this).val();resizeMyGraph(range);">
      <option value="7"><?php echo __("1 semaine");?></option>
      <option value="31" selected><?php echo __("1 mois");?></option>
      <option value="92"><?php echo __("1 trimestre");?></option>
      <option value="365"><?php echo __("1 an");?></option>
    </select>
  </div>
  <div id="myActualReport"></div>`);
  displayStat("#myActualReport",classMyStat,31);
}
var avatarData;
function displayPageAvatar()
{
  $("#page_avatar").html(`<h3><?php echo __("Mes avatars");?> :</h3>
  <div id="avatarContainer"></div>
  <h3><?php echo __("Ajouter un nouvel avatar");?> :</h3>
  <div id="eggContainer" class="item2buy" data-price="100">
  <img src="img/egg.png" width="140px" style="display:block;margin:auto;">
  <button class="BtnStd1" onclick="getEgg();">Acheter pour<br><span style="font-size:2em;vertical-align: middle;">100</span><span class="ruby_inline_L ruby"></span></button>
  </div>`);
  displayAvailableItem();
    $.getJSON("ajax.php?action=getMyAvatar", function(result)
      {
      avatarData=result;
      displayAvatar(avatarData);
    });
}
function displayAvailableItem()
{
  $.getJSON("ajax.php?action=getRubyAndXp", function(result)
    {
    nbreRuby=parseInt(result.nbreRuby);
    $(".item2buy")
      .css("opacity",1)
      .filter(function( index ) {
      return parseInt($( this ).attr("data-price")) >= nbreRuby;})
      .css("opacity",0.3);
    });
}
function displayAvatar(avatarData)
{
  console.log(avatarData);
  allMyAvatars=avatarData.allMyAvatars;
  myAvatar_id=avatarData.myAvatar_id;
  $("#avatarContainer .avatar_item").remove();
  for(rk in allMyAvatars)
  {
    avatar_id=allMyAvatars[rk].avatar_id;
    //avatar_status=allMyAvatars[rk].status;
    showAvatarInPage(avatar_id);
  }
  $("#avatarContainer .avatar_"+myAvatar_id).addClass("avatar_selected");
}
function getEgg()
{
  $.getJSON("ajax.php?action=buyEgg", function(result)
    {
    console.log(result);
    if(result.status=="moneyProblem")
    {}
    else if(result.status=="ok")
    {showAvatarInPage(result.newAvatar_id);}

    });
}
function showAvatarInPage(avatar_id){
  $("#avatarContainer").append(`<div class="avatar_item avatar_`+avatar_id+`" onclick="switchAvatar(`+avatar_id+`);">
    <img class="avatar_L avatar" src="avatar/avatar_`+avatar_id+`.png"><div>`);
}
function switchAvatar(avatar_id)
{
  $.getJSON("ajax.php?action=switchAvatar&avatar_id="+avatar_id, function(result)
    {
    console.log(result);
    $(".avatar_selected").removeClass("avatar_selected");
    $(".avatar_"+avatar_id).addClass("avatar_selected");
    $(".avatarID .avatar_img").attr("src","avatar/avatar_"+avatar_id+".png");
    });
}
function displayPageFriends()
{
  $("#page_friends").html(`<div style="text-align:left;">
    <h3>Mes amis :</h3>
    <div>Trouver des amis avec leurs mails :
    <div>
      <input type='mail' class="lookForFriendInput lookForUserInput" placeholder="<?php echo __("Prénom ou mail");?>">
      <button class="btnStd1" onclick='lookForFriend();'><?php echo __("Rechercher");?></button>
      <div style="position:relative;height:1;">
        <div class="lookForFriend_result lookForUser_result"></div>
      </div>
    </div>
    <div class="askForFriend_feedback"></div>
    <div id="myFriendsContainer"></div>
  </div>
  `);
    $.getJSON("ajax.php?action=getMyFriends", function(result)
      {
      myFriendsData=result;
      displayMyFriends(myFriendsData);
    });
}
function displayMyFriends(myFriendsData)
{
  $(".friend_item").remove();
  for(rk in myFriendsData)
  {
    F_user_id=myFriendsData[rk].friend_id;
    F_status=myFriendsData[rk].status;
    F_name=myFriendsData[rk].first_name+" "+myFriendsData[rk].last_name;
    F_avatar=myFriendsData[rk].avatar_id;
    F_xp=myFriendsData[rk].nbreCoins;
    if($(`.friend_`+F_user_id).length==0){
      $("#myFriendsContainer").append(`<div class="friend_`+F_user_id+` friend_item friend_status_`+F_status+`">
        <div class="avatar">
          <img src='avatar/avatar_`+F_avatar+`.png' class='avatar_img avatar_S'>
        </div>
        <div class="F_name">`+F_name+`</div>
      </div>`);
    }
    if(F_status=="accepted"){
      F_niveau=getLvlFromXp(F_xp);
      $(".friend_"+F_user_id).append("<div>Niveau "+F_niveau+" : "+F_xp+"xp</div>");
      $(".friend_"+F_user_id).append(`<div class="btnFriendsContainer">
        <a href="#" onclick="removeFriend(`+F_user_id+`);"><?php echo __("Retirer");?></a>
      <div>`)
    }
    if(F_status=="waiting"){
      F_niveau=getLvlFromXp(F_xp);
      $(".friend_"+F_user_id).append("<div>En attente d'acceptation</div>");
      $(".friend_"+F_user_id).append(`<div class="btnFriendsContainer">
        <a href="#" onclick="removeFriend(`+F_user_id+`);"><?php echo __("Retirer");?></a>
      <div>`)
    }
    if(F_status=="demand"){
      F_niveau=getLvlFromXp(F_xp);
      $(".friend_"+F_user_id).append(`<div class="btnFriendsContainer">
        <a href="#" onclick="addFriend(`+F_user_id+`);"><?php echo __("Ajouter comme ami");?></a>
        <a href="#" onclick="removeFriend(`+F_user_id+`);"><?php echo __("Retirer");?></a>
      <div>`)
    }
  }
}
function addFriend(F_user_id)
{
  $.getJSON("ajax.php?action=addFriend&F_user_id="+F_user_id, function(result)
    {
      displayPageFriends();
    });
}
function removeFriend(F_user_id)
{
  $.getJSON("ajax.php?action=removeFriend&F_user_id="+F_user_id, function(result)
    {
      $(".friend_"+F_user_id).remove();
    });
}
function lookForFriend()
{
  input=$(".lookForFriendInput").val();
  $.getJSON("ajax.php?action=getUserFromMailName&input="+input, function(usersFound)
    {
      $(".lookForFriend_result").show();
      $(".lookForFriend_result").html(`<div class='close_icon' style="float:right;width:20px;height:20px;" onclick='$(this).parent().html("");'></div><br>`);
      $(".userFound_item").remove();
      var usersFoundUseful=0;
      for(var rk in usersFound)
      {
        friend_id=usersFound[rk].user_id;
        friend_name=usersFound[rk].first_name+" "+usersFound[rk].last_name;
        friend_avatar_id=usersFound[rk].avatar_id;
        if($("#userFound_item_"+friend_id).length==0 && myFriendsData.filter(function(elem){return elem.friend_id==friend_id;}).length==0)
          { usersFoundUseful++;
            $(".lookForFriend_result").append(
              `<div id='userFound_item_`+friend_id+`' class='userFound_item'>
                <div class='avatar'>
                  <img src='avatar/avatar_`+friend_avatar_id+`_XS.png' class='avatar_img avatar_XS'>
                </div>
                <div class='user_name_menu'>`+friend_name+`</div>
                <button style="justify-self:end;" class='btnStd2' onclick='askForFriend(`+friend_id+`);'><?php echo __("Ajouter");?></button>
              </div>`);
          }
      }
      if(usersFoundUseful==0){$(".lookForFriend_result").append("<?php echo __("Introuvable sur ExoLingo");?>");}
    });
}
function askForFriend(friend_id)
{
  $(".lookForFriend_result").hide();
  $.getJSON("ajax.php?action=askForFriend&friend_id="+friend_id, function(result)
    {
      displayPageFriends();
      $(".askForFriend_feedback").html(result.status);
    });
}

function displayPageImportDeck()
{$('#page_import').html(`
  <div style="background-color:white;width:100%;display:inline-block;">
		<h3 style='float:left;text-align:left;margin:10px;padding-left:20px;display:inline-block;'><?php echo __("Choisissez des listes à importer dans ");?>`+class_info.class_name+`</h3>
		<div class="footerImport">
			<button class='btnImport unactivBtnImport BtnStd1' style="width:auto;" disabled onclick='importListInClass();'><?php echo __("Importer dans ");?>`+class_info.class_name+`</button>
		</div>
		<div>
			<input type="text" class='SearchListInput importBoxSearch typeahead' placeholder="<?php echo __("métier, ...");?>" style="vertical-align:middle;">
			<div class="filterTooStrict"><?php echo __("Aucune liste n'a été trouvé");?></div>
		</div>
	</div>

	<div id="deckToImport" style="position:relative;top:10px;">
	</div>`);
  $("#deckToImport").append(`<div id='import_Deck_vierge' onclick="location.href='edit_deck.php?deck_id=0';" class='deck' style='display:inline-flex;flex-direction:column;'>
		<div style='margin:auto;'><div class='plusImport'>+</div><br><?php echo __("Créer une nouvelle liste vierge");?></div></div>`);
	$("#deckToImport").show();
  if(typeof deckAvailable!="undefined"){
    drawAvailableDeck(deckAvailable);
  }
  else{
    $.getJSON("ajax.php?action=getAvailableDecks", function(decks_data)
    {	deckAvailable=decks_data;
      drawAvailableDeck(deckAvailable);
        // Constructing the suggestion engine
        var listeDesTagsB = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            local: listeDesTags
        });
        var listeDesNomsB = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
            //datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            local: listeDesNoms
        });

        // Initializing the typeahead
        $('.importBoxSearch').typeahead({
            hint: true,
            highlight: true, /* Enable substring highlighting */
            minLength: 0 /* Specify minimum characters required for showing suggestions */
        },
        {
            name: 'Noms',
            source: listeDesNomsB,
            templates:{
              header: '<h3 class="category-name">Noms</h3>',
            }
        },{
            name: 'Attributs',
            source: listeDesTagsB,
            templates:{
              header: '<h3 class="category-name">Attribut</h3>'
            }
        });
        $('.importBoxSearch').on("input",filterAvailableDeck);
        $('.importBoxSearch').bind('typeahead:selected', function(obj, datum, name) {
          //console.log(obj.data());
          //console.log(obj,datum,name);
          //console.log(JSON.stringify(obj)); // object
          // outputs, e.g., {"type":"typeahead:selected","timeStamp":1371822938628,"jQuery19105037956037711017":true,"isTrigger":true,"namespace":"","namespace_re":null,"target":{"jQuery19105037956037711017":46},"delegateTarget":{"jQuery19105037956037711017":46},"currentTarget":
          //console.log(JSON.stringify(datum)); // contains datum value, tokens and custom fields
          // outputs, e.g., {"redirect_url":"http://localhost/test/topic/test_topic","image_url":"http://localhost/test/upload/images/t_FWnYhhqd.jpg","description":"A test description","value":"A test value","tokens":["A","test","value"]}
          // in this case I created custom fields called 'redirect_url', 'image_url', 'description'
          //console.log(JSON.stringify(name)); // contains dataset name
          // outputs, e.g., "my_dataset"
        });
    });
  }
}
function displayPageWelcome(){
  $('#page_welcome').html("");
  if(userType=="prof"){$('#page_welcome').append(`
    <div class="sectionPageCourse">
      <div class="sectionBox">
        <div class="containerDescrip">
          <h2><?php echo __("Créer une nouvelle classe");?></h2>
          <p><?php echo __("Une classe est un espace regroupant vos élèves, des collègues et des listes de vocabulaire.");?></p>
        </div>
        <div class="containerBtnCourse">
          <button class="BtnStd1" onclick="createClass();"><?php echo __("Créer une classe");?></button>
        </div>
      </div>
      <!--<div class="sectionBox">
      <form onsubmit='return false;'>
        <div class="containerDescrip">
          <h2><?php echo __("Rejoindre la classe d'un collègue");?></h2>
          <p><?php echo __("En rejoignant la classe de votre collègue, vous accéderez aux listes de cette classe et serez en relation avec les élèves et les professeurs.");?></p>
        </div>
        <div class="containerBtnCourse">
          <input type='text' size='5' class='inputCodeMobile' placeholder='- - - -' style='text-align:center;padding-left:30px;' onkeyup='if(this.value.length>5){this.value=this.value.substr(0,5);}'>
          <br><br>
          <div class="msgErrorCode"></div>
          <button type="submit" class="BtnStd1" onclick="joinClassWithCode2();"><?php echo __("Rejoindre la classe");?></button>
        </div>
        </form>
      </div>-->
    </div>`);}
    if(userType=="eleve"){
    $('#page_welcome').append(`<div class="sectionPageCourse">
      <div class="sectionBox">
      <form onsubmit='return false;'>
        <div class="containerDescrip">
          <h2><?php echo __("Rejoindre la classe de votre professeur");?></h2>
          <p><?php echo __("En rejoignant la classe de votre professeur, vous accéderez aux listes de cette classe et serez en relation avec les élèves et les professeurs.");?></p>
        </div>
        <div class="containerBtnCourse">
          <input type='text' size='5' class='inputCodeMobile' placeholder='- - - -' style='text-align:center;padding-left:30px;' onkeyup='if(this.value.length>5){this.value=this.value.substr(0,5);}'>
          <br><br>
          <div class="msgErrorCode"></div>
          <button type="submit" class="BtnStd1" onclick="joinClassWithCode2();"><?php echo __("Rejoindre la classe");?></button>
        </div>
        </form>
      </div>
    </div>`);}

    $('#page_welcome').append(`<div class="sectionPageCourse">
      <div class="sectionBox">
        <div class="containerDescrip">
          <h2><?php echo __("Ma bibliothèque");?></h2>
          <p><?php echo __("Retrouvez toutes vos listes de vocabulaire, celle que vous avez importé, ainsi que toutes celle avec lesquelles vous avez joué.");?></p>
        </div>
        <div class="containerBtnCourse">
          <button  class="BtnStd1" onclick='location.href="decks.php?categorie=myDecks"'><?php echo __("Aller à ma bibliothèque");?></button>
        </div>
      </div>
    </div>
    `);
}
function displayPageMyCourses(myCourseData){
  $("#page_myCourses").html(`
    <div id="my_class_list" style="display:inline;text-align:left;"><h3><?php echo __("Mes classes");?></h3></div>
    <div id="ArchiveSlideBtn"><a href="#" onclick="$('#archive_class_list').slideToggle('slow');"><?php echo __("Afficher/Cacher les classes archivées");?></a></div>
    <div id="archive_class_list" style="display:none;"></div>`);
  $("#page_exoCourse").html(`
    <div id="public_class_list" style="display:inline;"><h3><?php echo __("Classes ExoLingo");?></h3></div>`);

      $("#ArchiveSlideBtn").hide();
      console.log("classes",myCourseData);
      $(".classItem").remove();
  		for(pos in myCourseData)
        {
          classLang=myCourseData[pos].lang_code2;
        	status=myCourseData[pos].status;
        	class_id=	myCourseData[pos].class_id;
        	class_name=myCourseData[pos].class_name;
        	promo=myCourseData[pos].promo;
        	enroll=myCourseData[pos].enroll;
        	role=myCourseData[pos].role;
        	if(status=="public")
        	{dest_list="#public_class_list";}
        	else if(status=="ok")
        	{dest_list="#my_class_list";}
        	else if(status=="archive")
        	{dest_list="#archive_class_list";
          $("#ArchiveSlideBtn").show();}
          else{dest_list="perso"}
          if(status=="public" && enroll)
          {dest_list="#public_class_list #my_class_list";}

        			$(dest_list).append(`<div class="classItem" id="classItem_`+class_id+`">
        			<div id="class_`+class_id+`" class="panoClass"></div>
              <div class="keyPrivateClass" title="<?php echo __("Classes privé");?>"></div>
              </div>`);
        			$('#class_'+class_id).append(`<span class="tinyFlag flag_`+classLang+`"></span><div class="ClassContent"></div>`);
        			$('#class_'+class_id+' > .ClassContent').append(`<div class="name InfoClass">`+class_name+`</div><div class="promo InfoClass">`+promo+`</div>`);
        			if(enroll==0){$('#class_'+class_id+' > .ClassContent').append('<div class="buttonJoin BtnStd1"  onclick="joinPublicClass('+class_id+');"><?php echo __("Rejoindre");?></div>');}
        			else{$('#class_'+class_id+' > .ClassContent').append('<div class="BtnStd1"  onclick="openClass('+class_id+');"><?php echo __("Ouvrir");?></div>');}
              //affichage couronne ou chapeau eleve
        			if(role=="eleve"){$('#class_'+class_id).append('<div class="img_role_class"><img src="img/icon_eleve.png"></div>');}
        			else if(role=="prof"){$('#class_'+class_id).append('<div class="img_role_class"><img src="img/icon_prof.png"></div>');}
        	}

        if($("#public_class_list .classItem").length==0){$("#public_class_list").hide();$("#tab_exoCourse").hide();}else{$("#public_class_list").show();$("#tab_exoCourse").show();}
      	if($("#archive_class_list .classItem").length==0){$("#archiveSlideBtn").hide();}else{$("#archiveSlideBtn").show();}
        if($("#archive_class_list .classItem").length+$("#my_class_list .classItem").length==0){$("#tab_myCourse").hide();}
      	if($("#my_class_list .classItem").length==0){$("#private_class_list").hide();}else{$("#private_class_list").show();}


}


function displayPageClassDecksData(classDecksData)//tabname=decks
{$('#page_decks').html("Page de progression par liste en cours de construction");}


var StudentStat=[];
var ClassStat=[];



function displayReportExo(nbre_jour)
{
  $('.report_page').hide();
  $('.report_page_exo').show();
  $('.selectPeriodeClass').show();
  $('.tab2').removeClass("activeT");
  $('.tab2_exo').addClass("activeT");
  $('.reportSection').show();
  $(".classLineItem").show();
  $(".iconDeckLine").remove();
  $(".tableScoreClass tr *:not(.user_name)").remove();
  $(".tableScoreClass tr:not(.tableHeader)").append("<td class='borderCell user_nbreData'></td>");
  $(".tableScoreClass .tableHeader").append("<th class='borderCell user_nbreData'>Nombre d'exercices</th>");
  resizeClassReport(nbre_jour);
}
function resizeClassReport(nbreJour){
  resizeMyGraph(nbreJour);
  classUsers=classReport.classUsers;
  statsNbreExo=classReport.statsNbreExo;
  classUsers.sort(function(a,b){return a.position-b.position;});
  nbreExoSurPeriodeClasse=0;
  $(".tableScoreClass .user_nbreData:not(th)").html("-");
  for(k in classUsers)
  {
    thisUserId=classUsers[k].user_id;
    statsNbreExoUser=statsNbreExo.filter(function(elem){return elem.user_id==thisUserId;});
    startingDate=moment().subtract(nbreJour,"days").format("YYYY-MM-DD")
    statsNbreExoUserPeriode=statsNbreExoUser.filter(function(elem){return new Date(elem.jour)>=new Date(startingDate);})
    nbreExoSurPeriode=0;
    for(rk in statsNbreExoUserPeriode)
    {
      nbreExoSurPeriode+=parseInt(statsNbreExoUserPeriode[rk].nbreExo);
    }
    nbreExoSurPeriodeClasse+=nbreExoSurPeriode;
    $(".tableScoreClass").find("#user_"+thisUserId).find(".user_nbreData").html(nbreExoSurPeriode);
  }
  $("#allClassLine").find(".user_nbreData").html(nbreExoSurPeriodeClasse);
}
function displayPageClassInvitData()//tabname=code
{
		code=class_info.code;
		class_name=class_info.class_name;
		promo=class_info.promo;

		$('#page_code').html(`<div style='text-align:left;'>
    <h3 style='margin: 50px 0;'><?php echo __('Inviter des participants à rejoindre la classe ');?>"`+class_name+`"</h3>
    <p><?php echo __("Le code pour rejoindre la classe est");?> :
      <div style='text-align:center;margin:30px 0;'><div class='code'>`+code+`</div></div>
    </p>
		<p><?php echo __("Vous pouvez également envoyer le lien suivant par mail");?> :
      <div style='text-align:center;margin:30px 0;'>
        <input type='text' id='inputCodeLink' readonly value='www.exolingo.com/joinClass.php?code=`+code+`'>
      </div>
    </p>
		<p><?php echo __("Inscrire des élèves avec leurs mails (séparé par une virgule)");?> :
      <div style='text-align:center;margin:30px 0;'>
        <input type='email' id='inputMailEleve' placeholer='eleve@ecole.com'>
        <button onclick="addUsersByMail('eleve');">Ajouter</button>
      </div>
    </p>
			<p><?php echo __("Inscrire des profs avec leurs mails (séparé par une virgule)");?> :
      <div style='text-align:center;margin:30px 0;'>
        <input type='email' id='inputMailProf' placeholer='prof@ecole.com'>
        <button onclick="addUsersByMail('prof');">Ajouter</button>
      </div>
    </p>
    </div>`);
}
function addUsersByMail(type)
{
  if(type=="prof")
  {mails=$("#inputMailProf").val();}
  else
  {mails=$("#inputMailEleve").val();}
  console.log(mails);
  class_id=class_info.class_id;
  $.getJSON("ajax.php?action=addUsersToClass&type="+type+"&class_id="+class_id+"&mails="+mails, function(result)
  {

  })
}
function displayPageAClassSettings(classProfs)//tabname=settings
{
  $("#page_settings").html('<div class="actionContainer col2"></div><div class="profContainer col2"></div>');
  $(".actionContainer").append("<h3><?php echo __("Actions :");?></h3>")
  $(".profContainer").append("<h3><?php echo __("Professeur(s) de cette classe :");?></h3>")
  if(class_info.role=="prof"){
    $(".actionContainer").append("<br><div class='BtnStd1' style='margin:auto;'  onclick='renameClass("+class_info.class_id+");'><?php echo __("Editer la classe");?></div>");
  }
  $(".actionContainer").append("<br><div class='BtnStd1' style='margin:auto;' style='margin:auto;background-color:orange;' onclick='goOutClass("+class_info.class_id+");'><?php echo __("Quitter cette classe");?></div>");
  if(class_info.role=="prof"){
    $(".actionContainer").append("<br><div class='BtnStd1' style='margin:auto;' onclick='addToArchive("+class_info.class_id+");'><?php echo __("Archiver cette classe");?></div>");
    //$(".pageAction").append("<br><div class='btn btnAction'  onclick='RemoveFromArchive("+class_info.class_id+");'>Sortir des archives cette classe</div>");
    $(".actionContainer").append("<br><div class='BtnStd1' style='margin:auto;background-color:red;margin-top:40px;'  onclick='delClass("+class_info.class_id+");'><?php echo __("Supprimer cette classe ");?></div>");
  }
  for(rk in classProfs)
  {
    $(".profContainer").append("<div>"+classProfs[rk].first_name+" "+classProfs[rk].last_name+"</div>");
  }
}
  var configMemo;
  var configNbreExo;
  var configRepartExo;
  var dataExo=[];
  var dataExoTmp=[];
  var dataExoCum=[];
  var dataTotalExo=[];
  var dataTotalExoCum=[];
  var dataTotalExoTmp=[];
  var joursArray=[];
  var dataDougnutsNum=[];
  var dataDougnutsLabel=[];
  var dataDougnutsColor=[];
  var gameData={
  QCMmot2image:{label:"<?php echo __("QCM retrouver l'image");?>",color:"rgba(230, 0, 0, 0.2)"},
  QCMimage2mot:{label:"<?php echo __("QCM retrouver le mot");?>",color:"rgba(230, 150, 0, 0.2)"},
  bazarLettre:{label:"<?php echo __("Bazar de lettre");?>",color:"rgba(230, 230, 0, 0.2)"},
  bazarMot:{label:"<?php echo __("Mots dans le désordre");?>",color:"rgba(150, 230, 0, 0.2)"},
  dictée:{label:"<?php echo __("Dictée");?>",color:"rgba(0, 230, 0, 0.2)"},
  gridLetter:{label:"<?php echo __("Grille de lettres");?>",color:"rgba(0, 230, 150, 0.2)"},
  prononciation:{label:"<?php echo __("Prononciation");?>",color:"rgba(0, 230, 230, 0.2)"},
  quizMixLetter:{label:"<?php echo __("Orthographe");?>",color:"rgba(0, 150, 230, 0.2)"},
  xWord:{label:"<?php echo __("Mots croisées");?>",color:"rgba(0, 150, 150, 0.2)"},
  validation:{label:"<?php echo __("Texte à trous");?>",color:"rgba(0, 0, 230, 0.2)"}
}
function displayStat(destination,classMyStat,nbre_jour)//tabname=myStats
{
  console.log(destination,classMyStat,nbre_jour);
  startingDateMemo=moment().subtract(nbre_jour,"days").format("YYYY-MM-DD");
  dataExo=[];
  dataExoTmp=[];
  dataExoCum=[];
  dataTotalExo=[];
  dataTotalExoCum=[];
  dataTotalExoTmp=[];
  joursArray=[];
  dataDougnutsNum=[];
  dataDougnutsLabel=[];
  dataDougnutsColor=[];
  today=moment().format("YYYY-MM-DD");
  startingDate=moment().subtract(nbre_jour,"days").format("YYYY-MM-DD");

  nbreTotalExo=0;
  stats=classMyStat.stats;
  nbreExoArray=classMyStat.nbreExo;
  OptimalRDs=classMyStat.OptimalRDs;
  nbremotsNow=classMyStat.nbreMotsEnMemoire;
  if(classMyStat.status=="student"){
  user_name=classMyStat.user_name;}
  else{
  user_name=class_info.class_name;}

  if(nbremotsNow==null){nbremotsNow=0;}
  if(classMyStat.nbreCoinsToday==null){classMyStat.nbreCoinsToday=0;}
  if(classMyStat.nbreCoins==null){classMyStat.nbreCoins=0;}
  if(classMyStat.nbreCoinsExo==null){classMyStat.nbreCoinsExo=0;}
  classMyStat.nbreCoinRoyalties=parseInt(classMyStat.nbreCoins)-parseInt(classMyStat.nbreCoinsExo);

  nbreExoArrayFiltred=nbreExoArray.filter(function(elem){return new Date(elem.jour)>=new Date(startingDate);});
  for(k in nbreExoArrayFiltred){
    nbreTotalExo+=parseInt(nbreExoArrayFiltred[k].nbreExo);
  }

    $(destination).html(`<div class="stat_container" style="text-align:center;margin:20px;">
    <h3><?php echo __("Rapport de ");?>`+user_name+`</h3>
    <table class="tableScore" style="margin:auto;">
          <tr><td><?php echo __("Nombre d'exercices réalisés dans cette classe");?></td><td class="nbreTotalExo">`+nbreTotalExo+`</td></tr>
          <tr class='infoStatStudentOnly'><td><?php echo __("Nombre total de mots en mémoire (toutes classes confondues)");?></td><td class="nbreMotsMemoire">`+classMyStat.nbreMotsEnMemoire+`</td></tr>
          <tr class='infoStatStudentOnly'><td><?php echo __("Nombre total de mots oubliés  (toutes classes confondues)");?></td><td class="nbreMotOublie">`+classMyStat.nbreMotsOublie+`</td></tr>
          <tr class='infoStatStudentOnly'><td><img src="img/light.png" width="25px" style="vertical-align:middle;"> <?php echo __("obtenus par exercices");?> </td><td class="nbreCoinsExo">`+classMyStat.nbreCoinsExo+`</td></tr>
          <tr class='infoStatStudentOnly'><td><img src="img/light.png" width="25px" style="vertical-align:middle;"> <?php echo __("obtenus par");?> <span style="color:grey;" title="<?php echo __("chaque exercice fait par n'importe quel utilisateur sur une de vos listes vous rapporte 1 points.");?>">royalties</span> </td><td class="nbreCoinsRoyalties">`+classMyStat.nbreCoinRoyalties+`</td></tr>
          <tr class='infoStatStudentOnly'><td><img src="img/light.png" width="25px" style="vertical-align:middle;"> <?php echo __("total");?></td><td class="nbreCoins">`+classMyStat.nbreCoins+`</td></tr>
    </table>
    <hr>
    <div class="chart-container">
			<h3><?php echo __("Exercices réalisés en fonction du temps");?></h3>
			<div id='nbreExoFait'></div>
				<canvas id="chartNbreExoTemps" width="500" height="200"></canvas>
		</div>
    <div class="chart-container">
			<h3><?php echo __("Répartition des exercices faits sur la durée sélectionnée");?></h3>
				<canvas id="chartRepartExo" width="500" height="200"></canvas>
		</div>

		<div class="chart-container infoStatStudentOnly">
			<h3><?php echo __("Nombre de mot en mémoire en fonction du temps");?></h3>
				<canvas id="chartMemo" width="500" height="200"></canvas>
		</div>
	</div>`);

  if(classMyStat.status!="student"){
    $(".infoStatStudentOnly").remove();
  }
  //graph nbre exo faits
  //variable de dataset


  if(parseInt(nbreExoArray.length)!=0)
  {jour_ini=nbreExoArray[0].jour;
  jour_fin=nbreExoArray[nbreExoArray.length-1].jour;
  jour_debut="";


      //organiser data avec Game>Jour
      for(k in nbreExoArray)
      {
        jour=nbreExoArray[k].jour;
        nbreExo=parseInt(nbreExoArray[k].nbreExo);
        game=nbreExoArray[k].name;
        if(jour_debut==""){jour_debut=jour;}
        if(typeof dataExoTmp[game]=="undefined"){dataExoTmp[game]=[];}
        if(typeof dataTotalExoTmp[jour]=="undefined"){dataTotalExoTmp[jour]=0;}
        dataTotalExoTmp[jour]+=nbreExo;
        dataExoTmp[game][jour]=nbreExo;
      }
      //Remplir les date ou on a pas de datas avec 0
      for(j=jour_ini; new Date(j)<=new Date(jour_fin) ;j=moment(j).add(1,"days").format("YYYY-MM-DD"))
      {
        totalCum=0;
        for(game in dataExoTmp)
        {
          if(typeof dataExoTmp[game][j] == "undefined")
          {dataExoTmp[game][j]=0;}
        }
        if(typeof dataTotalExoTmp[j] == "undefined")
        {dataTotalExoTmp[j]=0;}
      }
      //formater les datas pour le charts.
      for(j in dataTotalExoTmp){
        dataTotalExo.push({t:j,y:dataTotalExoTmp[j]});
      }
      for(game in dataExoTmp){
        dataExo[game]=[];
        for(j in dataExoTmp[game])
        {dataExo[game][j]=dataExoTmp[game][j];}
      }

      //remettre le tout dans l'ordre
      dataTotalExo.sort(sortByDate);
      //cumuler les résultats
      TotalCum=0;
      for(k in dataTotalExo)
      {
        TotalCum+=dataTotalExo[k].y;
        dataTotalExoCum.push({t:dataTotalExo[k].t,y:TotalCum});
      }
      
       for(game in dataExo)
       {
         TotalCum=0;
         for(j=today; new Date(j)>=new Date(startingDate) ;j=moment(j).subtract(1,"days").format("YYYY-MM-DD"))
         {
           if(typeof dataExo[game][j]!="undefined")
           {TotalCum+=dataExo[game][j];}
         }
         dataDougnutsNum.push(TotalCum);
         dataDougnutsLabel.push(gameData[game].label);
         dataDougnutsColor.push(gameData[game].color);
       }

      date_ini = new Date(jour_ini);
      date_fin = new Date(jour_fin);
      deltaJour1 = (date_fin - date_ini)/(1000*60*60*24);
      $(".rangeDays").attr("max",deltaJour1);
      date_debut = new Date(jour_debut);

      if(nbre_jour<20){radiExo=4;}else{radiExo=2;}

  }


  configNbreExo = {
  	type: 'line',
  	data: {
  		labels: [],//'0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
  		datasets: [{
  			label: "<?php echo __("Nombre d'exercices total");?>",
  			data: dataTotalExoCum,
  			borderColor: "#3399ff",
  			backgroundColor: 'transparent',
  			fill: false,
  			//cubicInterpolationMode: 'monotone'
  			lineTension: 0
  		}]
  	},
  	options: {
      elements:{point:{radius:4,borderWidth:2}},
  		legend:{display:true},
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
  								min:startingDate,
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


  // for(game in dataExoCum){
  // configNbreExo.data.datasets.push(
  //   {
  //     label: gameData[game].label,
  //     data: dataExoCum[game],
  //     borderColor: "transparent",
  //     pointBackgroundColor:"transparent",
  //     pointborderColor:"transparent",
  //     backgroundColor: gameData[game].color,//"#34ac9540",//
  //     fill: '-1',
  //     //cubicInterpolationMode: 'monotone'
  //     lineTension: 0
  //   });
  // }


  //daughnuts Répartition des exercices travaillés
  var ctxExo = document.getElementById('chartNbreExoTemps').getContext('2d');
  window.ExoChart = new Chart(ctxExo, configNbreExo);

  dataRepartition=[];
  games=[];

  configRepartExo = {
    type: 'doughnut',
    data: {
      datasets: [{
        data: dataDougnutsNum,
        backgroundColor: dataDougnutsColor,
        label: '<?php echo __("Répartitions exercices faits sur la période");?>'
      }],
      labels: dataDougnutsLabel
    },
    options: {
      responsive: true,
      legend: {
        position: 'top',
      },

      animation: {
        animateScale: true,
        animateRotate: true
      }
    }
  };

  var ctx = document.getElementById('chartRepartExo').getContext('2d');
  window.ChartRepartExo = new Chart(ctx, configRepartExo);

  //Graph Nbre mots en mémoire
  var datapointsMotsMemo = [];
  var datapointsMotsMemoPred = [];
  var radiMemo=4;
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
          if(jour_debut==""){jour_debut=jour;}
          datapointsMotsMemo.push({t:jour,y:nbreMots});
        if(k>stats.length-12){
          jour=stats[k].jour;
          Objectif=parseInt(stats[k].nbreMotTotal)+100;
          jourFuture=moment(jour).add(10,"days").format("YYYY-MM-DD");
          //datapointsObjectif.push({t:jourFuture,y:Objectif});
        }
      }
      date_ini = new Date(jour_ini);
      date_fin = new Date(jour_fin);
      deltaJour1 = (date_fin - date_ini)/(1000*60*60*24);
      $(".rangeDays").attr("max",deltaJour1);
      date_debut = new Date(jour_debut);
      today=moment().format("YYYY-MM-DD");
      jourFuture=moment().add(10,"days").format("YYYY-MM-DD");

      for(k=0;k<11;k++)
      {
        thisJour=moment().add(k,"days").format("YYYY-MM-DD");
        thisDay=+new Date(thisJour)/1000;
        NbreMotsPred = OptimalRDs.filter(rk => rk.OptimalRD > thisDay);
        NbreMotsPred=NbreMotsPred.length;
        datapointsMotsMemoPred.push({t:thisJour,y:NbreMotsPred});
      }

      //MemoChart.data.datasets[0].data=datapointsMotsMemo;
      //MemoChart.data.datasets[3].data=datapointsMotsMemoPred;
      //MemoChart.options.legend.display=false;
      //MemoChart.options.tooltips.mode="nearest";
      if(nbre_jour<20){radiMemo=4;}else{radiMemo=2;}
      //MemoChart.options.elements.point.radius=radi;
      //MemoChart.options.elements.point.backgroundColor="#ffffff";
      //MemoChart.options.elements.point.borderWidth=radi/2;
      //MemoChart.options.scales.xAxes[0].time.min=startingDate;
      //myLine.canvas.parentNode.style.height = '128px';
      //MemoChart.update();
  }



  configMemo = {
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
  			label: '<?php echo __("Prédiction mots en mémoire sans travailler");?>',
  			data: datapointsMotsMemoPred,
  			borderColor: "#63B9ff",
  			backgroundColor: 'rgba(0, 255, 0, 0)',
  			borderDash:[3, 3],
  			fill: true,
  			//cubicInterpolationMode: 'monotone'
  			lineTension: 0
  		}]
  	},
  	options: {
      elements:{point:{radius:radiMemo,borderWidth:radiMemo/2}},
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
  								min:startingDateMemo,
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
  var ctxMemo = document.getElementById('chartMemo').getContext('2d');
  window.MemoChart = new Chart(ctxMemo, configMemo);


}
function sortByDate(a,b){
  a = new Date(a.t);
  b = new Date(b.t);
    return a-b;
}
function sortByDateDESC(a,b){
  a = new Date(a.t);
  b = new Date(b.t);
    return b-a;
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
      $(".pageAction").append("<br><div class='BtnStd1'  onclick='renameClass("+class_id+");'><?php echo __("Editer la classe");?></div>");
    }
    $(".pageAction").append("<br><div class='BtnStd1' style='background-color:orange;' onclick='goOutClass("+class_id+");'><?php echo __("Quitter ce cours");?></div>");
    if(result.role=="prof"){
      $(".pageAction").append("<br><div class='BtnStd1'  onclick='addToArchive("+class_id+");'><?php echo __("Archiver cette classe");?></div>");
      //$(".pageAction").append("<br><div class='btn btnAction'  onclick='RemoveFromArchive("+class_info.class_id+");'>Sortir des archives cette classe</div>");
      $(".pageAction").append("<br><div class='BtnStd1' style='background-color:red;margin-top:40px;'  onclick='delClass("+class_id+");'><?php echo __("Supprimer cette classe");?></div>");
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

function openClass(class_id){
  location.href='decks.php?categorie=myClass&class_id='+class_id;
}

function joinPublicClass(class_id){
	$.getJSON("ajax.php?action=joinPublicClass&class_id="+class_id, function(result){
			window.location.href="decks.php?categorie=myClass&class_id="+class_id;
	});
}
var selectedLangForNewClass=1;
function handleID(){
  $('body').append(`<div class='fenetreSombre' onclick='$(this).remove();'>
  <div style='text-align:center;width: 50%;' class='fenetreClaire' onclick='event.stopPropagation();'>
    <img src='img/close.png' class='closeWindowIcon' onclick='$(\".fenetreSombre\").remove();'>
    <h3 style="text-align:left;margin:0 0 10px 10px;padding-left:20px;">
      <img src='img/arrow_left.png' class='backFenetre' onclick='$(\".fenetreSombre\").fadeOut(200,function() { $(this).remove(); });'><?php echo __("Nouvelle classe");?>
    </h3>
    <p>

      <div class='listEleveContainer'>
        <label>Indiquer les prénoms de vos élèves</label>
        <textarea class='listEleves'></textarea>
        <br>
        <button class='BtnStd1' onClick='cutListEleves();'><?php echo __("Suivant");?></button>
      </div>
      <div class='CuttedlistEleveContainer'>
        <div><-</div>
        <label>Liste de vos élèves</label>
        <div class="CuttedList"></div>
        <br>
        <button class='BtnStd1' onClick='generateIDfromList();'><?php echo __("Suivant");?></button>
      </div>

      <div class='IDContainer'>
      </div>
    </p>
  </div>
  </div>`);

}
function createClass(){
  $('body').append(`<div class='fenetreSombre' onclick='$(this).remove();'><div style='text-align:center;width: 50%;' class='fenetreClaire' onclick='event.stopPropagation();'><img src='img/close.png' class='closeWindowIcon' onclick='$(\".fenetreSombre\").remove();'>
  <h3 style="text-align:left;margin:0 0 10px 10px;padding-left:20px;"><img src='img/arrow_left.png' class='backFenetre' onclick='$(\".fenetreSombre\").fadeOut(200,function() { $(this).remove(); });'><?php echo __("Nouvelle classe");?></h3>
  <p>
  <div class='addClassContent newClassPage'>
      <input class='inputInfoClass' id='class_name' type='text' style='display:inline-block;' autocomplete='off' name='class_name' placeholder='<?php echo __("Nom de la classe");?>'/>
      <input class='inputInfoClass' id='promo' type='text' autocomplete='on' name='promo' value='2020-2021' placeholder='année de promo'/>
      <span>
        <?php echo __("Langue enseignée");?> : <span class='lang_name_NewClass'></span>
        <select class="newClass_input_lang" name="class_ids" required></select>
      </span>
      <div class="msgFeedback" style="display:none;"></div>
  <br>
     <button class='BtnStd1' onClick='newClass();'><?php echo __("Créer");?></button>
    </div></p>
  </div></div>`);
  $.getJSON("ajax.php?action=getTargetLang", function(result){
    var SelectizeDeckObj=$('.newClass_input_lang').selectize({
      valueField: 'lang_id',
      labelField: 'lang_name',
      searchField: ['lang_name'],
      placeholder: 'Select language',
      closeAfterSelect: true,
      options: result,
      render: {
        option: function(item, escape) {
          var html = `<div style="padding:3px;">
            <span class="tinyFlag flag_`+item.lang_code2+`"></span>
            <span class="">`+item.lang_name+`</span>
            </div>`;
          return html;
        }
      }
    });
	});

}

function newClass(){
	class_name=$("#class_name").val();
  if(class_name==""){class_name="<?php echo __("classe sans nom");?>";}
	promo=$("#promo").val();
  selectedLangForNewClass=$('.newClass_input_lang').val();
  if(selectedLangForNewClass!=""){
    $.getJSON("ajax.php?action=addClass&class_name="+class_name+"&promo="+promo+"&lang_id="+selectedLangForNewClass, function(result)
  	{
      if(result.status=="ok")
  		{
  		class_id=result.class_id;
      window.location.href='decks.php?categorie=myClass&class_id='+class_id;
      }
      else if(result.status=="limit")
      {alert("Nombre de classes maximum atteint.");}

  	})
    $(".fenetreSombre").remove();
  }
  else {
    $(".newClassPage .msgFeedback").html("Merci de selectionner une langue à apprendre pour la classe").show();
  }

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

		$('.pageCode').html("<h3 style='text-align:center;margin: 50px 0;'><?php echo __('Inviter des participants à rejoindre la classe');?><br>"+class_name+" <br><span style='font-size:0.8em;color:grey;'>"+promo+"</span></h3><?php echo __("Le code pour rejoindre la classe est");?> :<div style='text-align:center;margin:30px 0;'><div class='code'>"+code+"</div></div>"
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
  <h3><img src='img/arrow_left.png' class='backFenetre' onclick='$(\".fenetreSombre\").fadeOut(200,function() { $(this).remove(); });'><?php echo __("Renommer la classe");?></h3>
  <p>	<div class='addClassContent newClassPage'>
      <input class='inputInfoClass' id='class_name' type='text' style='display:inline-block;' autocomplete='off' name='class_name' placeholder='<?php echo __("Nom de la classe");?>' value='`+class_info.class_name+`'/>
      <input class='inputInfoClass' id='promo' type='text' autocomplete='on' name='promo' value='`+class_info.promo+`' placeholder='2020-2021'/>
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
	outConfirm=confirm("<?php echo __("Etes-vous sur de vouloir quitter cette classe ?");?>");
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
	del=confirm("<?php echo __("Etes-vous sur de vouloir supprimer cette classe ?");?>");
  if(del){
  	$(".fenetreSombre").remove();
  	$.getJSON("ajax.php?action=delClass&class_id="+class_id, function(result)
  	{
  		console.log("delete done");
  		window.location.href='decks.php';
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

function resizeStudentGraph(nbre_jour)
{
	startingDate=moment().subtract(nbre_jour,"days").format("YYYY-MM-DD");
	myLine.options.scales.xAxes[0].time.min=startingDate;
	myLine.update();
}
function resizeMyGraph(nbre_jour)
{
	startingDate=moment().subtract(nbre_jour,"days").format("YYYY-MM-DD");
  if(typeof MemoChart!="undefined")
  {
    	MemoChart.options.scales.xAxes[0].time.min=startingDate;
      ExoChart.options.scales.xAxes[0].time.min=startingDate;
    	MemoChart.update();
      ExoChart.update();
      dataDougnutsNum=[];
      dataDougnutsLabel=[];
      dataDougnutsColor=[];
      today=moment().format("YYYY-MM-DD");
      console.log(today,startingDate);
      for(game in dataExo)
      {
        TotalCum=0;
        for(j=today; new Date(j)>=new Date(startingDate) ;j=moment(j).subtract(1,"days").format("YYYY-MM-DD"))
        {
          if(typeof dataExo[game][j]!="undefined")
          {TotalCum+=dataExo[game][j];}
        }
        console.log(game,TotalCum,dataExo[game]);
        dataDougnutsNum.push(TotalCum);
        dataDougnutsLabel.push(gameData[game].label);
        dataDougnutsColor.push(gameData[game].color);
      }

      configRepartExo.data.datasets[0].data=dataDougnutsNum;
      configRepartExo.data.datasets[0].backgroundColor=dataDougnutsColor;
      configRepartExo.data.labels=dataDougnutsLabel;
    	ChartRepartExo.update();
  }

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
