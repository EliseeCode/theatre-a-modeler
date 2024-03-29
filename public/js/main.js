/* Copyright 2013 Chris Wilson
Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at
http://www.apache.org/licenses/LICENSE-2.0
Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/
/*
function play_audio(soundFile_id)
{
soundFile="uploads/carte_"+soundFile_id+".wav";
soundElem = document.createElement("audio");
		soundElem.setAttribute("id", "carte_son");
		soundElem.setAttribute("src", soundFile);
		
		
		soundElem.play();
		
		
}
*/








window.AudioContext = window.AudioContext || window.webkitAudioContext;
var audioContext = new AudioContext();
var audioInput = null,
realAudioInput = null,
inputPoint = null,
audioRecorder = null;
var rafID = null;
var analyserContext = null;
var canvasWidth, canvasHeight;
var recIndex = 0;
/* TODO:
- offer mono option
- "Monitor input" switch
*/

function saveAudio() {
audioRecorder.exportWAV( doneEncoding );
// could get mono instead by saying
// audioRecorder.exportMonoWAV( doneEncoding );
}

function gotBuffers_local2web( buffers ) {

if(buffers[0][0]==0){alert('probleme audio. Videz le cache.');};
//	var canvas = document.getElementById( "wavedisplay" );
//	drawBuffer( canvas.width, canvas.height, canvas.getContext('2d'), buffers[0] );
// the ONLY time gotBuffers is called is right after a new recording is completed -
// so here's where we should set up the download.
audioRecorder.exportWAV( doneEncoding_local2web );
}

function gotBuffers( buffers ) {
if(buffers[0][0]==0){alert('probleme audio. Videz le cache.');}
console.log('fonction got buffer active :'+buffers);
//	var canvas = document.getElementById( "wavedisplay" );
//	drawBuffer( canvas.width, canvas.height, canvas.getContext('2d'), buffers[0] );
// the ONLY time gotBuffers is called is right after a new recording is completed -
// so here's where we should set up the download.
audioRecorder.exportWAV( doneEncoding );
}

function doneEncoding_local2web( blob ) {

//enregistrement du fichier wav
var fileType = 'audio'; // or "audio"
var fileName = 'carte_'+id_son+'.wav';  // or "wav"     
var formData = new FormData();
formData.append(fileType + '-filename', fileName);
formData.append(fileType + '-blob', blob);
xhr('js/blob2wav_local2web.php', formData, function (fName) {
    window.open(location.href + fName);
});
function xhr(url, data, callback) {
    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
		
		
		//play_audio();
            //alert(request.responseText);
        }
    };
    request.open('POST', url);
    request.send(data);
}
//fin de l'envoi du wav

Recorder.setupDownload( blob, "myRecording" + ((recIndex<10)?"0":"") + recIndex + ".wav" );
recIndex++;
}

function doneEncoding( blob ) {
//enregistrement du fichier wav
var fileType = 'audio'; // or "audio"
var fileName = 'carte_'+id_son+'.wav';  // or "wav"     
var formData = new FormData();
formData.append(fileType + '-filename', fileName);
formData.append(fileType + '-blob', blob);
formData.append(fileType + '-id', id_son);
xhr('js/blob2wav.php', formData, function (fName) {
    window.open(location.href + fName);
});
function xhr(url, data, callback) {
    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
		 console.log(request.responseText);
		 if(request.responseText!='ERROR'){
		console.log('fonction xhr active:'+url+' '+data+' '+callback);
		$('#REC_son_'+id_mot).next().remove();
		$('#REC_son_'+id_mot).next().remove();
		$('#REC_son_'+id_mot).after('<TD id="suppr_son_'+id_mot+'"><img class="suppr_son" src="image/del_son.png" width="40px" onclick="del_audio('+id_mot+');"></TD>');
		$('#REC_son_'+id_mot).after('<TD id="play_son_'+id_mot+'"><img src="image/haut_parleur.png" onclick="play_audio('+id_mot+');" width="40px"></TD>');
			$("#sound_"+id_mot).remove();
			soundFile_id=id_mot;
			soundFile="uploads/carte_"+id_son+".wav";
			$("body").append('<audio id="sound_'+id_mot+'" src="'+soundFile+'">');
			document.getElementById('sound_'+soundFile_id).load();
						
		}
		else{
		
		}
		//play_audio();
            //alert(request.responseText);
        }
		
    };
    request.open('POST', url);
    request.send(data);
}
//fin de l'envoi du wav

Recorder.setupDownload( blob, "myRecording" + ((recIndex<10)?"0":"") + recIndex + ".wav" );
recIndex++;
}





