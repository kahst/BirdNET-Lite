<?php
 include_once "./scripts/common.php";

  if($kiosk == true) {
    echo "<div style='margin-top:20px' class=\"centered\"><h1><a><img class=\"topimage\" src=\"images/bnp.png\"></a></h1></div>
</div><div class=\"centered\"><h3>$site_name</h3></div><hr>";
  }

$totalcount_data = getDetectionCountAll();
if($totalcount_data['success'] == False){
  echo $totalcount_data['message'];
  header("refresh: 0;");
}
$totalcount = $totalcount_data['data'];


$todaycount_data = getDetectionCountToday();
if($todaycount_data['success'] == False){
	echo $todaycount_data['message'];
	header("refresh: 0;");
}
$todaycount = $todaycount_data['data'];


$hourcount_data = getDetectionCountLastHour();
if($hourcount_data['success'] == False){
	echo $hourcount_data['message'];
	header("refresh: 0;");
}
$hourcount = $hourcount_data['data'];


$mostrecent_data = getMostRecentDetection();
if($mostrecent_data['success'] == False){
	echo $mostrecent_data['message'];
	header("refresh: 0;");
}
$mostrecent = $mostrecent_data['data'];


$todayspeciestally_data = getSpeciesTalley();
if($todayspeciestally_data['success'] == False){
	echo $todayspeciestally_data['message'];
	header("refresh: 0;");
}
$todayspeciestally = $todayspeciestally_data['data'];


$totalspeciestally_data = getAllSpeciesTalley();
if($totalspeciestally_data['success'] == False){
	echo $totalspeciestally_data['message'];
	header("refresh: 0;");
}
$totalspeciestally = $totalspeciestally_data['data'];


if(isset($_GET['comname'])) {
 $birdName = $_GET['comname'];
 $birdName = str_replace("_", " ", $birdName);

 echo getBirdDetectionStats($birdName)['data'];

die();

}

// from https://stackoverflow.com/questions/2690504/php-producing-relative-date-time-from-timestamps
function relativeTime($ts)
{
    if(!ctype_digit($ts))
        $ts = strtotime($ts);

    $diff = time() - $ts;
    if($diff == 0)
        return 'now';
    elseif($diff > 0)
    {
        $day_diff = floor($diff / 86400);
        if($day_diff == 0)
        {
            if($diff < 60) return 'just now';
            if($diff < 120) return '1 minute ago';
            if($diff < 3600) return floor($diff / 60) . ' minutes ago';
            if($diff < 7200) return '1 hour ago';
            if($diff < 86400) return floor($diff / 3600) . ' hours ago';
        }
        if($day_diff == 1) return 'Yesterday';
        if($day_diff < 7) return $day_diff . ' days ago';
        if($day_diff < 31) return ceil($day_diff / 7) . ' weeks ago';
        if($day_diff < 60) return 'last month';
        return date('F Y', $ts);
    }
    else
    {
        $diff = abs($diff);
        $day_diff = floor($diff / 86400);
        if($day_diff == 0)
        {
            if($diff < 120) return 'in a minute';
            if($diff < 3600) return 'in ' . floor($diff / 60) . ' minutes';
            if($diff < 7200) return 'in an hour';
            if($diff < 86400) return 'in ' . floor($diff / 3600) . ' hours';
        }
        if($day_diff == 1) return 'Tomorrow';
        if($day_diff < 4) return date('l', $ts);
        if($day_diff < 7 + (7 - date('w'))) return 'next week';
        if(ceil($day_diff / 7) < 4) return 'in ' . ceil($day_diff / 7) . ' weeks';
        if(date('n', $ts) == date('n') + 1) return 'next month';
        return date('F Y', $ts);
    }
}


