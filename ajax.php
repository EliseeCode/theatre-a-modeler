<?php
if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest' ) {exit();}
include "db.php";
session_start();
include_once ("local_lang.php");
use Google\Cloud\Translate\TranslateClient;
//if(!isset($_SESSION["user_id"])){exit();}

function checkProfRight($class_id,$user_id,$mysqli)
{
  $sql="SELECT * FROM user_class WHERE user_class.user_id=".$user_id." AND user_class.class_id=".$class_id." AND user_class.role='prof'";
  $result = $mysqli->query($sql);
  $IamTeacher=$result->num_rows;
  $result->close();
  return $IamTeacher;
}
$dailyCoinsMax=500;
$current_tps=time();

$action=$mysqli->escape_string(htmlspecialchars($_GET['action']));
switch ($action){
case 'getConjugaison':
  $verbe=$mysqli->real_escape_string(htmlspecialchars($_GET['verbe']));
  $result = $mysqli->query('SELECT verbe_type,categorie,auxiliaire FROM verbes WHERE verbe = "' . $verbe.'"');
  $myResult = $result->fetch_assoc();
  $verbe_type=$myResult["verbe_type"];
  $categorie=$myResult["categorie"];
  $auxiliaire=$myResult["auxiliaire"];
  $conjug="";
  $xml=simplexml_load_file("conjugation-fr.xml") or die("Error: Cannot create object");
  foreach ($xml->children() as $value) {
    if($value["name"]==$verbe_type)
      {$conjug=$value;}
  }
  $data = array ('status'=>'ok',"verbe_type"=>$verbe_type,"categorie"=>$categorie,"auxiliaire"=>$auxiliaire,"conjug"=>$conjug);
  echo json_encode($data);
  $result->free();
break;
case 'delQuiz':
  $user_id=(int)$_SESSION['user_id'];
  $quiz_id=(int)$_GET['quiz_id'];
  $mysqli->query("UPDATE quiz SET active=0,delete_date=NOW()
  WHERE quiz.quiz_id=".$quiz_id." AND quiz.prof_id=".$user_id);
  // $mysqli->query("DELETE quiz,note_user_quiz FROM `quiz`
  // LEFT JOIN note_user_quiz ON note_user_quiz.quiz_id=quiz.quiz_id
  // WHERE quiz.quiz_id=".$quiz_id." AND quiz.prof_id=".$user_id);
  echo json_encode("");
break;
case 'delUser':
  $user_id=(int)$_SESSION['user_id'];
  $sql = "INSERT INTO deletedUser (user_id) VALUES (".$user_id.")";
  $mysqli->query($sql);
  $mysqli->query("DELETE FROM alerte WHERE user_id=".$user_id);
  //$mysqli->query("DELETE FROM user_licence WHERE user_id=".$user_id);
  $mysqli->query("DELETE FROM user_class WHERE user_id=".$user_id);
  $mysqli->query("DELETE FROM activite WHERE user_id=".$user_id);
  $mysqli->query("DELETE FROM users WHERE user_id=".$user_id);
  $mysqli->query("DELETE FROM note_user_quiz WHERE user_id=".$user_id);
  $mysqli->query("DELETE FROM tresor WHERE user_id=".$user_id);
  $mysqli->query("DELETE FROM duplic WHERE user_id=".$user_id);
  $mysqli->query("DELETE FROM user_deck_droit WHERE user_id=".$user_id);
  $mysqli->query("DELETE FROM bank WHERE user_id=".$user_id);
  $mysqli->query("DELETE FROM trophy WHERE user_id=".$user_id);
  $mysqli->query("DELETE FROM user_deck_droit WHERE user_id=".$user_id);
  $mysqli->query("DELETE FROM stat WHERE user_id=".$user_id);
  $mysqli->query("DELETE FROM classes WHERE creator_id=".$user_id." AND (status='perso' OR status='explore')");
//TODO:stopSubscription !

  echo json_encode("");
break;
case 'deleteDeck':
  $deck_id=(int)$_GET['deck_id'];
  $user_id=(int)$_SESSION['user_id'];
  $mysqli->query("UPDATE decks SET active=0,delete_date=NOW()
  WHERE decks.deck_id=".$deck_id." AND decks.user_id=".$user_id);
  // $result = $mysqli->query('SELECT numT.num,cards.card_id,cards.hasAudio,cards.hasImage FROM (SELECT COUNT(*) as num,card_id,hasAudio,hasImage FROM cards GROUP BY hasImage) AS numT LEFT JOIN cards ON cards.hasImage=numT.hasImage WHERE cards.deck_id=' . $deck_id);
  // while ($row = $result->fetch_assoc())
  // {$card_id=$row["card_id"];
  //  if($row["hasAudio"]==1){unlink("card_audio/card_".$card_id.".wav");}
  //  if($row["hasImage"]>0 && $row["num"]==1){unlink("card_img/card_".$row["hasImage"].".png");}
  // }
  // $result->free();
  //
  // $mysqli->query("DELETE decks,cards,activite,deck_class,tags,user_deck_droit FROM `decks`
  //   LEFT JOIN deck_class ON deck_class.deck_id=decks.deck_id
  //   LEFT JOIN cards ON cards.deck_id=decks.deck_id
  //   LEFT JOIN activite ON cards.card_id=activite.card_id
  //   LEFT JOIN tags ON tags.deck_id=decks.deck_id
  //   LEFT JOIN user_deck_droit ON user_deck_droit.deck_id=decks.deck_id
  //   WHERE decks.deck_id=".$deck_id);
  //
  //   $mysqli->query("DELETE FROM `duplicDeck`
  //     WHERE duplicDeck.deck_id=".$deck_id." OR duplicDeck.new_deck_id=".$deck_id);
  //$mysqli->query("UPDATE decks SET visible=0, lastChange=".$current_tps."  WHERE deck_id=".$deck_id);
  $data = array ('status'=>'ok');
  echo json_encode($data);
break;
case 'deleteCard':
  $card_id=(int)$_GET['card_id'];
  $user_id=(int)$_SESSION['user_id'];

  $sql="UPDATE cards LEFT JOIN user_card ON user_card.card_id=cards.card_id SET cards.active=0,cards.delete_date=NOW()
  WHERE cards.card_id=".$card_id;
  //AND user_card.user_id=".$user_id;
  $mysqli->query($sql);
  // $result = $mysqli->query('SELECT deck_id FROM cards WHERE card_id = ' . $card_id);
  // $myResult = $result->fetch_assoc();
  // $deck_id=$myResult["deck_id"];
  // $result->free();
  // $mysqli->query("UPDATE decks SET nbreMots=nbreMots-1, lastChange=".$current_tps." WHERE deck_id=".$deck_id);
  // $mysqli->query("DELETE FROM `cards` WHERE card_id=".$card_id);
  // $result = $mysqli->query('SELECT numT.num,cards.card_id,cards.hasImage FROM (SELECT COUNT(*) as num,card_id,hasImage FROM cards GROUP BY hasImage) AS numT LEFT JOIN cards ON cards.hasImage=numT.hasImage WHERE numT.card_id=' . $card_id.' LIMIT 1');
  // $row = $result->fetch_assoc();
  // $card_id=$row["card_id"];
  // $num=$row["num"];
  // $hasImage=$row["hasImage"];
  // if($hasImage>0 && $num==1){unlink("card_img/card_".$hasImage.".png");}
  // $result->free();
  // unlink("card_audio/card_".$card_id.".wav");
   $data = array ('status'=>'ok',"data"=>$sql);
  echo json_encode($data);
break;
case 'updateDeck':
  $deck_id=(int)$_GET['deck_id'];
  $deck_name=$mysqli->real_escape_string(htmlspecialchars($_GET['deck_name']));
  $sql="UPDATE decks SET deck_name='".$deck_name."', lastChange=".$current_tps." WHERE deck_id=".$deck_id;
  $mysqli->query($sql);
  $data = array ('status'=>'ok','sql'=> $sql);
  echo json_encode($data);
break;
case 'updateDeckLang':
  $user_id=(int)$_SESSION['user_id'];
  $lang_id=(int)$_GET['lang_id'];
  $deck_id=(int)$_GET['deck_id'];
  $sql1="UPDATE decks SET lang_id=".$lang_id.", lastChange=".$current_tps." WHERE deck_id=".$deck_id;
  $mysqli->query($sql1);
  $sql1="UPDATE cards SET lang_id=".$lang_id." WHERE deck_id=".$deck_id;
  $mysqli->query($sql1);

  $result = $mysqli->query('SELECT * FROM lang WHERE lang_id = ' . $lang_id);
  $mylang = $result->fetch_assoc();
  $result->free();

  $sql="DELETE FROM user_target_lang WHERE user_id=".$user_id." AND lang_id=".$lang_id;
  $mysqli->query($sql);
  $sql="INSERT INTO user_target_lang (user_id, lang_id) VALUE (".$user_id.",".$lang_id.")";
  $mysqli->query($sql);

  $data = array ('status'=>'ok','sql'=>$sql1,'langData'=> $mylang);
  echo json_encode($data);
break;
case 'updateCard':
  $card_id=(int)$_GET['card_id'];
  $mot=htmlspecialchars($_GET['mot']);
  $mot=$mysqli->real_escape_string($mot);
  $mot_trad=htmlspecialchars($_GET['mot_trad']);
  $mot_trad=$mysqli->real_escape_string($mot_trad);
  $mysqli->query("UPDATE cards SET mot='".$mot."',mot_trad='".$mot_trad."' WHERE card_id=".$card_id);
  $data = array ('status'=>'ok');
  echo json_encode($data);
break;
case 'addOneSentenceToCard':
  $card_id=(int)$_GET['card_id'];
  $sentence=htmlspecialchars($_GET['sentence']);
  //$sentence=$mysqli->real_escape_string($sentence);

  $sql = "INSERT INTO card_sentence (sentence,card_id) "
          . "VALUES (?,?)";
  //$sql = "INSERT INTO card_sentence (sentence,card_id) "
  //        . "VALUES (?,?)";
  //$mysqli->query($sql);
  //$mysqli->prepare($sql)->bind_param("si", $sentence, $card_id)->execute();
  $stmt=$mysqli->prepare($sql);
  $stmt->bind_param("si", $sentence, $card_id);
  $stmt->execute();
  $sentence_id=$stmt->insert_id;
  $stmt->close();
  echo json_encode($sentence_id);
break;
case 'updateOneSentenceToCard':
  $sentence_id=(int)$_GET['sentence_id'];
  $sentence=htmlspecialchars($_GET['sentence']);
  //$sentence=$mysqli->real_escape_string($sentence);
  if($sentence!="")
  {$sql = "UPDATE card_sentence SET sentence=? WHERE sentence_id=?";
  $stmt=$mysqli->prepare($sql);
  $stmt->bind_param("si", $sentence, $sentence_id);
  $stmt->execute();
  $stmt->close();
  }
  else{
    $sql = "DELETE FROM card_sentence WHERE sentence_id=?";
    $stmt=$mysqli->prepare($sql);
    $stmt->bind_param("i", $sentence_id);
    $stmt->execute();
    $stmt->close();
  }

  $mysqli->query($sql);

  //if($sentence!="")
  //{$sql = "UPDATE card_sentence SET sentence='".$sentence."' WHERE sentence_id=".$sentence_id;}
  //else{
  //  $sql = "DELETE FROM card_sentence WHERE sentence_id=".$sentence_id;
  //}
  //$mysqli->query($sql);
  echo json_encode("done");
break;
case 'delOneSentenceToCard':
  $card_id=(int)$_GET['card_id'];
  $sentence_id=(int)($_GET['sentence_id']);
  $sql = "DELETE FROM card_sentence WHERE card_id=".$card_id." AND sentence_id=".$sentence_id;
  $mysqli->query($sql);
  echo json_encode("done");
break;
case 'getAllSchool':
  $schools=array();
  $sql="SELECT school_id,school_name FROM schools WHERE schools.active=1";
  $result = $mysqli->query($sql);
  while($row = $result->fetch_assoc())
  {
    array_push($schools,$row);
  }
  $result->free();
  echo json_encode($schools);
break;
case 'deckHasText':
  $deck_id=(int)$_GET['deck_id'];
  //$texte=htmlspecialchars($_POST['texte']);
  $texte=$_POST['texte'];
  $texte=$mysqli->real_escape_string($texte);
  $mysqli->query("UPDATE decks SET texte='".$texte."', lastChange=".$current_tps." WHERE deck_id=".$deck_id);
break;
case 'deckHasNoText':
  $deck_id=(int)$_GET['deck_id'];
  $mysqli->query("UPDATE decks SET texte='', lastChange=".$current_tps." WHERE deck_id=".$deck_id);
break;
case 'deckHasVideo':
  $deck_id=(int)$_GET['deck_id'];
  $youtube_url=htmlspecialchars($_GET['youtube_id']);
  $youtube_url=$mysqli->real_escape_string($youtube_url);
  $regex1 = '/^http[s]?:\/\/(www\.)?youtube\.com\/watch\?v=([\w-]+).*$/i';
  $regex2 = '/^http[s]?:\/\/youtu\.be\/([\w-]+)$/i';
  if(preg_match($regex1, $youtube_url))
  {
    $query = explode('&', parse_url($youtube_url, PHP_URL_QUERY));
    $parameters = array();
    foreach ($query as $element)
    {   $parts = explode('=', $element);
        $parameters[$parts[0]] = $parts[1];}
    // Récupération du paramètre 'v'
    $youtube_id=$parameters['v'];
    $sql="UPDATE decks SET youtube_id='".$youtube_id."', lastChange=".$current_tps." WHERE deck_id=".$deck_id;
    echo json_encode(array("status"=>"done","sql"=>$sql,"youtube_id"=>$youtube_id));
    $mysqli->query($sql);
  }
  else if(preg_match($regex2, $youtube_url))
  {
    $query = explode('/', $youtube_url);
    $youtube_id=end($query);
    $sql="UPDATE decks SET youtube_id='".$youtube_id."', lastChange=".$current_tps." WHERE deck_id=".$deck_id;
    $mysqli->query($sql);
    echo json_encode(array("status"=>"done","sql"=>$sql,"youtube_id"=>$youtube_id));
  }
  else{
    echo json_encode(array("status"=>"problem"));
  }
break;
case 'deckHasNoVideo':
  $deck_id=(int)$_GET['deck_id'];
  $mysqli->query("UPDATE decks SET youtube_id='', lastChange=".$current_tps." WHERE deck_id=".$deck_id);
  echo json_encode("done");
break;
case 'deckHasPoster':
  $deck_id=(int)$_GET['deck_id'];
  $mysqli->query("UPDATE decks SET hasPoster=1, lastChange=".$current_tps." WHERE deck_id=".$deck_id);
  echo json_encode("done");
break;
case 'deckHasNoPoster':
  $deck_id=(int)$_GET['deck_id'];
  $mysqli->query("UPDATE decks SET hasPoster=0, lastChange=".$current_tps." WHERE deck_id=".$deck_id);
  echo json_encode("done");
break;
case 'deckHasAudio':
  $deck_id=(int)$_GET['deck_id'];
  $mysqli->query("UPDATE decks SET hasAudio=1, lastChange=".$current_tps." WHERE deck_id=".$deck_id);
  echo json_encode("done");
break;
case 'deckHasNoAudio':
  $deck_id=(int)$_GET['deck_id'];
  $mysqli->query("UPDATE decks SET hasAudio=0, lastChange=".$current_tps." WHERE deck_id=".$deck_id);
  echo json_encode("done");
break;
case 'deckHasImage':
  $deck_id=(int)$_GET['deck_id'];
  $mysqli->query("UPDATE decks SET hasImage=".$deck_id.", lastChange=".$current_tps." WHERE deck_id=".$deck_id);
  echo json_encode("done");
break;
case 'deckHasNoImage':
  $deck_id=(int)$_GET['deck_id'];
  $result = $mysqli->query('SELECT numT.num,decks.deck_id,decks.hasImage FROM (SELECT COUNT(*) as num,deck_id,hasImage FROM decks GROUP BY hasImage) AS numT LEFT JOIN decks ON decks.hasImage=numT.hasImage WHERE numT.deck_id=' . $deck_id);
  while ($row = $result->fetch_assoc())
  {
    if($row["hasImage"]>0 && $row["num"]==1){unlink("deck_img/deck_".$row["hasImage"].".png");}
  }
  $result->free();
  $mysqli->query("UPDATE decks SET hasImage=0 , lastChange=".$current_tps." WHERE deck_id=".$deck_id);
  echo json_encode("done");
break;
case 'cardHasImage':
  $card_id=(int)$_GET['card_id'];
  $mysqli->query("UPDATE cards SET hasImage=".$card_id." WHERE card_id=".$card_id);
  echo json_encode("done");
break;
case 'cardHasNoImage':
  $card_id=(int)$_GET['card_id'];
  $result = $mysqli->query('SELECT numT.num,cards.card_id,cards.hasImage FROM (SELECT COUNT(*) as num,card_id,hasImage FROM cards GROUP BY hasImage) AS numT LEFT JOIN cards ON cards.hasImage=numT.hasImage WHERE numT.card_id=' . $card_id);
  while ($row = $result->fetch_assoc())
  {
    if($row["hasImage"]>0 && $row["num"]==1){unlink("card_img/card_".$row["hasImage"].".png");}
  }
  $result->free();
  $mysqli->query("UPDATE cards SET hasImage=0 WHERE card_id=".$card_id);
  echo json_encode("done");
break;
case 'cardHasAudio':
  $card_id=(int)$_GET['card_id'];
  $mysqli->query("UPDATE cards SET hasAudio=1 WHERE card_id=".$card_id);
  echo json_encode("done");
break;
case 'cardHasNoAudio':
  $card_id=(int)$_GET['card_id'];
  $mysqli->query("UPDATE cards SET hasAudio=0 WHERE card_id=".$card_id);
  unlink("card_audio/card_".$card_id.".wav");
  echo json_encode("done");
break;
case 'addMagicNewCard':
  $deck_id=(int)$_GET['deck_id'];
  $mot=$mysqli->escape_string(htmlspecialchars($_GET['mot']));
  $phrase=array();
  $imgURLs="";
  $definition="";
  require_once '../../vendor/autoload.php';
  //Traduction
  $translate = new TranslateClient([
      'key' => $_ENV['GOOGLE_API_KEY_TRANSLATION']
  ]);
  $result = $translate->translate($mot, [
      'target' => 'tr','source' => 'fr','format'=>'text'
  ]);
  $mot=$mysqli->real_escape_string($mot);
  $mot_trad=$result['text'];
  //définition
  $definition="";
  //$url="https://googledictionaryapi.eu-gb.mybluemix.net/?define=".$mot."&lang=fr";
  $url="https://dictionary.yandex.net/api/v1/dicservice.json/lookup?key=dict.1.1.20190401T190024Z.548a8b648294db85.7b19b83399b26a02d9be4531f2a6f4313381e161&lang=fr-fr&flags=0x0004&text=".$mot;
  //$url=urlencode($url);
  $definition = file_get_contents($url);
  echo $definition;
break;

case 'AddNewCard':
  $deck_id=(int)$_GET['deck_id'];
  $user_id=(int)$_SESSION['user_id'];
  $mot=htmlspecialchars($_GET['mot']);
  $mot=$mysqli->real_escape_string($mot);
  $mot_trad=htmlspecialchars($_GET['mot_trad']);
  $mot_trad=$mysqli->real_escape_string($mot_trad);
  $lang_id=(int)$_GET['lang_id'];
  $sql = "INSERT INTO cards (deck_id,mot, mot_trad,lang_id) "
          . "VALUES (".$deck_id.",'".$mot."','".$mot_trad."',".$lang_id.")";
  $mysqli->query($sql);
  $card_id=$mysqli->insert_id;
  $sql = "INSERT INTO user_card (user_id,card_id,role) "
          . "VALUES (".$user_id.",".$card_id.",'creator')";
  $mysqli->query($sql);
  $sql = "INSERT INTO `card_deck`(card_id,deck_id) VALUES (".$card_id.",".$deck_id.")";
  $mysqli->query($sql);
  $result = $mysqli->query('SELECT COUNT(*) AS nbreMots FROM cards WHERE cards.active=1 AND deck_id = ' . $deck_id);
  $myResult = $result->fetch_assoc();
  $nbreMots=$myResult["nbreMots"];

  $mysqli->query("UPDATE decks SET nbreMots=".$nbreMots.", lastChange=".$current_tps." WHERE deck_id=".$deck_id);

  //$mysqli->query("UPDATE decks SET nbreMots=nbreMots+1 WHERE deck_id=".$deck_id);
  $data = array ('status'=>'ok','card_id'=>$card_id,'sql'=>$sql);
  echo json_encode($data);
break;

case 'forgetCard':
  $user_id=(int)$_SESSION["user_id"];
  $card_id=(int)$_GET["card_id"];
  $mysqli->query("DELETE FROM `activite` WHERE card_id=".$card_id." AND user_id=".$user_id);
  echo json_encode("done");
break;
case 'cardSetNewDelta':
  $user_id=(int)$_SESSION["user_id"];
  $card_id=(int)$_GET["card_id"];
  $star=(int)$_GET["star"];
  $current_tps=time();
  $sql = "SELECT * from activite where user_id=".$user_id." AND card_id=".$card_id;
  $result = $mysqli->query($sql);
  $myResult = $result->fetch_assoc();
  $line_exist=$result->num_rows;
  $result->close();
  if($line_exist){
    $LastRD=$myResult["LastRD"];
    $LastRD = new DateTime($LastRD);
    $LastRD=$LastRD->format('U');
    $OptimalRD=$myResult["OptimalRD"];
    $OptimalRD = new DateTime($OptimalRD);
    $OptimalRD=$OptimalRD->format('U');
  $Delta=$OptimalRD-$LastRD;
  $newDelta=$Delta;
  if($current_tps<=$OptimalRD)
    {
      //Croissance linéaire de Stabilité entre t=0 et t=optimumRT
      $NewDelta=round(3*($current_tps-$LastRD)+$Delta);
      $nextOptimalRD=$current_tps+round($NewDelta*(0.8+0.4*(rand(0,1000)/1000)));
    }
  else
    {
      $NewDelta=round(4*$Delta+min(15,1*($current_tps-$OptimalRD)/$Delta)*$Delta);
      $nextOptimalRD=$current_tps+round($NewDelta*(0.8+0.4*(rand(0,1000)/1000)));
    }

  if($star==1){$newDelta=3*24*60*60;}
  if($star==2){$newDelta=min(5*24*60*60,$NewDelta);}
  if($star==3){$newDelta=min(8*24*60*60,$NewDelta);}
  if($star==4){$newDelta=max(10*24*60*60,$NewDelta);}
  $nextOptimalRD=$current_tps+round($NewDelta*(0.8+0.4*(rand(0,1000)/1000)));
  echo json_encode("done");
  }
break;
case 'addActiviteGlobal':
  $user_id=(int)$_SESSION["user_id"];
  $card_id=(int)$_GET["card_id"];
  $exo_id=(int)$_GET["exo_id"];
  $correctness=(int)$_GET["correctness"];
  $game=htmlspecialchars($_GET['game']);
  $game=$mysqli->real_escape_string($game);

  $today=date("Y-m-d");
  $sql = "INSERT INTO activiteGlobal (card_id,user_id, jour,exo_id,correctness,timeStmp)
  VALUES (".$card_id.",".$user_id.",'".$today."',".$exo_id.",".$correctness.",NOW())";
  $mysqli->query($sql);
  echo json_encode($sql);
break;
case 'cardLearned':
  $user_id=(int)$_SESSION["user_id"];
  $card_id=(int)$_GET["card_id"];
  $exo_id=(int)$_GET["exo_id"];
  $pExercice=(int)$_GET["puissance"];//entre 10 et 100.
  $current_tps=time();
  $pmax=100;
  $sql = "SELECT * from activite where user_id=".$user_id." AND card_id=".$card_id;
  $result = $mysqli->query($sql);
  $myResult = $result->fetch_assoc();
  $line_exist=$result->num_rows;
  $result->close();
  $mysqli->query("UPDATE users left join decks on decks.user_id=users.user_id left join cards on cards.deck_id=decks.deck_id SET nbreCoins=nbreCoins+1  WHERE cards.card_id=".$card_id);
  $mysqli->query("UPDATE decks left join users on decks.user_id=users.user_id left join cards on cards.deck_id=decks.deck_id SET decks.royalties=decks.royalties+1  WHERE cards.card_id=".$card_id);

  if($line_exist){
  $LastRD=$myResult["LastRD"];
  $LastRD = new DateTime($LastRD);
  $LastRD=$LastRD->format('U');
  $OptimalRD=$myResult["OptimalRD"];
  $OptimalRD = new DateTime($OptimalRD);
  $OptimalRD=$OptimalRD->format('U');


  $puissance=(int)$myResult["puissance"];
  $Delta=$OptimalRD-$LastRD;
  //on actualise puissance en fonction du temps (il reste le meme si il vient d'etre mis a jours sinon, il tombe à 1.)
  $puissance=max(0,$puissance*($OptimalRD-$current_tps)/$Delta);
  //on calcule la nouvelle puissance en fonction de l'exercice réalisé
  $newPuissance=min(100,$puissance+$pExercice);
  if($current_tps<=$OptimalRD)
    {
      $NewDelta=round(3*$Delta*($newPuissance/100)*($newPuissance/100)*(1-($puissance/100)) + $Delta);
      //==OLD WAY
      //Croissance linéaire de Stabilité entre t=0 et t=optimumRT
      //$NewDelta=round(3*($current_tps-$LastRD)+$Delta);
      //on trouve le NewOptRD avec un terme qui rend aléatoire autour de la vrai valeur pour eviter le bourrage sur une journée.
      $nextOptimalRD=$current_tps+round($NewDelta*(0.9+0.2*(rand(0,1000)/1000)));
    }
    else
      {
        $NewDelta=round((3+(min(3,($current_tps-$OptimalRD)/$Delta))*$Delta)*($newPuissance/100)*($newPuissance/100)*(1-($puissance/100))+$Delta);
        //$NewDelta=round(4*$Delta+(min(15,($current_tps-$OptimalRD)/$Delta)*$Delta));
        $nextOptimalRD=$current_tps+round($NewDelta*(0.9+0.2*(rand(0,1000)/1000)));
      }
    $sql = "UPDATE activite SET puissance=".$newPuissance.",LastRD=NOW(),OptimalRD=FROM_UNIXTIME(".$nextOptimalRD.") WHERE card_id=".$card_id." AND user_id=".$user_id;
    $mysqli->query($sql);
  }
  else {//new card
    $newPuissance=$pExercice;
    $newPuissance=min(100,$pExercice);
    $NewDelta=round(4*24*60*60*(0.9+0.2*(rand(0,1000)/1000)));//4jours+-20%
    $nextOptimalRD=$current_tps+$NewDelta;
    $sql = "INSERT INTO activite (card_id,user_id, LastRD,OptimalRD,puissance) VALUES (".$card_id.",".$user_id.",NOW(),FROM_UNIXTIME(".$nextOptimalRD."),".$newPuissance.")";
    $mysqli->query($sql);
  }

  //gestion XP+Coins
  $coins2add=round($pExercice/10);
  if($puissance>80){$coins2add=0;}
  $today=date("Y-m-d");
    $sql="SELECT nbreCoins FROM bank WHERE user_id=".$user_id." AND jour='".$today."' LIMIT 1";
    $result = $mysqli->query($sql);
    $line_exist=$result->num_rows;
    $row =$result->fetch_assoc();
    $nbreCoinsToday=(int)$row["nbreCoins"];
    $_SESSION["nbreCoinsToday"]=(int)$row["nbreCoins"];
    $result->free();
  if($line_exist==0){
    $coins2add+=20;
  }
  $bankStatus="ok";
  //TODO:Put a limit for thos who have no licence.
  if($_SESSION["boltNoLimit"]!=true)
  {
    if($nbreCoinsToday+$coins2add>=$dailyCoinsMax)
    { $coins2add=max($dailyCoinsMax-$nbreCoinsToday,0);
      $bankStatus="limit";
      //echo json_encode(array("status"=>"limite atteinte","nbreCoins"=>$nbreCoins,"nbreCoinsToday"=>$_SESSION["nbreCoinsToday"],"coins2add"=>$coins2add));}
    }
  }

    if($line_exist==0){
      $mysqli->query('INSERT INTO bank (user_id,nbreCoins,jour) VALUES ('.$user_id.','.$coins2add.',"'.$today.'")');
    }
    else{
      $mysqli->query("UPDATE bank SET nbreCoins=nbreCoins+".$coins2add." WHERE user_id=".$user_id." AND jour='".$today."'");
    }

    $mysqli->query("UPDATE users SET nbreCoins=nbreCoins+".$coins2add." WHERE user_id=".$user_id);
    $result = $mysqli->query("SELECT nbreCoins FROM users WHERE user_id=".$user_id);
    $row =$result->fetch_assoc();
    $nbreCoins=(int)$row["nbreCoins"];
    $result->free();

    $newNbreCoinsToday=$nbreCoinsToday+$coins2add;

    //update sub_mission_user score:
    $subSqlActi='SELECT MAX(activiteGlobal.timeStmp) as LastRD,
            activiteGlobal.card_id as card_id
      FROM activiteGlobal
      LEFT JOIN card_deck as card_deck_acti ON card_deck_acti.card_id=activiteGlobal.card_id
           JOIN card_deck as card_deck_cond ON card_deck_cond.deck_id=card_deck_acti.deck_id
      WHERE correctness=1 AND card_deck_cond.card_id='.$card_id.' AND activiteGlobal.user_id='.$user_id.' AND activiteGlobal.exo_id='.$exo_id.'
      GROUP BY activiteGlobal.card_id';

    $sql4 = 'INSERT INTO sub_mission_user (sub_mission_id,user_id,success,score)
    SELECT sub_missions.sub_mission_id,'.$user_id.',IF(COUNT(*)>=sub_missions.quantity,1,0),COUNT(*) FROM
    user_class
    LEFT JOIN mission_class ON user_class.class_id=mission_class.class_id
    LEFT JOIN missions ON  mission_class.mission_id=missions.mission_id
    LEFT JOIN sub_missions ON missions.mission_id=sub_missions.mission_id
    LEFT JOIN card_deck ON card_deck.deck_id=sub_missions.deck_id
    JOIN ('.$subSqlActi.') as acti
      ON acti.card_id=card_deck.card_id
    WHERE missions.active=1 AND user_class.user_id='.$user_id.' AND acti.LastRD>missions.starting_date AND sub_missions.exo_id='.$exo_id.'
    GROUP BY sub_missions.sub_mission_id
    ON DUPLICATE KEY UPDATE sub_mission_user.score=VALUES(score),sub_mission_user.success=VALUES(success)';
    $mysqli->query($sql4);
    //
    //update missions_user success
    $sql="INSERT INTO mission_user (mission_id,user_id,success,completed_date)
          SELECT distinct missions.mission_id,".$user_id.",IF(SUM(sub_mission_user.success=1)>=missions.num_sub_missions,1,0), CURDATE()
          FROM missions
          LEFT JOIN sub_missions ON sub_missions.mission_id=missions.mission_id
          LEFT JOIN sub_mission_user ON sub_mission_user.sub_mission_id=sub_missions.sub_mission_id
          WHERE missions.active=1 AND sub_mission_user.user_id=".$user_id."
          GROUP BY missions.mission_id
          ON DUPLICATE KEY UPDATE
          mission_user.success=VALUES(success), mission_user.completed_date=CURDATE()";
    $mysqli->query($sql);
    //
    //update of the objectif scores
    $sql="INSERT INTO user_objectif (objectif_id,user_id,score,deadline)
          SELECT distinct class_objectif.objectif_id,".$user_id.",1,DATE_ADD(CURDATE(), INTERVAL ((class_objectif.day_num+7 -DAYOFWEEK(CURDATE()))%7) DAY)
          FROM class_objectif
          LEFT JOIN deck_class ON deck_class.class_id=class_objectif.class_id
          LEFT JOIN cards ON cards.deck_id=deck_class.deck_id
          LEFT JOIN user_class ON user_class.class_id=class_objectif.class_id
          WHERE cards.card_id=".$card_id." AND user_class.user_id=".$user_id." AND class_objectif.quantity>0
          ON DUPLICATE KEY UPDATE
          user_objectif.score=IF(user_objectif.deadline>=CURDATE(), user_objectif.score+1,user_objectif.score)";
    $mysqli->query($sql);
    //vérification si un objectif a été validé:
    $sql="SELECT user_objectif.objectif_id FROM user_objectif
    LEFT JOIN class_objectif ON class_objectif.objectif_id=user_objectif.objectif_id WHERE user_objectif.user_id=".$user_id." AND user_objectif.score>=class_objectif.quantity AND user_objectif.success=0";
    $result = $mysqli->query($sql);
    $line_exist=$result->num_rows;
    $completedObjectif_id=array();
    while($row = $result->fetch_assoc())
    {
      array_push($completedObjectif_id,$row["objectif_id"]);
    }
    $result->free();
    if($line_exist>0)
    {
      //Est-ce qu'il y a un streak avec l'user_id et l'objectif_id?
      $sql="INSERT IGNORE INTO user_streak (objectif_id,user_id,value,deadline)
            SELECT user_objectif.objectif_id,".$user_id.",0,DATE_ADD(user_objectif.deadline, 7 DAY)
            FROM user_streak
            LEFT JOIN user_objectif ON user_objectif.user_id=user_streak.user_id
            WHERE user_objectif.user_id=".$user_id."
            AND user_objectif.score>=user_objectif.quantity
            AND user_objectif.objectif_id=user_streak.objectif_id
            AND user_objectif.success=0";
      $mysqli->query($sql);
      //if deadline du streak dépacé=> remise à 0(fait dans un script CRON à minuit tous les jours)
      //else, +1
      $sql="UPDATE user_streak,user_objectif LEFT JOIN user_streak ON user_streak.user_id=user_objectif.user_id
      SET user_streak.value=user_streak.value+1,user_streak.deadline=DATE_ADD(user_objectif.deadline,7 DAY)
      WHERE user_streak.deadline>=user_objectif.deadline AND
      user_objectif.user_id=".$user_id." AND
      user_objectif.objectif_id=user_streak.objectif_id AND
      user_objectif.score>=user_objectif.quantity AND
      user_objectif.success=0 ";
      $mysqli->query($sql);

      $sql="UPDATE user_objectif SET success=1 WHERE user_id=".$user_id." AND score>=quantity AND success=0";
      $mysqli->query($sql);
      $tresorValue=10;
      $dateTime=date("Y-m-d H:i:s");
      //$mysqli->query('INSERT INTO user_tresor (user_id,value,type,creation_date) VALUES ('.$user_id.','.$tresorValue.',"objectif","'.$dateTime.'")');

    }



  $data = array ("sql"=>$sql,"sql4"=>$sql4,'nextOptimalRD'=>$nextOptimalRD,'puissance'=>$newPuissance,"nbreCoins"=>$nbreCoins,"coins2add"=>$coins2add,"bankStatus"=>$bankStatus);
  echo json_encode($data);
break;
case 'updateStats':
  $user_id = (int)$_GET['user_id'];
  $jour=date("Y-m-d");
  $dateTime=date("Y-m-d H:i:s");
  echo $jour;
  $current_tps=time();
  $stats=array();
  $sql="SELECT SUM(CASE WHEN OptimalRD > NOW() THEN 1 ELSE 0 END) AS nbreMots FROM cards LEFT JOIN activite ON activite.card_id=cards.card_id WHERE cards.cative=1 AND activite.user_id=".$user_id;
  echo $sql;
  $result = $mysqli->query($sql);
  $myResult = $result->fetch_assoc();
  $nbreMots=$myResult["nbreMots"];
  echo "<br>".$nbreMots;
  $result->close();

  $sql="SELECT * FROM stat WHERE user_id=".$user_id." AND jour='".$jour."'";
  $result = $mysqli->query($sql);
  $stat_exist=$result->num_rows;
  $myResult = $result->fetch_assoc();
  $nbreMotsAvant=$myResult["nbreMots"];
  $result->close();
  echo '<br>exist? :'.$stat_exist.'<br>';
  if($nbreMots!=$nbreMotsAvant){
    if($stat_exist)
    {
    $sql="UPDATE stat SET nbreMots=".$nbreMots." WHERE user_id=".$user_id." AND jour='".$jour."'";
    }
    else
    {
    $sql="INSERT INTO stat (user_id,jour,nbreMots) VALUES (".$user_id.",'".$jour."',".$nbreMots.")";
    }
    $mysqli->query($sql);
    echo $sql;
  }
break;
case 'nbreCardToReview':
  $nbreCards=array();
  $user_id = (int)$_SESSION['user_id'];
  $class_id = (int)$_GET['class_id'];
  $lang_id=(int)$_SESSION["target_lang"];
  $current_tps=time();
  $deadDelay=30*24*60*60;
  //$sql="SELECT COUNT(*) AS nbreCard FROM activite WHERE activite.OptimalRD<".$current_tps." AND user_id=".$user_id;
  $sql="SELECT distinct cards.card_id, cards.deck_id, cards.mot,cards.mot_trad, cards.hasImage, cards.hasAudio, activite.user_id, activite.LastRD, activite.OptimalRD FROM cards
  LEFT JOIN (SELECT * FROM `activite` WHERE activite.user_id=".$user_id." AND activite.OptimalRD<".$current_tps." AND activite.OptimalRD+".$deadDelay.">".$current_tps.") AS activite ON activite.card_id=cards.card_id
  LEFT JOIN verbes ON verbes.verbe=cards.mot
  LEFT JOIN card_sentence ON card_sentence.card_id=cards.card_id
  LEFT JOIN deck_class ON deck_class.deck_id=cards.deck_id
  LEFT JOIN decks ON decks.deck_id=deck_class.deck_id
  WHERE decks.active=1 AND cards.active=1 AND decks.lang_id=".$lang_id." AND deck_class.class_id=".$class_id." AND activite.user_id=".$user_id;
  $result = $mysqli->query($sql);
  $nbreCards=$result->num_rows;

  //$myResult=$result->fetch_assoc();
  //$nbreCards=$myResult['nbreCard'];
  $result->close();
  $data = array ('status'=>'ok','nbreCards'=>$nbreCards);
  echo json_encode($data);
break;
case 'getThisClassReports':
  $user_id = (int)$_SESSION['user_id'];
  $class_id = (int)$_GET['class_id'];

  //check user is Allowed
  if(checkProfRight($class_id,$user_id,$mysqli)==0){exit();}

  $classUsers=array();
  $sql="SELECT user_class.role,user_class.position,users.user_id,users.first_name,users.last_name FROM user_class LEFT JOIN users ON users.user_id=user_class.user_id WHERE users.active=1 AND user_class.class_id=".$class_id;
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
  				array_push($classUsers,$row);
  		}
  $result->close();

  $statsNbreExo=array();
  // $sql="SELECT  COUNT(*) as nbreExo,
  //               activiteGlobal.jour,
  //               activiteGlobal.exo_id,
  //               activiteGlobal.user_id FROM activiteGlobal
  // LEFT JOIN exos ON exos.exo_id=activiteGlobal.exo_id
  // LEFT JOIN users ON users.user_id=activiteGlobal.user_id
  // LEFT JOIN cards ON cards.card_id=activiteGlobal.card_id
  // LEFT JOIN deck_class ON deck_class.deck_id=cards.deck_id
  // LEFT JOIN decks ON deck_class.deck_id=decks.deck_id
  // LEFT JOIN user_class ON user_class.user_id=activiteGlobal.user_id
  // WHERE users.active=1
  // AND decks.active=1
  // AND user_class.class_id=".$class_id." AND user_class.role='eleve'
  // AND deck_class.class_id=".$class_id." GROUP BY activiteGlobal.user_id,activiteGlobal.jour ORDER BY activiteGlobal.jour ASC";
  // $sql="SELECT  COUNT(*) as nbreExo,
  //               activiteGlobal.jour,
  //               activiteGlobal.exo_id,
  //              FROM activiteGlobal
  // LEFT JOIN card_deck ON card_deck.card_id=activiteGlobal.card_id
  // LEFT JOIN deck_class ON deck_class.deck_id=card_deck.deck_id
  // LEFT JOIN user_class ON user_class.user_id=activiteGlobal.user_id
  // WHERE user_class.class_id=".$class_id." AND user_class.role='eleve'
  // AND deck_class.class_id=".$class_id." GROUP BY activiteGlobal.jour ORDER BY activiteGlobal.jour ASC";
  // $result = $mysqli->query($sql);
  // while ($row = $result->fetch_assoc()) {
  // 				array_push($statsNbreExo,$row);
  // 		}
  // $result->close();

  $data = array ('status'=>'ok','classUsers'=>$classUsers,'statsNbreExo'=>$statsNbreExo);
  echo json_encode($data);
break;
case 'getThisClassUserStat':
  $my_user_id = (int)$_SESSION['user_id'];
  $user_id = (int)$_GET['user_id'];
  $class_id = (int)$_GET['class_id'];

  //check user is Allowed
  if($user_id!=$my_user_id)
  {
   $sql="SELECT * FROM user_class LEFT JOIN (SELECT * FROM user_class WHERE user_class.user_id=".$user_id." AND user_class.class_id=".$class_id.") as user_class2 ON user_class2.class_id=user_class.class_id
   WHERE user_class.user_id=".$my_user_id." AND user_class.class_id=".$class_id." AND user_class.role='prof' AND user_class2.role='eleve'";
  $result = $mysqli->query($sql);
  $IamTeacher=$result->num_rows;
  $result->close();
  if($IamTeacher==0){exit();}
  }

  $sql="SELECT first_name,last_name FROM users
  WHERE user_id=".$user_id;
  $result = $mysqli->query($sql);
  $myResult = $result->fetch_assoc();
  $user_name=ucfirst($myResult["first_name"]." ".$myResult["last_name"]);
  $result->close();

  $statsNbreExo=array();
  $nbreTotalExo=0;
  $sql="SELECT COUNT(*) as nbreExo,activiteGlobal.jour,exos.name,activiteGlobal.exo_id FROM activiteGlobal
  LEFT JOIN exos ON exos.exo_id=activiteGlobal.exo_id
  LEFT JOIN cards ON cards.card_id=activiteGlobal.card_id
  LEFT JOIN deck_class ON deck_class.deck_id=cards.deck_id
  LEFT JOIN decks ON deck_class.deck_id=decks.deck_id
  WHERE cards.active=1 AND decks.active=1 AND activiteGlobal.user_id=".$user_id." AND deck_class.class_id=".$class_id." Group BY activiteGlobal.jour,activiteGlobal.exo_id ORDER BY activiteGlobal.jour ASC";
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
  				array_push($statsNbreExo,$row);
          $nbreTotalExo+=(int)$row["nbreExo"];
  		}
  $result->close();

  $stats=array();

  $sql="SELECT * FROM stat WHERE user_id=".$user_id." ORDER BY jour ASC";
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
          array_push($stats,$row);
      }
  $result->close();

  $RDs=array();
  $sql="SELECT OptimalRD FROM activite WHERE user_id=".$user_id;
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
          array_push($RDs,$row);
      }
  $result->close();

  $current_tps=time();
  $sql="SELECT SUM(IF(activite.OptimalRD>'".$current_tps."',1,0)) AS nbreMotsEnMemoire,SUM(IF(activite.OptimalRD>'".$current_tps."',0,1)) AS nbreMotsOublie FROM activite
  WHERE activite.user_id=".$user_id." GROUP BY activite.user_id";
  $result = $mysqli->query($sql);
  $myResult = $result->fetch_assoc();
  $nbreMotsEnMemoire=$myResult["nbreMotsEnMemoire"];
  $nbreMotsOublie=$myResult["nbreMotsOublie"];
  if($nbreMotsEnMemoire==null){$nbreMotsEnMemoire=0;}
  if($nbreMotsOublie==null){$nbreMotsOublie=0;}
  $result->close();

  $today=date("Y-m-d");
  $sql="SELECT nbreCoins FROM bank WHERE user_id=".$user_id." AND jour=".$today;
  $result = $mysqli->query($sql);
  $row =$result->fetch_assoc();
  $nbreCoinsToday=$row["nbreCoins"];
  $result->free();
  //Coin due to exo
  $today=date("Y-m-d");
  $sql="SELECT sum(nbreCoins) as nbreCoinsExo FROM bank WHERE user_id=".$user_id." group by user_id";
  $result = $mysqli->query($sql);
  $row =$result->fetch_assoc();
  $nbreCoinsExo=$row["nbreCoinsExo"];
  $result->free();
  //CoinTotal
  $sql="SELECT nbreCoins FROM users WHERE user_id=".$user_id;
  $result = $mysqli->query($sql);
  $row =$result->fetch_assoc();
  $nbreCoins=$row["nbreCoins"];
  $result->free();

  $data = array ('status'=>'student','user_name'=>$user_name,'nbreCoins'=>$nbreCoins,'nbreCoinsToday'=>$nbreCoinsToday,'nbreCoinsExo'=>$nbreCoinsExo,'nbreMotsEnMemoire'=>$nbreMotsEnMemoire,'nbreMotsOublie'=>$nbreMotsOublie,'nbreTotalExo'=>$nbreTotalExo,'nbreExo'=>$statsNbreExo,'stats'=>$stats,'OptimalRDs'=>$RDs);
  echo json_encode($data);
