<?php
include_once ("db.php");
session_start();


if(!isset($_SESSION['user_id'])){header("location:checkLoginCookie.php");exit();}
    // Makes it easier to read
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
	$(".settingClass,.codeEntete").hide();
	$(".buttonMesClasses").hide();
	$('.desktop').menuBreaker();
	//$('.mystats').css("background-color","var(--mycolor2fonce)");

</script>

<div class="center classPage bodyContent" style="padding-top:20px;">

	<h1><?php echo __("Objectif du jour");?></h1>
	<div id="objectif"></div>
	<div class="stat_container" style="display:none;height:42vw;max-height:350px; text-align:center;margin:20px;">

		<div class="chart-container">
			<h3><?php echo __("Types d'exercices");?></h3>
			<div id='nbreExoFait'></div>
		<!--<div class="chart-container" style="display:inline-block; position: relative; height:30vh; width:90vw;max-width:700px;">-->
				<canvas id="chart-area" width="500" height="200"></canvas>
		</div>
		<div class="chart-container">
			<h3><?php echo __("Nombre de mot en mémoire en fonction du temps");?></h3>
			<div><input class="rangeDays" type="range" name="points" min="4" max="365" value="60" onchange="range=$(this).val();resizeGraph(range);"></div>
		<!--<div class="chart-container" style="display:inline-block; position: relative; height:30vh; width:90vw;max-width:700px;">-->
				<canvas id="myChart" width="500" height="200"></canvas>
		</div>


	</div>




	<!--<h1 style="margin-top:40px;">Trophées</h1>
	<div class="listTrophy">
		<div class="Badgelocked SmallBadge trophy" id="trophy_100Mots">
			<div class="img">100</div>
			<div class="desc">Mémoriser 100 mots</div>
		</div>
		<div class="trophy SmallBadge Badgelocked" id="trophy_250Mots">
			<div class="img">250</div>
			<div class="desc">Mémoriser 250 mots</div>
			<div class="bonus">+1 perf</div>
		</div>
		<div class="trophy SmallBadge Badgelocked" id="trophy_500Mots">
			<div class="img">500</div>
			<div class="desc">Mémoriser 500 mots</div>
			<div class="bonus">+2 perf</div>
		</div>
		<div class="trophy SmallBadge Badgelocked" id="trophy_1000Mots">
			<div class="img">1000</div>
			<div class="desc">Mémoriser 1000 mots</div>
			<div class="bonus">+2 perf</div>
		</div>
		<div class="trophy SmallBadge Badgelocked" id="trophy_1500Mots">
			<div class="img">1500</div>
			<div class="desc">Mémoriser 1500 mots</div>
			<div class="bonus">+2 perf</div>
		</div>

		<div class="trophy SmallBadge Badgelocked" id="trophy_1jour">
			<div class="img">1</div>
			<div class="desc">Atteindre l'objectif 1 fois</div>

		</div>
		<div class="trophy SmallBadge Badgelocked" id="trophy_3jour">
			<div class="img">3</div>
			<div class="desc">Atteindre l'objectif 3 fois</div>
			<div class="bonus">+1 perf</div>
		</div>
		<div class="trophy SmallBadge Badgelocked" id="trophy_7jour">
			<div class="img">7</div>
			<div class="desc">Atteindre l'objectif 7 fois</div>
			<div class="bonus">+1 perf</div>
		</div>
		<div class="trophy SmallBadge Badgelocked" id="trophy_20jour">
			<div class="img">20</div>
			<div class="desc">Atteindre l'objectif 20 fois</div>
			<div class="bonus">+1 perf</div>
		</div>
		<div class="trophy SmallBadge Badgelocked" id="trophy_30jour">
			<div class="img">30</div>
			<div class="desc">Atteindre l'objectif 30 fois</div>
			<div class="bonus">+2 perf</div>
		</div>
		<div class="trophy SmallBadge Badgelocked" id="trophy_100jour">
			<div class="img">100</div>
			<div class="desc">Atteindre l'objectif 100 fois</div>
			<div class="bonus">+2 perf</div>
		</div>
		<div class="trophy SmallBadge Badgelocked" id="trophy_1crea">
			<div class="img">1</div>
			<div class="desc">Créer une liste validée par un professeur</div>
		</div>
		<div class="trophy SmallBadge Badgelocked" id="trophy_3crea">
			<div class="img">3</div>
			<div class="desc">Créer 3 listes validées par un professeur</div>
			<div class="bonus">+1 perf</div>
		</div>
		<div class="trophy SmallBadge Badgelocked" id="trophy_10crea">
			<div class="img">10</div>
			<div class="desc">Créer 10 listes validées par un professeur</div>
			<div class="bonus">+2 perf</div>
		</div>
		<div class="trophy SmallBadge Badgelocked" id="trophy_1quiz">
			<div class="img">1</div>
			<div class="desc">une victoire lors d'un quiz en classe</div>
		</div>
		<div class="trophy SmallBadge Badgelocked" id="trophy_3quiz">
			<div class="img">3</div>
			<div class="desc">3 victoires en quiz en classe</div>
			<div class="bonus">+1 perf</div>
		</div>
		<div class="trophy SmallBadge Badgelocked" id="trophy_10quiz">
			<div class="img">10</div>
			<div class="desc">10 victoires en quiz en classe</div>
			<div class="bonus">+1 perf</div>
		</div>-->
