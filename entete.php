<!--<meta name="google-signin-client_id" content="4631499565-5vhpg20lg2741qdoe7r4mvl4uc7qnfor.apps.googleusercontent.com">-->
<?php if(!isset($class_id)){$class_id="";}
include_once ("local_lang.php");
$link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ?
                "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
if($_SERVER['HTTP_HOST']=="localhost"){$link="http://localhost/";}
?>
<!--<link href="css/flags.css" rel="stylesheet">-->
<!--<script src="js/jquery-ui" ></script>-->

<style>
.buttonRetourList img{width:33px;position:relative;top:12px;margin-right:30px;}
.buttonRetourList:hover img{filter:drop-shadow(2px 5px 3px black);}
.buttonRetourCards img{width:33px;position:relative;top:12px;margin-right:30px;}
.buttonRetourCards:hover img{filter:drop-shadow(2px 5px 3px black);}
.flagEntete{position:relative;top:-15px;width:40px; height:30px;background-size:100% 100%;display:inline-block;border-radius:3px;}
.mylangSubMenu,.buttonAutreLang{width:104px !important;}
.mylangSubMenuItem{width:104px !important;position:relative;}
.subMenuLang, .AutreLang{width:auto !important;}
/*#fantome{transition:width 2s,height 4s;position:absolute;top:10px;left:10px;background-image:url(http://pngimg.com/uploads/ghost/ghost_PNG52.png);width:100px;height:150px;background-size:100% 100%;}
#fantome:hover{width:200px;height:300px;}*/
.separateurGauche:before{
  content:"";
  border-right:2px grey solid;
  height:30px;
  position: absolute;
  top: 15px;
}
.separateurDroite:before{
  content:"";
  border-right:2px grey solid;
  height:30px;
  position: absolute;
  top: 15px;
  right:0;
}
.invitationQuizContainer{padding:10px;position:fixed;bottom:0;right:0;width:440px;max-width:100%;z-index: 9;}
.invitationQuiz_item{position:relative;color:grey;width:400px;padding:10px;max-width:100%;margin:20px 0;background-color:white;box-shadow:0 0 10px gray;border-radius:10px;}
.invitationQuiz_item .titleTile{color:black;}
.subMenuClass{width:260px;}
.unclickable{color:black !important;}
.desktop .sectionTitle{color:black !important; background-color:grey;padding:10px;}
.myclassesSubMenu{display: flex;
    flex-direction: column;}
</style>
<!--<div id="fantome"></div>-->
<script src="socket.io/socket.io.js"></script>
<script>
//Conversion lang en Langues

//Objet volant pour thème
/*
vx=1;
vy=2;
setInterval(function(){
  newY=parseInt($("#fantome").css("top"))+vy;
  $("#fantome").css("top",newY+"px");
  newX=parseInt($("#fantome").css("left"))+vx;
  $("#fantome").css("left",newX+"px");
  if(newY+300>window.innerHeight || newY<0){vy=-vy;vx=Math.round((Math.random()*4)-2);}
  if(newX+200>window.innerWidth || newX<0){vx=-vx;vy=Math.round((Math.random()*4)-2);}
},30);
*/
if((typeof window!=='undefined')&&(typeof window.onload!=='undefined')){
	 //socket=io.connect('www.exolingo.com/socketLink');
	 socket=io.connect('/socketLink');
 }