break;
case 'getAllClassStat':
  $my_user_id = (int)$_SESSION['user_id'];
  $class_id = (int)$_GET['class_id'];
  //check user is Allowed
  if($user_id!=$my_user_id)
  {
   $sql="SELECT * FROM user_class WHERE user_class.user_id=".$my_user_id." AND user_class.class_id=".$class_id." AND user_class.role='prof'";
  $result = $mysqli->query($sql);
  $IamTeacher=$result->num_rows;
  $result->close();
  if($IamTeacher==0){exit();}
  }

  $user_name="";

  $statsNbreExo=array();
  $nbreTotalExo=0;
  $sql="SELECT distinct COUNT(*) as nbreExo,activiteGlobal.jour,exos.name FROM activiteGlobal
  LEFT JOIN exos ON exos.exo_id=activiteGlobal.exo_id
  LEFT JOIN cards ON cards.card_id=activiteGlobal.card_id
  LEFT JOIN deck_class ON deck_class.deck_id=cards.deck_id
  LEFT JOIN decks ON deck_class.deck_id=decks.deck_id
  LEFT JOIN user_class ON user_class.user_id=activiteGlobal.user_id
  WHERE cards.active=1 AND decks.active=1 AND deck_class.class_id=".$class_id." AND user_class.class_id=".$class_id." AND user_class.role='eleve' Group BY activiteGlobal.jour,activiteGlobal.exo_id ORDER BY activiteGlobal.jour ASC";
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
  				array_push($statsNbreExo,$row);
          $nbreTotalExo+=(int)$row["nbreExo"];
  		}
  $result->close();

  $stats=array();
  $RDs=array();
  $current_tps=time();
  $nbreMotsEnMemoire=0;
  $nbreMotsOublie=0;
  $nbreCoinsToday=0;
  $nbreCoinsExo=0;
  $nbreCoins=0;
  $data = array ('status'=>'class','user_name'=>$user_name,'nbreCoins'=>$nbreCoins,'nbreCoinsToday'=>$nbreCoinsToday,'nbreCoinsExo'=>$nbreCoinsExo,'nbreMotsEnMemoire'=>$nbreMotsEnMemoire,'nbreMotsOublie'=>$nbreMotsOublie,'nbreTotalExo'=>$nbreTotalExo,'nbreExo'=>$statsNbreExo,'stats'=>$stats,'OptimalRDs'=>$RDs);
  echo json_encode($data);
