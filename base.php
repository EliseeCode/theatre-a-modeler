<?php
include_once ("db.php");
session_start();
include_once ("local_lang.php");
$explodeURI=explode('/',$_SERVER['REQUEST_URI']);
$_SESSION['url']=end($explodeURI);
if(!isset($_SESSION['user_id'])){header("location:checkLoginCookie.php");exit();}

    // Makes it easier to read
		//if(!isset($_SESSION['user_id'])){header('Location: logout.php');}
	  $user_id = $_SESSION['user_id'];
    $first_name = $_SESSION['first_name'];
    $last_name = $_SESSION['last_name'];
    $email = $_SESSION['email'];
    $active = $_SESSION['active'];
		$type = $_SESSION['type'];
		$classe = $_SESSION['classe'];
		echo "<script>fullUserName='".$first_name." ".$last_name."';</script>";
		echo "<script>type='".$type."';</script>";
		echo "<script>user_id=".$user_id.";</script>";

		$sql="SELECT distinct verbe FROM verbes WHERE 1";
		$verbs=array();
		$result = $mysqli->query($sql);
		while ($row = $result->fetch_assoc()) {
		$verbs[]=$row['verbe'];}
			echo "<script>allVerbs=".json_encode($verbs).";</script>";
		$result->free();


?>
<!DOCTYPE html>
<html >
 <head>


    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>base</title>
    <!-- Bootstrap -->
	  <link href="css/main.css?ver=<?php echo filemtime('css/main.css');?>" rel="stylesheet">
		<link href="css/styleEntete.css?ver=<?php echo filemtime('css/styleEntete.css');?>" rel="stylesheet">
	  <link href="css/deck.css?ver=<?php echo filemtime('css/deck.css');?>" rel="stylesheet">
	  <link href="css/card.css?ver=<?php echo filemtime('css/card.css');?>" rel="stylesheet">
		<link href="css/myStyle.css?ver=<?php echo filemtime('css/myStyle.css');?>" rel="stylesheet">
		<link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16" />
		<link rel="stylesheet" type="text/css" href="css/navStyle.css"/>
		<link rel="stylesheet" type="text/css" href="css/rewardStyle.css"/>
		<script src="js/jquery-3.3.1.min.js"></script>
		<link href="css/styleLanguage.css?v8" rel="stylesheet" type="text/css" media="screen"/>
		 <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<script type="text/javascript" src="js/jquery-ui.js"></script>
		<script src='js/cookiesManager.js'></script>
		<script src="js/jquery-ui.js"></script>
		<script type="text/javascript" src="online-check/tiny_mce/tiny_mce.js"></script>
		<script type="text/javascript" src="online-check/tiny_mce/plugins/atd-tinymce/editor_plugin3.js?v3014013"></script>
		<script src="js/recorder.js"></script>
		<style>
      .navbar{
        margin-bottom:0;
        border-radius:0;
      }
			#menu_checktext_spellcheckermenu{width:300px;}
			#checkform{margin-top:40px;width:80%;max-width:1000px;display:inline-block;box-shadow:0 0 3px grey;}
			#checktext_path_row{height:0;}
			#MyConjugBtn{display:inline-block;vertical-align:middle;}
			#MycheckBtn,#MyConjugBtn{border: 0px;
    color: #fff;
    font-size: 18px;
    padding: 7px 20px;
    background-color: var(--mycolor2);
    cursor: pointer;
		text-align:center;}
		.mySubmitBtn{display:block;width:300px;max-width:20%;float:right;}
		#Myrecorder{
			background-color: lightgrey;
    text-align: center;
    padding: 10px;
    cursor: pointer;
    position: absolute;
    bottom: 40px;
    right: 40px;
    border-radius: 50%;
		}
			#Myrecorder:hover{background-color:var(--mycolor2);}
		.fullscreen-toggle {
		display:none;
		position: absolute;
    bottom: 25px;
    right: 20px;
    background-color: #F0F0F0;
		border:3px solid #F0F0F0;
		background-image:url(img/fullscreen_icon.png);
		background-size:cover;
		width:25px;
		height:25px;
		}
		.fullscreen-toggle a:hover{background-color: lightgrey;border:3px solid lightgrey;}

		#clavier{display:inline-block;float:left;}
		.touche{display:inline-block; color:var(--mycolor2);cursor:hand;margin:4px;padding:4px; background-color:white;}
		.touche:hover{color:black;}
    </style>
		<script>
		// translation of language variant names:
    tinyMCE.init({
        mode : "textareas",
        plugins                     : "AtD,paste",
        directionality              : 'auto',   // will display e.g. Persian in right-to-left
				height : "480",
        //Keeps Paste Text feature active until user deselects the Paste as Text button
        paste_text_sticky : true,
        //select pasteAsPlainText on startup
        setup : function(ed) {
            ed.onInit.add(function(ed) {
                ed.pasteAsPlainText = true;
                doit();  // check immediately when entering the page
            });
            ed.onKeyDown.add(function(ed, e) {
                if (e.ctrlKey && e.keyCode == 13) {  // Ctrl+Return
                    doit(true);
                    tinymce.dom.Event.cancel(e);
                } else if (e.keyCode == 27) {   // Escape
                    // doesn't work in firefox, the re-init in turnOffFullScreenView()
                    // might clash with event handling:
                    if ($('form#checkform').hasClass('fullscreen')) {
                        setTimeout(turnOffFullScreenView, 100);  // use timeout to prevent problems on Firefox
                    }
                }
            });
            // remove any 'no errors found' message:
            ed.onKeyUp.add(function(ed, e) {
                if (!e.keyCode || e.keyCode != 17) {  // don't hide if user used Ctrl+Return
                    $('#feedbackMessage').html('');
                }
            });
            ed.onPaste.add(function(ed, e) {
                $('#feedbackMessage').html('');
            });
        },

        /* translations: */
        languagetool_i18n_no_errors :
           {
            'fr': '<?php echo __("Aucune erreur trouvée.");?>'
           },
        languagetool_i18n_explain :
           {
            // "Explain..." - shown if there's an URL with a more detailed description:
            'fr': '<?php echo __("Plus d’informations…");?>'
           },

        languagetool_i18n_ignore_once :
           {
            // "Ignore this type of error" -- for non-spelling errors:
            'fr': '<?php echo __("Ignorer ce type d’erreur");?>'
           },
        languagetool_i18n_ignore_all :
        {
            // "Ignore error for this word" -- for spelling errors:
            'fr': '<?php echo __("Ignorer l’erreur pour ce mot");?>'
        },

        languagetool_i18n_rule_implementation :
           {
            // "Rule implementation":
            'fr': 'Implémentation de la règle…'
           },

        languagetool_i18n_suggest_word :
           {
            // "Suggest word for dictionary...":
            // *** Also set languagetool_i18n_suggest_word_url below if you set this ***
            'fr': '<?php echo __("Suggerer un mot pour le dictionnaire…");?>'
           },
        languagetool_i18n_suggest_word_url :
           {
            // "Suggest word for dictionary...":
             'fr': '<?php echo __("Suggerer un mot pour le dictionnaire…");?>'
           },

        languagetool_i18n_current_lang :    function() { return "fr"; },
        /* the URL of your proxy file: */
        languagetool_rpc_url                 : "https://"+window.location.hostname+"/languageTool",
        /* edit this file to customize how LanguageTool shows errors: */
        languagetool_css_url                 : "/online-check/tiny_mce/plugins/atd-tinymce/css/content.css?v5",
        /* this stuff is a matter of preference: */
        theme                              : "advanced",
        theme_advanced_buttons1            : "",
        theme_advanced_buttons2            : "",
        theme_advanced_buttons3            : "",
        theme_advanced_toolbar_location    : "none",
        theme_advanced_toolbar_align       : "left",
        theme_advanced_statusbar_location  : "bottom",  // activated so we have a resize button
        theme_advanced_path                : false,     // don't display path in status bar
        theme_advanced_resizing            : true,
        theme_advanced_resizing_use_cookie : false,
        /* disable the gecko spellcheck since AtD provides one */
        gecko_spellcheck                   : false
    });

     function fullscreen_toggle() {
       if ($('form#checkform').hasClass('fullscreen')) {
         turnOffFullScreenView();
       } else {
         turnOnFullScreenView();
        // if (_paq) { _paq.push(['trackEvent', 'Action', 'SwitchToFullscreen']); } // Piwik tracking
       }
       return false;
     }

    function turnOffFullScreenView() {
        // re-init the editor - this way we lose the error markers, but it's needed
        // to get proper position of the context menu:
        // source: http://stackoverflow.com/questions/4651676/how-do-i-remove-tinymce-and-then-re-add-it
        tinymce.EditorManager.execCommand('mceRemoveControl',true, 'checktext');
        tinymce.EditorManager.execCommand('mceAddControl', true, 'checktext');
        $('form#checkform').removeClass('fullscreen');
        $('body').removeClass('fullscreen');
        $('iframe#checktext_ifr').height(270);
        tinymce.execCommand('mceFocus', false, 'checktext');
    }

    function turnOnFullScreenView() {
        tinymce.EditorManager.execCommand('mceRemoveControl',true, 'checktext');
        tinymce.EditorManager.execCommand('mceAddControl', true, 'checktext');
        $('body').addClass('fullscreen');
        $('form#checkform').addClass('fullscreen');
        $('iframe#checktext_ifr').height( $(window).height() - $('#editor_controls').outerHeight() - $('#handle').outerHeight() );
        tinymce.execCommand('mceFocus', false, 'checktext');
    }

    function doit(doLog) {
        document.checkform._action_checkText.disabled = true;
        var langCode = '<?php echo $_SESSION["target_lang_code2"];?>';
        //if (doLog) {
          //  if (_paq) { _paq.push(['trackEvent', 'Action', 'CheckText', langCode]); } // Piwik tracking
        //}
        tinyMCE.activeEditor.execCommand('mceWritingImprovementTool', langCode);
    }

    $(function(){
     $(window).resize(function(){
       if ($('form#checkform').hasClass('fullscreen')) {
         $('iframe#checktext_ifr').height( $(window).height() - $('#editor_controls').outerHeight() );
       }
     });
    });

    </script>