if(isset($_GET['ajax_detections']) && $_GET['ajax_detections'] == "true"  ) {
	$result0 = getTodaysDetections($_GET['display_limit'], $_GET['searchterm'], $_GET['hard_limit']);

  if($result0['success'] == False){
    echo $result0['message'];
    header("refresh: 0;");
	}
	$result0 = $result0['data'];
  ?> <table>
   <?php

  $iterations = 0;
  $lines=null;
  $licenses_urls = array();

foreach($result0 as $todaytable)
  {
    $iterations++;

   //Get the flickr image for this detection
   $flickr_Image = getFlickrImage($todaytable);

   if ($flickr_Image['image_found']) {
	   //Remap the data from returned data into an array that is referenced in our HTML
	   //to save have to rewrite or adjust it
	   $flickr_Image_data = $flickr_Image['data'];

	   $image = [
		   0 => $flickr_Image_data['Com_Name_clean'],
		   1 => $flickr_Image_data['photos'][0]['image_url'],
		   2 => $flickr_Image_data['photos'][0]['photo_title'],
		   3 => $flickr_Image_data['photos'][0]['modal_text'],
		   4 => $flickr_Image_data['photos'][0]['author_link'],
		   5 => $flickr_Image_data['photos'][0]['license_url']
	   ];
   }

   //Fill in the missing variables as they're not created at each loop anymore, getFlickrImage generates this data now as per what the orignal loop did
   $filename = $flickr_Image_data['filename_path'];
   $filename_formatted = $flickr_Image_data['filename_formatted'];
   $sciname = $flickr_Image_data['Sci_Name_clean'];
   $comname =$flickr_Image_data['Com_Name_clean'];

  ?>
        <?php if(isset($_GET['display_limit']) && is_numeric($_GET['display_limit'])){ ?>
          <tr class="relative" id="<?php echo $iterations; ?>">
          <td class="relative">
            <img style='cursor:pointer;right:45px' src='images/delete.svg' onclick='deleteDetection("<?php echo $filename_formatted; ?>")' class="copyimage" width=25 title='Delete Detection'>
            <a target="_blank" href="index.php?filename=<?php echo $todaytable['File_Name']; ?>"><img class="copyimage" title="Open in new tab" width=25 src="images/copy.png"></a>
        
            
          <div class="centered_image_container">
            <?php if(!empty($config["FLICKR_API_KEY"]) && strlen($image[2]) > 0) { ?>
              <img onclick='setModalText(<?php echo $iterations; ?>,"<?php echo urlencode($image[2]); ?>", "<?php echo $image[3]; ?>", "<?php echo $image[4]; ?>", "<?php echo $image[1]; ?>", "<?php echo $image[5]; ?>")' src="<?php echo $image[1]; ?>" class="img1">
            <?php } ?>

            <?php echo $todaytable['Time'];?><br> 
          <b><a class="a2" href="https://allaboutbirds.org/guide/<?php echo $comname;?>" target="top"><?php echo $todaytable['Com_Name'];?></a></b><img style="height: 1em;cursor:pointer" title="View species stats" onclick="generateMiniGraph(this, '<?php echo $comname; ?>')" width=25 src="images/chart.svg"><br>
          <a class="a2" href="https://wikipedia.org/wiki/<?php echo $sciname;?>" target="top"><i><?php echo $todaytable['Sci_Name'];?></i></a><br>
          <b>Confidence:</b> <?php echo round((float)round($todaytable['Confidence'],2) * 100 ) . '%';?><br></div><br>
          <video onplay='setLiveStreamVolume(0)' onended='setLiveStreamVolume(1)' onpause='setLiveStreamVolume(1)' controls poster="<?php echo $filename.".png";?>" preload="none" title="<?php echo $filename;?>"><source preload="none" src="<?php echo $filename;?>"></video>
          </td>
        <?php } else { //legacy mode ?>
          <tr class="relative" id="<?php echo $iterations; ?>">
          <td><?php if($_GET['kiosk'] == true) { echo relativeTime(strtotime($todaytable['Time'])); } else {echo $todaytable['Time'];}?><br></td>
          <td id="recent_detection_middle_td">
          <div>
            <div>
            <?php if(!empty($config["FLICKR_API_KEY"]) && (isset($_GET['hard_limit']) || $_GET['kiosk'] == true) && strlen($image[2]) > 0) { ?>
              <img style="float:left;height:75px;" onclick='setModalText(<?php echo $iterations; ?>,"<?php echo urlencode($image[2]); ?>", "<?php echo $image[3]; ?>", "<?php echo $image[4]; ?>", "<?php echo $image[1]; ?>", "<?php echo $image[5]; ?>")' src="<?php echo $image[1]; ?>" id="birdimage" class="img1">
            <?php } ?>
          </div>
            <div>
            <b><a class="a2" <?php if($_GET['kiosk'] == false){?>href="https://allaboutbirds.org/guide/<?php echo $comname;?>"<?php } else {echo "style='color:blue;'";} ?> target="top"><?php echo $todaytable['Com_Name'];?></a></b>
                <?php
                    //If on mobile, add in a icon to link off to the recording so the user can see more info
                    if (isset($_GET['mobile'])) {
						?>
                            <br>
                            <img style="height: 1em;cursor:pointer;float:unset;display:inline" title="View species stats" onclick="generateMiniGraph(this, '<?php echo $comname; ?>')" width=25 src="images/chart.svg">
                            <a target="_blank" href="index.php?filename=<?php echo $todaytable['File_Name']; ?>"><img style="height: 1em;cursor:pointer;float:unset;display:inline" class="copyimage-mobile" title="Open in new tab" width=16 src="images/copy.png"></a>'
						<?php
                    }else{
                        //Else just put the species stats icon
                        ?>
						    <img style="height: 1em;cursor:pointer;float:unset;display:inline" title="View species stats" onclick="generateMiniGraph(this, '<?php echo $comname; ?>')" width=25 src="images/chart.svg">
                        <?php
					}
                ?>
                <br>
            <a class="a2" <?php if($_GET['kiosk'] == false){?>href="https://wikipedia.org/wiki/<?php echo $sciname;?>"<?php } else {echo "style='color:blue;'";} ?> target="top"><i><?php echo $todaytable['Sci_Name'];?></i></a><br>
            </div>
          </div>
          </td>
          <td><b>Confidence:</b> <?php echo round((float)round($todaytable['Confidence'],2) * 100 ) . '%';?><br></td>
          <?php if(!isset($_GET['mobile'])) { ?>
              <td style="min-width:180px"><audio controls preload="none" title="<?php echo $filename;?>"><source preload="none" src="<?php echo $filename;?>"></audio></td>
          <?php } ?>
        <?php } ?>
  <?php }?>
        </tr>
      </table>

  <?php 
  if($iterations == 0) {
    echo "<h3>No Detections For Today.</h3>";
  }
  
  // don't show the button if there's no more detections to be displayed, we're at the end of the list
  if($iterations >= 40 && isset($_GET['display_limit']) && is_numeric($_GET['display_limit'])) { ?>
  <center>
  <button class="loadmore" onclick="loadDetections(<?php echo $_GET['display_limit'] + 40; ?>, this);" value="Today's Detections">Load 40 More...</button>
  </center>
  <?php }

  die();
}

