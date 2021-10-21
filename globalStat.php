<?php
include_once ("db.php");
session_start();
		$MailTracker=array();
		$sql="SELECT COUNT(*) as numMailSent, sum(case when openingDate != '0000-00-00' then 1 else 0 end) AS numMailOpen,sendingDate,sujet FROM emailTracker WHERE 1 Group by sendingDate,sujet ORDER BY sendingDate DESC";
		$result = $mysqli->query($sql);
		while ($row = $result->fetch_assoc()) {
						array_push($MailTracker,$row);
				}
		$result->close();
		echo "<script>MailTracker=".json_encode($MailTracker).";</script>";

		$ActiviteGlobal=array();
		$today=date('Ymd');
		$ThreeWeeksAgo=date('Ymd', strtotime('-21 day', strtotime($today)));
		$sql="SELECT COUNT(*) as num,game FROM activiteGlobal WHERE 1 Group by game ORDER BY num DESC";
		$result = $mysqli->query($sql);
		while ($row = $result->fetch_assoc()) {
						array_push($ActiviteGlobal,$row);
				}
		$result->close();
		echo "<script>ActiviteGlobal=".json_encode($ActiviteGlobal).";</script>";



		$ActiviteGlobal2=array();
		$sql="SELECT COUNT(*) as num,jour FROM activiteGlobal WHERE 1 Group by jour ORDER BY jour ASC";
		echo $sql;
		$result = $mysqli->query($sql);
		while ($row = $result->fetch_assoc()) {
						array_push($ActiviteGlobal2,$row);
				}
		$result->close();
		echo "<script>ActiviteGlobal2=".json_encode($ActiviteGlobal2).";</script>";

		$ActiviteGlobal3=array();
		$sql="SELECT COUNT(*) as num FROM activiteGlobal WHERE 1 Group by user_id";
		$result = $mysqli->query($sql);
		while ($row = $result->fetch_assoc()) {
						array_push($ActiviteGlobal3,$row);
				}
		$result->close();
		echo "<script>ActiviteGlobal3=".json_encode($ActiviteGlobal3).";</script>";



		$sql="SELECT SUM(nbreCoins) as nbreCoins FROM users WHERE 1";
		$result = $mysqli->query($sql);
		$row = $result->fetch_assoc();
		$nbreCoins=$row["nbreCoins"];
		$result->close();

		$sql="SELECT COUNT(*) as nbreTotalExo FROM activiteGlobal WHERE 1";
		$result = $mysqli->query($sql);
		$row = $result->fetch_assoc();
		$nbreTotalExo=$row["nbreTotalExo"];
		$result->close();


		$sql="SELECT COUNT(*) as nbreUsers FROM users WHERE active=1";
		$result = $mysqli->query($sql);
		$row = $result->fetch_assoc();
		$nbreUsers=$row["nbreUsers"];
		$result->close();

		$sql="SELECT COUNT(*) as nbreDecks FROM decks WHERE 1";
		$result = $mysqli->query($sql);
		$row = $result->fetch_assoc();
		$nbreDecks=$row["nbreDecks"];
		$result->close();

		$sql="SELECT COUNT(*) as nbreCards FROM cards WHERE 1";
		$result = $mysqli->query($sql);
		$row = $result->fetch_assoc();
		$nbreCards=$row["nbreCards"];
		$result->close();

?>
<!DOCTYPE html>
<html >
 <head>


    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mes stats</title>
    <!-- Bootstrap -->
	  <link href="css/main.css?ver=<?php echo filemtime('css/main.css');?>" rel="stylesheet">
		<link href="css/styleEntete.css?ver=<?php echo filemtime('css/styleEntete.css');?>" rel="stylesheet">
		<link href="css/myStyle.css?ver=<?php echo filemtime('css/myStyle.css');?>" rel="stylesheet">
		<link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16" />
		<link rel="stylesheet" type="text/css" href="css/navStyle.css"/>
		<script src="js/jquery-3.3.1.min.js"></script>
		<script src="js/Moment.js"></script>
		<script src="js/charts.js"></script>
		<style>
      .navbar{
        margin-bottom:0;
        border-radius:0;
      }
			.chart-container{
				background-color: white;
    margin: 10px;
    padding: 20px;
    box-shadow: 0 0 5px grey;
		vertical-align: top;display:inline-block; max-width:700px;width:100vw;text-align:center;}
    </style>
</head>

<body class="fond">
	<?php include "entete.php";?>
<script>
	$('.mystats').addClass("active");
	$(".settingClass").hide();
	$(".buttonMesClasses").hide();
	$('.desktop').menuBreaker();
	//$('.mystats').css("background-color","var(--mycolor2fonce)");