</div>

<script>
/*$.getJSON("ajax.php?action=getTrophyUser&user_id="+user_id, function(result){
for(k in result)
{
	type=result[k].type;
	nbre=result[k].nbre;
	switch(type){
  	case 'crea':
		if(nbre>=10){$("#trophy_10crea").removeClass("Badgelocked").prependTo(".listTrophy");}
		if(nbre>=3){$("#trophy_3crea").removeClass("Badgelocked").prependTo(".listTrophy");}
		if(nbre>=1){$("#trophy_1crea").removeClass("Badgelocked").prependTo(".listTrophy");}
    break;
		case 'quiz':
		if(nbre>=10){$("#trophy_10quiz").removeClass("Badgelocked").prependTo(".listTrophy");}
		if(nbre>=3){$("#trophy_3quiz").removeClass("Badgelocked").prependTo(".listTrophy");}
		if(nbre>=1){$("#trophy_1quiz").removeClass("Badgelocked").prependTo(".listTrophy");}
    break;
		case '1500mots':
		$("#trophy_1500Mots").removeClass("Badgelocked").prependTo(".listTrophy");
		break;
		case '1000mots':
		$("#trophy_1000Mots").removeClass("Badgelocked").prependTo(".listTrophy");
		break;
		case '500mots':
		$("#trophy_500Mots").removeClass("Badgelocked").prependTo(".listTrophy");
		break;
		case '250mots':
		$("#trophy_250Mots").removeClass("Badgelocked").prependTo(".listTrophy");
		break;
		case '100mots':
		$("#trophy_100Mots").removeClass("Badgelocked").prependTo(".listTrophy");
		break;
		case 'jours':
		if(nbre>=100){$("#trophy_100jour").removeClass("Badgelocked").prependTo(".listTrophy");}
		if(nbre>=30){$("#trophy_30jour").removeClass("Badgelocked").prependTo(".listTrophy");}
		if(nbre>=20){$("#trophy_20jour").removeClass("Badgelocked").prependTo(".listTrophy");}
		if(nbre>=7){$("#trophy_7jour").removeClass("Badgelocked").prependTo(".listTrophy");}
		if(nbre>=3){$("#trophy_3jour").removeClass("Badgelocked").prependTo(".listTrophy");}
		if(nbre>=1){$("#trophy_1jour").removeClass("Badgelocked").prependTo(".listTrophy");}
		break;
	}
}
});*/
var stats; var OptimalRDs;
$.getJSON("ajax.php?action=getStats&user_id="+user_id, function(result){
	console.log(result);
stats=result.stats;
OptimalRDs=result.OptimalRDs;
getStatTodayUser();
});