if(isset($_GET['today_stats'])) {
  ?>
  <table>
      <tr>
  <th>Total</th>
  <th>Today</th>
  <th>Last Hour</th>
  <th>Unique Species Total</th>
  <th>Unique Species Today</th>
      </tr>
      <tr>
      <td><?php echo $totalcount['COUNT(*)'];?></td>
      <form action="" method="GET">
      <td><input type="hidden" name="view" value="Recordings"><?php if($kiosk == false){?><button type="submit" name="date" value="<?php echo date('Y-m-d');?>"><?php echo $todaycount['COUNT(*)'];?></button><?php } else { echo $todaycount['COUNT(*)']; }?></td>
      </form>
      <td><?php echo $hourcount['COUNT(*)'];?></td>
      <form action="" method="GET">
      <td><?php if($kiosk == false){?><button type="submit" name="view" value="Species Stats"><?php echo $totalspeciestally['COUNT(DISTINCT(Com_Name))'];?></button><?php }else { echo $totalspeciestally['COUNT(DISTINCT(Com_Name))']; }?></td>
      </form>
      <form action="" method="GET">
      <td><input type="hidden" name="view" value="Recordings"><?php if($kiosk == false){?><button type="submit" name="date" value="<?php echo date('Y-m-d');?>"><?php echo $todayspeciestally['COUNT(DISTINCT(Com_Name))'];?></button><?php } else { echo $todayspeciestally['COUNT(DISTINCT(Com_Name))']; }?></td>
      </form>
      </tr>
    </table>
<?php   
die(); 
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>BirdNET-Pi DB</title>
  <style>
</style>
</head>
<div class="viewdb">
  <dialog style="margin-top: 5px;max-height: 95vh;
  overflow-y: auto;overscroll-behavior:contain" id="attribution-dialog">
    <h1 id="modalHeading"></h1>
    <p id="modalText"></p>
    <button style="font-weight:bold;color:blue" onclick="hideDialog()">Close</button>
    <button style="font-weight:bold;color:blue" onclick="if(confirm('Are you sure you want to blacklist this image?')) { blacklistImage(); }">Blacklist this image</button>
  </dialog>
  <script src="static/dialog-polyfill.js"></script>
  <script src="static/Chart.bundle.js"></script>
  <script src="static/chartjs-plugin-trendline.min.js"></script>
  
  <script>
    function deleteDetection(filename,copylink=false) {
    if (confirm("Are you sure you want to delete this detection from the database?") == true) {
      const xhttp = new XMLHttpRequest();
      xhttp.onload = function() {
        if(this.responseText == "OK"){
          if(copylink == true) {
            window.top.close();
          } else {
            location.reload();
          }
        } else {
          alert("Database busy.")
        }
      }
      xhttp.open("GET", "play.php?deletefile="+filename, true);
      xhttp.send();
    }
  }

    var last_photo_link;
  var dialog = document.querySelector('dialog');
  dialogPolyfill.registerDialog(dialog);

  function showDialog() {
    document.getElementById('attribution-dialog').showModal();
  }

  function hideDialog() {
    document.getElementById('attribution-dialog').close();
  }

  function blacklistImage() {
    const match = last_photo_link.match(/\d+$/); // match one or more digits
    const result = match ? match[0] : null; // extract the first match or return null if no match is found
    console.log(last_photo_link)
    const xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
      if(this.responseText.length > 0) {
       location.reload();
      }
    }
    xhttp.open("GET", "overview.php?blacklistimage="+result, true);
    xhttp.send();

  }

  function setModalText(iter, title, text, authorlink, photolink, licenseurl) {
    document.getElementById('modalHeading').innerHTML = "Photo: \""+decodeURIComponent(title.replaceAll("+"," "))+"\" Attribution";
    <?php if($kiosk == false) { ?>
      document.getElementById('modalText').innerHTML = "<div><img style='border-radius:5px;max-height: calc(100vh - 15rem);display: block;margin: 0 auto;' src='"+photolink+"'></div><br><div style='white-space:nowrap'>Image link: <a target='_blank' href="+text+">"+text+"</a><br>Author link: <a target='_blank' href="+authorlink+">"+authorlink+"</a><br>License URL: <a href="+licenseurl+" target='_blank'>"+licenseurl+"</a></div>";
    <?php } else { ?>
      document.getElementById('modalText').innerHTML = "<div><img style='border-radius:5px;max-height: calc(100vh - 15rem);display: block;margin: 0 auto;' src='"+photolink+"'></div><br><div style='white-space:nowrap'>Image link: <a target='_blank'>"+text+"</a><br>Author link: <a target='_blank'>"+authorlink+"</a><br>License URL: <a target='_blank'>"+licenseurl+"</a></div>";
    <?php } ?>
    last_photo_link = text;
    showDialog();
  }
  </script>  
    <h3>Number of Detections</h3>
    <div id="todaystats"><table>
      <tr>
  <th>Total</th>
  <th>Today</th>
  <th>Last Hour</th>
  <th>Unique Species Total</th>
  <th>Unique Species Today</th>
      </tr>
      <tr>
      <td><?php echo $totalcount['COUNT(*)'];?></td>
      <form action="" method="GET">
      <td><input type="hidden" name="view" value="Recordings"><?php if($kiosk == false){?><button type="submit" name="date" value="<?php echo date('Y-m-d');?>"><?php echo $todaycount['COUNT(*)'];?></button><?php } else { echo $todaycount['COUNT(*)']; }?></td>
      </form>
      <td><?php echo $hourcount['COUNT(*)'];?></td>
      <form action="" method="GET">
      <td><?php if($kiosk == false){?><button type="submit" name="view" value="Species Stats"><?php echo $totalspeciestally['COUNT(DISTINCT(Com_Name))'];?></button><?php }else { echo $totalspeciestally['COUNT(DISTINCT(Com_Name))']; }?></td>
      </form>
      <form action="" method="GET">
      <td><input type="hidden" name="view" value="Recordings"><?php if($kiosk == false){?><button type="submit" name="date" value="<?php echo date('Y-m-d');?>"><?php echo $todayspeciestally['COUNT(DISTINCT(Com_Name))'];?></button><?php } else { echo $todayspeciestally['COUNT(DISTINCT(Com_Name))']; }?></td>
      </form>
      </tr>
    </table></div>


    <h3>Today's Detections <?php if($kiosk == false) { ?>— <input autocomplete="off" size="11" type="text" placeholder="Search..." id="searchterm" name="searchterm"><?php } ?></h3>

    <div style="padding-bottom:10px" id="detections_table"><h3>Loading...</h3></div>

    <?php if($kiosk == false) { ?>
    <button onclick="switchViews(this);" class="legacyview">Legacy view</button>
    <?php } ?>