</script>
<nav id="navbar">
      <div class="menu">
        <ul class="desktop" onclick="$('.mobile').removeClass('open');">
          <div class="left" style="padding-left:20px;">
            <li style="float:left;" class="enteteBtn buttonRetourList enteteCards enteteEdit enteteStats enteteProfil" onclick="location.href='decks.php?categorie=last'"><img src="img/back2.png"></li>
            <li style="float:left;" class="enteteBtn buttonRetourCards enteteGames" style="display:none;" onclick="ini_memory();"><img src="img/back2.png"></li>
            <!--<li style="float:left;" class=""><img src="img/logo.png" width="45px" style="margin-right:20px;position:relative;top:7px;"></li>-->
            <!-- <li style="float:left;" class="enteteBtn buttonMesLang enteteClasses enteteDecks enteteLangs enteteStats enteteEdit">
              <a href="#"><div class="flagSubMenu flagEntete"></div><img src="img/arrow_down.png" width="20px" style="float:right;margin-left:10px;margin-right:15px;"></a>
              <ul class="submenu mylangSubMenu">
                <li class="buttonAutreLang" style="">
                  <a href="lang.php" class="AutreLang" ><?php echo __("Autre");?></a>
                </li>
              </ul>
            </li> -->
            <!-- <li style="float:left;" class="enteteBtn buttonMesClasses enteteClasses enteteDecks enteteStats">
              <a href="decks.php?categorie=myDecks">
                <img src='img/icon_perso.png' title='<?php echo __("Ma bibliothèque");?>' style='width:25px;'>
              </a>
            </li> -->
            <li style="float:left;" class="enteteBtn buttonMesClasses enteteClasses enteteDecks enteteStats">
              <a href="#" class="ClassFlag">

              </a>
            </li>
            <li style="float:left;" class="enteteBtn buttonMesClasses enteteClasses enteteDecks enteteStats">
              <a href="#" style="display:flex;"><span class="TitleClassMenu" style="font-size: 1.5em;line-height: 22px;"><?php echo __("Mes classes");?></span><img src="img/arrow_down.png" width="20px" style="float:right;margin-left:10px;margin-right:15px;"></a>
              <ul class="submenu myclassesSubMenu" style="max-height: 80vh;overflow: overlay;display:flex;">
                <!--<li><a href='decks.php?categorie=myDecks' style="color:var(--mycolor2);"><?php //echo __("Mes listes");?></a></li>-->
                <li style="order:-1;" class="codeEntete enteteBtn enteteNotEleve enteteRoleProf enteteNotAutodidact enteteDecks" onclick="windowClass('code');">
                  <a href="#" class="clickable">
                  Code : <span class="miniCode" style="padding:8px;">ABCD</span>
                  </a>
                </li>
                <li style="order:-1;" class="settingEntete enteteBtn enteteNotAutodidact enteteDecks" onclick="windowClass('settings');">
              		<a href="#">
                  Paramètres de la classe
              		</a>
              	</li>
                <li style="order:-1;" class="marksEntete enteteBtn enteteNotEleve enteteRoleProf enteteNotAutodidact enteteDecks" onclick="windowClass('report');">
              		<a href="#" class="clickable">
                  Notes et Rapports
              		</a>
              	</li>
                <li style="order:9;" class="buttonAddClass enteteNotAutodidact">
                  <a href="#" onclick="showJoinClassWindow();" class="JoinClass"><?php echo __("Rejoindre une classe");?></a>
                </li>
                <li  style="order:9;" class="buttonAddClass enteteNotAutodidact enteteNotEleve">
                  <a href="#" onclick="createClass();" class="createClass"><?php echo __("Créer une nouvelle classe");?></a>
                </li>
                <!--<li class=" buttonAddClass enteteNotAutodidact enteteNotEleve">
                  <a href="#" onclick="showJoinClassWindow();" class="JoinClass"><?php //echo __("Rejoindre le cours d'un collègue");?></a>
                </li>-->
                <!-- <li  class=" buttonAddClass enteteNotProf">
                  <a href="classes.php" class="JoinClass"><?php echo __("Rejoindre une classe ExoLingo");?></a>
                </li> -->
                <!--<li class="buttonAddClass" style="">
                  <a href="classes.php" class="JoinClass" ><?php //echo __("Mes cours");?></a>
                </li>-->
                <li style="order:8;" class="subMenuArchiveContainer enteteNotEleve enteteNotAutodidact"><a href="#" onclick="$('.submenuArchive').slideToggle();"><?php echo __("Cours archivés");?><img src="img/arrow_down.png" width="20px" style="float:right;margin-left:10px;margin-right:15px;"></a>
                  <div class="submenuArchive" style="display:none;border-bottom:3px grey solid;">

                  </div>
                </li>
              </ul>
            </li>

            <!-- <li class="separateurGauche enteteBtn settingClass enteteNotEleve enteteNotAutodidact enteteDecks"  onclick="windowClass('settings');"><a href="#" style="color:var(--mycolor2);"><?php echo __("Rapports et actions");?></a></li> -->



          </div>
          <div class="centerEntete">
            <!--<li onclick="inviter();" class="enteteBtn buttonInvitation"><a href="#">Invitation</a></li>
            <li class="enteteBtn buttonBadgeStat"><a href="myClass.php">Badges & Stat</a></li>
            <li class="enteteBtn buttonMyDecks" onclick=""><a href="#" onclick="location.href='decks.php?categorie=myDecks';">Mes Listes</a></li>
            <li class="enteteBtn buttonListes"><a href="decks.php?categorie=myClass">Listes</a>
              <ul class="submenu">
                <li class="subButtonAllDecks"><a href='decks.php'>Toutes les listes</a></li>
                <li class="subButtonMyClass"><a href='decks.php?categorie=myClass'>Listes de la classe</a></li>

                <li class="subButtonMyDecks"><a href='decks.php?categorie=myDecks'>Mes Listes</a></li>
              </ul>
            </li>
            <li class="enteteBtn buttonListesIn"><a href="#" onclick="categorie='myClass';getList('myClass');">Listes</a>
              <ul class="submenu">
                <li class="subButtonAllDecks"><a href='#' onclick="categorie='';getList(categorie);">Toutes les listes</a></li>
                <li class="subButtonMyClass"><a href='#' onclick="categorie='myClass';getList(categorie);">Listes de la classe</a></li>
                <li class="subButtonMyDecks"><a href='#' onclick="categorie='myDecks';getList(categorie);">Mes Listes</a></li>
              </ul>
            </li>-->
          </div>
          <div class="right">
            <!--<li class="enteteBtn new_deck_btn"><a href="edit_deck.php?deck_id=0"><?php //echo __("+ Nouvelle liste");?></a></li>-->
            <!-- <li  class="separateurGauche enteteDecks enteteBtn">
              <a href="#" onclick="windowClass('welcome');" class="createClass"><img src="img/settings.png" style="width:30px;"></a>
            </li> -->
            <li class="enteteBtn enteteModule enteteDecks enteteNotAutodidact enteteNotProf"><a class="" href="#"><?php echo __("En plus");?><img src="img/arrow_down.png" width="20px" style="float:right;margin-left:10px;"></a>
            <ul class="submenu">
                <li class="enteteBtn buttonJoinQuizEleve"><a href="quizEleve.php" class="onglet_quizEleve"><?php echo __("Rejoindre un quiz");?></a></li>
                <!--<li class="enteteBtn buttonJoinBoxEleve"><a href="boxEleve.php" title='<?php //echo __("Mise en Boîte");?>' class="onglet_boxEleve"><?php //echo __("Rejoindre une Mise en boîte");?></a></li>-->
                <li><a href='base.php' class=''><?php echo __("Rédaction");?></a></li>
                <li><a href='onlineRecorder.php' class=''><?php echo __("Enregistrement");?></a></li>
            </ul>
          </li>
          <!--<li class="separateurGauche enteteBtn enteteCoin enteteDecks enteteNotProf">
            <a class="" href="#" style="position:relative;padding-left:50px;">
              <img  src="img/pileIcon.png" style="width:30px;position:absolute;top:24px;left:5px;"><span id="objectif"></span>
            </a>
          </li>-->

          <li style="float:right;min-width:200px;" class="enteteBtn buttonProfil enteteDecks enteteLangs enteteClasses enteteEdit enteteStats enteteProfil">
            <a href='#' class="user_name_entete" style="width:200px;"></a>
            <ul class="submenu">
              <li><a href='profile.php'><?php echo __("Compte");?></a></li>
              <li><a href='#' onclick='signOut();'><?php echo __("Deconnexion");?></a></li>

            </ul>
          </li>



        </div>
        </ul>
        <div id="openMenu">
          <div class="left" style="padding-left:10px;">
            <!-- <li class="enteteBtn buttonMesLang enteteClasses enteteDecks enteteLangs enteteStats enteteEdit" style="margin:0;padding:0;">
              <a href="lang.php" style="margin:0;padding:21px 0;"><div class="flagSubMenu flagEntete"></div></a>
            </li> -->
            <li style="float:left;" class="enteteBtn buttonRetourList enteteCards enteteStats enteteEdit enteteProfil" onclick="location.href='decks.php?categorie=last'"><img src="img/back2.png"></li>
            <li style="float:left;" class="enteteBtn buttonRetourCards enteteGames" style="display:none;" onclick="ini_memory();"><img src="img/back2.png"></li>
          </div>
          <div class="centerEntete">

            <li href="#" id='indicateurClassMenu' class="enteteDecks">
              <span class="class_name" style="white-space: nowrap;padding:0px;font-size: 1.2em;line-height: 60px;color:var(--mycolor2);"><?php echo __("Mes classes");?></span>
            </li>
            <!--<li><img src="img/logo.png" width="45px" style="position:relative;top:5px;"></li>-->
          </div>
          <div class="right">
            <li id="openMobileMenu" class="enteteGames enteteCards enteteDecks enteteClasses enteteLangs enteteEdit enteteStats enteteProfil"><img src="img/settings.png" width="45px" style="position:relative;padding:10px 0; height:100%;"></li>
          </div>
        </div>

        <ul class="mobile" onclick="$('.mobile').removeClass('open');$('.overlay').hide();">
          <li href="#" style="padding:20px 0;" class="codeEntete"><img src="img/key.png" width="30px" style="vertical-align: middle;"><span class="miniCode" style="margin:10px;padding:10px;font-size: 1em;border:2px solid white;color:white;"><?php ?>TEST</span></li>
          <!-- <li class="enteteBtn enteteDecks"><a href="#" onclick="windowClass('welcome');" style="color:white;"><?php echo __("Accueil");?></a></li> -->
          <!-- <li class="enteteBtn settingClass enteteNotEleve enteteNotAutodidact enteteDecks"><a href="#" onclick="windowClass('settings');" style="color:white;"><?php echo __("Rapports et actions");?></a></li> -->

          <li style="order:-1;" class="settingEntete enteteBtn enteteNotAutodidact enteteDecks" onclick="windowClass('settings');">
            <a href="#">
            Paramètres de la classe
            </a>
          </li>
          <li style="order:-1;" class="marksEntete enteteBtn enteteNotEleve enteteRoleProf enteteNotAutodidact enteteDecks" onclick="windowClass('report');">
            <a href="#">
            Notes et Rapports
            </a>
          </li>



          <li  class="buttonAddClass enteteNotAutodidact enteteNotProf">
            <a href="#" onclick="showJoinClassWindow();" class="JoinClass"><?php echo __("Rejoindre la classe d'un professeur");?></a>
          </li>
          <li  class="buttonAddClass enteteNotAutodidact enteteNotEleve">
            <a href="#" onclick="windowClass('welcome');" class="createClass"><?php echo __("Créer une nouvelle classe");?></a>
          </li>

          <li class="enteteBtn buttonJoinQuizEleve enteteDecks enteteClasses enteteEdit enteteCards enteteLang enteteStats enteteNotAutodidact enteteNotProf"><a href="quizEleve.php" class="onglet_quizEleve"><?php echo __("Rejoindre un quiz");?></a></li>
          <span style="" class="enteteBtn buttonMesClasses enteteDecks">
            <span class="submenu myclassesSubMenu">
              <li class="buttonAddClass enteteDecks enteteCards" style="">
                <a href="classes.php" class="JoinClass" ><?php echo __("Mes classes");?></a>
              </li>
            </span>
          </span>




          <li style="" class="enteteBtn buttonStats enteteDecks enteteNotProf"><a href="#" onclick="windowClass('myStats');" class="mystats"><?php echo __("Mes statistiques et trophées");?></a></li>';
          <li style="" class="enteteBtn buttonProfil enteteDecks enteteCards enteteClasses enteteStats enteteLangs enteteEdit"><a href="profile.php"><?php echo __("Mon profil");?></a></li>

          <li class="enteteDecks enteteCards enteteClasses enteteLangs enteteStats enteteProfil enteteEdit"><a href='#' onclick='signOut();'><?php echo __("Deconnexion");?></a></li>


        </ul>
      </div>
    </nav>
    <div class="overlay"></div>
    <div class="invitationQuizContainer"></div>


    <script type="text/javascript" src="js/menu-breaker.js"></script>
    <!--<script src="https://apis.google.com/js/platform.js?onload=start" async defer></script>-->
    <script>

      if(typeof userType!="undefined")
      {
        if(userType=="prof")
        { $(".enteteNotProf").hide();}
        else if(userType=="autodidact")
        { $(".enteteNotAutodidact").hide();}
        else if(userType=="eleve")
        { $(".enteteNotEleve").hide();}
      }
      else {
        $(".enteteNotProf").hide();
        $(".enteteNotAutodidact").hide();
        $(".enteteNotEleve").hide();
      }

    var xp_lvl_scale=[0,100,250,600,1500,3000,6000,10000,20000,45000,1000000,100000000000000];
    function updateXp(XPTotal)
    {
      lvl_color=["#7F7FFF","#FF2AAA","#FFAA00","#7FFF2A","#2AFFAA","#7F2AFF","#FF007F","#FF7F00","#FF3000","#FF7FFF","#0000FF","#FF0000"];

      for(k in xp_lvl_scale)
      {
        k=parseInt(k);
        XPTotal=parseInt(XPTotal);
        if(XPTotal<=parseInt(xp_lvl_scale[k+1]) && XPTotal>=parseInt(xp_lvl_scale[k]))
          { lvl=parseInt(k)+1;
            xp_max=xp_lvl_scale[k+1];
            xp_min=xp_lvl_scale[k];
          $(".xp_lvl,.xp_lvl_deck").html("<?php echo __("Niveau");?> "+lvl);
          xp_actuel=(XPTotal-xp_lvl_scale[k]);
          ecart_next_lvl=(xp_lvl_scale[k+1]-xp_lvl_scale[k]);
          $(".xp_num").html(XPTotal);
          $(".xpBilan").html(XPTotal+"/"+xp_max)
          $(".xp_min").html(xp_min+"<img src='img/lightBlack.png' width='10px' style='vertical-align: bottom;'>");
          $(".xp_max").html(xp_max+"<img src='img/lightBlack.png' width='10px' style='vertical-align: bottom;'>");
          $(".xp_max_deck").html(xp_max);
          pcentXP=Math.round(xp_actuel*100/ecart_next_lvl);
          $(".XPbar").css("width",pcentXP+'%');
          $(".XPbar").css("background-color",lvl_color[k]);
          lvlTexture=lvl%3;
          lvlAvatar=Math.round(lvl/2);
          //$(".avatar img").attr("src","img/lvl"+lvlAvatar+".png");
          $(".XPbar").css("background-image","url(img/textureXP"+lvlTexture+".png)");
          $("#XPbarContainer").addClass("XP_shine");
          $(".XP_shine:before").css("border-color",lvl_color[k]);
          $(".XP_shine").on('animationend', function(e) {
          //$(".XP_shine").removeClass("XP_shine");
          });
          }
      }

    }
    function getLvlFromXp(XPTotal)
    {
      XPTotal=parseInt(XPTotal);
      for(k in xp_lvl_scale)
      {
        k=parseInt(k);
        if(XPTotal<=parseInt(xp_lvl_scale[k+1]) && XPTotal>=parseInt(xp_lvl_scale[k]))
          { lvl=parseInt(k)+1;}
      }
      return lvl;
    }
    /*function start(){
      console.log("func start");
      gapi.load('auth2', function() {
        console.log("start");
        auth2 = gapi.auth2.init({
          client_id: '4631499565-5vhpg20lg2741qdoe7r4mvl4uc7qnfor.apps.googleusercontent.com',
          // Scopes to request in addition to 'profile' and 'email'
          //scope: 'additional_scope'
        });
      });
    }
    var revokeAllScopes = function() {
      auth2.disconnect();
    }*/
    //function signOut(){revokeAllScopes();location.href="logout.php";}
    function signOut(){location.href="logout.php";}
