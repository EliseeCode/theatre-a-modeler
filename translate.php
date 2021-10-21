<?php
//Le but de ce fichier est d'insérer dans la base de donnée la liste des phrase nécessitant une Traduction
//Un autre script se chargera de générer les fichiers JSON a partir de la base de donnée a fin de servir de base pour la traduction en prod.
require '../../vendor/autoload.php';
use Google\Cloud\Translate\TranslateClient;
$translate = new TranslateClient([
    'key' => $_ENV['GOOGLE_API_KEY_TRANSLATION'];
]);
include_once ("db.php");
session_start();


if(!isset($_SESSION['user_id']) || !isset($_SESSION['active'])){
	header("location:checkLoginCookie.php");
exit();}
$_SESSION['local_lang']="en";
include_once ("local_lang.php");

// Makes it easier to read
$user_id = $_SESSION['user_id'];
//autorise que les personnes avec la licence trans_interface
$sql="SELECT COUNT(*) as flagLicence FROM user_licence
LEFT JOIN licences ON user_licence.licence_id=licences.licence_id WHERE user_licence.user_id=".$user_id." AND licences.active=1 AND licences.date_fin>CURRENT_TIMESTAMP AND licence_type='trans_interface' LIMIT 1";
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();
if($row["flagLicence"]==0)
{header("location:index.php?c=flagLicence");exit();}
$result->close();

//Récupération des langues autorisés
$sql="SELECT lang.* FROM user_licence
LEFT JOIN licences ON user_licence.licence_id=licences.licence_id
LEFT JOIN lang ON licences.lang_id=lang.lang_id
 WHERE user_licence.user_id=".$user_id." AND licences.active=1 AND licences.date_fin>CURRENT_TIMESTAMP AND licence_type='trans_interface'";
$result = $mysqli->query($sql);
$num_rows = $result->num_rows;
if($num_rows==0){header("location:index.php?c=recupLang");exit();}
$dataLang=array();
while($row = $result->fetch_assoc())
{array_push($dataLang,$row);
 array_push($lang_array,$row["lang_id"]);
}
$result->close();
//Verification langue.
$showDeck=false;
if(isset($_GET['target_lang']))
{
  $lang_id=(int)$_GET['target_lang'];
  if (in_array($lang_id, $lang_array))
    {
    $_SESSION['target_lang']=$lang_id;
    $showDeck=true;
    }
  else{
    header("location:index.php?c=langNotInArray");exit();
    $lang_id="";
    }
}




if(isset($_POST["translatewithGoogleLang_id"]))
{
  echo "translateGoogle";

  //get lang_code2
  //$lang_id=(int)$_POST["translatewithGoogleLang_id"];
  $sql="select lang_code2,lang_id FROM lang WHERE lang_interface=1 AND lang_id!=1";
  $result=$mysqli->query($sql);
  $listLang=array();

  while($row = $result->fetch_assoc())
  {
  $lang_id=$row["lang_id"];
  $lang_code2=$row["lang_code2"];
  array_push($listLang,array($lang_id,$lang_code2));
  }

  //echo $lang_code2;
  //get sentence to translate (those with empty trad)
  $nbretradLimit=1000;
  $nbretrad=0;
  foreach($listLang as $langArray)
  {

      $lang_id=$langArray[0];
      $lang_code2=$langArray[1];
      echo "<br>".$lang_id."-".$lang_code2."<br>";
      $sql="select interfaceSent.sentence_id,interfaceSent.sentence_long,T.traduction FROM interfaceSent LEFT JOIN (SELECT * FROM interfaceTrad WHERE interfaceTrad.lang_id=".$lang_id.") AS T ON T.sentence_id=interfaceSent.sentence_id";
      $result=$mysqli->query($sql);
      $dataTradLangGoogle=array();
      while ($row = $result->fetch_assoc()) {
        if(!isset($row['traduction']) || empty($row['traduction']))
              {
                if($nbretrad<$nbretradLimit)
                {
                  $sent_id=$row["sentence_id"];
                  $traduction=$translate->translate($row["sentence_long"], [
                      'target' => $lang_code2,'source' => "fr",'format'=>'text'
                  ]);
                  $nbretrad++;
                  array_push($dataTradLangGoogle,array($sent_id,$traduction['text']));
                }
              }
          }

    $sql="INSERT INTO interfaceTrad (sentence_id, lang_id,traduction,user_id,status) VALUE (?,?,?,?,?)";
    $statement=$mysqli->prepare($sql);
    $statusTrad="google";
    foreach($dataTradLangGoogle as $dataGoogle)
    { $sentence_id=$dataGoogle[0];//sentence_id
      $tradLine=$dataGoogle[1];//traduction
      $sql="DELETE FROM interfaceTrad WHERE sentence_id=".$sentence_id." AND lang_id=".$lang_id;
      $mysqli->query($sql);
      echo $sentence_id.":".$lang_id.":".$tradLine."<br>";
      if($tradLine!=""){
        $statement->bind_param("iisis",$sentence_id,$lang_id,$tradLine,$user_id,$statusTrad);
        $statement->execute();
      }
    }

    //ecriture du fichier JSON
    $sql="select interfaceSent.sentence_long,interfaceTrad.traduction FROM interfaceSent LEFT JOIN interfaceTrad ON interfaceTrad.sentence_id=interfaceSent.sentence_id WHERE interfaceTrad.lang_id=".$lang_id;
    $result=$mysqli->query($sql);
    $dataTradLangJSON=array();
    while ($row = $result->fetch_assoc()) {
      //echo $row["sentence_long"];
            $dataTradLangJSON[$row["sentence_long"]]=$row["traduction"];
        }
     $fp = fopen("gettext/".$lang_code2.'.json', 'w') or die("Unable to open file!");;
     fwrite($fp, json_encode($dataTradLangJSON));
     fclose($fp);

  }

}

