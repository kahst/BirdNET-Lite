<?php
error_reporting(E_ERROR);
ini_set('display_errors',1);
if(isset($_GET['ajax_csv'])) {

if (file_exists('./scripts/thisrun.txt')) {
  $config = parse_ini_file('./scripts/thisrun.txt');
} elseif (file_exists('./scripts/firstrun.ini')) {
  $config = parse_ini_file('./scripts/firstrun.ini');
}
$RECS_DIR = $config["RECS_DIR"];

$user = shell_exec("awk -F: '/1000/{print $1}' /etc/passwd");
$home = shell_exec("awk -F: '/1000/{print $6}' /etc/passwd");
$home = trim($home);
$files = scandir($RECS_DIR."/".date('F-Y')."/".date('d-l')."/", SCANDIR_SORT_ASCENDING);
$newest_file = $files[2];


if($newest_file == $_GET['newest_file']) {
  die();
} 

echo "file,".$newest_file."\n";

$row = 1;
if (($handle = fopen($RECS_DIR."/".date('F-Y')."/".date('d-l')."/".$newest_file.".csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if($row != 1){
          $num = count($data);
          for ($c=0; $c < $num; $c++) {
              $exp = explode(';',$data[$c]);
              echo (($exp[0]+$exp[1])/2).",".$exp[3].",".$exp[4]."\n";
          }
        }
        $row++;
    }
    fclose($handle);
}
die();
}
?>
<script>  
// CREDITS: https://codepen.io/jakealbaugh/pen/jvQweW

// UPDATE: there is a problem in chrome with starting audio context
//  before a user gesture. This fixes it.
var started = null;
var player = null;
var gain = 128;
const ctx = null;
let fps =[];
let avgfps;
let requestTime;

<?php 
if(isset($_GET['legacy']) && $_GET['legacy'] == "true") {
  echo "var legacy = true;";
} else {
  echo "var legacy = false;";
}
?>

window.onload = function(){
  var playersrc =  document.getElementById('playersrc');
  playersrc.onerror = function() {
    window.location="views.php?view=Spectrogram&legacy=true";
  };

  // if user agent includes iPhone or Mac use legacy mode
  if(((window.navigator.userAgent.includes("iPhone") || window.navigator.userAgent.includes("Mac")) && !window.navigator.userAgent.includes("Chrome")) || legacy == true) {
    document.getElementById("spectrogramimage").style.display="";
    document.body.querySelector('canvas').remove();
    document.getElementById('player').remove();
    document.body.querySelector('h1').remove();
    document.getElementsByClassName("centered")[0].remove()

    <?php 
    if (file_exists('./scripts/thisrun.txt')) {
    $config = parse_ini_file('./scripts/thisrun.txt');
  } elseif (file_exists('./scripts/firstrun.ini')) {
    $config = parse_ini_file('./scripts/firstrun.ini');
  }
  $refresh = $config['RECORDING_LENGTH'];
  $time = time();
  ?>
    // every $refresh seconds, this loop will run and refresh the spectrogram image
  window.setInterval(function(){
    document.getElementById("spectrogramimage").src = "/spectrogram.png?nocache="+Date.now();
  }, <?php echo $refresh; ?>*1000);
  } else {
    document.getElementById("spectrogramimage").remove();

  var audioelement =  window.parent.document.getElementsByTagName("audio")[0];
  if (typeof(audioelement) != 'undefined') {

    document.getElementById('player').remove();

    player = audioelement;
    if (started) return;
    started = true;
    initialize();
  } else {
    player = document.getElementById('player');
    player.oncanplay = function() {
      if (started) return;
        started = true;
        initialize();
    };
  }
  player.play();
  
  }
};

function fitTextOnCanvas(text,fontface,yPosition){    
    var fontsize=300;
    do{
        fontsize--;
        CTX.font=fontsize+"px "+fontface;
    }while(CTX.measureText(text).width>document.body.querySelector('canvas').width)
    CTX.font = CTX.font=(fontsize*0.35)+"px "+fontface;
    CTX.fillText(text,document.body.querySelector('canvas').width - (document.body.querySelector('canvas').width * 0.50),yPosition);
}