function toggleRecording( e,id_son,id_mot ) {
console.log('la fonction toggleRecording est active:'+e+' '+id_son);
if (e.classList.contains("recording")) {
// stop recording
console.log('contient la class recording donc stop le record:'+e.classList);
audioRecorder.stop();
e.classList.remove("recording");
$('.REC_son').attr('src','image/mic128.png');
audioRecorder.getBuffers( gotBuffers );

//Icone son
			$('#REC_son_'+id_mot).next().html('<img src="image/attente.gif" width="25px">');
	
//window.location='#';
} 
else {
console.log('ne contient pas la class recording... allons y:'+e+' '+id_son);
// start recording
if (!audioRecorder)
{
console.log('il n y a pas de audioRecorder...FIN DU CHEMIN');
return;}
else{console.log('on continue, il y a AudioRecorder');}
e.classList.add("recording");
console.log('on nettoie le audioRecorder:');
audioRecorder.clear();
console.log('class recording ajouté et on enregistre dans 3...2...1...');
audioRecorder.record();}



}
function convertToMono( input ) {
var splitter = audioContext.createChannelSplitter(2);
var merger = audioContext.createChannelMerger(2);
input.connect( splitter );
splitter.connect( merger, 0, 0 );
splitter.connect( merger, 0, 1 );
return merger;
}
function cancelAnalyserUpdates() {
window.cancelAnimationFrame( rafID );
rafID = null;
}
function updateAnalysers(time) {
if (!analyserContext) {
//var canvas = document.getElementById("analyser");
//canvasWidth = canvas.width;
//canvasHeight = canvas.height;
//analyserContext = canvas.getContext('2d');
}
// analyzer draw code here
{
var SPACING = 3;
var BAR_WIDTH = 1;
var numBars = Math.round(canvasWidth / SPACING);
var freqByteData = new Uint8Array(analyserNode.frequencyBinCount);
analyserNode.getByteFrequencyData(freqByteData);
//analyserContext.clearRect(0, 0, canvasWidth, canvasHeight);
//analyserContext.fillStyle = '#F6D565';
//analyserContext.lineCap = 'round';
var multiplier = analyserNode.frequencyBinCount / numBars;
// Draw rectangle for each frequency bin.
for (var i = 0; i < numBars; ++i) {
var magnitude = 0;
var offset = Math.floor( i * multiplier );
// gotta sum/average the block, or we miss narrow-bandwidth spikes
for (var j = 0; j< multiplier; j++)
magnitude += freqByteData[offset + j];
magnitude = magnitude / multiplier;
var magnitude2 = freqByteData[i * multiplier];
//analyserContext.fillStyle = "hsl( " + Math.round((i*360)/numBars) + ", 100%, 50%)";
//analyserContext.fillRect(i * SPACING, canvasHeight, BAR_WIDTH, -magnitude);
}
}
rafID = window.requestAnimationFrame( updateAnalysers );
}
function toggleMono() {
if (audioInput != realAudioInput) {
audioInput.disconnect();
realAudioInput.disconnect();
audioInput = realAudioInput;
} else {
realAudioInput.disconnect();
audioInput = convertToMono( realAudioInput );
}
audioInput.connect(inputPoint);
}
function gotStream(stream) {
inputPoint = audioContext.createGain();
// Create an AudioNode from the stream.
realAudioInput = audioContext.createMediaStreamSource(stream);
audioInput = realAudioInput;
audioInput.connect(inputPoint);
// audioInput = convertToMono( input );
analyserNode = audioContext.createAnalyser();
analyserNode.fftSize = 2048;
inputPoint.connect( analyserNode );
audioRecorder = new Recorder( inputPoint );
zeroGain = audioContext.createGain();
zeroGain.gain.value = 0.0;
inputPoint.connect( zeroGain );
zeroGain.connect( audioContext.destination );
updateAnalysers();
}
function initAudio() {
if (!navigator.getUserMedia)
navigator.getUserMedia = navigator.webkitGetUserMedia || navigator.mozGetUserMedia;
if (!navigator.cancelAnimationFrame)
navigator.cancelAnimationFrame = navigator.webkitCancelAnimationFrame || navigator.mozCancelAnimationFrame;
if (!navigator.requestAnimationFrame)
navigator.requestAnimationFrame = navigator.webkitRequestAnimationFrame || navigator.mozRequestAnimationFrame;
navigator.getUserMedia({audio:true}, gotStream, function(e) {
alert('Error getting audio');
console.log(e);
});
}
initAudio();
//window.addEventListener('load', initAudio );