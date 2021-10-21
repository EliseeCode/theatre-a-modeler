
<?php
include "db.php";
session_start();


$jour=date("Y-m-d");
echo $jour;
$current_tps=time();
$sql="SELECT * FROM `stat` WHERE jour='".$jour."'";
$result = $mysqli->query($sql);
$statExist=$result->num_rows;
$result->close();
if($statExist!=0)
{
	$sql = "DELETE FROM stat WHERE jour='".$jour."'";
  $mysqli->query($sql);
}
$stats=array();
$sql="SELECT activite.user_id, COUNT(*) AS nbreMotTotal,SUM(CASE WHEN OptimalRD > ".$current_tps." THEN 1 ELSE 0 END) AS nbreMots FROM cards INNER JOIN activite ON activite.card_id=cards.card_id WHERE 1 GROUP BY activite.user_id" ;
echo $sql;
$result = $mysqli->query($sql);
while ($row = $result->fetch_assoc()) {
				array_push($stats,$row);
		}
$result->close();

  //$sql="INSERT INTO stat (user_id,jour,nbreMots) VALUES (".$user_id.",'".$jour."',".$nbreMots.")";

  $data = array();
echo "<br><br>";
$string=implode(",",$stats);
echo $string;
  foreach($stats as $stat)
      {
        //print_r $myResult;
        $data[] = "(" . addslashes($stat["user_id"]) . ",'".$jour."'," . addslashes($stat["nbreMots"]) . "," . addslashes($stat["nbreMotTotal"]) . ")";
      }
  $data = implode("," , $data);
  echo "<br>".$data;
  $sql = "INSERT INTO stat (user_id,jour,nbreMots,nbreMotTotal) VALUES $data";
  $mysqli->query($sql);