</div>

<?php if($kiosk == true) { ?>
  <script>
    const scrollToTop = () => {
  const c = document.documentElement.scrollTop || document.body.scrollTop;
  if (c > 0) {
    window.requestAnimationFrame(scrollToTop);
    window.scrollTo(0, c - c / 8);
  }
};
</script>
<button onclick="scrollToTop();" style="background-color: #dbffeb;padding: 20px;position: fixed;bottom: 5%;right: 5%;transition:box-shadow 280ms cubic-bezier(0.4, 0, 0.2, 1);box-shadow:0px 3px 1px -2px rgb(0 0 0 / 20%), 0px 2px 2px 0px rgb(0 0 0 / 14%), 0px 1px 5px 0px rgb(0 0 0 / 12%);">Scroll To Top</button>
<?php } ?>

<script>

var timer = '';
searchterm = "";

<?php if($kiosk == false) { ?>
document.getElementById("searchterm").onkeydown = (function(e) {
  if (e.key === "Enter") {
      clearTimeout(timer);
      searchDetections(document.getElementById("searchterm").value);
      document.getElementById("searchterm").blur();
  } else {
     /*
     clearTimeout(timer);
     timer = setTimeout(function() {
        searchDetections(document.getElementById("searchterm").value);

        setTimeout(function() {
            // search auto submitted and now the user is probably scrolling, get the keyboard out of the way & prevent browser from jumping to the top when a video is played
            document.getElementById("searchterm").blur();
        }, 2000);
     }, 1000);
     */
  }
});
<?php } ?>