</script>

<div class="center">

	<div class="stat_container" style="text-align:center;margin:20px;margin-top:90px;">
		<h1>Bilan de VocaSion</h1>
		<h3>Sept 2019 - Aujourd'hui</h3>
		<div class="chart-container">
			<h3>Bilan brut</h3>
			<table style="display:inline-block;">
				<tr>
					<td>Nombre d'exercices faits<td>
					<td><?php echo $nbreTotalExo;?><td>
				</tr>
				<tr>
					<td>Nombre d'utilisateurs<td>
					<td><?php echo $nbreUsers;?><td>
				</tr>
				<tr>
					<td>Nombre de cartes<td>
					<td><?php echo $nbreCards;?><td>
				</tr>
				<tr>
					<td>Nombre de listes<td>
					<td><?php echo $nbreDecks;?><td>
				</tr>
				<tr>
					<td>Nombre de pièces d'or<td>
					<td><?php echo $nbreCoins;?><td>
				</tr>
			</table>
		</div>
		<div class="chart-container">
			<h3>Ouverture mail</h3>
			<div id='mailTracker'></div>
		<!--<div class="chart-container" style="display:inline-block; position: relative; height:30vh; width:90vw;max-width:700px;">-->
				<canvas id="OpeningMail-area" width="500" height="200"></canvas>
		</div>
		<div class="chart-container">
			<h3>Types d'exercices</h3>
			<div id='nbreExoFait'></div>
		<!--<div class="chart-container" style="display:inline-block; position: relative; height:30vh; width:90vw;max-width:700px;">-->
				<canvas id="chart-area" width="500" height="200"></canvas>
		</div>
		<div class="chart-container">
			<h3>Nombre d'exercices en fonction du temps</h3>

		<!--<div class="chart-container" style="display:inline-block; position: relative; height:30vh; width:90vw;max-width:700px;">-->
				<canvas id="myChart" width="500" height="200"></canvas>
		</div>

		<div class="chart-container">
			<h3>Repartition des utilisateurs par nombre d'exercices</h3>

		<!--<div class="chart-container" style="display:inline-block; position: relative; height:30vh; width:90vw;max-width:700px;">-->
				<canvas id="myChartBar" width="500" height="200"></canvas>
		</div>


	</div>

</div>

