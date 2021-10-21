<?php
/* Displays all error messages */
include_once ("db.php");
session_start();


if(!isset($_SESSION['user_id'])){header("location:checkLoginCookie.php");exit();}
if($_SESSION['user_id']!=7){header("location:index.php");exit();}
$user_id = $_SESSION['user_id'];
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$email = $_SESSION['email'];
$active = $_SESSION['active'];
$type = $_SESSION['type'];
echo "<script>fullUserName='".$first_name." ".$last_name."';</script>";
echo "<script>type='".$type."';</script>";
echo "<script>user_id=".$user_id.";</script>";
$classes=array();
//get users data
echo "<script>user=".json_encode($user).";</script>";

?>
<!DOCTYPE html>
<html >
 <head>


    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil</title>
    <!-- Bootstrap -->
    <link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16" />
    <link href="css/main.css?ver=<?php echo filemtime('css/main.css');?>" rel="stylesheet">
    <link href="css/styleEntete.css?ver=<?php echo filemtime('css/styleEntete.css');?>" rel="stylesheet">
    <link href="css/myStyle.css?ver=<?php echo filemtime('css/myStyle.css');?>" rel="stylesheet">
    <link href="css/navStyle.css?ver=<?php echo filemtime('css/navStyle.css');?>" rel="stylesheet">
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/jquery-ui.js"></script>
  </head>
<style>
  .navbar{
    margin-bottom:0;
    border-radius:0;
  }
</style>
<?php
echo "<script>user_id=".json_encode($user_id).";</script>";
echo "<script>first_name=".json_encode($first_name).";</script>";
echo "<script>last_name=".json_encode($last_name).";</script>";
echo "<script>email=".json_encode($email).";</script>";

?>

<body class="fond">

<style>
.lang_item{padding:10px; margin:20px;display:inline-block;box-shadow:0 0 3px grey;background-color:white;border-radius:10px;}
.lang_item:hover{transform:none;}
</style>
	<div class="center" style="padding-top:100px;">
    <div>
      <h1>Administration des Langues à apprendre</h1>
      <div id="lang_container">
      </div>
    </div>
  </div>
<script>
$.getJSON("ajax.php?action=getAllLang", function(result)
{
  console.log(result);
for(langRk in result)
{
  lang_id=result[langRk].lang_id;
  lang_code2=result[langRk].lang_code2;
  lang_code3=result[langRk].lang_code3;
  lang_code2_2=result[langRk].lang_code2_2;
  lang_interface_ok=result[langRk].lang_interface;
  lang_interface_build=result[langRk].interface_build;
  lang_name=result[langRk].lang_name;
  lang_deck=result[langRk].lang_deck;
  $("#lang_container").append(`
    <div id='lang_item_`+lang_id+`' class='lang_item scaleOver'>
      <div class='flagStd flag_`+lang_code2+`'></div>
      <div><input class='lang_name' type="text" placeholder='name' value="`+lang_name+`"></div>

      <div><label>Langue disponible à apprendre(deck)<input class='lang_deck' type="checkbox"></label></div>
      <div><label>Langue interface en construction<input class='lang_interface_build' type="checkbox"></label></div>
      <div><label>Langue interface vérifié<input class='lang_interface_ok' type="checkbox"></label></div>
      <div><input class='lang_code2' type="text" placeholder='code2' value="`+lang_code2+`"></div>
      <div><input class='lang_code2_2' type="text" placeholder='code2_2' value="`+lang_code2_2+`"></div>
      <div><input class='lang_code3' type="text" placeholder='code3' value="`+lang_code3+`"></div>
      <button onclick='changeLangSetting("`+lang_id+`")'>Change</button>
      <button onclick='moveUp("`+lang_id+`")'>MoveUp</button>
  </div>`);
  if(lang_deck==1)
  {$("#lang_item_"+lang_id).find(".lang_deck").prop("checked",true);}
  else{$("#lang_item_"+lang_id).find(".lang_deck").prop("checked",false);}

  if(lang_interface_build==1)
  {$("#lang_item_"+lang_id).find(".lang_interface_build").prop("checked",true);}
  else{$("#lang_item_"+lang_id).find(".lang_interface_build").prop("checked",false);}

  if(lang_interface_ok==1)
  {$("#lang_item_"+lang_id).find(".lang_interface_ok").prop("checked",true);}
  else{$("#lang_item_"+lang_id).find(".lang_interface_ok").prop("checked",false);}
}

$("#lang_container").sortable({
items: ".lang_item",
//axis:'y',
update: function (event, ui) {
var data = $(this).sortable('serialize');
console.log(data);
$.getJSON({
      data: data,
      type: 'POST',
      url: 'ajax.php?action=setOrderLang'
  },function(result){console.log(result);});
  }
});

});
function moveUp(deck_id)
	{$("#lang_item_"+deck_id).prependTo('#lang_container');
  var data = $("#lang_container").sortable('serialize');
  console.log(data);
  $.getJSON({
        data: data,
        type: 'POST',
        url: 'ajax.php?action=setOrderLang'
    },function(result){console.log(result);});
}
function changeLangSetting(lang_id)
{
  lang_name=$("#lang_item_"+lang_id).find(".lang_name").val();
  lang_code2=$("#lang_item_"+lang_id).find(".lang_code2").val();
  lang_code2_2=$("#lang_item_"+lang_id).find(".lang_code2_2").val();
  lang_deck=$("#lang_item_"+lang_id).find(".lang_deck").prop("checked");
  lang_interface_ok=$("#lang_item_"+lang_id).find(".lang_interface_ok").prop("checked");
  lang_interface_build=$("#lang_item_"+lang_id).find(".lang_interface_build").prop("checked");
  if(lang_deck){lang_deck=1;}else{lang_deck=0;}
  lang_code3=$("#lang_item_"+lang_id).find(".lang_code3").val();
$.getJSON("ajax.php?action=changeAdminLang&lang_id="+lang_id+"&lang_name="+lang_name+"&lang_code2="+lang_code2+"&lang_code2_2="+lang_code2_2+"&lang_code3="+lang_code3+"&lang_deck="+lang_deck+"&lang_interface_ok="+lang_interface_ok+"&lang_interface_build="+lang_interface_build, function(result)
{
  $("#lang_item_"+lang_id).find("button").html("done");
});
}



</script>
</body>
</html>
