<?php

include_once ("db.php");
session_start();
$explodeURI=explode('/',$_SERVER['REQUEST_URI']);
//$_SESSION['url']=end($explodeURI);
if(!isset($_SESSION['user_id'])){header("location:checkLoginCookie.php");exit();}
$user_id = $_SESSION['user_id'];
//Uniquement reservé à Elisée SUperAdmin
if($user_id!=7){header("location:index.php");exit();}
if ($_SERVER['REQUEST_METHOD'] == 'POST')
  {
    if(isset($_POST["idToBorrow"])){
      $idToBorrow=(int)$_POST["idToBorrow"];
      $result = $mysqli->query("SELECT * FROM users WHERE user_id='$idToBorrow'");

      if ( $result->num_rows == 0 ){ // User doesn't exist
          $_SESSION['message'] = "Aucun utilisateur inscrit avec cette adresse email !";
          header("location: error.php");
      }
      else { // User exists
          $user = $result->fetch_assoc();
      		    $_SESSION['user_id'] = $user['user_id'];
              $_SESSION['type'] = $user['type'];
              $_SESSION['classe'] = $user['classe'];
              $_SESSION['email'] = $user['email'];
              $_SESSION['first_name'] = $user['first_name'];
              $_SESSION['last_name'] = $user['last_name'];
              $_SESSION['active'] = $user['active'];
              require 'decks.php';
      }
  }
  }
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$email = $_SESSION['email'];
$active = $_SESSION['active'];
$type = $_SESSION['type'];
$classe = $_SESSION['classe'];
echo "<script>fullUserName='".$first_name." ".$last_name."';</script>";
echo "<script>type='".$type."';</script>";
echo "<script>user_id=".$user_id.";</script>";
//if($type!="prof"){header("location:decks.php");exit();}
$_SESSION['url']="";

//get other classes infos
$sql="SELECT classes.class_id,classes.class_name,classes.promo,users.first_name,users.last_name,users.user_id FROM classes
JOIN user_class ON user_class.class_id=classes.class_id
JOIN users ON user_class.user_id=users.user_id WHERE users.type='prof' ORDER BY classes.class_id ASC";
$myClasses=array();
$result = $mysqli->query($sql);
while ($row = $result->fetch_assoc()) {
$myClasses[]=$row;}
  echo "<script>myClasses=".json_encode($myClasses).";console.log(myClasses);</script>";
$result->free();
?>

<!DOCTYPE html>
<html >
 <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ADMIN</title>
    <link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16" />

    <!-- Bootstrap -->
	  <link href="css/main.css?ver=<?php echo filemtime('css/main.css');?>" rel="stylesheet">
		<link href="css/styleEntete.css?ver=<?php echo filemtime('css/styleEntete.css');?>" rel="stylesheet">
	  <link href="css/deck.css?ver=<?php echo filemtime('css/deck.css');?>" rel="stylesheet">
	  <link href="css/card.css?ver=<?php echo filemtime('css/card.css');?>" rel="stylesheet">
		<link href="css/myStyle.css?ver=<?php echo filemtime('css/myStyle.css');?>" rel="stylesheet">
    <link href="css/navStyle.css?ver=<?php echo filemtime('css/navStyle.css');?>" rel="stylesheet">
    <script src="js/jquery-3.3.1.min.js"></script>
		<script src="js/Moment.js"></script>
		<script src="js/charts.js"></script>
    <script src="js/jquery-ui.js"></script>
		<style>
      .navbar{
        margin-bottom:0;
        border-radius:0;
      }
    </style>
</head>

<body class="fond">

  <?php include "entete.php";?>
  <script>
	$(".buttonRetourList").hide();
	$(".buttonMyClass").hide();
  $(".buttonMyDecks").hide();
  $(".buttonBadgeStat").show();
  $(".buttonBadgeStat").addClass("active");
  $(".buttonListes").show();


  $(".buttonInvitation").show();

	$('.desktop').menuBreaker();
	</script>
<style>
.table_listProf{display:inline-block;margin:50px;vertical-align:top;}
</style>
	<div style="text-align:center;" class="bodyContent">
  <div>
    <form action="adminProf.php" method="post">
    <input type="number" name="idToBorrow"><button type="submit">Go Borrow Id</button></div>
  </form>
  <div class="class_container" style="margin:20px;">

  </div>
</div>
<script>
function removeProf(user_id,class_id)
{
  $("#user_class_"+user_id+"_"+class_id).remove();
  $.getJSON("ajax.php?action=kickOutUser&class_id="+class_id+"&user_id="+user_id, function(result){});
}

for(k in myClasses)
  {
    var myLine=myClasses[k];
    console.log(myLine);
    class_id=myLine.class_id;
    class_name=myLine.class_name;
    user_id=myLine.user_id;
    Fullname=myLine.first_name+" "+myLine.last_name;
    if($('#class_'+class_id).length==0){$(".class_container").append('<table class="table_listProf"  id="class_'+class_id+'"><tr><th>'+class_id+'</th><th col_span=2>'+class_name+'</th></tr></table>');}
    $('#class_'+class_id).append("<tr id='user_class_"+user_id+"_"+class_id+"'><td><img src='img/close.png' width='25px' onclick='removeProf("+user_id+","+class_id+");'></td><td>"+user_id+"</td><td>"+Fullname+"</td></tr>");
  }
	</script>


</body>
</html>