function getStatTodayUser()
{
$.getJSON("ajax.php?action=getStatsTodayUser", function(result){
	console.log(result.stats);
	nbreMotsEnMemoire=result.nbreMotsEnMemoire;
	$('#objectif').html(nbreMotsEnMemoire);

	getPersonalStat(user_id,nbreMotsEnMemoire,30);
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
				maxTicksLimit:4,
				display: true,
				scaleLabel: {
					display: false,
					labelString: 'Nombre de mots'
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


//array.filter(function(currentValue, index, arr), thisValue)

function resizeGraph(nbre_jour)
{
	startingDate=moment().subtract(nbre_jour,"days").format("YYYY-MM-DD");
	myLine.options.scales.xAxes[0].time.min=startingDate;
	myLine.update();
}
function getPersonalStat(user_id, nbremotsNow,nbre_jour) {
	var ListeJourStat=[];
	for(s in stats){if(ListeJourStat.indexOf(stats[s].jour)==-1){ListeJourStat.push(stats[s].jour);}}
	console.log("stats.length: ", ListeJourStat.length,ListeJourStat);
	nbre_jour=Math.min(ListeJourStat.length,nbre_jour);
$('.stat_container').slideDown();
$(".stat_user_selected").removeClass("stat_user_selected");
$("#user_"+user_id).addClass("stat_user_selected");
	var ctx = document.getElementById('myChart').getContext('2d');

	if(window.myLine==undefined)
	{window.myLine = new Chart(ctx, config);}

	//$('.user_name_title').html(user_name);


		datapointsMotsMemo = [];
		datapointsMotsVus = [];
		datapointsMotsMemoPred = [];
		datapointsMotsVusPred = [];
		datapointsObjectif = [];
		datapointsToday = [];

		datapoints=[];
		datapoints2=[];
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
						if(jour_debut==""){jour_debut=jour;}
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
				//objectif=10+deltaJour1*10;

				date_debut = new Date(jour_debut);
				//deltaJour2 = (date_debut - date_ini)/(1000*60*60*24)
				//objectif_debut=10+deltaJour2*10;

				//myLine.data.datasets[2].data=[{t:jour_debut,y:objectif_debut},{t:jour_fin,y:objectif}];
				//10jours apres:
				today=moment().format("YYYY-MM-DD");
				jourFuture=moment().add(10,"days").format("YYYY-MM-DD");
				//startingDate=moment().subtract(nbre_jour,"days").format("YYYY-MM-DD");
				//objectif10j=parseInt(nbreMotTotal)+100;
				//datapointsObjectif=[{t:today,y:nbreMotTotal},{t:jourFuture,y:objectif10j}];
				//datapointsMotsToday=[{t:today,y:0},{t:today,y:objectif10j}];
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
				//myLine.data.datasets[1].data=datapointsMotsVus;
				//myLine.data.datasets[2].data=datapointsMotsToday;
				myLine.data.datasets[3].data=datapointsMotsMemoPred;
				//myLine.data.datasets[4].data=datapointsMotsVusPred;
				//myLine.data.datasets[5].data=datapointsObjectif;
				myLine.options.legend.display=false;
				myLine.options.tooltips.mode="nearest";
				if(nbre_jour<20){radi=4;}else{radi=2;}
				myLine.options.elements.point.radius=radi;
				myLine.options.elements.point.backgroundColor="#ffffff";
				myLine.options.elements.point.borderWidth=radi/2;
				startingDate=moment().subtract(nbre_jour,"days").format("YYYY-MM-DD");
				myLine.options.scales.xAxes[0].time.min=startingDate;
				//myLine.canvas.parentNode.style.height = '128px';
				myLine.update();
		}


}
















		var config2 = {
			type: 'doughnut',
			data: {
				datasets: [{
					data: [
						1,1,1,1,1
					],
					backgroundColor: [
						'#2399FF',
						'#3399BF',
						'#3399AF',
						'#3369AF',
						'#33398F',
						'#33196F'
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

		window.onload = function() {
			var ctx = document.getElementById('chart-area').getContext('2d');
			window.myDoughnut = new Chart(ctx, config2);
			$.getJSON("ajax.php?action=getExoUser&user_id=<?php echo $user_id; ?>", function(result){
				console.log(result);
				datas=[];
				games=[];
				nbreExoFait=0;
				for(k in result){
					datas.push(result[k].num);
					nbreExoFait+=parseInt(result[k].num);
					games.push(result[k].game);
				}
				$("#nbreExoFait").html(nbreExoFait+" <?php echo __("exercices faits");?>");
				config2.data.datasets[0].data=datas;
				config2.data.labels=games
				window.myDoughnut.update();
				});
		};















</script>

<script src="js/index.js"></script>

</body>
</html>