break;
case 'getStats':
  $stats=array();
  $user_id = (int)$_GET['user_id'];
  $sql="SELECT * FROM stat WHERE user_id=".$user_id." ORDER BY jour ASC";
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
  				array_push($stats,$row);
  		}
  $result->close();

  $RDs=array();
  $sql="SELECT OptimalRD FROM activite WHERE user_id=".$user_id;
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
  				array_push($RDs,$row);
  		}
  $result->close();

  $data = array ('status'=>'ok','stats'=>$stats,'OptimalRDs'=>$RDs);
  echo json_encode($data);
break;
case 'getStatsTodayUser':
  $stats=array();
  $user_id = (int)$_SESSION['user_id'];
  $sql="SELECT * FROM stat WHERE user_id=".$user_id." ORDER BY jour DESC LIMIT 10";
  $result = $mysqli->query($sql);
  while($myResult = $result->fetch_assoc())
  {
    $jour=$myResult["jour"];
    $nbreMotTotal=$myResult["nbreMotTotal"];
    if($jour==null){$jour=date("Y-m-d");}
    if($nbreMotTotal==null){$nbreMotTotal=0;}
  $stats[]=array("jour"=>$jour,"nbreMotTotal"=>$nbreMotTotal);
  }
  $result->close();
  $current_tps=time();
  $sql="SELECT SUM(IF(activite.OptimalRD>'".$current_tps."',1,0)) AS nbreMotsEnMemoire FROM activite
  WHERE activite.user_id='".$user_id."' GROUP BY activite.user_id";
  $result = $mysqli->query($sql);
  $myResult = $result->fetch_assoc();
  $nbreMotsEnMemoire=$myResult["nbreMotsEnMemoire"];
  if($nbreMotsEnMemoire==null){$nbreMotsEnMemoire=0;}
  $result->close();

  $data = array ('status'=>'ok','nbreMotsEnMemoire'=>$nbreMotsEnMemoire,'stats'=>$stats);
  echo json_encode($data);
break;
case 'getClassDecksWork':
  $class_id = (int)$_GET['class_id'];
  $prof_id = (int)$_SESSION['user_id'];
  $current_tps=time();
  $decksWork=array();
  $decks=array();
  //Recupération des nbre de mot par deck et par eleves
  $sql="SELECT COUNT(*) AS nbreExoDone, SUM(IF(activite.OptimalRD>'".$current_tps."',1,0)) AS nbreMotsEnMemoire, decks.nbreMots,users.user_id,decks.deck_id FROM users
  JOIN user_class ON users.user_id=user_class.user_id
  JOIN deck_class ON deck_class.class_id=user_class.class_id
  JOIN decks ON decks.deck_id=deck_class.deck_id
  JOIN cards ON cards.deck_id=decks.deck_id
  JOIN activite ON activite.card_id=cards.card_id AND activite.user_id=users.user_id
  WHERE cards.active=1 AND decks.active=1 AND users.active=1 AND user_class.class_id=".$class_id." GROUP BY decks.deck_id, users.user_id";
  //echo $sql;
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
  				array_push($decksWork,$row);
  		}
  $result->close();
  //recupération de toutes les infos des decks dans la classe
  $sql="SELECT deck_class.position, hasImage, deck_name, decks.deck_id,nbreMots,decks.status FROM decks
  LEFT JOIN deck_class ON decks.deck_id=deck_class.deck_id
  WHERE decks.active=1 AND deck_class.class_id=".$class_id." ORDER BY decks.deck_id DESC";
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
  				array_push($decks,$row);
  		}
  $result->close();

  $data = array ('status'=>'ok','decksWork'=>$decksWork,'decks'=>$decks);
  echo json_encode($data);
break;
case 'getClassQuizs':
  $class_id = (int)$_GET['class_id'];
  $prof_id = (int)$_SESSION['user_id'];
  $current_tps=time();
  $stats=array();
  $decks=array();
  //$users=array();
  //$trophy=array();
  $marks=array();
  $quizs=array();

  //recupération des $quizs
  $sql="SELECT quiz.quiz_id,quiz.expire,note_user_quiz.noteMax,quiz.deck_id,decks.deck_name,decks.hasImage FROM note_user_quiz
  LEFT JOIN quiz ON note_user_quiz.quiz_id=quiz.quiz_id
  LEFT JOIN decks ON quiz.deck_id=decks.deck_id
  WHERE decks.active=1 AND quiz.active=1 AND quiz.class_id=".$class_id." AND quiz.prof_id=".$prof_id." GROUP BY quiz_id ORDER BY quiz.expire ASC";
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
          array_push($quizs,$row);
  		}
  $result->close();
  //récupération des notes:
  $sql="SELECT note_user_quiz.user_id as user_id,quiz.quiz_id, note_user_quiz.note FROM `quiz`
  LEFT JOIN note_user_quiz ON note_user_quiz.quiz_id=quiz.quiz_id
  LEFT JOIN decks ON quiz.deck_id=decks.deck_id
  WHERE decks.active=1 AND quiz.active=1 AND quiz.class_id=".$class_id." AND quiz.prof_id=".$prof_id." ORDER BY quiz.quiz_id ASC";
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
          array_push($marks,$row);
  		}
  $result->close();

  $data = array ('status'=>'ok','quizs'=>$quizs,'marks'=>$marks);
  echo json_encode($data);
break;

case 'AddCorrection':
  $user_id=(int)$_SESSION['user_id'];
  $mysqli->query("UPDATE users SET nbreCorrection=nbreCorrection+1 WHERE user_id=".$user_id);
break;

case 'getPixImage':
    $q=htmlspecialchars($_GET['q']);
    $image_type=htmlspecialchars($_GET['image_type']);
    //TODO:change languageHere
    $url = 'https://pixabay.com/api/?key=464174-f2c416bb8f1ed189ef43af950&q='.$q.'&lang=fr&page=1&per_page=20&image_type='.$image_type;//'.$image_type; // path to your JSON file
    $data = file_get_contents($url); // put the contents of the file into a variable
    //$image = json_decode($data); // decode the JSON feed
    echo json_encode($data);
break;

case 'save_img':
  if(isset($_GET['link'])){$link=htmlspecialchars($_GET['link']);}else{echo('pas de GET');return;}
  if(isset($_GET['id'])){$id=(int)($_GET['id']);}else{return;}
  	$file = $link;
  	$newfile = './card_img/card_'.$id.'.png';
  	$res=copy($file,$newfile);
    $mysqli->query("UPDATE cards SET hasImage=".$id." WHERE card_id=".$id);
    echo json_encode("done".$res);
break;
case 'save_deck_img':
  if(isset($_GET['link'])){$link=htmlspecialchars($_GET['link']);}else{echo('pas de GET');return;}
  if(isset($_GET['deck_id'])){$deck_id=(int)($_GET['deck_id']);}else{echo('pas de deck_id');return;}
  	$file = $link;
  	$newfile = './deck_img/deck_'.$deck_id.'.png';
  	copy($file,$newfile);
    $mysqli->query("UPDATE decks SET hasImage=".$deck_id." WHERE deck_id=".$deck_id);
    echo json_encode("done");
break;
case 'ChangeClassName':
  $class_id=(int)$_GET['class_id'];
  $name=htmlspecialchars($_GET['name']);
  $name=$mysqli->real_escape_string($name);
  $promo=htmlspecialchars($_GET['promo']);
  $promo=$mysqli->real_escape_string($promo);
  $lang_id=(int)$_GET['lang'];

  $mysqli->query("UPDATE classes SET lang_id=".$lang_id." ,class_name='".$name."',promo='".$promo."' WHERE class_id=".$class_id);
break;
case 'changeNotif':
  $user_id=(int)($_SESSION['user_id']);
  $val=(int)($_GET['val']);
  $sql="UPDATE users SET notification=".$val." WHERE user_id=".$user_id;
  $mysqli->query($sql);
  echo json_encode("done");
break;
case 'changeFame':
  $user_id=(int)($_SESSION['user_id']);
  $val=(int)($_GET['val']);
  $sql="UPDATE users SET fame=".$val." WHERE user_id=".$user_id;
  $mysqli->query($sql);
  echo json_encode("done");
break;
case 'changeUserType':
  $user_id=(int)($_SESSION['user_id']);
  $val=htmlspecialchars($_GET['val']);
  $type=$mysqli->real_escape_string($val);
  $sql="UPDATE users SET type='".$type."' WHERE user_id=".$user_id;
  $mysqli->query($sql);
  $_SESSION['type']=$type;
  echo json_encode("done");
break;

case 'changeLang':
  $user_id=(int)($_SESSION['user_id']);
  $val=htmlspecialchars($_GET['val']);
  $val=$mysqli->real_escape_string($val);
  $sql="UPDATE users SET lang='".$val."' WHERE user_id=".$user_id;
  $mysqli->query($sql);
  echo json_encode("done");
break;

case 'getUserTargetLang':
  $user_id=(int)($_SESSION['user_id']);
  $sql="SELECT lang.lang_id,lang.lang_name,lang.lang_code2 FROM `user_target_lang` LEFT JOIN lang ON lang.lang_id=user_target_lang.lang_id WHERE user_target_lang.user_id=".$user_id." ORDER BY user_target_lang.changed_time DESC";
  $result = $mysqli->query($sql);
  $langData=array();
  while($row = $result->fetch_assoc())
  {array_push($langData,$row);}
  echo json_encode($langData);
break;
case 'getUsers':
  $class_id=(int)($_GET['class_id']);
  $user_id=(int)($_SESSION['user_id']);
  if(checkProfRight($class_id,$user_id,$mysqli)==0){exit();}
  $sql="SELECT distinct CONCAT(users.first_name,' ',users.last_name) as user_name,
              users.avatar_id,
              users.nbreCoins,
              users.ruby,
              users.user_id,
              user_class.role,
              user_class.position
               FROM users
  LEFT JOIN user_class ON user_class.user_id=users.user_id
  WHERE user_class.class_id=".$class_id." AND users.active=1 GROUP BY users.user_id ORDER BY user_class.position DESC";
  $result = $mysqli->query($sql);
  $usersData=array();
  while($row = $result->fetch_assoc())
  {array_push($usersData,$row);}
  echo json_encode($usersData);
break;
case 'getReportDeckExo':
  $class_id=(int)($_GET['class_id']);
  $exo_id=(int)($_GET['exo_id']);
  $deck_id=(int)($_GET['deck_id']);
  $user_id=(int)($_SESSION['user_id']);
  if(checkProfRight($class_id,$user_id,$mysqli)==0){exit();}
  $sql1='SELECT distinct user_class.user_id,IFNULL(SUM(acti.flag),0) as score FROM user_class
  LEFT JOIN
  (SELECT activiteGlobal.card_id as card_id,
          activiteGlobal.user_id as user_id,
          1 as flag
    FROM activiteGlobal
    LEFT JOIN cards ON cards.card_id=activiteGlobal.card_id
    LEFT JOIN user_class ON user_class.user_id=activiteGlobal.user_id
    LEFT JOIN card_deck ON card_deck.card_id=cards.card_id
    WHERE cards.active=1 AND correctness=1 AND user_class.class_id='.$class_id.' AND card_deck.deck_id='.$deck_id.' AND activiteGlobal.exo_id='.$exo_id.'
    GROUP BY CONCAT(activiteGlobal.card_id,"-",activiteGlobal.user_id)) as acti
    ON acti.user_id=user_class.user_id
  WHERE user_class.class_id='.$class_id.'
  GROUP BY user_class.user_id';
  $result = $mysqli->query($sql1);
  $scoresData=array();
  while($row = $result->fetch_assoc())
  {array_push($scoresData,$row);}
  $result->free();

  $sql2 = 'SELECT decks.deck_name,decks.hasImage,COUNT(*) as total_quantity,SUM(cards.hasAudio=1) as audio_quantity, SUM(cards.hasImage!=0 OR cards.mot_trad!="") as image_quantity FROM cards
  LEFT JOIN card_deck ON card_deck.card_id=cards.card_id
  LEFT JOIN decks ON decks.deck_id=card_deck.deck_id
  Where cards.active=1 AND card_deck.deck_id ='.$deck_id;
  $result = $mysqli->query($sql2);
  $row = $result->fetch_assoc();
  $total_quantity=$row['total_quantity'];
  $image_quantity=$row['image_quantity'];
  $audio_quantity=$row['audio_quantity'];
  $deck_name=$row['deck_name'];
  $deck_hasImage=$row['hasImage'];
  $result->free();
  if($exo_id==6){$quantity=$audio_quantity;}
  else{$quantity=$image_quantity;}

  $sql3 = 'SELECT * FROM exos
  Where exos.exo_id ='.$exo_id;
  $result = $mysqli->query($sql3);
  $exo = $result->fetch_assoc();
  $result->free();

  $deck=array("deck_id"=>$deck_id,"quantity"=>$quantity,"deck_name"=>$deck_name,"hasImage"=>$deck_hasImage);
  echo json_encode(array("scoresData"=>$scoresData,"deck"=>$deck,"exo"=>$exo));