function applyText(text,x,y,opacity) {
  console.log("conf: "+opacity)
  console.log(text+" "+parseInt(x)+" "+y)
  if(opacity < 0.2) {
    opacity = 0.2;
  }
  CTX.textAlign = "center";
    CTX.fillStyle = "rgba(255, 255, 255, "+opacity+")";
  CTX.font = '15px Roboto Flex';
  //fitTextOnCanvas(text,"Roboto Flex",document.body.querySelector('canvas').scrollHeight * 0.35)
  CTX.fillText(text,parseInt(x),y)
  CTX.fillStyle = 'hsl(280, 100%, 10%)';
}

var add =0;
var newest_file;
var lastbird;
function loadDetectionIfNewExists() {
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
    // if there's a new detection that needs to be updated to the page
    if(this.responseText.length > 0 && !this.responseText.includes("Database")) {
      
      var split = this.responseText.split("\n")
      for(var i = 1;i < split.length; i++) {
        if(parseInt(split[i].split(",")[0]) >= 0){

          newest_file =  split[0].split(",")[1]
          //applyText(split[i].split(",")[1],document.body.querySelector('canvas').width - ((parseInt(split[i].split(",")[0]))*avgfps), (document.body.querySelector('canvas').height * 0.50))
          
          d1 = new Date(newest_file.split("-")[0]+"/"+newest_file.split("-")[1]+"/"+newest_file.split("-")[2]+ " "+newest_file.split("-")[4].replace(".wav",""))
          console.log("d1 "+d1)
          d2 = new Date(xhttp.getResponseHeader("Date"));
          console.log("d2 "+d2)
          timeDiff = (d2-d1)/1000;

          // stagger Y placement if a new bird
          if(split[i].split(",")[1] != lastbird || split[i].split(",")[1].length > 13) {
            add+= 15;
            if(add >= 60) {
             add = 0;
            }

            //if(parseFloat(add + document.body.querySelector('canvas').height * 0.50) > document.body.querySelector('canvas').height || parseFloat(add + document.body.querySelector('canvas').height * 0.50) <= 0) {
             // add = 0;
            //}
          }
          console.log(add)

          // Date csv file was created + relative detection time of bird + mic delay
          secago = Math.abs(timeDiff) - split[i].split(",")[0] - 6.8;

          x = document.body.querySelector('canvas').width - ((parseInt(secago))*avgfps);
          // if the text is too close to the right side of the canvas and will be cut off, wait 3 seconds before adding text
          if(x > document.body.querySelector('canvas').width - (3*avgfps)) {
        setTimeout(function (split,i,x,add) {
          console.log("ADD:"+add)
          console.log(split[i])
          console.log("originally at "+x+", now waiting 2 sec and at "+(x-(3*avgfps)))
        applyText(split[i].split(",")[1],(x - (3*avgfps)), ((document.body.querySelector('canvas').height * 0.50) + add ), split[i].split(",")[2]);
      }, 2000, split, i, x, add)
      } else {
        applyText(split[i].split(",")[1],x, ((document.body.querySelector('canvas').height * 0.50) + add ), split[i].split(",")[2])
      }
  

          lastbird = split[i].split(",")[1]
        }
        
      }
    }
  }
  xhttp.open("GET", "spectrogram.php?ajax_csv=true&newest_file="+newest_file, true);
  xhttp.send();
  
}

window.setInterval(function(){
   loadDetectionIfNewExists();
}, 500);

var compressor = undefined;
var SOURCE;
var ACTX;
var ANALYSER;
var gainNode;

function toggleCompression(state) {
  //var biquadFilter = ACTX.createBiquadFilter();
  //biquadFilter.type = "highpass";
 // biquadFilter.frequency.setValueAtTime(13000, ACTX.currentTime);
  if(state == true) {
    SOURCE.disconnect(gainNode)
    gainNode.disconnect(ANALYSER);
    gainNode.disconnect(ACTX.destination);
    SOURCE.connect(compressor);
    compressor.connect(ANALYSER);
    ANALYSER.connect(gainNode);
    gainNode.connect(ACTX.destination);
    //biquadFilter.connect(ANALYSER);
    //biquadFilter.connect(ACTX.destination);
  } else {
    SOURCE.disconnect(compressor);
    compressor.disconnect(ANALYSER);
    ANALYSER.disconnect(gainNode);
    gainNode.disconnect(ACTX.destination);
    SOURCE.connect(gainNode);
    gainNode.connect(ANALYSER);
    gainNode.connect(ACTX.destination);
  }
}

