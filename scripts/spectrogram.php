<script>
// CREDITS: https://codepen.io/jakealbaugh/pen/jvQweW

// UPDATE: there is a problem in chrome with starting audio context
//  before a user gesture. This fixes it.
var started = null;
window.onload = function(){
  if (started) return;
  started = true;
  initialize();
};

function initialize() {
  document.body.querySelector('h1').remove();
  const CVS = document.body.querySelector('canvas');
  const CTX = CVS.getContext('2d');
  const W = CVS.width = window.innerWidth;
  const H = CVS.height = window.innerHeight;

  const ACTX = new AudioContext();
  const ANALYSER = ACTX.createAnalyser();

  ANALYSER.fftSize = 2048;  
  
  process();

  function process() {
    const SOURCE = ACTX.createMediaElementSource(document.getElementById('player'));
    SOURCE.connect(ANALYSER);
    SOURCE.connect(ACTX.destination)
    const DATA = new Uint8Array(ANALYSER.frequencyBinCount);
    const LEN = DATA.length;
    const h = (H / LEN + .8);
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
        let rat = DATA[i] / 128;
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