console.log("menu-breaker");
$(".buttonAlert").hide();
$(".buttonEditDeck").hide();
$(".buttonHome").remove();
$(".buttonMyDecks").hide();
$(".buttonBadgeStat").hide();
$(".buttonListes").hide();
$(".buttonInvitation").hide();
$(".buttonListesIn").hide();

$(".buttonRetourCards").hide();
$('.onglet_quizEleve').on("click",function(){location.href="quizEleve/"+user_id});

function joinClassWithCode(){
	code=$(".inputInfoClass").val();
  location.href='joinClass.php?code='+code;
}
function joinClassWithCode2(){
  //check if the code existsif not put error in .msgErrorCode
  code=$(".inputCodeMobile").val();
  if(code.length==4){
     $.getJSON("ajax.php?action=checkifcode&code="+code, function(result)
	    {
        if(result.status=="ok"){location.href='joinClass.php?code='+code;}
        else if(result.status=="bad code"){$(".msgErrorCode").html("<?php echo __("Il y a aucune classe avec ce code.");?>");}
        else if(result.status=="already in"){location.href="decks.php?categorie=myClass&class_id="+result.class_id}
      });
  }
  else{$(".msgErrorCode").html("<?php echo __("Le code est composé de quatre lettres.");?>"+code+"-"+code.length);}
}
function joinClassWithCode3(){
  //check if the code existsif not put error in .msgErrorCode
  code=$(".inputCodeMobile3").val();
  if(code.length==4){
     $.getJSON("ajax.php?action=checkifcode&code="+code, function(result)
	    {
        if(result.status=="ok"){location.href='joinClass.php?code='+code;}
        else if(result.status=="bad code"){$(".msgErrorCode").html("<?php echo __("Il y a aucune classe avec ce code.");?>");}
        else if(result.status=="already in"){location.href="decks.php?categorie=myClass&class_id="+result.class_id}
      });
  }
  else{$(".msgErrorCode").html("<?php echo __("Le code est composé de quatre lettres.");?>"+code+"-"+code.length);}
}