if(isset($_POST["getLineFromPHP"]) && $_POST["getLineFromPHP"]==1)
{
  echo "Récupération des lignes depuis les fichiers sources.";
  $dir="./";
  $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::SELF_FIRST );
  $iterator->setMaxDepth(1);
  $Regex = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
  $linesFound=array();
  //$Regex = new RegexIterator($iterator, '/^[\.]+[^\\]*[\\]?[^\\]+[\\]?[^\\]+\.php$i', RecursiveRegexIterator::GET_MATCH);
  foreach ( $Regex as $path ) {
    $myPHPfile = fopen($path[0], "r") or die("Unable to open file!");
    if(filesize($path[0])>0)
      {$myReadFile=fread($myPHPfile,filesize($path[0]));
        preg_match_all('/__\([\"\']{1}(.*)[\"\']{1}\)/U', $myReadFile, $matches);
        if(isset($matches[1]))
        {
          foreach($matches[1] as $line2translate)
          {
            if(isset($linesFound[$line2translate]))
            {
              $linesFound[$line2translate]["occurence"]++;
              array_push($linesFound[$line2translate]["files"],$path[0]);
            }
            else{
              $linesFound[$line2translate]=array();
              $linesFound[$line2translate]["occurence"]=1;
              $linesFound[$line2translate]["files"]=array($path[0]);
            }
          }
        }
      }
    fclose($myPHPfile);
  }
  //print_r($linesFound);
  $mysqli->query('UPDATE interfaceSent SET occurence=0 WHERE 1');
   $statement = $mysqli->prepare('INSERT INTO interfaceSent (sentence,sentence_long,occurence,files)  VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE occurence=?, files=?');
   foreach($linesFound as $line => $dataLine)
   {
     //print_r($dataLine);
       $shortLine=substr($line,0,90);
       $filesName=join(", ",$dataLine["files"]);
       $occurence=(int)$dataLine["occurence"];
       //echo $shortLine."-".$filesName.$occurence."<br>";
       $statement->bind_param("ssisis",$shortLine,$line,$occurence,$filesName,$occurence,$filesName);
       $statement->execute();
   }
  $statement->close();
}
$dataTrad=array();
$result=$mysqli->query('SELECT interfaceSent.sentence_id,interfaceSent.sentence_long,interfaceSent.occurence,interfaceSent.files,lang.lang_code2,lang.lang_id,interfaceTrad.traduction,interfaceTrad.status,interfaceTrad.trad_id FROM interfaceSent
  LEFT JOIN interfaceTrad ON interfaceSent.sentence_id=interfaceTrad.sentence_id
  LEFT JOIN lang ON lang.lang_id=interfaceTrad.lang_id WHERE 1');
while ($row = $result->fetch_assoc()) {
        array_push($dataTrad,$row);
    }
$result->close();

echo "<script>dataTrad=".json_encode($dataTrad).";</script>";
echo "<script>dataLang=".json_encode($dataLang).";</script>";

?>
<!DOCTYPE html>
<html >
 <head>


    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Listes</title>
    <!-- Bootstrap -->
		<link rel="stylesheet" type="text/css" href="css/navStyle.css?ver=<?php echo filemtime('css/navStyle.css');?>"/>
		<link rel="stylesheet" type="text/css" href="css/DataTables.min.css"/>
	  <link href="css/main.css?ver=<?php echo filemtime('css/main.css');?>" rel="stylesheet">
		<link href="css/styleEntete.css?ver=<?php echo filemtime('css/styleEntete.css');?>" rel="stylesheet">
		<link href="css/myStyle.css?ver=<?php echo filemtime('css/myStyle.css');?>" rel="stylesheet">
		<link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16" />

  	<script src="js/jquery-3.3.1.min.js"></script>
		<script src="js/DataTables.min.js"></script>

		<style>
      .navbar{
        margin-bottom:0;
        border-radius:0;
      }
      textarea:focus{border:5px var(--mycolor2bis) solid;}
      .languageToShowContainer .tinyFlag{margin:20px;transform:scale(1.3);}
      .languageToShowContainer .tinyFlag:hover{margin:20px;transform:scale(1.5);}
      .lang_flag_item{text-align:left;vertical-align:middle;display:inline-block;background-color:white;padding:5px;margin:5px;box-shadow:0 0 5px grey;width:250px;height:60px;overflow: hidden;;}
      .selectedLangFlag{box-shadow:0 0 0 5px var(--mycolor2bis);;background-image:url(img/visi.png);background-size:15px 15px;background-position:right 10px center;background-repeat:no-repeat;}
      .editable{box-shadow:0 0 0 5px #6090CC;background-image:url(img/stylo.png);background-size:15px 15px;background-position:right 10px center;background-repeat:no-repeat;}
      .invisible{background-image:url(img/novisi.png);background-size:15px 15px;background-position:right 10px center;background-repeat:no-repeat;}
      .fond{text-align:center;}
      .slideUp{display:none;}
      .accordeonsTop .slideUpTop:after{content:"";background-image:url(img/arrow_down.png);background-size:cover;width:20px;height:20px;padding-left:15px;margin-left:15px;float:right;}
  		.accordeonsTop label:not(.slideUpTop):after{content:"";background-image:url(img/arrow_down.png);background-size:cover;width:20px;height:20px;padding-left:15px;margin-left:15px;float:right;transform:rotate(180deg);}
  		.accordeonsTop label:after{transition:0.5s;}
      .lineContainer{padding:5px;}
      .sentence_item{padding:5px;background-color:white;margin:5px;box-shadow:0 0 3px grey;}
      .extraInfo{font-size:0.6em;display:none;color:grey;}
      .originSentence:hover .extraInfo{display:block;}
      .originSentence{margin-bottom:5px;
        text-align: left;
        margin-left: 12%;
        }
      .status{vertical-align:middle;display:inline-block;width:25px;height:25px;background-size:cover;background-repeat:no-repeat;background-position:center;}
      .google_icon{background-image:url(img/google.png);}
      .human_icon{background-image:url(img/humanEdit.png);}
      .checked_icon{background-image:url(img/check3.png);}
      .content{display:inline-block;width:80%;}
      textarea{vertical-align:middle;width:80%;}
      </style>
		</style>
</head>

<body class="fond">
  <h1>Traduction de l'interface :</h1>
  <?php if($_SESSION["user_id"]==7){?>
  <form class="" action="#" method="post">
    <input type="hidden" name="getLineFromPHP" value="1">
    <button type="submit">get Line From PHP</button>
  </form>
  <!-- <form class="" action="#" method="post">
    <select name="createJSONlang_id" class="selectLang"></select>
    <button type="submit">make JSON</button>
  </form> -->
  <form class="" action="#" method="post">
    <input type="hidden" name="translatewithGoogleLang_id" value="1">
    <button type="submit">Translate empty sentence with google</button>
  </form>
<?php }?>
  <div class="accordeonsTop" onclick="retract(this);">
      <label style="background-color:white;" class="label_edition">
        <?php echo __("Langues à afficher/éditer : (cliquez deux fois pour éditer)");?>
      </label>
  </div>
  <div class="retractable">
    <div class="languageToShowContainer"></div>
  </div>
  <div class="lineContainer"></div>
  <script>
  for(k in dataLang)
  {
    lang_code2=dataLang[k].lang_code2;
    lang_id=dataLang[k].lang_id;
    lang_name=dataLang[k].lang_name;
    lang_interface_build=dataLang[k].interface_build;
    if(lang_interface_build==1)
    {
      $(".selectLang").append("<option value='"+lang_id+"'>"+lang_name+"</option>");
      if($('.lang_flag_'+lang_id).length==0){
          $(".languageToShowContainer").append(`
            <div onclick="toggleLanguage('`+lang_code2+`',`+lang_id+`);" title='`+lang_name+`' class='invisible lang_flag_item lang_flag_`+lang_id+`'>
              <span class='tinyFlag flag_`+lang_code2+`'></span>
              <span>`+lang_name+`</span>
            </div>`);
          }
    }

  }
  //add sentences
  sentence_ids=[];
  for(k in dataTrad)
  {
    data=dataTrad[k];
    sentence_id=data.sentence_id;
    if(sentence_ids.indexOf(sentence_id)==-1){sentence_ids.push(sentence_id);}
    sentence_long=data.sentence_long;
    occurence=data.occurence;
    files=data.files;
    lang_code2=data.lang_code2;
    traduction=data.traduction;
    status=data.status;
    trad_id=data.trad_id;
    if($('.sentence_item_'+sentence_id).length==0 && occurence>0){
      $(".lineContainer").append(`<div class='sentence_item sentence_item_`+sentence_id+`' data-sent='`+sentence_id+`'>

          <div class='originSentence'>`+sentence_long+`<div class="extraInfo">x`+occurence+` in `+files+`</div></div>
          <div class='translationContainer'></div>

      </div>`);
    }
  }


  //add trad.
  function showTradLang(lang_id){

    dataLangFiltered=dataLang.filter(function(elem){return elem.lang_id==lang_id;});
    for(k in dataLangFiltered)
    {
      lang_code2=dataLangFiltered[k].lang_code2;
      lang_id=dataLangFiltered[k].lang_id;
      lang_interface_build=dataLangFiltered[k].interface_build;
      if(lang_interface_build==1)
      {
            $(".translationContainer").append(`<div class="translation_`+lang_code2+` translation_item">

            <div class="content">
            <span class='tinyFlag flag_`+lang_code2+`'></span>
            <textarea disabled rows="2" class="textarea_lang_`+lang_id+`" data-lang="`+lang_id+`" onchange="updateTrad(this.dataset.sent,this.dataset.lang,'human');"></textarea>
            </div>
            <div class="status status_lang_`+lang_id+`" data-lang="`+lang_id+`" onclick="updateTrad(this.dataset.sent,this.dataset.lang,'checked');"></div>
            </div>`);
      }
    }
    $(".sentence_item").each(
      function() {
        sentence_id=$(this).attr("data-sent");
        $(this).find(".textarea_lang_"+lang_id).attr("data-sent",sentence_id);
        $(this).find(".status_lang_"+lang_id).attr("data-sent",sentence_id);
      }
    )

    dataTradFiltered=dataTrad.filter(function(elem){return elem.lang_id==lang_id;});
    console.log(dataTradFiltered);
    for(k in dataTradFiltered)
    {
      data=dataTradFiltered[k];
      sentence_id=data.sentence_id;
      lang_code2=data.lang_code2;
      traduction=data.traduction;
      status=data.status;
      trad_id=data.trad_id;
      if(trad_id != null)
      {
        $(".sentence_item_"+sentence_id+" .translation_"+lang_code2+" .status").addClass(status+"_icon");
        $(".sentence_item_"+sentence_id+" .translation_"+lang_code2+" textarea").html(traduction);
      }
    }
  }

  function updateTrad(sentence_id,lang_id,status){
    tradLine=$(".sentence_item_"+sentence_id+" .textarea_lang_"+lang_id).val();
    $.getJSON("ajax_trad.php?action=setTrad&status="+status+"&lang_id="+lang_id+"&sentence_id="+sentence_id+"&tradLine="+tradLine, function(result)
    {
      $(".sentence_item_"+sentence_id+" .status_lang_"+lang_id).removeClass("checked_icon human_icon google_icon").addClass(status+"_icon");
    });
  }
  function toggleLanguage(lang_code2,lang_id)
  {
    listClass=["selectedLangFlag","editable","invisible"]
    console.log(lang_id);
    if($(".lang_flag_"+lang_id).hasClass("selectedLangFlag")){
      $(".lang_flag_"+lang_id).removeClass("selectedLangFlag").addClass("editable");
      $(".textarea_lang_"+lang_id).prop("disabled",false);
    }
    else if($(".lang_flag_"+lang_id).hasClass("editable")){
      $(".lang_flag_"+lang_id).removeClass("editable").addClass("invisible");;
      $(".translation_"+lang_code2).remove();
    }
    else{
      $(".lang_flag_"+lang_id).removeClass("invisible").addClass("selectedLangFlag");
      showTradLang(lang_id);
      $(".textarea_lang_"+lang_id).prop("disabled",true);
    }
  }



  function retract(that)
  {$(that).parent().find('.retractable').slideToggle();
   $(that).find("label").toggleClass("slideUpTop");
  }

  </script>
</body>
</html>