function switchViews(element) {
  if(searchterm == ""){
    document.getElementById("detections_table").innerHTML = "<h3>Loading <?php echo $todaycount['COUNT(*)']; ?> detections...</h3>";
  } else {
    document.getElementById("detections_table").innerHTML = "<h3>Loading...</h3>";
  }
  if(element.innerHTML == "Legacy view") {
    element.innerHTML = "Normal view";
    loadDetections(undefined);
  } else if(element.innerHTML == "Normal view") {
    element.innerHTML = "Legacy view";
    loadDetections(40);
  }
}
function searchDetections(searchvalue) {
    document.getElementById("detections_table").innerHTML = "<h3>Loading...</h3>";
    searchterm = searchvalue;
    if(document.getElementsByClassName('legacyview')[0].innerHTML == "Normal view") {
      loadDetections(undefined,undefined);  
    } else {
      loadDetections(40,undefined);
    }
}
function loadDetections(detections_limit, element=undefined) {
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
    <?php if($kiosk == false) { ?>
      document.getElementsByClassName("legacyview")[0].style.display="unset";
    <?php } ?>
    if(typeof element !== "undefined")
    {
     element.remove();
     document.getElementById("detections_table").innerHTML+= this.responseText;
    } else {
     document.getElementById("detections_table").innerHTML= this.responseText;
    }
    
  }
  if(searchterm != ""){
    xhttp.open("GET", "todays_detections.php?ajax_detections=true&display_limit="+detections_limit+"&searchterm="+searchterm, true);
  } else {
    <?php if($kiosk == true) { ?>
      xhttp.open("GET", "todays_detections.php?ajax_detections=true&display_limit="+detections_limit+"&kiosk=true", true);
    <?php } else { ?>
      xhttp.open("GET", "todays_detections.php?ajax_detections=true&display_limit="+detections_limit, true);
    <?php } ?>
  }
  xhttp.send();
}
function refreshTodayStats() {
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
    if(this.responseText.length > 0 && !this.responseText.includes("Database is busy")) {
      document.getElementById("todaystats").innerHTML = this.responseText;
    }
  }
  xhttp.open("GET", "todays_detections.php?today_stats=true", true);
  xhttp.send();
}
window.addEventListener("load", function(){
  <?php if($kiosk == true) { ?>
    document.getElementById("myTopnav").remove();
    loadDetections(undefined);
    refreshTodayStats();
    // refresh the kiosk detection list every minute
    setTimeout(function() {
        loadDetections(undefined);
        refreshTodayStats();
    }, 60000);
  <?php } else { ?>
    loadDetections(40);
  <?php } ?>
});
</script>

