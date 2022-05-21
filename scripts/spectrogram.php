<script>  
// CREDITS: https://codepen.io/jakealbaugh/pen/jvQweW

// UPDATE: there is a problem in chrome with starting audio context
//  before a user gesture. This fixes it.
var started = null;
var player = null;
const ctx = null;
window.onload = function(){
  var audioelement =  window.parent.document.getElementsByTagName("audio")[0];
  if (typeof(audioelement) != 'undefined') {

    document.getElementById('player').remove();

    player = audioelement;
  } else {
    player = document.getElementById('player');
  }
  if (started) return;
  started = true;
  initialize();
};

function applyText(text) {
    CTX.fillStyle = 'white';
  CTX.font = '25px Roboto Flex';
  CTX.fillText(text, document.body.querySelector('canvas').scrollWidth - (document.body.querySelector('canvas').scrollWidth * 0.20), document.body.querySelector('canvas').scrollHeight * 0.35);
  CTX.fillStyle = 'hsl(280, 100%, 10%)';
}

var previous_detection_identifier = null;
function loadDetectionIfNewExists() {
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
    // if there's a new detection that needs to be updated to the page
    if(this.responseText.length > 0 && !this.responseText.includes("Database")) {
      if(previous_detection_identifier != null){
          applyText(this.responseText.split(",")[0].replace("_"," "));
      }
      previous_detection_identifier = this.responseText.split(",")[1];
    }
  }
  xhttp.open("GET", "overview.php?ajax_detections=true&previous_detection_identifier="+previous_detection_identifier+"&only_name=true", true);
  xhttp.send();
}

window.setInterval(function(){
   loadDetectionIfNewExists();
}, 5000);

function initialize() {
  document.body.querySelector('h1').remove();
  const CVS = document.body.querySelector('canvas');
  CTX = CVS.getContext('2d');
  const W = CVS.width = window.innerWidth;
  const H = CVS.height = window.innerHeight;

  const ACTX = new AudioContext();
  const ANALYSER = ACTX.createAnalyser();

  ANALYSER.fftSize = 2048;  
  
  process();

  function process() {
    const SOURCE = ACTX.createMediaElementSource(player);
    SOURCE.connect(ANALYSER);
    SOURCE.connect(ACTX.destination)
    const DATA = new Uint8Array(ANALYSER.frequencyBinCount);
    const LEN = DATA.length;
    const h = (H / LEN + 0.9);
    const x = W - 1;
    CTX.fillStyle = 'hsl(280, 100%, 10%)';
    CTX.fillRect(0, 0, W, H);

    loop();

    function loop() {
      window.requestAnimationFrame(loop);
      let imgData = CTX.getImageData(1, 0, W - 1, H);

      CTX.fillRect(0, 0, W, H);
      CTX.putImageData(imgData, 0, 0);
      ANALYSER.getByteFrequencyData(DATA);
      for (let i = 0; i < LEN; i++) {
        let rat = DATA[i] / 196 ;
        let hue = Math.round((rat * 120) + 280 % 360);
        let sat = '100%';
        let lit = 10 + (70 * rat) + '%';
        CTX.beginPath();
        CTX.strokeStyle = `hsl(${hue}, ${sat}, ${lit})`;
        CTX.moveTo(x, H - (i * h));
        CTX.lineTo(x, H - (i * h + h));
        CTX.stroke();
      }
    }
  }
}

</script>
<style>
html, body {
  height: 100%;
}

canvas {
  display: block;
  height: 85%;
  width: 100%;
}

h1 {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  margin: 0;
}
</style>


<audio style="display:none" controls="" crossorigin="anonymous" id='player' autoplay=""><source src="/stream"></audio>
<h1>Loading...</h1>
<canvas></canvas>
