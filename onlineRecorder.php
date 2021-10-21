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



?>
<!DOCTYPE html>
<html >
 <head>


    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Online audio recorder</title>
    <!-- Bootstrap -->
	  <link href="css/main.css?ver=<?php echo filemtime('css/main.css');?>" rel="stylesheet">
		<link href="css/styleEntete.css?ver=<?php echo filemtime('css/styleEntete.css');?>" rel="stylesheet">
	  <link href="css/deck.css?ver=<?php echo filemtime('css/deck.css');?>" rel="stylesheet">
	  <link href="css/card.css?ver=<?php echo filemtime('css/card.css');?>" rel="stylesheet">
		<link href="css/myStyle.css?ver=<?php echo filemtime('css/myStyle.css');?>" rel="stylesheet">
		<link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16" />
		<link rel="stylesheet" type="text/css" href="css/navStyle.css"/>
		<link href="css/styleLanguage.css?v8" rel="stylesheet" type="text/css" media="screen"/>
		 <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<script src="js/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui.js"></script>

		<script src="js/jquery-ui.js"></script>
	  <!--<script src="js/recorder.js"></script>-->
		<script src="https://cdn.rawgit.com/mattdiamond/Recorderjs/08e7abd9/dist/recorder.js"></script>
		<style>
		#controls {

		  display: inline-flex;
		  margin-top: 2rem;
			width:400px;
			max-width:100%;
		}
		button {
		  flex-grow: 1;
		  height: 2.5rem;
		  min-width: 2rem;
		  border: none;
		  border-radius: 0.15rem;
		  background: #ed341d;
		  margin-left: 2px;
		  box-shadow: inset 0 -0.15rem 0 rgba(0, 0, 0, 0.2);
		  cursor: pointer;
		  display: flex;
		  justify-content: center;
		  align-items: center;
		  color:#ffffff;
		  font-weight: bold;
		  font-size: 1rem;
		}
		button:hover, button:focus {
		  outline: none;
		  background: #c72d1c;
		}
		button::-moz-focus-inner {
		  border: 0;
		}
		button:active {
		  box-shadow: inset 0 1px 0 rgba(0, 0, 0, 0.2);
		  line-height: 3rem;
		}
		button:disabled {
		  pointer-events: none;
		  background: lightgray;
		}
		button:first-child {
		  margin-left: 0;
		}
		audio {
		  display: block;
		  width: 100%;
		  margin-top: 0.2rem;
		}
		li {
		  list-style: none;
		  margin-bottom: 1rem;
		}
		#formats {
		  margin-top: 0.5rem;
		  font-size: 80%;
		}
		.panneauBlanc{display:inline-block;width:800px; max-width:100%;background-color:white;padding:10px;border-radius:3px;box-shadow:0 0 3px grey;}
		#recordingsList{padding:10px;}
    </style>
		<script>


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
//$('.desktop').menuBreaker();
</script>

<div class="center bodyContent">
	<textarea placeholder="Texte à lire" style="display:inline-block;width:90%;height:30vh;"></textarea>
	<div id="controls">
	    <button id="recordButton"><?php echo __("Record");?></button>
	    <button id="pauseButton" disabled><?php echo __("Pause");?></button>
	    <button id="stopButton" disabled><?php echo __("Stop");?></button>
	</div>
	<div id="formats"></div>
	<div class="panneauBlanc" style="display:none:">
	<h3><?php echo __("Enregistrements :");?></h3>
	<ol id="recordingsList"></ol>
</div>
	<script>
	//webkitURL is deprecated but nevertheless
	numFile=1;
	URL = window.URL || window.webkitURL;

	var gumStream; 						//stream from getUserMedia()
	var rec; 							//Recorder.js object
	var input; 							//MediaStreamAudioSourceNode we'll be recording

	// shim for AudioContext when it's not avb.
	var AudioContext = window.AudioContext || window.webkitAudioContext;
	var audioContext //audio context to help us record

	var recordButton = document.getElementById("recordButton");
	var stopButton = document.getElementById("stopButton");
	var pauseButton = document.getElementById("pauseButton");

	//add events to those 2 buttons
	recordButton.addEventListener("click", startRecording);
	stopButton.addEventListener("click", stopRecording);
	pauseButton.addEventListener("click", pauseRecording);