break;
case 'getTargetLang':
  $sql="SELECT * FROM `lang` WHERE lang_deck=1";
  $result = $mysqli->query($sql);
  $langData=array();
  while($row = $result->fetch_assoc()){array_push($langData,$row);}
  echo json_encode($langData);
break;
case 'getTargetTranslateLang':
  $deck_id=(int)($_GET['deck_id']);
  $user_id=(int)($_SESSION['user_id']);
  if($user_id==7)
  {
    $sql="SELECT * FROM `lang` WHERE lang_deck=1";
    $result = $mysqli->query($sql);
    $langData=array();
    while($row = $result->fetch_assoc()){array_push($langData,$row);}
    $result->free();

    $sql="SELECT lang.lang_id FROM `lang` LEFT JOIN decks ON decks.lang_id=lang.lang_id LEFT JOIN duplicDeck ON duplicDeck.new_deck_id=decks.deck_id WHERE duplicDeck.deck_id=".$deck_id;
    $result = $mysqli->query($sql);
    $langUsedData=array();
    while($row = $result->fetch_assoc()){array_push($langUsedData,$row);}
    $result->free();
    $data = array ('langs'=>$langData,'langsUsed'=>$langUsedData);
    echo json_encode($data);
  }
break;
case 'updateFav':
$fav=(int)$_GET["fav"];
$user_id=(int)$_SESSION["user_id"];
$card_id=(int)$_GET["card_id"];
$sql="DELETE FROM `favorite` WHERE favorite.user_id=".$user_id." AND favorite.card_id=".$card_id;
$mysqli->query($sql);
if($fav==1){
$sql = "INSERT INTO favorite(user_id,card_id) VALUE (".$user_id.",".$card_id.")";
$mysqli->query($sql);
}
echo json_encode($fav);
break;
case 'getNbreFav':
$target_lang=(int)$_SESSION["target_lang"];
$user_id=(int)$_SESSION["user_id"];
$sql="SELECT COUNT(favorite.card_id) as nbreFav FROM `favorite`
LEFT JOIN cards ON cards.card_id=favorite.card_id
WHERE cards.active=1 AND favorite.user_id=".$user_id." AND cards.lang_id=".$target_lang." ORDER BY favorite.addTime DESC";
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();
$result->close();
echo json_encode(array("sql"=>$sql,"nbreFav"=>$row['nbreFav'],"targetLang"=>$target_lang));
break;
case 'getAllLang': //AdminLang
  $sql="SELECT * FROM `lang` WHERE 1 ORDER BY position ASC";
  $result = $mysqli->query($sql);
  $langData=array();
  while($row = $result->fetch_assoc()){array_push($langData,$row);}
  $result->close();
  echo json_encode($langData);
break;
case 'changeAdminLang': //adminLang
  $user_id=(int)($_SESSION['user_id']);
  if($user_id==7)
  {
    $lang_id=(int)($_GET['lang_id']);
    $lang_deck=(int)($_GET['lang_deck']);
    if($_GET['lang_interface_ok']=="true"){$lang_interface_ok=1;}else{$lang_interface_ok=0;};
    if($_GET['lang_interface_build']=="true"){$lang_interface_build=1;}else{$lang_interface_build=0;};
    $lang_code2=htmlspecialchars($_GET['lang_code2']);
    $lang_code2_2=htmlspecialchars($_GET['lang_code2_2']);
    $lang_code3=htmlspecialchars($_GET['lang_code3']);
    $lang_name=htmlspecialchars($_GET['lang_name']);
    $sql = "UPDATE lang SET interface_build=".$lang_interface_build.",lang_interface=".$lang_interface_ok.",lang_deck=".$lang_deck.",lang_code2='".$lang_code2."',lang_code3='".$lang_code3."',lang_code2_2='".$lang_code2_2."',lang_name='".$lang_name."' WHERE lang_id=".$lang_id;
    $mysqli->query($sql);
    echo json_encode($sql);
  }
break;
case 'changeTargetLang':
  $user_id=(int)($_SESSION['user_id']);
  $lang_id=(int)($_GET['lang_id']);
  $sql="DELETE FROM user_target_lang WHERE user_id=".$user_id." AND lang_id=".$lang_id;
  $mysqli->query($sql);
  $sql="INSERT INTO user_target_lang (user_id, lang_id) VALUE (".$user_id.",".$lang_id.")";
  $mysqli->query($sql);
  $sql="SELECT lang_id,lang_code2,lang_code2_2,lang_code3,lang_name_Origin FROM lang WHERE lang_id=".$lang_id;
  $result = $mysqli->query($sql);
  $row = $result->fetch_assoc();
  $_SESSION['target_lang_name']=$row["lang_name_Origin"];
  $_SESSION["target_lang_code3"]=$row["lang_code3"];
  $_SESSION["target_lang_code2"]=$row["lang_code2"];
  $_SESSION["target_lang_code2_2"]=$row["lang_code2_2"];
  $_SESSION["target_lang"]=(int)$row["lang_id"];
  $result->close();
  echo json_encode("done".$_SESSION["target_lang"].'-'.(int)$row["lang_id"]);
break;
case 'removeTargetLang':
  $user_id=(int)($_SESSION['user_id']);
  $lang_id=(int)($_GET['lang_id']);
  $sql="DELETE FROM user_target_lang WHERE user_id=".$user_id." AND lang_id=".$lang_id;
  $mysqli->query($sql);
  echo json_encode("done");
break;

case 'getSchoolInfo':
  $user_id=(int)($_SESSION['user_id']);
    $sql="SELECT schools.school_name,schools.password FROM `schools`
    LEFT JOIN users ON users.school_id=schools.school_id
    WHERE schools.active=1 AND users.user_id=".$user_id;
    $result = $mysqli->query($sql);
    $schoolData=array();
    while($row = $result->fetch_assoc()){$schoolData=$row;}
    echo json_encode($schoolData);
    $result->close();
break;

case 'setMnemo':
  $card_id=(int)$_GET['card_id'];
  $user_id=(int)$_GET['user_id'];
  $phrase=htmlspecialchars($_GET['phrase']);
  $phrase=$mysqli->real_escape_string($phrase);
  $sql = "INSERT INTO mnemotech (card_id,user_id, phrase) "
          . "VALUES (".$card_id.",".$user_id.",'".$phrase."')";
  $mysqli->query($sql);
  $card_id=$mysqli->insert_id;
  echo $card_id;
break;
case 'deleteMnemo':
  $mnemo_id=(int)$_GET['mnemo_id'];
  $sql = "DELETE FROM mnemotech WHERE id=".$mnemo_id;
  $mysqli->query($sql);
break;
case 'getMnemo':
  $card_id=(int)$_GET['card_id'];
  $mnemo=array();
  $sql="SELECT * FROM mnemotech WHERE card_id=".$card_id;
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
  				array_push($mnemo,$row);
  		}
  $result->close();
  echo json_encode($mnemo);
break;

case 'getMnemoNumber':
  $card_id=(int)$_GET['card_id'];
  $sql="SELECT COUNT(*) AS nbreComment FROM mnemotech WHERE card_id=".$card_id;
  $result = $mysqli->query($sql);
  $myResult = $result->fetch_assoc();
  $nbreComment=$myResult["nbreComment"];
  $result->close();
  echo $nbreComment;
break;
case 'updateCheckedDeck':
  $deck_id=(int)$_GET['deck_id'];
  $checkedValue=(int)$_GET['checked'];
  $sql = "UPDATE decks SET checked=".$checkedValue." WHERE deck_id=".$deck_id;
  $mysqli->query($sql);
  echo json_encode("done");
break;
case 'setLang':
  $deck_id=(int)$_GET['deck_id'];
  $lang_id=(int)$_GET['lang'];
  $sql = "UPDATE decks SET lang_id=".$lang_id." WHERE deck_id=".$deck_id;
  $mysqli->query($sql);
  $sql = "UPDATE cards SET lang_id=".$lang_id." WHERE deck_id=".$deck_id;
  $mysqli->query($sql);
  echo json_encode("done");
break;
case 'setTag':
  $deck_id=(int)$_GET['deck_id'];
  $tag=htmlspecialchars($_GET['tag']);
  $tag=$mysqli->real_escape_string($tag);
  $sql = "DELETE FROM tags WHERE deck_id=".$deck_id." AND tag_name='".$tag."'";
  $mysqli->query($sql);

  $sql = "INSERT INTO tags (deck_id, tag_name) "
          . "VALUES (".$deck_id.",'".$tag."')";
  $mysqli->query($sql);
  $tag_id=$mysqli->insert_id;
  echo $tag_id;
break;
case 'deleteTag':
  $deck_id=(int)$_GET['deck_id'];
  $tag=htmlspecialchars($_GET['tag']);
  $tag=$mysqli->real_escape_string($tag);
  $sql = "DELETE FROM tags WHERE deck_id=".$deck_id." AND tag_name='".$tag."'";
  $mysqli->query($sql);
break;
case 'getTag':
  $deck_id=(int)$_GET['deck_id'];
  $tags=array();
  $sql="SELECT * FROM tags WHERE deck_id=".$deck_id;
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
  				array_push($tags,$row);
  		}
  $result->close();
  echo json_encode($tags);
break;
case 'getTagList':
  $user_id=(int)$_SESSION['user_id'];
  $deck_id=(int)$_GET['deck_id'];
  $tags=array();
  $sql="SELECT tag_name, tag_id FROM tags LEFT JOIN
  user_deck_droit ON user_deck_droit.deck_id=tags.deck_id WHERE user_deck_droit.user_id=".$user_id." AND user_deck_droit.droit='admin' AND tag_name NOT IN (select tag_name From tags where deck_id=".$deck_id.") GROUP BY tag_name" ;
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
  				array_push($tags,$row);
  		}
  $result->close();
  echo json_encode($tags);
break;
case 'getPhrasePlus':
  $mot=$mysqli->real_escape_string(htmlspecialchars($_GET['mot']));
  $lang_id=(int)$_GET['target_lang_id'];
  $phrase=array();
  $sql="SELECT tatoeba_sent.phrase FROM tatoeba_sent LEFT JOIN lang ON lang.lang_code3=tatoeba_sent.lang WHERE tatoeba_sent.phrase REGEXP '[[:<:]]".$mot."[[:>:]]' AND lang.lang_id='".$lang_id."' LIMIT 15";
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
  				array_push($phrase,$row);
  		}
  $result->close();

  //echo json_encode($sql);
  echo json_encode($phrase);
break;
case 'getPhrase':
  $mot=$mysqli->real_escape_string(htmlspecialchars($_GET['mot']));
  $phrase=array();
  $sql="SELECT phrase FROM tatoeba_sent WHERE phrase REGEXP '[[:<:]]".$mot."[[:>:]]' LIMIT 3";
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
  				array_push($phrase,$row);
  		}
  $result->close();
  echo json_encode($phrase);
break;
case 'translateDeck':
  /*$phrase=urlencode(htmlspecialchars($_GET['mot']));

  $url = 'https://translate.yandex.net/api/v1.5/tr.json/translate?key=&lang=fr-tr&text='.$phrase;
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_TIMEOUT, 100);
  curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . './cacert.pem');
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
  $data = curl_exec($curl);
  curl_close($curl);*/
  //j'utilise google pour traduire
  if($_SESSION["user_id"]!=7){exit();}
  $lang_ini_id=(int)$_GET['lang_ini'];
  $lang_fin_id=(int)$_GET['lang_fin'];
  $deck_id=(int)$_GET["deck_id"];
  $sql="SELECT lang_code2 FROM lang WHERE lang_id=".$lang_ini_id;
  $result = $mysqli->query($sql);
  $row = $result->fetch_assoc();
  $lang_ini=$row['lang_code2'];
  $sql="SELECT lang_code2 FROM lang WHERE lang_id=".$lang_fin_id;
  $result = $mysqli->query($sql);
  $row = $result->fetch_assoc();
  $lang_fin=$row['lang_code2'];
  
  $translate = new TranslateClient([
      'key' => $_ENV["GOOGLE_API_KEY_TRANSLATION"]
  ]);
  // Translate text from fr to ....
  //j'enleve les audios
  $sql="SELECT card_id,hasAudio FROM cards WHERE deck_id=".$deck_id." AND hasAudio=1";
  $result = $mysqli->query($sql);
  while($row = $result->fetch_assoc())
  {
    $card_id=$row["card_id"];
    if($row["hasAudio"]==1){unlink("card_audio/card_".$card_id.".wav");}
  }
  $result->free();

  $values="";
  $virgule="";
  $sql="SELECT card_id,mot FROM `cards` WHERE deck_id=".$deck_id;
  $result = $mysqli->query($sql);
  $cards=array();
  while ($row = $result->fetch_assoc()) {
  //array_push($stats,$row);
  $mot_fin = $translate->translate($row["mot"], [
      'target' => $lang_fin,'source' => $lang_ini,'format'=>'text'
  ]);
  $values.=$virgule.'('.$row['card_id'].',"'.$mot_fin['text'].'","'.$row["mot"].'",0)';
  $virgule=",";
  }
  $result->close();
  //titre et lang deck
  $sql="SELECT deck_name,status FROM `decks` WHERE deck_id=".$deck_id;
  $result = $mysqli->query($sql);
  $cards=array();
  $row = $result->fetch_assoc();
  $deck_name=substr($row["deck_name"], 0, -6);
  $deck_status=$row["status"];
  //array_push($stats,$row);
  $deck_name_translate = $translate->translate($deck_name, [
      'target' => $lang_fin,'source' => $lang_ini,'format'=>'text'
  ]);
  $result->close();

  $sql='UPDATE decks SET status="'.$deck_status.'",deck_name="'.$deck_name_translate["text"].'", lang_id="'.$lang_fin_id.'" WHERE deck_id='.$deck_id;
  $mysqli->query($sql);
  $sql='UPDATE cards SET lang_id="'.$lang_fin_id.'" WHERE deck_id='.$deck_id;
  $mysqli->query($sql);

  //je ne fais une seule requette pour updater toutes les carteq
  $sql="INSERT IGNORE cards (card_id,mot,mot_trad,hasAudio) VALUES ".$values."
  ON DUPLICATE KEY UPDATE hasAudio=VALUES(hasAudio),mot=VALUES(mot),mot_trad=VALUES(mot_trad)";

  $mysqli->query($sql);
  echo json_encode("done");
  
break;
case 'getTagsDecksList':
  $tagdecks=array();
  //$niveau=htmlspecialchars($_SESSION['niveau']);
  //$niveau=$mysqli->real_escape_string($niveau);
  $sql="SELECT tag_name,COUNT(DISTINCT decks.deck_id) AS nbreDeck FROM tags
  LEFT JOIN decks ON decks.deck_id=tags.deck_id WHERE decks.active=1 GROUP BY tag_name";
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
  				array_push($tagdecks,$row);
  		}
  $result->close();
  echo json_encode($tagdecks);
break;
case 'getDecks'://obsolete?
  //récupération des decks "ok et coop" des classes des élèves et des listes perso de l'élève
  $user_id=(int)$_SESSION['user_id'];
  $decks=array();
  $nbreMots=array();
  $current_tps=time();
  $sql="SELECT distinct classes.class_name, deck_class.status as deck_class_status, decks.user_id as user_id,decks.royalties as royalties, tags.tag_name,
  decks.deck_name,decks.deck_id,decks.hasImage,decks.classe,decks.lang,decks.nbreMots,decks.position, users.user_id as creator_id,users.first_name,users.last_name,users.classe, decks.likes,deck_class.position as deck_position FROM `decks`
  LEFT JOIN deck_class ON decks.deck_id=deck_class.deck_id
  LEFT JOIN user_class ON deck_class.class_id=user_class.class_id
  LEFT JOIN tags ON decks.deck_id=tags.deck_id
  LEFT JOIN users ON users.user_id=decks.user_id
  LEFT JOIN classes ON classes.class_id=user_class.class_id
  WHERE decks.active=1 AND ((deck_class.status='ok' OR deck_class.status='coop') AND user_class.user_id = ".$user_id.") OR decks.user_id=".$user_id." ORDER BY deck_class.position ASC , users.type DESC";

  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc())
  {
    array_push($decks,$row);
  }
  $result->close();
  //récupération des résultats de l'élève
  $sql="SELECT Distinct deck_class.status as deck_class_status,activite.card_id,activite.user_id,decks.deck_id,COUNT(OptimalRD) AS NbreKnown,decks.nbreMots FROM `decks`
  LEFT JOIN deck_class ON decks.deck_id=deck_class.deck_id
  LEFT JOIN cards ON decks.deck_id=cards.deck_id
  LEFT JOIN activite ON activite.card_id=cards.card_id
  WHERE cards.active=1 AND decks.active=1 AND activite.user_id=".$user_id." AND OptimalRD > ".$current_tps." GROUP BY decks.deck_id, deck_class.class_id";
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc())
  {
    array_push($nbreMots,$row);
  }
  //echo $sql;
  $data = array ('decks'=>$decks,'nbreMots'=>$nbreMots);
  echo json_encode($data);

break;

case 'getClassDecks':
  $user_id=(int)$_SESSION['user_id'];
  $class_id=(int)($_GET['class_id']);
  $decks=array();
  $lang_id=0;
  $current_tps=time();
  if(isset($_SESSION["target_lang"]))
  {$lang_id=(int)$_SESSION["target_lang"];}

  $sql="SELECT classes.status FROM classes WHERE classes.class_id=".$class_id;
  $result = $mysqli->query($sql);
  $row = $result->fetch_assoc();
  $class_status=$row["status"];
  $result->free();
  if($class_status=="explore")
  {
    $sql="SELECT tags.tag_name,decks.status as deck_status, decks.user_id, decks.deck_name,decks.deck_id,decks.royalties,decks.hasImage,decks.classe,decks.nbreMots,users.first_name,users.last_name, decks.likes FROM `decks`
    LEFT JOIN users ON users.user_id=decks.user_id
    LEFT JOIN tags ON tags.deck_id=decks.deck_id
    WHERE decks.active=1 AND users.active=1 AND decks.lang_id='".$lang_id."' AND (decks.status='public' OR decks.status='premium')";
  }
  else if($class_status=="perso")
  {
    $sql="SELECT deck_class.position as position,t.droit as droit, users.type as creator_type,classes.status as class_status,decks.status as deck_status,deck_class.status as deck_class_status, decks.visible,decks.user_id, tags.tag_name,
    decks.deck_name,decks.royalties,decks.deck_id,decks.lang,deck_class.visible,decks.hasImage,decks.classe,decks.nbreMots,decks.position, users.first_name,users.last_name,users.classe,decks.likes FROM `decks`
    LEFT JOIN tags ON decks.deck_id=tags.deck_id
    LEFT JOIN users ON users.user_id=decks.user_id
    LEFT JOIN deck_class ON decks.deck_id=deck_class.deck_id
    LEFT JOIN (SELECT droit,user_id,deck_id FROM user_deck_droit WHERE user_id=".$user_id." ) as t ON t.deck_id=decks.deck_id
    LEFT JOIN classes ON deck_class.class_id=classes.class_id
    WHERE users.active=1 AND decks.active=1 AND deck_class.class_id = ".$class_id." AND decks.lang_id=".$lang_id."
    ORDER BY deck_class.status DESC,deck_class.position ASC, deck_class.visible DESC , users.type DESC";
  }
  else {
    $sql="SELECT distinct deck_class.position as position,t.droit as droit, users.type as creator_type,classes.status as class_status,decks.status as deck_status,deck_class.status as deck_class_status,
    decks.visible,decks.user_id, tags.tag_name, decks.deck_name,decks.royalties,decks.deck_id,decks.lang,deck_class.visible,decks.hasImage,decks.classe,decks.nbreMots,decks.position, users.first_name,users.last_name,users.classe,decks.likes FROM `decks`
    LEFT JOIN tags ON decks.deck_id=tags.deck_id
    LEFT JOIN users ON users.user_id=decks.user_id
    LEFT JOIN deck_class ON decks.deck_id=deck_class.deck_id
    LEFT JOIN (SELECT droit,user_id,deck_id FROM user_deck_droit WHERE user_id=".$user_id." ) as t ON t.deck_id=decks.deck_id
    LEFT JOIN classes ON deck_class.class_id=classes.class_id
    WHERE decks.active=1 AND deck_class.class_id = ".$class_id."
    ORDER BY deck_class.status DESC,deck_class.position ASC, deck_class.visible DESC , users.type DESC";
  }
  
  	$result = $mysqli->query($sql);
    //echo $sql;
  while ($row = $result->fetch_assoc())
  {
    array_push($decks,$row);
  }
  $result->close();
  //récupération des résultats de l'élève
  $nbreMots=array();
  $sql="SELECT Distinct deck_class.status as deck_class_status,activite.card_id,activite.user_id,decks.deck_id,COUNT(OptimalRD) AS NbreKnown,decks.nbreMots FROM `decks`
  LEFT JOIN deck_class ON decks.deck_id=deck_class.deck_id
  LEFT JOIN cards ON decks.deck_id=cards.deck_id
  LEFT JOIN activite ON activite.card_id=cards.card_id
  WHERE cards.active=1 AND decks.active=1 AND deck_class.class_id = ".$class_id." AND activite.user_id=".$user_id." AND OptimalRD > ".$current_tps." GROUP BY decks.deck_id, deck_class.class_id";
  $result = $mysqli->query($sql);
  //echo $sql;
  while ($row = $result->fetch_assoc())
  {
    array_push($nbreMots,$row);
  }
  //echo $sql;
  //echo json_encode($decks);
  $data = array ('decks'=>$decks,'nbreMots'=>$nbreMots,'lang_id'=>$lang_id);
  echo json_encode($data);
