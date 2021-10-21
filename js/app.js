
/*
function startRecording(button) {
	recorder && recorder.record();

	$(button).addClass('stop_icon');
	$(button).removeClass('recording_icon');
	$('.recording_icon').hide();

}
function stopRecording(button,card_id) {
	recorder && recorder.stop();
	$(button).removeClass('stop_icon');
	$(button).addClass('recording_icon');
	$('.recording_icon').show();
	// create WAV download link using audio data blob
	createDownloadLink(card_id);

	recorder.clear();
}



};

*/














//webkitURL is deprecated but nevertheless
URL = window.URL || window.webkitURL;

var gumStream; 						//stream from getUserMedia()
var rec; 							//Recorder.js object
var input; 							//MediaStreamAudioSourceNode we'll be recording
var card_id_audio
// shim for AudioContext when it's not avb.
var AudioContext = window.AudioContext || window.webkitAudioContext;
var audioContext //audio context to help us record

//var recordButton = document.getElementById("recordButton");
//var stopButton = document.getElementById("stopButton");
//var pauseButton = document.getElementById("pauseButton");

//add events to those 2 buttons
//recordButton.addEventListener("click", startRecording);
//stopButton.addEventListener("click", stopRecording);
//pauseButton.addEventListener("click", pauseRecording);

function startRecording(button) {
	console.log("recordButton clicked");

	/*
		Simple constraints object, for more advanced audio features see
		https://addpipe.com/blog/audio-constraints-getusermedia/
	*/

    var constraints = { audio: true, video:false }

 	/*
    	Disable the record button until we get a success or fail from getUserMedia()
	*/
	$(button).addClass('stop_icon');
	$(button).removeClass('recording_icon');
	$('.recording_icon').hide();
	//recordButton.disabled = true;
	//stopButton.disabled = false;
	//pauseButton.disabled = false

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
    	//recordButton.disabled = false;
    	//stopButton.disabled = true;
    	//pauseButton.disabled = true
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

function stopRecording(button,card_id) {
	console.log("stopButton clicked");
card_id_audio=card_id;
	//disable the stop button, enable the record too allow for new recordings
	//stopButton.disabled = true;
	//recordButton.disabled = false;
	//pauseButton.disabled = true;

	//reset button just in case the recording is stopped while paused
	//pauseButton.innerHTML="Pause";
	$(button).removeClass('stop_icon');
	$(button).addClass('recording_icon');
	$('.recording_icon').show();
	// create WAV download link using audio data blob
	//createDownloadLink(card_id);
	//tell the recorder to stop the recording
	rec.stop();

	//stop microphone access
	gumStream.getAudioTracks()[0].stop();

	//create the wav blob and pass it on to createDownloadLink
	rec.exportWAV(createDownloadLink);
	//createDownloadLink(card_id);
}


function createDownloadLink(blob) {
		console.log(blob);
		var url = URL.createObjectURL(blob);
		//$('#play_son_'+card_id+' > audio').remove();
		//var li = document.getElementById("play_son_"+card_id);
		var au = document.createElement('audio');
		au.controls = true;
		au.src = url;

		//li.appendChild(au);
		$('#ligne_carte_'+card_id_audio).find(".icon_audio").show();
		$('#ligne_carte_'+card_id_audio).find(".addContentIconAudio").show();
		//li.appendChild(hf);

		//======================
		var formData = new FormData();
		var fileType = 'audio'; // or "audio"
		var fileName = 'card_'+card_id_audio+'.wav';  // or "wav"
		formData.append("type", "card_audio");
		formData.append("id", card_id_audio);
		formData.append('fileToUpload', blob);
		//formData.append("fileToUpload", url);
		var request = new XMLHttpRequest();
		request.upload.onprogress = function (evt) {
								 var percentComplete = parseInt(evt.loaded *100/ evt.total);
				//		 $('.upload_progress').css("width",percentComplete+'%');
		}
		cacheBreaker=new Date().getTime();
		request.onreadystatechange = function() {
			if (request.readyState == XMLHttpRequest.DONE) {
					console.log(request.responseText);
					$.getJSON("ajax.php?action=cardHasAudio&card_id="+card_id_audio, function(result){console.log(result);
						for(k in cards){
							if(cards[k].card_id==card_id_audio){
								cards[k].hasAudio="1";
								socket.emit('cardUpdate',{dataCard:cards[k],updatedStuf:"audio"});
							}
						}
					});
					soundFile="card_audio/card_"+card_id_audio+".wav?v="+cacheBreaker;
					if(!$("#audio_"+card_id_audio).length){
						console.log("creation de l'élem Audio");
						$("body").append('<audio id="audio_'+card_id_audio+'" src="'+soundFile+'">');
						}
						else{

							$("#audio_"+card_id_audio).attr("src",soundFile="card_audio/card_"+card_id_audio+".wav?v="+cacheBreaker);
						}

			}
		}

		request.open("POST", "upload.php");
		request.send(formData);
}