<style>
  .tooltip {
  background-color: white;
  border: 1px solid #ccc;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
  padding: 10px;
  transition: opacity 0.2s ease-in-out;
}
</style>

<script>
function generateMiniGraph(elem, comname) {
  // Make an AJAX call to fetch the number of detections for the bird species
  var xhr = new XMLHttpRequest();
  xhr.open('GET', '/todays_detections.php?comname=' + comname);
  xhr.onload = function() {
    if (xhr.status === 200) {
      var detections = JSON.parse(xhr.responseText);

      console.log(detections)

      // Create a div element for the chart window
      var chartWindow = document.createElement('div');
      chartWindow.className = "chartdiv"
      chartWindow.style.position = 'fixed';
      chartWindow.style.top = '0%';
      chartWindow.style.left = '50%';
      chartWindow.style.width = window.innerWidth < 700 ? '40%' : '20%';
      chartWindow.style.height = window.innerWidth < 700 ? '25%' : '16%';
      chartWindow.style.backgroundColor = '#fff';
      chartWindow.style.zIndex = '9999';
      chartWindow.style.overflow = 'auto';
      chartWindow.style.borderRadius = '5px';
      chartWindow.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.2)';

      document.body.appendChild(chartWindow);


            // Create a canvas element for the chart
      var canvas = document.createElement('canvas');
      canvas.width = chartWindow.offsetWidth;
      canvas.height = chartWindow.offsetHeight;
      chartWindow.appendChild(canvas);

      // Create a new Chart.js chart
      var ctx = canvas.getContext('2d');
      var chart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: detections.map(item => item.date),
          datasets: [{
            label: 'Detections',
            data: detections.map(item => item.count),
            backgroundColor: '#9fe29b',
            borderColor: '#77c487',
            borderWidth: 1,
            lineTension: 0.3, // Add smoothing to the line
            pointRadius: 1, // Make the data points smaller
            pointHitRadius: 10, // Increase the area around data points for mouse events

          trendlineLinear: {
            style: "rgba(55, 99, 64, 0.5)",
            lineStyle: "solid",
            width: 1.5
          }

          }]
        },
        options: {
          layout: {
            padding: {
              right: 10
            }
          },
          title: {
            display: true,
            text: 'Detections Over 30d'
          },
          legend: {
            display: false
          },
          scales: {
            xAxes: [{
              display: false,
              gridLines: {
                display: false // Hide the gridlines on the x-axis
              },
              ticks: {
                autoSkip: true,
                maxTicksLimit: 2
              }
            }],
            yAxes: [{
              gridLines: {
                display: false // Hide the gridlines on the y-axis
              },
              ticks: {
                beginAtZero: true,
                precision: 0,
                stepSize: 1
              }
            }]
          }
        }
      });

      // Position the chart window to the right of the button
      var buttonRect = elem.getBoundingClientRect();
      var chartRect = chartWindow.getBoundingClientRect();
      if (window.innerWidth < 700) {
        chartWindow.style.left = 'calc(75% - ' + (chartRect.width / 2) + 'px)';
      } else {
        chartWindow.style.left = (buttonRect.right + 10) + 'px';
      }

      // Calculate the top position of the chart to center it with the button
      var buttonCenter = buttonRect.top + (buttonRect.height / 2);
      var chartHeight = chartWindow.offsetHeight;
      var chartTop = buttonCenter - (chartHeight / 2);
      chartWindow.style.top = chartTop + 'px';

      // Add a close button to the chart window
      var closeButton = document.createElement('button');
      closeButton.id = "chartcb";
      closeButton.innerText = 'X';
      closeButton.style.position = 'absolute';
      closeButton.style.top = '5px';
      closeButton.style.right = '5px';
      closeButton.addEventListener('click', function() {
        document.body.removeChild(chartWindow);
      });
      chartWindow.appendChild(closeButton);
    }
  };
  xhr.send();
}

// Listen for the scroll event on the window object
window.addEventListener('scroll', function() {
  // Get all chart elements
  var charts = document.querySelectorAll('.chartdiv');
  
  // Loop through all chart elements and remove them
  charts.forEach(function(chart) {
    chart.parentNode.removeChild(chart);
  });
});

</script>