break;
case 'addUsersToClass':
  $user_id=(int)$_SESSION["user_id"];
  $class_id=(int)$_GET["class_id"];
  $mails=htmlspecialchars($_GET['mails']);
  $mails=$mysqli->real_escape_string($mails);
  $mails=explode(',',$mails);
  $type=htmlspecialchars($_GET['type']);
  $type=$mysqli->real_escape_string($type);
  //check if user has right to add users (if user is "profs")
  $result = $mysqli->query('SELECT user_class.role FROM user_class Where class_id = ' . $class_id.' AND user_id='.$user_id);
  $row = $result->fetch_assoc();
    $role=$row["role"];
  $result->free();

  if($role=="prof"){
    foreach($mails as $mail)
    {
      $sql="SELECT user_id FROM users Where email ='".$mail."'";
      $result = $mysqli->query($sql);
      $row = $result->fetch_assoc();
      $this_user_id=$row["user_id"];
      $result->close();
      if($this_user_id!=null){

        $sql="DELETE FROM user_class Where user_id =".$this_user_id." AND class_id=".$class_id;
        $mysqli->query($sql);
        $sql='INSERT INTO user_class (user_id, class_id, role) VALUE ('.$this_user_id.','.$class_id.',"'.$type.'")';
        $mysqli->query($sql);
      }
    }
  }
  echo json_encode("done");
break;
case 'getMyDecks':
  $user_id=(int)$_SESSION["user_id"];
  //if($_SESSION['type']=="prof")
  //{
    $decks=array();
    $sql="SELECT t.droit, decks.status as deck_status, classes.class_name,decks.user_id, tags.tag_name,decks.deck_name,decks.royalties,decks.deck_id,decks.hasImage,decks.nbreMots,decks.likes,decks.lang FROM `decks`
    LEFT JOIN tags ON decks.deck_id=tags.deck_id
    LEFT JOIN deck_class ON decks.deck_id=deck_class.deck_id
    LEFT JOIN classes ON deck_class.class_id=classes.class_id
    LEFT JOIN user_class ON user_class.class_id=classes.class_id
    LEFT JOIN (SELECT droit,user_id,deck_id FROM user_deck_droit WHERE user_id=".$user_id." ) as t ON t.deck_id=decks.deck_id
    WHERE  decks.active=1 AND t.user_id = ".$user_id." AND (user_class.user_id=".$user_id." OR deck_class.class_id IS NULL) AND (classes.status!='archive' OR classes.status IS NULL) ORDER BY decks.deck_name ASC";
    $result = $mysqli->query($sql);
    while ($row = $result->fetch_assoc())
    {array_push($decks,$row);}
    $result->close();
    $data = array ('decks'=>$decks,'nbreMots'=>array());
    echo json_encode($data);
  //}
break;
case 'getAvailableDecks':
  $user_id=(int)$_SESSION["user_id"];
  $lang_id=(int)$_SESSION["target_lang"];

    $decks=array();
    $sql="SELECT tags.tag_name,decks.status as deck_status, decks.user_id, decks.deck_name,decks.deck_id,decks.royalties,decks.hasImage,decks.classe,decks.nbreMots,users.first_name,users.last_name, decks.likes FROM `decks`
    LEFT JOIN users ON users.user_id=decks.user_id
    LEFT JOIN tags ON tags.deck_id=decks.deck_id
    LEFT JOIN user_deck_droit ON decks.deck_id=user_deck_droit.deck_id
    WHERE decks.active=1 AND decks.lang_id='".$lang_id."' AND ((user_deck_droit.user_id=".$user_id.") OR (decks.user_id = ".$user_id." OR decks.status='public' OR decks.status='premium'))";
    //echo json_encode($sql);
    $result = $mysqli->query($sql);
    while ($row = $result->fetch_assoc())
    {array_push($decks,$row);}
    $result->close();
    $data = array ('decks'=>$decks,'nbreMots'=>array());
    echo json_encode($data);
break;

case 'SetVisi':
  $class_id=(int)($_GET['class_id']);
  $deck_id=(int)($_GET['deck_id']);
  $visi=(int)($_GET['visi']);
  $sql="UPDATE deck_class SET visible=".$visi." WHERE class_id=".$class_id." AND deck_id=".$deck_id;
  $mysqli->query($sql);
break;
case 'removeDeckFromClass':
  $class_id=(int)($_GET['class_id']);
  $deck_id=(int)($_GET['deck_id']);
  $sql = "DELETE FROM deck_class WHERE deck_id=".$deck_id." AND class_id=".$class_id;
  $mysqli->query($sql);
break;
case 'addDecksToClass':
  $class_id=(int)($_GET['class_id']);
  $deckslist=htmlspecialchars($_GET['decks']);
  $deckslist=$mysqli->real_escape_string($deckslist);
  $decks_id=explode(',',$deckslist);

  $multipleValueDelete="";
  $multipleValueInsert="";
  $virgule="";
  foreach ($decks_id as $deck_id) {
    $deck_id=(int)$deck_id;
    $multipleValueDelete.=$virgule."(".$class_id.",".$deck_id.")";
    $multipleValueInsert.=$virgule."(".$class_id.",".$deck_id.",1)";
    $virgule=",";
  }
  $sql = "DELETE FROM deck_class WHERE (class_id,deck_id) IN (".$multipleValueDelete.")";
  $mysqli->query($sql);
  $sql = "INSERT INTO deck_class (class_id,deck_id,visible) "
          . "VALUES ".$multipleValueInsert;
  $mysqli->query($sql);
  echo json_encode($sql);
break;
case 'addDeckToClass':
  //TODO: put limitation on adding premium decks.
  $user_id=(int)($_SESSION['user_id']);
  $class_id=(int)($_GET['class_id']);
  $deck_id=(int)($_GET['deck_id']);
  //check if the user has rights on this class.
  $result = $mysqli->query('SELECT user_class.role FROM user_class Where class_id = ' . $class_id.' AND user_id='.$user_id);
  $row = $result->fetch_assoc();
    $role=$row["role"];
  $result->free();
  //check si le deck est premiums
  $importable=true;
  if($_SESSION["premiumDeckAccess"]==false){
  $result = $mysqli->query('SELECT decks.status FROM decks Where deck_id = ' . $deck_id);
  $row = $result->fetch_assoc();
    $status=$row["status"];
  $result->free();
    if($status=="premium")
    {$importable=false;}
  }
  if($role=="prof" && $importable)//role sur la classe
  {

  $sql = "DELETE FROM deck_class WHERE deck_id=".$deck_id." AND class_id=".$class_id;
  $mysqli->query($sql);
  $sql = "INSERT INTO deck_class (deck_id,class_id,visible) "
          . "VALUES (".$deck_id.",".$class_id.",0)";
  $mysqli->query($sql);
  echo json_encode("ok");
  }
break;
case 'ProposeDeckToClass':
  $class_id=(int)($_GET['class_id']);
  $deck_id=(int)($_GET['deck_id']);
  $sql = "DELETE FROM deck_class WHERE deck_id=".$deck_id." AND class_id=".$class_id;
  $mysqli->query($sql);
  $sql = "INSERT INTO deck_class (deck_id,class_id,visible,status) "
          . "VALUES (".$deck_id.",".$class_id.",0,'waiting')";
  $mysqli->query($sql);
break;
case 'ProposeDeckCoopToClass':
  $class_id=(int)($_GET['class_id']);
  $deck_id=(int)($_GET['deck_id']);
  $user_id=(int)($_SESSION['user_id']);
  $result = $mysqli->query('SELECT user_class.role FROM user_class Where class_id = ' . $class_id.' AND user_id='.$user_id);
  $row = $result->fetch_assoc();
    $role=$row["role"];
  $result->free();
  if($role=="prof")
  {$sql = "DELETE FROM deck_class WHERE deck_id=".$deck_id." AND class_id=".$class_id;
  $mysqli->query($sql);
  $sql = "INSERT INTO deck_class (deck_id,class_id,visible,status) "
          . "VALUES (".$deck_id.",".$class_id.",0,'coop')";
  $mysqli->query($sql);}
break;
case 'getTrophyUser':
  $user_id=(int)$_SESSION['user_id'];
  //pour le nbre de quiz gagné, le nombre de carte mystère trouvé, le nbre de jours au dessus de l'objectif
  $sql="SELECT COUNT(*) AS nbre,type FROM `trophy` WHERE user_id=".$user_id." GROUP BY type";
  $result = $mysqli->query($sql);
  $trophy=array();
  while ($row = $result->fetch_assoc()) {
  				array_push($trophy,$row);
  		}
  $result->close();
  $sql="SELECT COUNT(*) AS nbre FROM `decks` WHERE classe!='myDecks' AND user_id=".$user_id;
  $result = $mysqli->query($sql);
  $row = $result->fetch_assoc();
  array_push($trophy,array("nbre"=>$row["nbre"],"type"=>"crea"));
  $result->close();
  echo json_encode($trophy);
break;
case 'SetDroitToProf':
  $user_id=(int)$_SESSION['user_id'];
  $deck_id=(int)$_GET['deck_id'];
  $class_id=(int)$_GET['class_id'];
  $droit=htmlspecialchars($_GET['droit']);
  $droit=$mysqli->real_escape_string($droit);
  $result = $mysqli->query('SELECT user_deck_droit.droit FROM user_deck_droit Where deck_id = ' . $deck_id.' AND user_id='.$user_id.' ORDER BY droit ASC');
  $row = $result->fetch_assoc();
    $Creatordroit=$row["droit"];
  $result->free();
  if($Creatordroit=="admin")
  {
    $today=date("Y-m-d");
    $sql1='DELETE user_deck_droit FROM user_deck_droit left join user_class on user_class.user_id=user_deck_droit.user_id Where user_deck_droit.deck_id = ' . $deck_id.' AND user_class.class_id='.$class_id.' AND user_class.role="prof" AND user_deck_droit.droit!="admin"';
    $sql2='INSERT INTO user_deck_droit(user_id,deck_id,jour,droit) SELECT user_class.user_id,'.$deck_id.',"'.$today.'","'.$droit.'" FROM user_class LEFT JOIN user_deck_droit ON user_deck_droit.user_id=user_class.user_id WHERE user_class.class_id='.$class_id.' AND user_class.role="prof" AND user_deck_droit.droit!="admin"';
    $sql3 = "INSERT INTO `deck_class`(`deck_id`, `class_id`,status)
    SELECT ".$deck_id.",user_class.class_id,'shared' FROM user_class LEFT JOIN users ON user_class.user_id=users.user_id LEFT JOIN user_class as UC ON UC.user_id=user_class.user_id where UC.class_id=".$class_id." AND UC.role='prof' AND user_class.role='perso'";
    $mysqli->query($sql1);
    $mysqli->query($sql2);
    $mysqli->query($sql3);
    echo json_encode(array("status"=>'ok',"sql1"=>$sql1,"sql2"=>$sql2));
  }
break;
case 'getUserWithDroit':
  $user_id=(int)$_SESSION['user_id'];
  $deck_id=(int)$_GET['deck_id'];
  $result = $mysqli->query('SELECT user_deck_droit.droit FROM user_deck_droit Where deck_id = ' . $deck_id.' AND user_id='.$user_id.' ORDER BY droit ASC');
  $row = $result->fetch_assoc();
    $droit=$row["droit"];
  $result->free();
  if($droit=="admin")
  {
    $users=array();
  $sql="SELECT user_deck_droit.droit,users.first_name,users.last_name,users.user_id FROM user_deck_droit LEFT JOIN users on users.user_id=user_deck_droit.user_id Where deck_id =" . $deck_id;
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
  				array_push($users,$row);
  		}
  echo json_encode($users);
  $result->close();
  }
  else{echo json_encode("probleme");}
break;
case 'getMyFriends':
  $user_id=(int)$_SESSION['user_id'];
  $friends=array();
  $sql="SELECT distinct user_friends.friend_id,user_friends.status,users.first_name,users.last_name,users.nbreCoins,users.avatar_id FROM user_friends LEFT JOIN users ON user_friends.friend_id=users.user_id Where user_friends.user_id =".$user_id;
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
          array_push($friends,$row);
      }
  $result->close();
  echo json_encode($friends);
break;
case 'addFriend':
  $F_user_id=(int)$_GET['F_user_id'];
  $user_id=(int)$_SESSION['user_id'];
  $sql = "DELETE FROM `user_friends` WHERE (`user_id`=".$user_id." AND `friend_id`=".$F_user_id.") OR (`friend_id`=".$user_id." AND `user_id`=".$F_user_id.")";
  $mysqli->query($sql);
  $sql = "INSERT INTO `user_friends`(`user_id`, `friend_id`, `status`)
  VALUES (".$F_user_id.",".$user_id.",'accepted'),(".$user_id.",".$F_user_id.",'accepted')";
  $mysqli->query($sql);
  echo json_encode("done");
break;
case 'removeFriend':
  $F_user_id=(int)$_GET['F_user_id'];
  $user_id=(int)$_SESSION['user_id'];
  $sql = "DELETE FROM `user_friends` WHERE (`user_id`=".$user_id." AND `friend_id`=".$F_user_id.") OR (`friend_id`=".$user_id." AND `user_id`=".$F_user_id.")";
  $mysqli->query($sql);
    echo json_encode("done");
break;
case 'checkDemandFriend':
  $user_id=(int)$_SESSION['user_id'];
  $friends=array();
  $sql="SELECT user_friends.friend_id,user_friends.status,users.first_name,users.last_name,users.nbreCoins,users.avatar_id FROM user_friends LEFT JOIN users ON user_friends.friend_id=users.user_id Where user_friends.friend_id =".$user_id." AND status='demand'";
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
          array_push($friends,$row);
      }
  $result->close();
  echo json_encode($friends);
break;
case 'askForFriend':
  $user_id=(int)$_SESSION['user_id'];
  $friend_id=(int)$_GET['friend_id'];

  if($friend_id==$user_id){echo json_encode(array("status"=>"Vous êtes déjà votre meilleur ami !"));exit();}
  //check if a demand is already pending of if they are already friend
  $sql="SELECT COUNT(*) as alreadyFriend,status FROM user_friends Where user_id=".$user_id." AND friend_id =".$friend_id;
  $result = $mysqli->query($sql);
  $row = $result->fetch_assoc();
  $alreadyFriend=$row["alreadyFriend"];
  $alreadyFriendStatus=$row["status"];
  $result->close();
  if($alreadyFriend>0){
    if($alreadyFriendStatus=='accepted'){echo json_encode(array("status"=>"Vous êtes déjà amis !"));exit();}
    else{echo json_encode(array("status"=>"Demande déjà envoyé !"));exit();}
  }

  $sql = "DELETE FROM `user_friends` WHERE user_id=".$user_id." AND friend_id =".$friend_id;
  $mysqli->query($sql);

  $sql = "INSERT INTO `user_friends`(`user_id`, `friend_id`, `status`)
  VALUES (".$friend_id.",".$user_id.",'demand'),(".$user_id.",".$friend_id.",'waiting')";
  $mysqli->query($sql);

  $sql="SELECT email FROM users Where user_id =".$friend_id;
  $result = $mysqli->query($sql);
  $row = $result->fetch_assoc();
  $F_mail=$row["email"];
  $result->close();

  $first_name=htmlspecialchars($_SESSION["first_name"]);
  $destinataire=$F_mail;
  $email_expediteur='info@exolingo.com';
  $email_reply='no-reply@exolingo.com';
  $sujet=$first_name.' wants to be your friend ( ExoLingo )';
  $message_texte='Hi,'.
       $first_name.' wants to be your friend and follow you.'.
       'http://www.exolingo.com/decks.php';

  $message_html='Hi,'."<br>".
       $first_name.' wants to be your friend and follow you.<br>'.
       '<a href="http://www.exolingo.com/decks.php">Click here to see the demand</a>';

  $frontiere = '-----=' . md5(uniqid(mt_rand()));
  $headers = 'From: "ExoLingo" <'.$email_expediteur.'>'."\n";
  $headers .= 'Return-Path: <'.$email_reply.'>'."\n";
  $headers .= 'MIME-Version: 1.0'."\n";
  $headers .= 'Content-Type: multipart/alternative; boundary="'.$frontiere.'"';

  $message = 'This is a multi-part message in MIME format.'."\n\n";

  $message .= '--'.$frontiere."\n";
  $message .= 'Content-Type: text/plain; charset="iso-8859-1"'."\n";
  $message .= 'Content-Transfer-Encoding: 8bit'."\n\n";
  $message .= $message_texte."\n\n";

  $message .= '--'.$frontiere."\n";
  $message .= 'Content-Type: text/html; charset="iso-8859-1"'."\n";
  $message .= 'Content-Transfer-Encoding: 8bit'."\n\n";
  $message .= $message_html."\n\n";

  $result=mail($destinataire,$sujet,$message,$headers,"-f ".$email_expediteur) ;
  echo json_encode(array("status"=>""));
break;
case 'getUserFromMailName':
  $user_id=(int)$_SESSION['user_id'];
  $input=htmlspecialchars($_GET['input']);
  $input=$mysqli->real_escape_string($input);
  $usersFound=array();
  $sql="SELECT users.avatar_id,users.first_name,users.last_name,users.user_id FROM users WHERE users.user_id!=".$user_id." AND (users.email='".$input."' OR CONCAT(users.first_name,' ',users.last_name) LIKE '%".$input."%') LIMIT 10";
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
          array_push($usersFound,$row);
      }
  $result->close();

  echo json_encode($usersFound);
break;
case 'getMyAvatar':
  $user_id=(int)$_SESSION['user_id'];
  $allMyAvatars=array();
  $sql="SELECT * FROM user_avatar Where user_id=".$user_id;
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
          array_push($allMyAvatars,$row);
      }
  $result->close();
  echo json_encode(array("allMyAvatars"=>$allMyAvatars,"myAvatar_id"=>(int)$_SESSION["avatar_id"]));
break;
case 'switchAvatar':
  $user_id=(int)$_SESSION['user_id'];
  $avatar_id=(int)$_GET['avatar_id'];
  $sql="UPDATE users SET avatar_id=".$avatar_id." WHERE user_id=".$user_id;
  $mysqli->query($sql);
  $_SESSION['avatar_id']=$avatar_id;
  echo json_encode("done");