<script>
//Nombre d'exercices en fonction du temps, par jours et en cumulé.
var config = {
	type: 'line',
	data: {
		labels: [],//'0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
		datasets: [{
			label: 'Nbre exo Cumulés',
			yAxisID: "y-axis-0",
			data: [],
			borderColor: "#3399ff",
			backgroundColor: 'rgba(0, 255, 0, 0.2)',
			fill: false,
			//cubicInterpolationMode: 'monotone'
			lineTension: 0
		},
		{
			label: 'Nbre exo Mots',
			yAxisID: "y-axis-1",
			data: [],
			borderColor: "#ff9933",
			backgroundColor: 'rgba(0, 255, 0, 0.2)',
			fill: false,
			//cubicInterpolationMode: 'monotone'
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
								min:"2018-01-01",
								unit:"day"
						},
				display: true,
				scaleLabel: {
					display: true
				}
			}],
			yAxes: [{
				min:0,
				"id": "y-axis-0",
				maxTicksLimit:4,
				display: true,
				scaleLabel: {
					display: false,
					labelString: 'Nombre exo cumulé'
				},
				ticks: {
					source:'data',
					min:0,
					maxTicksLimit:4
					//suggestedMin: -10,
					//suggestedMax: 200,
				}
			},{
				min:0,
				"id": "y-axis-1",
				position: "right",

				maxTicksLimit:4,
				display: true,
				scaleLabel: {
					display: false,
					labelString: 'Nombre exo'
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

//Config des data pour le nombre d'elèves par nombre d'exercices effectué.
config3={
    type: 'bar',
    data: {
        labels: [],
        datasets: [{
            label: '',
            data: [12, 19, 3, 5, 2, 3],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        }
    }
}

//Config du % d'exercices fait par type d'exercice.
		var config2 = {
			type: 'doughnut',
			data: {
				datasets: [{
					data: [
						1,1,1,1,1
					],
					backgroundColor: [
						'#2399FF',
						'#FF9933',
						'#99FF33',
						'#3366EF',
						'#F3398F',
						'#FF3333',
						'#2333FF',
						'#FF9933',
						'#33FF33'
					],
					label: 'Mes Exercices'
				}],
				labels: [

				]
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
		//Config % ouverture mails
		var MailTrackerConf = {
				type: 'bar',
				data: {
			labels: ["test","ddd","dd"],
			datasets: [{
				label: 'Open',
				backgroundColor: "#557FFF",
				data: [
					2,3,2
				]
			}, {
				label: 'Not opened yet',
				backgroundColor: "#FF557F",
				data: [
					1,2,5
				]
			}]
		},
		options: {
			title: {
				display: true,
				text: 'Chart.js Bar Chart - Stacked'
			},
			tooltips: {
				mode: 'index',
				intersect: false
			},
			responsive: true,
			scales: {
				xAxes: [{
					stacked: true,
				}],
				yAxes: [{
					stacked: true
				}]
			}
		}
		};









		window.onload = function() {
			//config du % ouverture des mails
			var ctx = document.getElementById('OpeningMail-area').getContext('2d');
			window.myMailTrackerGraph = new Chart(ctx, MailTrackerConf);


				datasSent=[];
				datasOpen=[];
				label=[];
				for(k in MailTracker){
					datasSent.push(parseInt(MailTracker[k].numMailSent)-parseInt(MailTracker[k].numMailOpen));
					datasOpen.push(parseInt(MailTracker[k].numMailOpen));
					sujet=MailTracker[k].sujet;
					sendingDate=MailTracker[k].sendingDate;
					label.push(sujet+" "+sendingDate);
				}
				MailTrackerConf.data.datasets[0].data=datasOpen;
				MailTrackerConf.data.datasets[1].data=datasSent;
				MailTrackerConf.data.labels=label
				window.myMailTrackerGraph.update();

			//Config du % d'exercices fait par type d'exercice.
			var ctx = document.getElementById('chart-area').getContext('2d');
			window.myDoughnut = new Chart(ctx, config2);


				datas=[];
				games=[];
				nbreExoFait=0;
				for(k in ActiviteGlobal){
					datas.push(ActiviteGlobal[k].num);
					nbreExoFait+=parseInt(ActiviteGlobal[k].num);
					games.push(ActiviteGlobal[k].game);
				}
				$("#nbreExoFait").html(nbreExoFait+" exercices faits");
				config2.data.datasets[0].data=datas;
				config2.data.labels=games
				window.myDoughnut.update();

				//Config du nombre d'exo en fonction du temps
				var ctx = document.getElementById('myChart').getContext('2d');
				if(window.myLine==undefined)
				{window.myLine = new Chart(ctx, config);}
					datapoints=[];
					datapoints2=[];
					nbreExoCum=0;
							for(k in ActiviteGlobal2)
							{		nbreExoCum+=parseInt(ActiviteGlobal2[k].num);
									datapoints.push({t:ActiviteGlobal2[k].jour,y:nbreExoCum});
									datapoints2.push({t:ActiviteGlobal2[k].jour,y:parseInt(ActiviteGlobal2[k].num)});
							}
							myLine.data.datasets[0].data=datapoints;
							myLine.data.datasets[1].data=datapoints2;
							myLine.options.legend.display=false;
							myLine.options.tooltips.mode="nearest";
							myLine.options.elements.point.radius=2;
							myLine.options.elements.point.backgroundColor="#ffffff";
							myLine.options.elements.point.borderWidth=1;
							//startingDate=moment().subtract(30,"days").format("YYYY-MM-DD");
							startingDate="2021-09-01";
							myLine.options.scales.xAxes[0].time.min=startingDate;
							myLine.update();



							//Import des data pour le nombre d'elèves par nombre d'exercices effectué.
			var ctx3 = document.getElementById('myChartBar').getContext('2d');
			var myChartBar = new Chart(ctx3, config3);

			datapoints=[];
			labelsCum=[];
			nbreExoCum=0;
					for(k in ActiviteGlobal3)
					{	if(ActiviteGlobal3[k].num>10){
						cat=Math.round(parseInt(ActiviteGlobal3[k].num)/500);
							//if(cat>5){cat=5;}
							catp=cat+1;
							if(datapoints[cat]==undefined){datapoints[cat]=1;labelsCum[cat]=cat+"-"+catp+"x500";}
							else{datapoints[cat]+=1;}
						}
					}
					for(k=0;k<datapoints.length;k++)
					{if(datapoints[cat]==undefined){datapoints[cat]=0;labelsCum[cat]=cat+"x500";}}
					//labelsCum[5]="500 et +";
					myChartBar.data.datasets[0].data=datapoints;
					myChartBar.data.labels=labelsCum;
					myChartBar.options.legend.display=false;
					myChartBar.options.tooltips.mode="nearest";
					myChartBar.options.elements.point.radius=2;
					myChartBar.options.elements.point.backgroundColor="#ffffff";
					myChartBar.options.elements.point.borderWidth=1;
					myChartBar.update();


		};















</script>

<script src="js/index.js"></script>

</body>
</html>
