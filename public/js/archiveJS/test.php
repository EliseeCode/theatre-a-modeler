<head>
</head>
<body>
test
<script>
<?php include "recorder.js"; ?>
var audio_context;
var recorder;

function startUserMedia(stream) {
    var input = audio_context.createMediaStreamSource(stream);    
    input.connect(audio_context.destination);    
    recorder = new Recorder(input);
}

function process() {
 recorder.record();

 setTimeout(function() {
    recorder.getBuffer(function(data) {
        console.log(data);
    });
 }, 3000);
}

window.onload = function init() {
try {
  window.AudioContext = window.AudioContext || window.webkitAudioContext;
  navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia;
  window.URL = window.URL || window.webkitURL;

  audio_context = new AudioContext;
} catch (e) {
    console.log(e);
}

navigator.getUserMedia({audio: true}, startUserMedia);

setTimeout(process, 2500); 
};
</script>
</body>