break;
case 'getRubyAndXp':
$user_id=(int)$_SESSION['user_id'];

$sql="SELECT ruby,nbreCoins FROM users WHERE user_id=".$user_id;
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();
$user_ruby=$row["ruby"];
$xp=$row["nbreCoins"];
$result->close();

echo json_encode(array("nbreRuby"=>$user_ruby,"xp"=>$xp));

break;
case 'buyEgg':
  $user_id=(int)$_SESSION['user_id'];

  $sql="SELECT ruby FROM users WHERE user_id=".$user_id;
  $result = $mysqli->query($sql);
  $row = $result->fetch_assoc();
  $user_ruby=$row["ruby"];
  $result->close();
  if($user_ruby>=100){
    $sql="UPDATE users SET ruby=ruby-100 WHERE user_id=".$user_id;
    $mysqli->query($sql);
  }
  else{
    echo json_encode(array("status"=>"MoneyProblem"));exit();
  }
  //check for other condition (lvl...)
  $user_avatar_list=array();
  $sql="SELECT * FROM user_avatar WHERE user_id=".$user_id;
  $result = $mysqli->query($sql);
  while($row = $result->fetch_assoc())
  {array_push($user_avatar_list,$row["avatar_id"]);}
  $result->close();

  $newAvatar_id=rand(0,720);
  while(array_search($newAvatar_id,$user_avatar_list))
  {
    $newAvatar_id++;
    $newAvatar_id%720;
  }
  $sql="INSERT INTO user_avatar (user_id,avatar_id,status) VALUES (".$user_id.",".$newAvatar_id.",'ok')";
  $mysqli->query($sql);
  //$_SESSION["avatar_id"]=$newAvatar_id;
  echo json_encode(array("status"=>"ok","newAvatar_id"=>$newAvatar_id));
break;
case 'setUserWithDroit':
  $user_id=(int)$_SESSION['user_id'];
  $sharedUser_id=(int)$_GET['sharedUser_id'];
  $deck_id=(int)$_GET['deck_id'];

  $droit=htmlspecialchars($_GET['droit']);
  $droit=$mysqli->real_escape_string($droit);
  //verif que l'user a les droits:
  $result = $mysqli->query('SELECT user_deck_droit.droit FROM user_deck_droit Where deck_id ='. $deck_id.' AND user_id='.$user_id);
  $row = $result->fetch_assoc();
  $myDroit=$row["droit"];
  $result->free();
  if($myDroit=="admin")
  {
  //get user from email
    $sql="SELECT email, user_id FROM users Where user_id =".$sharedUser_id;
    $result = $mysqli->query($sql);
    $row = $result->fetch_assoc();
    $this_user_id=$row["user_id"];
    $email=$row["email"];
    $result->close();
    if($this_user_id==null){echo json_encode(array("status"=>"Aucun utilisateur trouvé avec cet ID."));exit();}
    $result = $mysqli->query('SELECT user_deck_droit.droit FROM user_deck_droit Where deck_id = ' . $deck_id.' AND user_id='.$this_user_id.' LIMIT 1');
    $row = $result->fetch_assoc();
    $myDroit=$row["droit"];
    $result->free();
    if($myDroit=="admin")
    {echo json_encode(array("status"=>"l'utilisateur a déjà les droits d'administrateur sur cette liste"));exit();}
    if($myDroit==null){
      $today=date("Y-m-d");
      $sql = "INSERT INTO `user_deck_droit`(`user_id`, `deck_id`, `jour`, `droit`)
      VALUES (".$this_user_id.",".$deck_id.",'".$today."','".$droit."')";
      $mysqli->query($sql);
      $sql = "INSERT INTO `deck_class`(`deck_id`, `class_id`,status)
      SELECT ".$deck_id.",user_class.class_id,'shared' from user_class where user_id=".$this_user_id." AND role='perso'";
      $mysqli->query($sql);
      }
    else{
      $today=date("Y-m-d");
      $sql = "UPDATE `user_deck_droit` SET `jour`='".$today."', droit='".$droit."'
      WHERE user_id=".$this_user_id." AND deck_id=".$deck_id;
      $mysqli->query($sql);
      $sql = "INSERT INTO `deck_class`(`deck_id`, `class_id`, `status`)
      SELECT ".$deck_id.",user_class.class_id,'shared' from user_class where user_id=".$this_user_id." AND role='perso'";
      $mysqli->query($sql);
      }

      $result = $mysqli->query('SELECT deck_name FROM decks Where deck_id = ' . $deck_id);
      $row = $result->fetch_assoc();
      $deck_name=$row["deck_name"];
      $result->free();

      $result = $mysqli->query('SELECT first_name,last_name FROM users Where user_id = ' . $user_id);
      $row = $result->fetch_assoc();
      $first_name=$row["first_name"];
      $last_name=$row["last_name"];
      $result->free();

      $result = $mysqli->query('SELECT first_name,email FROM users Where user_id = ' . $this_user_id);
      $row = $result->fetch_assoc();
      $email=$row["email"];
      $first_name_dest=$row["first_name"];
      $result->free();

        $name=$first_name." ".$last_name;
        $destinataire=$email;
        $email_expediteur='info@exolingo.com';
        $email_reply='no-reply@exolingo.com';
        $sujet=$first_name.' shared a list with you ( ExoLingo )';
        $message_texte='Hi '.$first_name_dest.','.
             $first_name.' shared the list '.$deck_name.' with you: '.
             'http://www.exolingo.com/edit_deck.php?deck_id='.$deck_id;

        $message_html='Hi '.$first_name_dest.','."<br>".
             $first_name.' shared a list with you :<br>'.
             '<a href="http://www.exolingo.com/edit_deck.php?deck_id='.$deck_id.'">'.$deck_name.'</a>';

        $frontiere = '-----=' . md5(uniqid(mt_rand()));
        $headers = 'From: "ExoLingo" <'.$email_expediteur.'>'."\n";
        $headers .= 'Return-Path: <'.$email_reply.'>'."\n";
        $headers .= 'MIME-Version: 1.0'."\n";
        $headers .= 'Content-Type: multipart/alternative; boundary="'.$frontiere.'"';

        $message = 'This is a multi-part message in MIME format.'."\n\n";

        $message .= '--'.$frontiere."\n";
        $message .= 'Content-Type: text/plain; charset="iso-8859-1"'."\n";
        $message .= 'Content-Transfer-Encoding: 8bit'."\n\n";
        $message .= $message_texte."\n\n";

        $message .= '--'.$frontiere."\n";
        $message .= 'Content-Type: text/html; charset="iso-8859-1"'."\n";
        $message .= 'Content-Transfer-Encoding: 8bit'."\n\n";
        $message .= $message_html."\n\n";

        $result=mail($destinataire,$sujet,$message,$headers,"-f ".$email_expediteur) ;
         if (!$result)
         {echo json_encode("ok");}
         else{ echo json_encode("ok");}


  }
  else{echo json_encode(array("status"=>"Vous n'êtes pas administrateur de cette liste"));}

break;
case 'addQuizTrophy':
  $user_id=(int)$_GET['user_id'];
  $sql = "INSERT INTO trophy (user_id,type) "
          . "VALUES (".$user_id.",'quiz')";
  $mysqli->query($sql);
break;
case 'getQuizTrophyNumber':
  $user_id=(int)$_GET['user_id'];
  $sql="SELECT COUNT(*) AS nbre FROM `trophy` WHERE type='quiz' AND user_id=".$user_id;
  $result = $mysqli->query($sql);
  $row = $result->fetch_assoc();
  echo $row["nbre"];
break;
//=========MOBILE===============
case 'mobile_getDecksList':
  $tag=htmlspecialchars($_GET['tag']);
  $tag=$mysqli->real_escape_string($tag);
  $niveau=htmlspecialchars($_GET['niveau']);
  $niveau=$mysqli->real_escape_string($niveau);
  $condition_tag="";
  $join_tag="";
  if($tag!="all"){
      $condition_tag=" AND tag_name='".$tag."' ";
      $join_tag=" LEFT JOIN tags ON decks.deck_id=tags.deck_id ";
  }
  $decks=array();
  $current_tps=time();
  if($niveau=="myDecks"){
  	$sql="SELECT deck_name,decks.deck_id,decks.visible,decks.hasImage,decks.classe,decks.nbreMots,decks.position, SUM(CASE WHEN OptimalRD > ".$current_tps." THEN 1 ELSE 0 END) AS NbreKnown FROM `decks`
    LEFT JOIN cards ON cards.deck_id=decks.deck_id LEFT JOIN (SELECT * FROM activite WHERE activite.user_id=".$user_id.") AS activite ON activite.card_id=cards.card_id
    ".$join_tag." WHERE visible=1 ".$condition_tag." AND decks.classe = '" . $niveau . "' AND decks.user_id=".$user_id." GROUP BY decks.deck_id";
  }
  else if($niveau=="StudentDecks"){
  	$sql="SELECT users.first_name, users.last_name, users.classe AS user_classe, deck_name,decks.deck_id,decks.visible,decks.hasImage,decks.classe,decks.nbreMots,decks.position, SUM(CASE WHEN OptimalRD > ".$current_tps." THEN 1 ELSE 0 END) AS NbreKnown FROM `decks`
    LEFT JOIN cards ON cards.deck_id=decks.deck_id LEFT JOIN (SELECT * FROM activite WHERE activite.user_id=".$user_id.") AS activite ON activite.card_id=cards.card_id LEFT JOIN users ON users.user_id=decks.user_id
    ".$join_tag." WHERE visible=1 ".$condition_tag." AND decks.classe = 'myDecks' AND decks.status!='prof' GROUP BY decks.deck_id";
  }
  else {
  	$sql="SELECT deck_name,decks.deck_id,decks.visible,decks.hasImage,decks.classe,decks.nbreMots,decks.position FROM `decks`
    LEFT JOIN cards ON cards.deck_id=decks.deck_id
    ".$join_tag." WHERE visible=1 ".$condition_tag." AND decks.classe = '" . $niveau . "' GROUP BY decks.deck_id";

  }

  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
  				array_push($decks,$row);
  		}
  $result->close();
  echo json_encode($decks);
break;

case 'getName':
  $user_id=(int)$_GET['user_id'];
  $sql="SELECT first_name,last_name,user_id FROM users WHERE user_id=".$user_id;
  $result = $mysqli->query($sql);
  $row = $result->fetch_assoc();
  $result->close();
  echo json_encode($row);
break;
case 'addAlert':
  $card_id=(int)$_GET['card_id'];
  $user_id=(int)$_SESSION['user_id'];
  $alert_comment=htmlspecialchars($_GET['alert_comment']);
  $alert_comment=$mysqli->real_escape_string($alert_comment);
  $sql="SELECT first_name, last_name FROM users WHERE user_id=".$user_id;
  $result = $mysqli->query($sql);
  $row = $result->fetch_assoc();
  $result->close();
  $name_lanceur=$row["first_name"]." ".$row["last_name"];


  $sql="UPDATE cards SET alert='1' WHERE card_id=".$card_id;
  $mysqli->query($sql);
  $sql = "INSERT INTO alerte (user_id,card_id,comment) VALUES (".$user_id.",".$card_id.",'".$alert_comment."')";

  $mysqli->query($sql);

  //get name and mail form deck_creator

  $sql="SELECT first_name,last_name,email, decks.deck_id FROM users LEFT JOIN decks ON decks.user_id=users.user_id LEFT JOIN cards ON cards.deck_id=decks.deck_id WHERE card_id=".$card_id;
  $result = $mysqli->query($sql);
  $row = $result->fetch_assoc();
  $result->close();

  $name=$row["first_name"]." ".$row["last_name"];
  $destinataire=$row["email"];
  $email_expediteur='info@exolingo.com';
  $email_reply='no-reply@exolingo.com';
  $sujet='Alerte sur une de vos cartes ( ExoLingo )';
  $message_texte='Bonjour '.$first_name.','.
       $name_lanceur.' a signalé le problème suivant sur votre liste:'.$alert_comment.' Clique sur le lien pour corriger l\'alerte : '.
       'http://www.exolingo.com/edit_deck.php?deck_id='.$row["deck_id"];

  $message_html='Bonjour '.$first_name.','."<br>".
       $name_lanceur.' a signalé le problème suivant sur votre liste:<p>'.$alert_comment.'</p><br>Clique sur le lien suivant pour voir les alertes et corriger ta liste :<br>'.
       '<a href="http://www.ExoLingo.com/edit_deck.php?deck_id='.$row["deck_id"].'">Voir ma liste</a>';

  $frontiere = '-----=' . md5(uniqid(mt_rand()));
  $headers = 'From: "ExoLingo" <'.$email_expediteur.'>'."\n";
  $headers .= 'Return-Path: <'.$email_reply.'>'."\n";
  $headers .= 'MIME-Version: 1.0'."\n";
  $headers .= 'Content-Type: multipart/alternative; boundary="'.$frontiere.'"';

  $message = 'This is a multi-part message in MIME format.'."\n\n";

  $message .= '--'.$frontiere."\n";
  $message .= 'Content-Type: text/plain; charset="iso-8859-1"'."\n";
  $message .= 'Content-Transfer-Encoding: 8bit'."\n\n";
  $message .= $message_texte."\n\n";

  $message .= '--'.$frontiere."\n";
  $message .= 'Content-Type: text/html; charset="iso-8859-1"'."\n";
  $message .= 'Content-Transfer-Encoding: 8bit'."\n\n";
  $message .= $message_html."\n\n";

  $result=mail($destinataire,$sujet,$message,$headers,"-f ".$email_expediteur) ;
   if (!$result)
   {echo json_encode("done");}
   else{ echo json_encode("done");}
break;
case 'deleteAlert':
  $card_id=(int)$_GET['card_id'];
  $sql="UPDATE cards SET alert='0' WHERE card_id=".$card_id;
  $mysqli->query($sql);
  $sql = "DELETE FROM alerte WHERE card_id=".$card_id;
  $mysqli->query($sql);
  echo json_encode("done");
break;


case 'addAlertDeck':
  $deck_id=(int)$_GET['deck_id'];
  $user_id=(int)$_SESSION['user_id'];
  $alert_comment=htmlspecialchars($_GET['alert_comment']);
  $alert_comment=$mysqli->real_escape_string($alert_comment);
  $sql="SELECT first_name, last_name FROM users WHERE user_id=".$user_id;
  $result = $mysqli->query($sql);
  $row = $result->fetch_assoc();
  $result->close();
  $name_lanceur=$row["first_name"]." ".$row["last_name"];


  $sql="UPDATE decks SET alertDeck='1' WHERE deck_id=".$deck_id;
  $mysqli->query($sql);echo 'done in cards';

  //get name and mail form deck_creator

  $sql="SELECT first_name,last_name,email FROM users LEFT JOIN decks ON decks.user_id=users.user_id WHERE deck_id=".$deck_id;
  $result = $mysqli->query($sql);
  $row = $result->fetch_assoc();
  $result->close();

  $name=$row["first_name"]." ".$row["last_name"];
  $destinataire=$row["email"];
  $email_expediteur='info@exolingo.com';
  $email_reply='no-reply@exolingo.com';
  $sujet='Alerte sur une de vos listes ( ExoLingo )';
  $message_texte='Bonjour '.$first_name.','.
       $name_lanceur.' a signalé le problème suivant sur votre liste:'.$alert_comment.' Clique sur le lien pour corriger l\'alerte : '.
       'http://www.exolingo.com/edit_deck.php?deck_id='.$deck_id;

  $message_html='Bonjour '.$first_name.','."<br>".
       $name_lanceur.' a signalé le problème suivant sur votre liste:<p>'.$alert_comment.'</p><br>Clique sur le lien suivant pour voir les alertes et corriger ta liste :<br>'.
       '<a href="http://www.exolingo.com/edit_deck.php?deck_id='.$deck_id.'">Voir ma liste</a>';

  $frontiere = '-----=' . md5(uniqid(mt_rand()));
  $headers = 'From: "ExoLingo" <'.$email_expediteur.'>'."\n";
  $headers .= 'Return-Path: <'.$email_reply.'>'."\n";
  $headers .= 'MIME-Version: 1.0'."\n";
  $headers .= 'Content-Type: multipart/alternative; boundary="'.$frontiere.'"';

  $message = 'This is a multi-part message in MIME format.'."\n\n";

  $message .= '--'.$frontiere."\n";
  $message .= 'Content-Type: text/plain; charset="iso-8859-1"'."\n";
  $message .= 'Content-Transfer-Encoding: 8bit'."\n\n";
  $message .= $message_texte."\n\n";

  $message .= '--'.$frontiere."\n";
  $message .= 'Content-Type: text/html; charset="iso-8859-1"'."\n";
  $message .= 'Content-Transfer-Encoding: 8bit'."\n\n";
  $message .= $message_html."\n\n";

  $result=mail($destinataire,$sujet,$message,$headers,"-f ".$email_expediteur) ;
   if (!$result)
   {echo "Le mail de conf n a pas plus envoyé";}
   else{ echo "Le mail de conf a été envoyé";}
break;
case 'deleteAlertDeck':
  $deck_id=(int)$_GET['deck_id'];
  $sql="UPDATE decks SET alertDeck='0' WHERE deck_id=".$deck_id;
  $mysqli->query($sql);
break;
case 'delClass':
  $user_id=(int)$_SESSION["user_id"];
  $class_id=(int)$_GET['class_id'];
  $mysqli->query("UPDATE classes
    LEFT JOIN user_class ON user_class.class_id=classes.class_id SET classes.active=0,classes.delete_time=NOW()
  WHERE classes.class_id=".$class_id." AND user_class.role='prof' AND user_class.user_id=".$user_id);
  
  echo json_encode("deleted");
break;

case 'addClass':
  $user_id=(int)$_SESSION["user_id"];
  $class_name=htmlspecialchars($_GET['class_name']);
  $class_name=$mysqli->real_escape_string($class_name);
  $lang_id=(int)$_GET['lang_id'];
  $promo=htmlspecialchars($_GET['promo']);
  $promo=$mysqli->real_escape_string($promo);
  //limitation à 100 class
  if($_SESSION["classNoLimit"]!=true)
  {
    $sql="SELECT COUNT(*) as nbreClass FROM user_class WHERE user_class.user_id=".$user_id." AND user_class.role='prof'";
    $result = $mysqli->query($sql);
    $row = $result->fetch_assoc();
    $nbreClass=$row['nbreClass'];
    $result->close();
    if($nbreClass>100)
      {echo json_encode(array("status"=>"limit"));
      exit();}
  }
  $sql = "INSERT INTO classes (class_name,promo,status,school_id,code,lang_id,creator_id) "
          . "VALUES ('".$class_name."','".$promo."','ok','0','',".$lang_id.",".$user_id.")";
  $mysqli->query($sql);
  $class_id=$mysqli->insert_id;
  $code=($class_id*9009+1000)%152000;
  $code+=8000;
  $code=base_convert($code,10,20);
  $base20 = array("A","B","C","D","E","F","G","H","U","J","K","L","M","N","O","P","Q","R","S","T");
  $origin   = array("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f","g","h","i","j");
  $code = str_replace($origin, $base20, $code);
  $mysqli->query("UPDATE classes SET code='".$code."' WHERE class_id=".$class_id);
  $sql = "INSERT INTO user_class (user_id,class_id,role) VALUES (".$user_id.",".$class_id.",'prof')";
  $mysqli->query($sql);

  $sql="DELETE FROM user_target_lang WHERE user_id=".$user_id." AND lang_id=".$lang_id;
  $mysqli->query($sql);
  $sql="INSERT INTO user_target_lang (user_id, lang_id) VALUE (".$user_id.",".$lang_id.")";
  $mysqli->query($sql);
  //echo json_encode($class_id);
  echo json_encode(array("status"=>"ok","class_id"=>$class_id));
break;
case 'getClassProf':
  $user_id=(int)$_SESSION['user_id'];
  $classes=array();
  $sql="SELECT classes.class_id,classes.class_name,classes.promo FROM classes LEFT JOIN user_class ON user_class.class_id=classes.class_id WHERE user_class.role='prof' AND classes.status='ok' AND user_class.user_id=".$user_id;
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
  				array_push($classes,$row);
  		}
  $result->close();
  echo json_encode($classes);