function showJoinClassWindow()
{$('.fenetreSombre').remove();
$('body').append(`<div class='fenetreSombre' onclick='$(this).remove();'><div style='text-align:center;' class='fenetreClaire' onclick='event.stopPropagation();'>
<img src='img/close.png' class='closeWindowIcon' onclick='$(".fenetreSombre").remove();'>
<form autocomplete="off" onSubmit="return false;">
<h2 style='text-align:left;margin:0 0 50px 0;'><?php echo __("Rejoindre une classe");?></h2>
<p><?php echo __("Ecrire le code de la classe (donné par votre professeur)");?></p>
<input type='text' size='5' class='inputCodeMobile3' placeholder='- - - -' style='' onkeyup='if(this.value.length>5){this.value=this.value.substr(0,5);}'><br><br>
<div class="msgErrorCode"></div>
<button type='submit' class='invitationBtn' onclick='joinClassWithCode3();'><?php echo __("Valider");?></button></form>
</div></div>`);
$(".inputCodeMobile3").focus();
}
function putTargetLanguageInEntete()
{
  $(".mylangSubMenu").hide();
  $.getJSON("ajax.php?action=getUserTargetLang", function(result)
  {
    if(result.length>0){
      $(".flagEntete").addClass("flag_"+result[0].lang_code2);
    }
    $('.desktop').menuBreaker();
  });
}
function langUpdateButton()
{
  //$(".mylangSubMenuItem").remove();
  $.getJSON("ajax.php?action=getUserTargetLang", function(result)
  {
    // for(langRk=1;langRk<result.length;langRk++)
    // {lang=result[langRk];
    //   if($(".subMenuLang_"+lang.lang_id).length==0)
    //   {
    //     htmlLang="<li class='mylangSubMenuItem subMenuLang_"+lang.lang_id+"'><a href='#' onclick='changeLanguage("+lang.lang_id+");'><div class='flagSubMenu flag_"+lang.lang_code2+"'></div></a><img src='img/close.png' class='closeWindowIcon' onclick='removeLang("+lang.lang_id+")'></li>";
    //     $('.mylangSubMenu').prepend(htmlLang);
    //   }
    // }
    if(result.length==0){location.href='lang.php';}
      //$(".flagEntete").addClass("flag_"+result[0].lang_code2);
      //classesUpdateButton(result[0].lang_id);
    $('.desktop').menuBreaker();
  });
  classesUpdateButton();
}
function changeLanguage(lang_id){
  $.getJSON("ajax.php?action=changeTargetLang&lang_id="+lang_id, function(result)
  {
  //location.href=window.location.pathname+"?target_lang="+lang_id;
  location.href=window.location.pathname+"?categorie=last";
  });
}
function removeLang(lang_id){
  $(".subMenuLang_"+lang_id).remove();
  $.getJSON("ajax.php?action=removeTargetLang&lang_id="+lang_id, function(result)
  {});
}
function showCodeInEntete()
{code=class_info.code;
  $(".miniCode").html(code);

}
function classesUpdateButton()
{
  $.getJSON("ajax.php?action=getMyClasses", function(result)
  {
    console.log(result);
    myClasses=result;
    console.log(myClasses);
    for(idC in myClasses)
    {
      socket.emit('join_class', idC);
      myclass_name=myClasses[idC]["class_name"];
      myclass_promo=myClasses[idC]["promo"];
      myclass_role=myClasses[idC]["role"];
      myclass_status=myClasses[idC]["status"];
      myclass_enroll=myClasses[idC]["enroll"];
      myclass_lang_code2=myClasses[idC]["lang_code2"];
      htmlClass="";
      if(myclass_status=="perso")
      {
      htmlClass=`
        <li class='subMenuClass subMenuClass_`+idC+`' style='order:1;'>
          <a href='decks.php?class_id=`+idC+`&categorie=myClass'>
            <img src='img/icon_perso.png' style='width:10%;margin:0 5%;vertical-align:middle;'>
            <div style='display:inline-block;vertical-align:middle;'>
              <span><?php echo __("Ma bibliothèque");?></span><br>
            </div>
          </a>
        </li>`;
      }
      if(myclass_status=="ok" || myclass_status=="archive")
      {
      htmlClass=`
        <li class='subMenuClass subMenuClass_`+idC+`' style='order:2;'>
          <a href='decks.php?class_id=`+idC+`&categorie=myClass'>
            <span class='tinyFlag flag_`+myclass_lang_code2+`'></span>
            <img src='img/icon_`+myclass_role+`.png' title='`+myclass_role+`' style='width:10%;margin:0 5%;vertical-align:middle;'>
            <div style='display:inline-block;vertical-align:middle;'>
              <span>`+myclass_name+`</span><br>
              <span style='font-size:0.9em;'>`+myclass_promo+`</span>
            </div>
          </a>
        </li>`;
      }
      if(myclass_status=="archive")
      { //$('.myclassesSubMenu').append(htmlClass);
         $('.submenuArchive').append(htmlClass);
         $('.submenuArchive a').attr("href","#");
        $(".subMenuClass_"+idC+" a").append("<img src='img/unarchive.png' style='width:20px;margin:0 10px;vertical-align:middle;float:right;' onclick='RemoveFromArchive("+idC+");' title='<?php echo __("Retirer des archives");?>'>");
      }
      else if((myclass_status=="ok" || myclass_status=="public") && myclass_enroll==1){$('.myclassesSubMenu').append(htmlClass);}
      else if(myclass_status=="perso"){$('.myclassesSubMenu').prepend(htmlClass);}
      else if(myclass_status=="explore"){$('.myclassesSubMenu').prepend(htmlClass);}
    }
    $('.subMenuArchiveContainer').appendTo('.myclassesSubMenu');
    $('.desktop .buttonAddClass').appendTo('.myclassesSubMenu');
    //check if there is quizzes
    checkQuiz();
    if($('.submenuArchive li').length==0){$('.submenuArchive').parent().remove();}
    //$('.submenuArchive').parent().appendTo(".myclassesSubMenu:eq(0)");
  $('.desktop').menuBreaker();
  });
}
socket.on('newQuizForYou', function(){console.log("newQuizForYou");checkQuiz()});
socket.on('closeQuizForYou', function(){console.log("closeQuizForYou");checkQuiz()});
function checkQuiz(){
  $('.invitationQuiz_item').remove();
  console.log("newQuizForMe");
  $.getJSON("ajax.php?action=getAllGames", function(data){
    console.log(data);

  for(i in data){
    quiz_class_name=data[i].class_name;
    quiz_id=data[i].quiz_id;
    quiz_class_promo=data[i].promo;
    quiz_prof_name=data[i].prof_name;
    quiz_deck_name=data[i].deck_name;
    quiz_deck_hasImage=data[i].hasImage;
    if(quiz_deck_hasImage>0){urlDeckImage="deck_img/deck_"+quiz_deck_hasImage+".png";}
    else{urlDeckImage="img/default_deck.png";}
    $('.invitationQuizContainer').append(
      `<div class="invitationQuiz_item Shinytile">
        <img src='img/close.png' class='closeWindowIcon' onclick='$(this).closest(".invitationQuiz_item").fadeOut(200,function() { $(this).remove(); });'>
        <img src="img/stat2.png" class="titleIcon"><h3 class="titleTile"><?php echo __("Quiz en classe !");?></h3>
        <div>
          <div style="margin:auto;">
            <p><?php echo __("Votre professeur")?> `+quiz_prof_name+`(`+quiz_class_name+`) <?php echo __("vous invite à rejoindre un quiz en classe sur :");?></p>
            <div style="display:flex;"><img src=`+urlDeckImage+` style="object-fit: cover;width:60px;height:60px;margin:auto;"><div style="color:black;margin:auto;">`+quiz_deck_name+`</div></div>
            <div style="text-align:right;"><button onClick="location.href='quizEleve.php?quiz_id=`+quiz_id+`';" class="btnStd1" style="width:auto;margin:10px;"><?php echo __("Rejoindre le quiz");?></button></div>
          </div>
        </div>
      </div>`);
  }
});
}