</head>

<body class="fond">

<?php include "entete.php";?>
<script>
$(".buttonHome").hide();
$(".buttonMesClasses").hide();
$(".buttonMyDecks").hide();
$(".buttonMyClass").hide();
langUpdateButton();
//$('.desktop').menuBreaker();
</script>

<div class="center bodyContent">
	<h1>Ma rédaction</h1>
<form id="checkform" class="" name="checkform" action="#" method="post">
    <div id="handle"><div id="feedbackMessage"></div></div>
    <div class="window">
			<div id="editor_controls">

					<div id="feedbackErrorMessage"></div>
					<div id="clavier">
					</div>
						<div class="mySubmitBtn">
								<input id="MycheckBtn" type="button" name="_action_checkText" value="Vérifier"
											 onClick="doit(true);return false;" title="Vérifier le texte">
						</div>
					<div style="clear:both;"></div>

			</div>



        <div id="checktextpara" style="margin: 0;position:relative;">

              <textarea id="checktext" name="text" style="width: 100%" rows="10" placeholder="<?php echo __("Écrivez votre texte ici puis cliquez sur 'Vérifier' pour détecter les erreurs.");?>"></textarea>
              <div class="fullscreen-toggle">
                    <a href="#" title="toggle fullscreen mode" onClick="fullscreen_toggle();return false;"></a>
              </div>

								<div id="dialog" style="text-align:left;"></div>
								<div id="microphone">
								<div id="waves" style="display:none;">
								<div style="animation-delay: -350ms;" class="wave"></div>
								<div style="animation-delay: -400ms;" class="wave"></div>
								<div style="animation-delay: -500ms;" class="wave"></div>
								<div style="animation-delay: -200ms;" class="wave"></div>
								<div style="animation-delay: -300ms;" class="wave"></div>
								<div class="wave"></div>
								</div>
									<div id="Myrecorder" type="button" name="_action_checkText" onClick="startMic();return false;" title="speech2text"><img src="img/micro.png" width="21px"></div>
								</div>


        </div>
				<div style="margin:10px;">
					<input type="text" id='verbeInput' onkeyup="lookForVerb();" placeholder="verbe à l'infinitif" style="vertical-align:middle;padding:8px;">
					<div id="MyConjugBtn" type="button" onclick="showConjug();return false;" title="Rechercher la conjugaison du verbe">Conjuguer</div>
					<div id="affichageConjug" style="display:none;"></div>
					<div id="resultTest"></div>
				</div>
    </div>