break;
case 'getExo':
  $exos=array();
  $sql="SELECT exo_id,name FROM exos WHERE 1";
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
  				array_push($exos,$row);
  		}
  $result->close();
  echo json_encode($exos);
break;
case 'getExoMissionable':
  $exos=array();
  $sql="SELECT exo_id,name FROM exos WHERE missionable=1";
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
  				array_push($exos,$row);
  		}
  $result->close();
  echo json_encode($exos);
break;
case 'getDeckProf':
  $user_id=(int)$_SESSION['user_id'];
  $decks=array();
  $decksTmp=array();

  $sql="SELECT decks.deck_id, decks.deck_name, decks.hasImage, deck_class.class_id FROM decks
  LEFT JOIN deck_class ON decks.deck_id=deck_class.deck_id
  LEFT JOIN user_class ON user_class.class_id=deck_class.class_id
  LEFT JOIN classes ON user_class.class_id=classes.class_id
  WHERE classes.status='ok' AND user_class.user_id=".$user_id." AND user_class.role='prof' ORDER BY decks.deck_id ASC";
  $result = $mysqli->query($sql);
  $previous_deck_id=-1;
  while ($row = $result->fetch_assoc()) {
          if($row["deck_id"]!=$previous_deck_id)
          {
            $decksTmp[$row['deck_id']]=$row;
            $decksTmp[$row['deck_id']]["class_ids"]=array();
          }
          array_push($decksTmp[$row['deck_id']]["class_ids"],$row["class_id"]);
          $previous_deck_id=$row["deck_id"];
    }
  foreach($decksTmp as $deck)
  {array_push($decks,$deck);}
  $result->close();
  echo json_encode($decks);
break;
case 'getDeckClass':
  $user_id=(int)$_SESSION['user_id'];
  $class_id=(int)$_GET['class_id'];
  $decks=array();
  if(checkProfRight($class_id,$user_id,$mysqli)==0){exit();}
  $sql="SELECT decks.deck_id, decks.deck_name, decks.hasImage FROM decks
  LEFT JOIN deck_class ON decks.deck_id=deck_class.deck_id
  WHERE deck_class.class_id=".$class_id." ORDER BY decks.deck_id ASC";
  $result = $mysqli->query($sql);
  $previous_deck_id=-1;
  while ($row = $result->fetch_assoc()) {
    array_push($decks,$row);
  }
  $result->close();
  echo json_encode($decks);
break;
case 'getClassInfo':
  $class_id=(int)$_GET['class_id'];
  $sql="SELECT code,class_name,promo FROM classes WHERE class_id=".$class_id;
  $result = $mysqli->query($sql);
  $row = $result->fetch_assoc();
  $result->close();
  echo json_encode($row);
break;
case 'getProfFromClass':
  $class_id=(int)$_GET['class_id'];
  $profs=array();
  $sql="SELECT users.first_name,users.last_name FROM user_class
  LEFT join users on users.user_id=user_class.user_id
  WHERE users.active=1 AND user_class.class_id=".$class_id." AND user_class.role='prof'";
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
  				array_push($profs,$row);
  		}
  $result->close();
  echo json_encode($profs);
break;
case 'getParticipant':
  $class_id=(int)$_GET['class_id'];
  $users=array();
  $sql="SELECT * FROM users
  LEFT JOIN user_class ON users.user_id=user_class.user_id
  WHERE users.active=1 AND user_class.class_id=".$class_id." ORDER BY user_class.position ASC";
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
  				array_push($users,$row);
  		}
  $result->close();
  echo json_encode("getParticipant Done");
break;
case 'joinClass':
  $class_id=(int)$_GET['class_id'];
  $user_id=(int)$_SESSION['user_id'];
  $sql = "INSERT INTO user_class (user_id,class_id,role) VALUES (".$user_id.",".$class_id.",'eleve')";
  $mysqli->query($sql);
  $sql="SELECT lang_id FROM `classes` WHERE class_id=".$class_id;
  $result = $mysqli->query($sql);
  $row = $result->fetch_assoc();
  $lang_id=$row["lang_id"];
  $result->free();
  $sql="DELETE FROM user_target_lang WHERE user_id=".$user_id." AND lang_id=".$lang_id;
  $mysqli->query($sql);
  $sql="INSERT INTO user_target_lang (user_id, lang_id) VALUE (".$user_id.",".$lang_id.")";
  $mysqli->query($sql);
  echo json_encode("join Done");
break;
case 'joinPublicClass':
  $class_id=(int)$_GET['class_id'];
  $user_id=(int)$_SESSION['user_id'];
  $sql="SELECT status FROM classes WHERE class_id=".$class_id;
  $result = $mysqli->query($sql);
  $row = $result->fetch_assoc();
  $result->close();
  if($row["status"]!="public"){exit();}
  else{
  $sql = "INSERT INTO user_class (user_id,class_id,role) VALUES (".$user_id.",".$class_id.",'eleve')";
  $mysqli->query($sql);
  echo json_encode("join Done");
  }
break;
case 'kickOutUser':
  $class_id=(int)$_GET['class_id'];
  $user_id=(int)$_GET['user_id'];
  $myUser_id=(int)$_SESSION['user_id'];
  $sql="SELECT role FROM user_class WHERE user_id=".$myUser_id." AND class_id=".$class_id;
  $result = $mysqli->query($sql);
  $row = $result->fetch_assoc();
  $result->close();
  if($row["role"]!="prof"){exit();}
  else{
  $sql = "DELETE FROM user_class WHERE user_id=".$user_id." AND class_id=".$class_id;
  $mysqli->query($sql);
  }
  echo json_encode("goOut Done");
break;
case 'upgradeProf':
  $class_id=(int)$_GET['class_id'];
  $user_id=(int)$_GET['user_id'];
  $myUser_id=(int)$_SESSION['user_id'];
  //je verifie que celui qui upgrade est prof
  $sql="SELECT role FROM user_class WHERE user_id=".$myUser_id." AND class_id=".$class_id;
  $result = $mysqli->query($sql);
  $row = $result->fetch_assoc();
  $result->close();
  if($row["role"]!="prof"){echo json_encode("Not Allowed");exit();}
  else{
  $sql = "UPDATE user_class SET role='prof' WHERE user_id=".$user_id." AND class_id=".$class_id;
  $mysqli->query($sql);
  echo json_encode("goOut Done");
  }

break;
case 'goOutClass':
  $class_id=(int)$_GET['class_id'];
  $user_id=(int)$_SESSION['user_id'];
  $sql = "DELETE FROM user_class WHERE user_id=".$user_id." AND class_id=".$class_id;
  $mysqli->query($sql);
  echo json_encode("goOut Done");
break;
case 'addToArchive':
  $class_id=(int)$_GET['class_id'];
  $mysqli->query("UPDATE classes SET status='archive' WHERE class_id=".$class_id);
  echo json_encode("archive Done");
break;
case 'removeFromArchive':
  $class_id=(int)$_GET['class_id'];
  $mysqli->query("UPDATE classes SET status='ok' WHERE class_id=".$class_id);
  echo json_encode("Unarchive Done");
break;
case 'setOrderStat':
  $i = 0;
  foreach ($_POST['user'] as $value) {
      // Execute statement:
    $mysqli->query("UPDATE user_class SET position = ".$i." WHERE user_id = ".$value);
      $i++;
}
break;
case 'setOrderDeck':
  $i = 0;
  $class_id=(int)$_GET["class_id"];
  foreach ($_POST['deck'] as $value) {
      // Execute statement:
    $mysqli->query("UPDATE deck_class SET position = ".$i." WHERE deck_id = ".$value." AND class_id=".$class_id);
      $i++;
  }
  echo json_encode("test".$i." value:".$value." class:".$class_id);
break;
case 'setOrderLang':
  $i = 0;
  foreach ($_POST['lang_item'] as $value) {
      // Execute statement:
    $mysqli->query("UPDATE lang SET position = ".$i." WHERE lang_id = ".$value);
      $i++;
  }
  echo json_encode("test".$i." value:".$value);
break;

case 'setOrderClasses':
  $user_id = $_SESSION['user_id'];
  $i = 0;
  echo json_encode($_POST);
  foreach ($_POST['classItem'] as $value) {
      // Execute statement:
    $mysqli->query("UPDATE user_class SET position = ".$i." WHERE class_id = ".$value." AND user_id=".$user_id);
      $i++;
  }
break;
case 'setOrderCards':
  $type = $_SESSION['type'];
  if($type=="prof"){
  $i = 0;

  foreach ($_POST['card'] as $value) {
      // Execute statement:
    $mysqli->query("UPDATE cards SET position = ".$i." WHERE card_id = ".$value);
      $i++;
  }
  }
break;
case 'accepterDeck':
  $deck_id=(int)$_GET["deck_id"];
  $class_id=(int)$_GET["class_id"];
  $mysqli->query("UPDATE deck_class SET status = 'ok' WHERE deck_id = ".$deck_id." AND class_id=".$class_id);
  echo json_encode("done");
break;
case 'refuserDeck':
  $deck_id=(int)$_GET["deck_id"];
  $class_id=(int)$_GET["class_id"];
  $mysqli->query("DELETE FROM `deck_class` WHERE deck_id = ".$deck_id." AND class_id=".$class_id);
  echo json_encode("done");
break;
case 'proposeDeck':
  $sql = "DELETE FROM deck_class WHERE  deck_id = ".$deck_id." AND class_id=".$class_id;
  $mysqli->query($sql);
  $sql = "INSERT INTO deck_class (class_id,deck_id,status) VALUES (".$class_id.", ".$deck_id.",'waiting')";
  $mysqli->query($sql);
break;
case 'duplicDeck':
  $user_id=(int)$_SESSION["user_id"];
  $deck_id=(int)$_GET["deck_id"];
  $today=date("Y-m-d");
  //vérifier que l'utilisateur ne fait pas plus de 10 duplication par jours
  $result = $mysqli->query('SELECT count(*) as nbreDuplic FROM duplic WHERE user_id = ' . $user_id.' AND jour='.$today);
  $myResult = $result->fetch_assoc();
  $nbreDuplic=$myResult["nbreDuplic"];
  $result->free();
  if($nbreDuplic>30){echo json_encode($nbreDuplic);exit();}
  $sql = "INSERT INTO `duplic`(`user_id`, `jour`,type)
  VALUES (".$user_id.",'".$today."','deck')";
  $mysqli->query($sql);

  //Récupérer les info sur le decks
  $sql="SELECT deck_name, visible, classe, hasImage, nbreMots, user_id,lang_id,status FROM `decks` WHERE deck_id=".$deck_id;
  $result = $mysqli->query($sql);
  $deck_data=array();
  $row = $result->fetch_assoc();
  $deck_data=$row;
  $result->free();
  //Récupérer la liste des card_id
  $sql="SELECT card_id, hasImage, hasAudio, mot, mot_trad, position,lang_id FROM `cards` WHERE deck_id=".$deck_id;
  $result = $mysqli->query($sql);
  $cards_data=array();
  while ($row = $result->fetch_assoc()) {
    array_push($cards_data,$row);
  };
  $result->free();
  //Récupérer la liste des sentences
  $sql="SELECT card_sentence.card_id, card_sentence.sentence_id,card_sentence.sentence FROM card_sentence LEFT JOIN `cards` ON cards.card_id=card_sentence.card_id WHERE cards.deck_id=".$deck_id;
  $result = $mysqli->query($sql);
  $sentences_data=array();
  while ($row = $result->fetch_assoc()) {
    array_push($sentences_data,$row);
  };
  $result->free();
  //Récupérer la liste des tags
  $sql="SELECT tag_name FROM `tags` WHERE deck_id=".$deck_id;
  $result = $mysqli->query($sql);
  $tags_data=array();
  while ($row = $result->fetch_assoc()) {
    array_push($tags_data,$row);
  };
  $result->free();

  //créer un nouveau deck (deck_name,user,visible,statut)
  //if(isset($_GET["deck_name"]))
  //{$newDeck_name=$_GET["deck_name"];}else{
    $newDeck_name=$deck_data['deck_name']."-".__("copie");
  //}
  //{$newDeck_name=$_GET["deck_name"];}else{$newDeck_name=$deck_data['deck_name'];}
  $sql = "INSERT INTO `decks`(`deck_name`, `visible`, `hasImage`, `nbreMots`, `user_id`, `lastChange`,lang_id,status)
  VALUES ('".$newDeck_name."',1,".$deck_data['hasImage'].",".$deck_data['nbreMots'].",".$user_id.",".$current_tps.",".$deck_data['lang_id'].",'ok')";
  $mysqli->query($sql);
  //echo $sql;
  //récupérer le nouveau deck_id
  $newDeck_id=$mysqli->insert_id;
  //save deck relations
  $sql = "INSERT INTO `duplicDeck`(`deck_id`,`new_deck_id`)
  VALUES (".$deck_id.",".$newDeck_id.")";
  $mysqli->query($sql);
  //inserer les droits
  $sql = "INSERT INTO `user_deck_droit`(`user_id`, `deck_id`, `jour`, `droit`)
  VALUES (".$user_id.",".$newDeck_id.",'".$today."','admin')";
  $mysqli->query($sql);
  //echo $newDeck_id;

  //Copier l'image si hasImage dans decks
  // if($deck_data['hasImage']>0){
  //   if (copy("deck_img/deck_".$deck_data['hasImage'].".png","deck_img/deck_".$newDeck_id.".png")) {
  //       //echo "deck Image has been copied.<br>";
  //   } else {
  //       //echo "deck Image has NOT been copied.<br>";
  //   }
  // }
  //pour chaque cartes

  foreach ($cards_data as $card_data) {
    $card_id=$card_data["card_id"];
    $card_hasImage=$card_data["hasImage"];
    $card_hasAudio=$card_data["hasAudio"];
    $mot=$card_data["mot"];
    $mot_trad=$card_data["mot_trad"];
    $position=$card_data["position"];
    $lang_id=$card_data["lang_id"];

   $sql = "INSERT INTO `cards`(`deck_id`, `mot`, `mot_trad`, `hasImage`, `hasAudio`, `position`,lang_id)
   VALUES (".$newDeck_id.",'".$mot."','".$mot_trad."',".$card_hasImage.",".$card_hasAudio.",".$position.",".$lang_id.")";
   $mysqli->query($sql);

  //Récupérer le card_id
  $newCard_id=$mysqli->insert_id;
  $sql = "INSERT INTO `user_card`(user_id,card_id,droit)
  VALUES (".$user_id.",".$newCard_id.",'creator')";
  $mysqli->query($sql);
  $sql = "INSERT INTO `card_deck`(card_id,deck_id) VALUES (".$newCard_id.",".$deck_id.")";
  $mysqli->query($sql);
  //copier les sentence
  $insertion="";
  $virgule="";
  foreach($sentences_data as $sentence_data)
  {
    if($sentence_data["card_id"]==$card_id)
    {$insertion.=$virgule."(".$newCard_id.",'".$sentence_data["sentence"]."')";
    $virgule=",";}
  }
  $sql = "INSERT INTO `card_sentence`(`card_id`, `sentence`)
  VALUES ".$insertion;
  $mysqli->query($sql);

   //si audio copier l'audio
   if($card_hasAudio){
     if (copy("card_audio/card_".$card_id.".wav","card_audio/card_".$newCard_id.".wav")) {
         //echo "card audio has been copied.<br>";
     } else {
         //echo "card audio has NOT been copied.<br>";
     }
   }
   //si image copier l'image
   // if($card_hasImage>0){
   //   if (copy("card_img/card_".$card_hasImage.".png","card_img/card_".$newCard_id.".png")) {
   //       //echo "card Image has been copied.<br>";
   //   } else {
   //       //echo "card Image has NOT been copied.<br>";
   //   }
   // }
  }
  //pour chaque tags, les dupliquer avec le nouveau deck.
  $insertion="";
  $virgule="";
  foreach($tags_data as $tag_data)
  {
    $insertion.=$virgule."(".$newDeck_id.",'".$tag_data["tag_name"]."')";
    $virgule=",";
  }
  $sql = "INSERT INTO `tags`(`deck_id`, `tag_name`)
  VALUES ".$insertion;
  $mysqli->query($sql);
  echo json_encode(array("newDeck_id"=>$newDeck_id,"oldDeck_id"=>$deck_id,"deck_data"=>$deck_data,"cards_data"=>$cards_data));
break;
case 'changeCategorie':
  $_SESSION["categorie"]=htmlspecialchars($_GET['categorie']);
  echo json_encode("ok Ajax à compléter");
break;
case 'setCodeQuiz':
    $user_id=(int)$_SESSION["user_id"];
    if(isset($_SESSION["quiz_id"])){
        $quiz_id=(int)$_SESSION["quiz_id"];
        $mysqli->query("UPDATE quiz SET status='over' WHERE prof_id=".$user_id);
        $mysqli->query("UPDATE quiz SET status='open' WHERE quiz_id=".$quiz_id);
    }
  echo json_encode("done");
break;
case 'setClassQuiz':
    $user_id=(int)$_SESSION["user_id"];
    $quiz_id=(int)$_SESSION["quiz_id"];
    $class_id=$_GET["class_id"];
    $mysqli->query("UPDATE quiz SET status='over' WHERE prof_id=".$user_id);
    $mysqli->query("UPDATE quiz SET status='open',class_id=".$class_id." WHERE quiz_id=".$quiz_id);

  echo json_encode("done");
break;
case 'overClassQuiz':
    $user_id=(int)$_SESSION["user_id"];
    $quiz_id=(int)$_SESSION["quiz_id"];
    $mysqli->query("UPDATE quiz SET status='over' WHERE quiz_id=".$quiz_id." AND prof_id=".$user_id);
    echo json_encode("done");
break;
case 'openClassQuiz':
    $user_id=(int)$_SESSION["user_id"];
    $quiz_id=(int)$_SESSION["quiz_id"];
    $mysqli->query("UPDATE quiz SET status='open' WHERE quiz_id=".$quiz_id." AND prof_id=".$user_id);
    echo json_encode("done");
break;
case 'setMarksQuiz':
  $prof_id=(int)$_SESSION["user_id"];
  $quiz_id=(int)$_SESSION["quiz_id"];
  //on verifie que la personne qui fait la demande possede bien le quiz
  $result = $mysqli->query('SELECT * FROM quiz WHERE prof_id='.$prof_id.' AND quiz_id='.$quiz_id);
  $flag =$result->num_rows;
  if($flag==1){

    $noteMax=(int)$_GET["noteMax"];
    $marks=$_GET["marks"];
    $mysqli->query("UPDATE quiz SET status='over' WHERE quiz_id=".$quiz_id);
    $multipleValueInsert="";
    $virgule="";
    foreach ($marks as $dataMark) {
      $score=(int)$dataMark['score'];
      $player_id=(int)$dataMark['player_id'];
      $multipleValueInsert.=$virgule."(".$quiz_id.",".$score.",".$player_id.",".$noteMax.")";
      $virgule=",";
    }
    $sql = "INSERT INTO note_user_quiz (quiz_id,note,user_id,noteMax)"
            . "VALUES ".$multipleValueInsert;
    $mysqli->query($sql);
  }
  echo json_encode("done");
break;
case 'changeMark':
  $quiz_id=(int)$_GET["quiz_id"];
  $note=(int)$_GET["note"];
  $user_id=(int)$_GET["user_id"];
  $prof_id=(int)$_SESSION["user_id"];
  //on verifie que la personne qui fait la demande possede bien le quiz
  $result = $mysqli->query('SELECT * FROM quiz WHERE prof_id='.$prof_id.' AND quiz_id='.$quiz_id);
  $flag =$result->num_rows;
  if($flag==1){
    $row = $result->fetch_assoc();
    $noteMax=$row["note_max"];
    $result->free();
    $sql1="DELETE FROM note_user_quiz WHERE quiz_id=".$quiz_id." AND user_id=".$user_id;
    $mysqli->query($sql1);
    $sql2="INSERT INTO note_user_quiz (quiz_id,user_id,note,noteMax)  VALUES (".$quiz_id.", ".$user_id.",".$note.",".$noteMax.")";
    $mysqli->query($sql2);
  }

  echo json_encode("-".$flag."-".$sql1."-".$sql2);