function initialize() {
  document.body.querySelector('h1').remove();
  const CVS = document.body.querySelector('canvas');
  CTX = CVS.getContext('2d');
  const W = CVS.width = window.innerWidth;
  const H = CVS.height = window.innerHeight;

  ACTX = new AudioContext();
  ANALYSER = ACTX.createAnalyser();

  ANALYSER.fftSize = 2048;  
  
  try{
    process();
  } catch(e) {
    console.log(e)
    window.top.location.reload();
  }



  function process() {
    SOURCE = ACTX.createMediaElementSource(player);
    

    compressor = ACTX.createDynamicsCompressor();
    compressor.threshold.setValueAtTime(-50, ACTX.currentTime);
    compressor.knee.setValueAtTime(40, ACTX.currentTime);
    compressor.ratio.setValueAtTime(12, ACTX.currentTime);
    compressor.attack.setValueAtTime(0, ACTX.currentTime);
    compressor.release.setValueAtTime(0.25, ACTX.currentTime);
    gainNode = ACTX.createGain();
    gainNode.gain = 1;
    SOURCE.connect(gainNode);
    gainNode.connect(ANALYSER);
    gainNode.connect(ACTX.destination);

    document.getElementById("compression").removeAttribute("disabled");

    console.log(SOURCE);
    const DATA = new Uint8Array(ANALYSER.frequencyBinCount);
    const LEN = DATA.length;
    const h = (H / LEN + 0.9);
    const x = W - 1;
    CTX.fillStyle = 'hsl(280, 100%, 10%)';
    CTX.fillRect(0, 0, W, H);

    loop();

    function loop(time) {
      if (requestTime) {
          fpsval = Math.round(1000/((performance.now() - requestTime)))
          if(fpsval > 0){
              fps.push( fpsval);
          }
      }
      if(fps.length > 0){
          avgfps = fps.reduce((a, b) => a + b) / fps.length;
      }
      requestTime = time;
      window.requestAnimationFrame((timeRes) => loop(timeRes));
      let imgData = CTX.getImageData(1, 0, W - 1, H);

      CTX.fillRect(0, 0, W, H);
      CTX.putImageData(imgData, 0, 0);
      ANALYSER.getByteFrequencyData(DATA);
      for (let i = 0; i < LEN; i++) {
        let rat = DATA[i] / 255 ;
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

<img id="spectrogramimage" style="width:100%;height:100%;display:none" src="/spectrogram.png?nocache=<?php echo $time;?>">

<div class="centered">
  <div style="display:inline" id="gain" >
  <label>Gain: </label>
  <span class="slidecontainer">
    <input name="gain_input" type="range" min="0" max="250" value="100" class="slider" id="gain_input">
    <span id="gain_value"></span>%
  </span>
  </div>
  —
  <div style="display:inline" id="comp" >
  <label>Compression: </label>
    <input name="compression" type="checkbox" id="compression" disabled>
  </div>
</div>

<audio style="display:none" controls="" crossorigin="anonymous" id='player' preload="none"><source id="playersrc" src="/stream"></audio>
<h1>Loading...</h1>
<canvas></canvas>

<script>
var slider = document.getElementById("gain_input");
var output = document.getElementById("gain_value");
output.innerHTML = slider.value; // Display the default slider value

// Update the current slider value (each time you drag the slider handle)
slider.oninput = function() {
  output.innerHTML = this.value;
  gainNode.gain.setValueAtTime((this.value/(100/2)), ACTX.currentTime);
  gain=Math.abs(this.value - 255);
}

var compression = document.getElementById("compression");
compression.onclick = function() {
  toggleCompression(this.checked);
}
</script>