</form>
</div>
<script type="text/javascript">
charList=["à","â","ç","è","é","ê","ô","ù","û","ï","œ"];
for(k in charList)
{
	$("#clavier").append("<div class='touche' onclick='addChar(\""+charList[k]+"\")'>"+charList[k]+"</div>");
}
$("#clavier").append("<br>");
for(k in charList)
{
	$("#clavier").append("<div class='touche' onclick='addChar(\""+charList[k].toUpperCase()+"\")'>"+charList[k].toUpperCase()+"</div>");
}



$("#verbeInput").autocomplete({
	 source: allVerbs,
	 minLength: 3,
	 //select: function( event, ui ) {
		 //$("#resultTest").text("input was: '"+ this.value + "' and selection was: "+ ui.item.value);
	 //}
 });
 function speakText(elem){
	text2read=elem.innerText;
 	SpeechSynthesisItem = new SpeechSynthesisUtterance(text2read);
 	SpeechSynthesisItem.lang="fr-FR";
 	speechSynthesis.speak(SpeechSynthesisItem);
 }

function addChar(char)
{
	var ed = tinyMCE.activeEditor;
	ed.execCommand('mceInsertContent', false, char);
	console.log(char);
}
function showConjug()
{
	verb=$("#verbeInput").val();
	console.log(verb);
	$.getJSON("ajax.php?action=getConjugaison&verbe="+verb, function(result)
	{
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
	tempsname={present:"Présent",'imperative-present':"Présent",future:"Futur",imperfect:"Imparfait",'simple-past':"Passé simple",'past-participle':"Passé",'present-participe':"Présent"};
	$("#affichageConjug").html("");
		for(mode in conjug)
		{	if(mode!="@attributes" && mode!="participle" && mode!="infinitive")
			{$("#affichageConjug").append("<div id='"+mode+"' class='conjugModes'><h1>"+modename[mode]+"</h1></div>");
			for(temps in conjug[mode])
				{
				$("#"+mode).append("<div id='"+mode+"_"+temps+"' class='conjugTemps' onclick='speakText(this);'><h2>"+tempsname[temps]+"</h2></div>")
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
						if(typeof(Conjugaisons[k].i)=="object"){Conjugaisons[k].i="";}
						$("#"+mode+"_"+temps).append("<span >"+pronom[k]+pronoPrefix[k]+racine+"<span class='endings'>"+Conjugaisons[k].i+"</span>"+pronoSufix[k]+"</span><br>");
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
}

function lookForVerb()
{
	verb=$("#verbeInput").val();
	console.log(verb);
	//$.getJSON("ajax.php?action=getConjugaison&verbe="+verb, function(result)
	//{
	//console.log(result);
	//});
}


var SpeechRecognition = SpeechRecognition || webkitSpeechRecognition;
var SpeechGrammarList = SpeechGrammarList || webkitSpeechGrammarList;
var SpeechRecognitionEvent = SpeechRecognitionEvent || webkitSpeechRecognitionEvent;

function startMic()
{
	console.log("start MIC");
$("#Myrecorder").hide();
$("#waves").show();
var grammar = '#JSGF V1.0; grammar phrase; public <phrase>="";';
var recognition = new SpeechRecognition();
var speechRecognitionList = new SpeechGrammarList();
speechRecognitionList.addFromString(grammar, 1);
recognition.grammars = speechRecognitionList;
recognition.lang = 'fr-FR';
recognition.interimResults = false;
recognition.maxAlternatives = 1;
recognition.start();

recognition.onresult = function(event) {
  $("#Myrecorder").fadeIn("slow");
  $("#waves").hide();
  var speechResult = event.results[0][0].transcript;
	var ed = tinyMCE.activeEditor;
	ed.execCommand('mceInsertContent', false, speechResult);
	//TinyContent=ed.getContent();
	//ed.setContent(TinyContent + speechResult);
  //$("#input_audio").html(speechResult);
	doit(true);
//$("#microphone").html(parseInt(event.results[0][0].confidence*100)+"%");
}

recognition.onspeechend = function() {
  recognition.stop();
  $("#Myrecorder").show();
  $("#waves").hide();
}

recognition.onerror = function(event) {
	$("#Myrecorder").show();
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

$(document).mouseup(function(e)
{
	var container = $('#dialog');
	if (!container.is(e.target) && container.has(e.target).length === 0)
	{	  $('#dialog').html("");
	}
});


</script>



</body>
</html>