break;
case 'getAllGames':
  $user_id=(int)$_SESSION["user_id"];
  $now=time();
  $games=array();
  $result = $mysqli->query('SELECT decks.deck_name,decks.hasImage,quiz.quiz_id,classes.class_name,classes.promo,quiz.prof_id,users.first_name as prof_name FROM quiz
    LEFT JOIN user_class ON quiz.class_id=user_class.class_id
    LEFT JOIN classes ON classes.class_id=user_class.class_id
    LEFT JOIN decks ON decks.deck_id=quiz.deck_id
    LEFT JOIN users ON quiz.prof_id=users.user_id
    WHERE quiz.active=1 AND user_class.user_id='.$user_id.' AND quiz.status="open" AND expire> '.$now);
  while ($game = $result->fetch_assoc()) {
  array_push($games,$game);
  }
  echo json_encode($games);
break;
case 'toggleDeckStatus':
  $user_id=(int)$_SESSION["user_id"];
  if($user_id!=7){exit();}
  $deck_id=(int)$_GET["deck_id"];
  $deck_status_name=$mysqli->real_escape_string(htmlspecialchars($_GET['deck_status_name']));

  $result = $mysqli->query('SELECT status FROM decks WHERE deck_id='.$deck_id);
  $row = $result->fetch_assoc();
  $currentStatus=$row['status'];
  if($currentStatus==$deck_status_name){$newStatus="ok";}
  else{$newStatus=$deck_status_name;}
  $sql='UPDATE decks SET status="'.$newStatus.'" WHERE deck_id='.$deck_id;
  $mysqli->query($sql);
  echo json_encode(array("sql"=>$sql,"newStatus"=>$newStatus));
break;
case 'togglelikeDeck':
  $user_id=(int)$_SESSION["user_id"];
  $deck_id=(int)$_GET["deck_id"];
  $result = $mysqli->query('SELECT * FROM user_deck_like WHERE user_id='.$user_id.' AND deck_id='.$deck_id);
  $flag =$result->num_rows;
  if($flag==1){
    $result = $mysqli->query('DELETE FROM user_deck_like WHERE user_id='.$user_id.' AND deck_id='.$deck_id);
    $result = $mysqli->query('UPDATE decks SET decks.likes=decks.likes-1 WHERE deck_id='.$deck_id);
    echo json_encode("delete");
  }elseif($flag==0){
    $now=time();
    $result = $mysqli->query('INSERT INTO user_deck_like (user_id,deck_id,likeDate) VALUES ('.$user_id.','.$deck_id.','.$now.')');
    $result = $mysqli->query('UPDATE decks SET decks.likes=decks.likes+1 WHERE deck_id='.$deck_id);
    echo json_encode("added");
  }
break;
case 'getCoinsUser':
  $user_id=(int)$_SESSION["user_id"];
  //CoinToday
  $today=date("Y-m-d");
  $sql="SELECT nbreCoins FROM bank WHERE user_id=".$user_id." AND jour=".$today;
  $result = $mysqli->query($sql);
  if($row =$result->fetch_assoc()){
  $nbreCoinsToday=$row["nbreCoins"];
  }
  else
  {$nbreCoinsToday=0;}
  $result->free();
  //CoinTotal
  $sql="SELECT nbreCoins FROM users WHERE user_id=".$user_id;
  $result = $mysqli->query($sql);
  $row =$result->fetch_assoc();
  $nbreCoins=$row["nbreCoins"];
  $result->free();
  echo json_encode(array("nbreCoins"=>$nbreCoins,"nbreCoinsToday"=>$nbreCoinsToday));
break;
case 'addCoins':
  $user_id=(int)$_SESSION["user_id"];
  $coins2add=(int)$_GET["nbre"];
  $today=date("Y-m-d");
    $sql="SELECT nbreCoins FROM bank WHERE user_id=".$user_id." AND jour='".$today."' LIMIT 1";
    $result = $mysqli->query($sql);
    $line_exist=$result->num_rows;
    $row =$result->fetch_assoc();
    $nbreCoinsToday=(int)$row["nbreCoins"];
    $_SESSION["nbreCoinsToday"]=(int)$row["nbreCoins"];
    $result->free();
  if($line_exist==0){
    $coins2add+=20;
  }
  if($nbreCoinsToday+$coins2add>=$dailyCoinsMax)
  { $coins2add=max($dailyCoinsMax-$nbreCoinsToday,0);
    //echo json_encode(array("status"=>"limite atteinte","nbreCoins"=>$nbreCoins,"nbreCoinsToday"=>$_SESSION["nbreCoinsToday"],"coins2add"=>$coins2add));}
  }

    if($line_exist==0){
      $mysqli->query('INSERT INTO bank (user_id,nbreCoins,jour) VALUES ('.$user_id.','.$coins2add.',"'.$today.'")');
    }
    else{
      $mysqli->query("UPDATE bank SET nbreCoins=nbreCoins+".$coins2add." WHERE user_id=".$user_id." AND jour='".$today."'");
    }
    $mysqli->query("UPDATE users SET nbreCoins=nbreCoins+".$coins2add." WHERE user_id=".$user_id);
    $result = $mysqli->query("SELECT nbreCoins FROM users WHERE user_id=".$user_id);
    $row =$result->fetch_assoc();
    $nbreCoins=(int)$row["nbreCoins"];
    $result->free();

    $newNbreCoinsToday=$nbreCoinsToday+$coins2add;
    echo json_encode(array("status"=>"ok","nbreCoins"=>$nbreCoins,"coins2add"=>$coins2add));

break;
case 'checkExoTresor':
  $user_id=(int)$_SESSION["user_id"];
  $latestExoTresorCreated='';

  $sql1="SELECT MAX(user_tresor.creation_date) as latestExoTresorCreated FROM user_tresor WHERE user_tresor.user_id=".$user_id." AND user_tresor.type='exo' GROUP BY user_tresor.type LIMIT 1";
  $result = $mysqli->query($sql1);
  $row =$result->fetch_assoc();
  $latestExoTresorCreated=$row["latestExoTresorCreated"];
  if($row["latestExoTresorCreated"]==NULL){$latestExoTresorCreated='0000-00-00 00:00:00';}

  $result->free();

  $sql2="SELECT COUNT(*) as ExoSinceTresor FROM activite LEFT JOIN user_tresor ON activite.user_id=user_tresor.user_id WHERE activite.user_id=".$user_id." AND activite.LastRD>'".$latestExoTresorCreated."'";
  $result = $mysqli->query($sql2);
  $row =$result->fetch_assoc();
  $ExoSinceTresor=$row["ExoSinceTresor"];
  $result->free();
  //recupération de la valeur du trésor:
  $tresor=0;
  $tresorValue=0;
  if($ExoSinceTresor>=10)
  {
    //$tresorValue=floor(($ExoSinceTresor/10)*(rand(0,1)));
    $tresorValue=floor(rand(0,2));
    $tresor=1;
  }

  $dateTime=date("Y-m-d H:i:s");
  if($tresor==1){
     $mysqli->query('INSERT INTO user_tresor (user_id,value,type,creation_date) VALUES ('.$user_id.','.$tresorValue.',"exo","'.$dateTime.'")');
   }
   $tresorData=array();
   $sql3="SELECT user_tresor.tresor_id,user_tresor.type,user_tresor.value FROM user_tresor WHERE user_tresor.user_id=".$user_id." AND user_tresor.opening_date IS NULL";
   $result = $mysqli->query($sql3);
   while($row =$result->fetch_assoc()){
     array_push($tresorData,$row);
   }
   $result->free();
  echo json_encode(array("tresorData"=>$tresorData));
break;

case 'openTresor':
  $tresor_id=(int)$_GET["tresor_id"];
  $user_id=(int)$_SESSION["user_id"];

  $mysqli->query("UPDATE users,user_tresor SET users.ruby=users.ruby+user_tresor.value, user_tresor.opening_date=NOW() WHERE user_tresor.user_id=users.user_id AND user_tresor.opening_date IS NULL AND user_tresor.tresor_id=".$tresor_id." AND user_tresor.user_id=".$user_id);
  echo json_encode("ok");
break;
case 'checkEditionRight':
  $deck_id=(int)$_GET["deck_id"];
  $user_id=(int)$_SESSION["user_id"];
  $result = $mysqli->query('SELECT user_deck_droit.droit FROM user_deck_droit Where deck_id = ' . $deck_id.' AND user_id='.$user_id);
  $row = $result->fetch_assoc();
    $droit=$row["droit"];
  $result->free();
  $isInCoopMod=0;
  $classes=array();
  $result = $mysqli->query('SELECT IF(deck_class.deck_id='.$deck_id.',deck_class.status,"build") as status, classes.class_id, class_name,promo,deck_class.deck_id, classes.status as classStatus,user_class.role FROM classes LEFT JOIN user_class ON user_class.class_id=classes.class_id LEFT JOIN deck_class ON deck_class.class_id=classes.class_id WHERE classes.status!="archive" AND user_class.user_id = ' . $user_id .'  ORDER BY classes.class_id ASC');
  $class_id_old=-1;
  while ($row = $result->fetch_assoc()) {
          if($class_id_old!=$row["class_id"])
          {
          $status="build";
          if($row["deck_id"]==$deck_id){$status=$row["status"];$class_id_old=$row["class_id"];}
          if($status=="coop"){$isInCoopMod=1;}
          }
      }
  $result->free();
  if($droit!="admin" && $droit!="modif" && $isInCoopMod==0){
  echo json_encode("out");}
  else{echo json_encode("ok");}
break;
case 'getExoUser':
  $user_id=(int)$_GET["user_id"];
  $exoData=[];
  $result = $mysqli->query('SELECT exos.name as game,count(*) as num FROM `activiteGlobal`
  LEFT JOIN exos ON exos.exo_id=activiteGlobal.exo_id
  WHERE user_id='.$user_id.' GROUP BY exo_id ORDER BY num DESC');
  while ($row = $result->fetch_assoc()) {
  array_push($exoData,$row);
  }
  $result->free();
  echo json_encode($exoData);
break;
case "getMyClasses":
  $user_id=(int)$_SESSION["user_id"];
  //$lang_id=(int)$_SESSION["target_lang"];
  //$sql="SELECT classes.class_id,classes.class_name,classes.promo,classes.status,user_class.role FROM classes JOIN user_class ON user_class.class_id=classes.class_id WHERE (classes.status='perso' OR classes.lang_id=".$lang_id.") AND user_class.user_id=".$user_id." ORDER BY classes.status ASC";
  $sql="SELECT lang.lang_code2,user_class.role,classes.lang,classes.lang_id, classes.class_id,classes.class_name,classes.promo,classes.status,1 as enroll
  FROM classes
  LEFT JOIN user_class ON user_class.class_id=classes.class_id
  LEFT JOIN lang ON classes.lang_id=lang.lang_id
  WHERE classes.active=1 AND user_class.user_id=".$user_id;
  $myClasses=array();
  $listOfClassId=array();
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
  $myClasses[$row['class_id']]=$row;
  array_push($listOfClassId,$row['class_id']);
  }
  $result->free();
  echo json_encode($myClasses);
break;
case "getThisClass":
  $user_id=(int)$_SESSION["user_id"];
  $class_id=(int)$_GET["class_id"];
  $sql="SELECT classes.class_id,classes.class_name,classes.promo,classes.status,user_class.role FROM classes JOIN user_class ON user_class.class_id=classes.class_id WHERE user_class.user_id=".$user_id." AND user_class.class_id=".$class_id;
  $thisClasses=array();
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
  $thisClasses=$row;
  }
  $result->free();
  echo json_encode($thisClasses);
break;
case 'getHallOfFame':
  $user_id=(int)$_SESSION["user_id"];
  $class_id=(int)$_GET["class_id"];
  $HallOfFame=array();
  //classroom
  $sql="SELECT SUM(IF(bank.jour > DATE_SUB(NOW(), INTERVAL 1 WEEK),bank.nbreCoins,0)) as score,users.user_id,users.avatar_id,users.first_name,users.last_name,users.nbreCoins FROM bank
  LEFT JOIN users ON users.user_id=bank.user_id
  LEFT JOIN user_class ON users.user_id=user_class.user_id
  WHERE user_class.class_id=".$class_id." AND user_class.role!='prof' GROUP BY users.user_id ORDER BY score DESC";

  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
    array_push($HallOfFame,$row);
  }
  $result->free();
  //friends
  $sql="SELECT SUM(IF(bank.jour > DATE_SUB(NOW(), INTERVAL 1 WEEK),bank.nbreCoins,0)) as score,users.user_id,users.avatar_id,users.first_name,users.last_name,users.nbreCoins FROM bank
  LEFT JOIN users ON users.user_id=bank.user_id
  LEFT JOIN user_friends ON users.user_id=user_friends.friend_id
  WHERE user_friends.user_id=".$user_id." GROUP BY users.user_id ORDER BY score DESC";
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
    array_push($HallOfFame,$row);
  }
  $result->free();
  echo json_encode($HallOfFame);
break;
case 'nbreEleve':
  $class_id=(int)$_GET["class_id"];
  $sql = "SELECT COUNT(*) as nbreEleve from user_class
  LEFT JOIN users ON users.user_id=user_class.user_id
  where users.active=1 AND user_class.role='eleve' AND user_class.class_id=".$class_id;
  $result = $mysqli->query($sql);
  $myResult = $result->fetch_assoc();
  $nbreEleve=$myResult["nbreEleve"];
  $result->free();
  echo json_encode(array ('status'=>"ok","nbreEleve"=>$nbreEleve));
break;
case 'nbreMyClassProf':
  $user_id=(int)$_SESSION["user_id"];
  $sql = "SELECT COUNT(*) as nbreClass from user_class
  LEFT JOIN classes ON classes.class_id=user_class.class_id
  where classes.active=1 AND role='prof' AND user_id=".$user_id;
  $result = $mysqli->query($sql);
  $myResult = $result->fetch_assoc();
  $nbreClass=$myResult["nbreClass"];
  $result->free();
  echo json_encode(array ('status'=>"ok","nbreClass"=>$nbreClass));
break;
case 'nbreMyClassEleve':
  $user_id=(int)$_SESSION["user_id"];
  $sql = "SELECT COUNT(*) as nbreClass from user_class
  LEFT JOIN classes ON classes.class_id=user_class.class_id
  where classes.active=1 AND role='eleve' AND user_id=".$user_id;
  $result = $mysqli->query($sql);
  $myResult = $result->fetch_assoc();
  $nbreClass=$myResult["nbreClass"];
  $result->free();
  echo json_encode(array ('status'=>"ok","nbreClass"=>$nbreClass));
break;
case "checkifcode":
  $user_id=(int)$_SESSION["user_id"];
  $code=$mysqli->real_escape_string(htmlspecialchars($_GET['code']));
  $sql = "SELECT class_id from classes where classes.active AND code='".$code."'";
  $result = $mysqli->query($sql);
  $myResult = $result->fetch_assoc();
  $line_exist=$result->num_rows;
  $class_id=$myResult["class_id"];
  $result->free();

  if($line_exist==0){$status="bad code";$class_id="null";}
  else{
    $sql = "SELECT * from user_class LEFT JOIN classes ON classes.class_id=user_class.class_id
    where classes.active=1 AND user_id=".$user_id." AND class_id=".$class_id;
    $result = $mysqli->query($sql);
    $line2_exist=$result->num_rows;
    if($line2_exist==0){$status="ok";}
    else{$status="already in";}
  }
  echo json_encode(array ('status'=>$status,"class_id"=>$class_id));
break;
case "getlicencesUser":
  $user_id=(int)$_SESSION["user_id"];
  $sql="SELECT * FROM user_licence LEFT JOIN licences ON licences.licence_id=user_licence.licence_id WHERE user_licence.user_id=".$user_id." ORDER BY licences.active DESC";
  $licenceUser=array();
  $result = $mysqli->query($sql);
  while ($row = $result->fetch_assoc()) {
    array_push($licenceUser,$row);
  }
  $result->free();
  echo json_encode($licenceUser);
break;
case 'checkQuiz':
  $game_id=(int)($_GET['game_id']);
  $result = $mysqli->query('SELECT quiz_id FROM quiz WHERE quiz.active=1 AND game_id = '. $game_id.' AND status="open" LIMIT 1');
  $myResult = $result->fetch_assoc();
  $quiz_id=$myResult["quiz_id"];
  echo json_encode($quiz_id);
  $result->free();
break;
case 'updateFirstName':
  $user_id=(int)($_SESSION['user_id']);
  $pseudo=$mysqli->real_escape_string(htmlspecialchars($_GET['pseudo']));
  $mysqli->query('UPDATE users SET first_name="'.$pseudo.'" WHERE user_id = '. $user_id);
  echo json_encode("done");
break;
case 'ImportCardsFromDeck':
  $user_id=(int)($_SESSION['user_id']);
  $deck_id=(int)($_GET['deck_id']);
  $importDecks=$_GET['importDecks'];

  $today=date("Y-m-d");
  //vérifier que l'utilisateur est propriétaire de la liste.
  $result = $mysqli->query('SELECT count(*) as flag FROM user_deck_droit WHERE user_id = ' . $user_id.' AND deck_id='.$deck_id.' AND (droit="modif" OR droit="admin")');
  $myResult = $result->fetch_assoc();
  $flag=$myResult["flag"];
  $result->free();
  if($flag==0){echo json_encode("pas les droits");exit();}
//Récupérer la liste des card_id
  $cards_data=array();
  foreach($importDecks as $importDeck)
  { $importDeck_id=(int)$importDeck;
    $sql="SELECT card_id, hasImage, hasAudio, mot, mot_trad, position,lang_id FROM `cards` WHERE deck_id=".$importDeck_id;
    $result = $mysqli->query($sql);
    while ($row = $result->fetch_assoc()) {
      array_push($cards_data,$row);
    };
    $result->free();
  }

  foreach ($cards_data as $card_data) {
    $card_id=$card_data["card_id"];
    $card_hasImage=$card_data["hasImage"];
    $card_hasAudio=$card_data["hasAudio"];
    $mot=$card_data["mot"];
    $mot_trad=$card_data["mot_trad"];
    $position=$card_data["position"];
    $lang_id=$card_data["lang_id"];

   $sql = "INSERT INTO `cards`(`deck_id`, `mot`, `mot_trad`,`hasImage`, `hasAudio`, `position`,lang_id)
   VALUES (".$deck_id.",'".$mot."','".$mot_trad."',".$card_hasImage.",".$card_hasAudio.",".$position.",".$lang_id.")";
   $mysqli->query($sql);

  //Récupérer le card_id
  $newCard_id=$mysqli->insert_id;
  $sql = "INSERT INTO `user_card`(user_id,card_id,droit)
  VALUES (".$user_id.",".$newCard_id.",'creator')";
  $mysqli->query($sql);

  $sql = "INSERT INTO `card_deck`(card_id,deck_id) VALUES (".$newCard_id.",".$deck_id.")";
  $mysqli->query($sql);
   //si audio copier l'audio
   if($card_hasAudio){
     if (copy("card_audio/card_".$card_id.".wav","card_audio/card_".$newCard_id.".wav")) {
         //echo "card audio has been copied.<br>";
     } else {
         //echo "card audio has NOT been copied.<br>";
     }
   }
  }

  echo json_encode(array("deck_id"=>$deck_id,"importDecks"=>$importDecks));
break;
case 'other':
  $user_id=(int)$_SESSION['user_id'];
  //echo $user_id;
   //$json_data=$mysqli->query("UPDATE decks SET visible=0 WHERE deck_id=".$deck_id)->fetch_object()->json_data;
   //$result = $mysqli->query('SELECT COUNT(*) FROM cards WHERE deck_id = ' . $deck_id);
   //$myResult = $result->fetch_assoc();
   //$deck_id=$myResult["deck_id"];
   //$data = array ('status'=>'ok',"json"=>json_data);
   //echo json_encode($data);
break;

}