function inviter()
{
    code=class_info.code;
    $('.fenetreSombre').remove();
    $('body').append("<div class='fenetreSombre' onclick='$(this).remove();'><div class='fenetreClaire' onclick='event.stopPropagation();'><img src='img/close.png' class='closeWindowIcon' onclick='$(\".fenetreSombre\").remove();'>"
    +"<h1 style='text-align:center;margin:0 0 50px 0;'><?php echo __("Inviter des participants");?></h1><?php echo __("Le code pour rejoindre la classe est");?> :<div style='text-align:center;margin:30px 0;'><div class='code'>"+code+"</div></div>"
    +"<br><div><?php echo __("Le lien pour rejoindre la classe (à envoyer par mail aux élèves/professeurs)");?> :<div style='text-align:center;margin:30px 0;'><input type='text' id='inputCodeLink' readonly value='www.exolingo.com/joinClass.php?code="+code+"'><div class='copyLink' onclick='copyLink();'><?php echo __("Copier");?></div></div></div></div></div>")
}

function goTuto(elem,comment)
{
if($(elem).length){
  $(elem).show();
  $("#coverTuto").remove();
  pos=$(elem).offset();
  width=$(elem).innerWidth();
  height=$(elem).innerHeight();
  pos2={left:pos.left+width,top:pos.top+height};
  posMiddle={left:pos.left+0.5*width,top:pos.top+0.5*height};
  $("body").append("<div id='coverTuto' onclick='$(this).remove();'><div style='margin:auto;'>"+comment+"</div>")
  $("#coverTuto").css("clip-path","clip-path: polygon(0% 0%, 0% 100%, 25% 100%, 25% 25%, 75% 25%, 75% 75%, 25% 75%, 25% 100%, 100% 100%, 100% 0%)");
  document.getElementById("coverTuto").style.clipPath="polygon(0% 0%, 0% 100%, "+pos.left+"px 100%, "+pos.left+"px "+pos.top+"px, "+pos2.left+"px "+pos.top+"px, "+pos2.left+"px "+pos2.top+"px, "+pos.left+"px "+pos2.top+"px, "+pos.left+"px 100%, 100% 100%, 100% 0%)";
  $("#coverTuto").css("background-image","radial-gradient(circle at "+posMiddle.left+"px "+posMiddle.top+"px, #3c6da480, #0c73e3)");
  }
}

function toTitleCase(str) {
	return str.replace(/\w\S*/g, function(txt){
			return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
	});
}
fullUserName=toTitleCase(fullUserName);
$(".user_name_entete").html(fullUserName);
/*
var lastScrollTop = 0;
    $(window).on('scroll', function() {
        st = $(this).scrollTop();
        if(st < lastScrollTop) {
            console.log('up 1');
            //$("#navbar").slideDown();
            $("#navbar").addClass("navSlideUp");
            $("#navbar").removeClass("navSlideDown");
        }
        else {
            console.log('down 1');
            //$("#navbar").slideUp();
            $("#navbar").addClass("navSlideDown");
            $("#navbar").removeClass("navSlideUp");
        }
        lastScrollTop = st;
    });*/

    $('.desktop').menuBreaker();
    $(window).on('load resize', function () {
    $('.mobile').height($(window).height() - $('nav').height());
    $('.desktop').menuBreaker();
    });


    $.getJSON("ajax.php?action=getCoinsUser", function(result){
      nbreCoinsToday=result.nbreCoinsToday;
      nbreCoins=result.nbreCoins;
      //$('.nbreCoins').html(nbreCoins);
      updateXp(nbreCoins);
    });
    </script>