$('.panneauBlanc').hide();
	function startRecording() {
		console.log("recordButton clicked");

		/*
			Simple constraints object, for more advanced audio features see
			https://addpipe.com/blog/audio-constraints-getusermedia/
		*/

	    var constraints = { audio: true, video:false }

	 	/*
	    	Disable the record button until we get a success or fail from getUserMedia()
		*/

		recordButton.disabled = true;
		stopButton.disabled = false;
		pauseButton.disabled = false

		/*
	    	We're using the standard promise based getUserMedia()
	    	https://developer.mozilla.org/en-US/docs/Web/API/MediaDevices/getUserMedia
		*/

		navigator.mediaDevices.getUserMedia(constraints).then(function(stream) {
			console.log("getUserMedia() success, stream created, initializing Recorder.js ...");

			/*
				create an audio context after getUserMedia is called
				sampleRate might change after getUserMedia is called, like it does on macOS when recording through AirPods
				the sampleRate defaults to the one set in your OS for your playback device

			*/
			audioContext = new AudioContext();

			//update the format

			/*  assign to gumStream for later use  */
			gumStream = stream;

			/* use the stream */
			input = audioContext.createMediaStreamSource(stream);

			/*
				Create the Recorder object and configure to record mono sound (1 channel)
				Recording 2 channels  will double the file size
			*/
			rec = new Recorder(input,{numChannels:1})

			//start the recording process
			rec.record()

			console.log("Recording started");

		}).catch(function(err) {
		  	//enable the record button if getUserMedia() fails
	    	recordButton.disabled = false;
	    	stopButton.disabled = true;
	    	pauseButton.disabled = true
		});
	}

	function pauseRecording(){
		console.log("pauseButton clicked rec.recording=",rec.recording );
		if (rec.recording){
			//pause
			rec.stop();
			pauseButton.innerHTML="Resume";
		}else{
			//resume
			rec.record()
			pauseButton.innerHTML="Pause";

		}
	}

	function stopRecording() {
		console.log("stopButton clicked");
		$('.panneauBlanc').show();
		//disable the stop button, enable the record too allow for new recordings
		stopButton.disabled = true;
		recordButton.disabled = false;
		pauseButton.disabled = true;

		//reset button just in case the recording is stopped while paused
		pauseButton.innerHTML="Pause";

		//tell the recorder to stop the recording
		rec.stop();

		//stop microphone access
		gumStream.getAudioTracks()[0].stop();

		//create the wav blob and pass it on to createDownloadLink
		rec.exportWAV(createDownloadLink);
	}

	function createDownloadLink(blob) {

		var url = URL.createObjectURL(blob);
		var au = document.createElement('audio');
		var li = document.createElement('li');
		var link = document.createElement('a');

		//name of .wav file to use during upload and download (without extendion)
		var filename = "mon_enregistrement_"+numFile;
		numFile++;
		//add controls to the <audio> element
		au.controls = true;
		au.src = url;

		//save to disk link
		link.href = url;
		link.download = filename+".wav"; //download forces the browser to donwload the file using the  filename
		link.innerHTML = "<?php echo __("Télécharger");?>";

		//add the new audio element to li
		li.appendChild(au);

		//add the filename to the li
		li.appendChild(document.createTextNode(filename+".wav "))

		//add the save to disk link to li
		li.appendChild(link);

		//upload link

		//li.appendChild(document.createTextNode (" "))//add a space in between
		//li.appendChild(upload)//add the upload link to li

		//add the li element to the ol
		recordingsList.appendChild(li);
	}

	</script>
	<script src="https://cdn.rawgit.com/mattdiamond/Recorderjs/08e7abd9/dist/recorder.js"></script>
</div>




</body>
</html>
