<?php
if(file_exists('./scripts/common.php')){
	include_once "./scripts/common.php";
}else{
	include_once "./common.php";
}

$caddypwd = $config['CADDY_PWD'];
if (!isset($_SERVER['PHP_AUTH_USER'])) {
  header('WWW-Authenticate: Basic realm="My Realm"');
  header('HTTP/1.0 401 Unauthorized');
  echo '<table><tr><td>You cannot edit the settings for this installation</td></tr></table>';
  exit;
} else {
  $submittedpwd = $_SERVER['PHP_AUTH_PW'];
  $submitteduser = $_SERVER['PHP_AUTH_USER'];
  if($submittedpwd !== $caddypwd || $submitteduser !== 'birdnet'){
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo '<table><tr><td>You cannot edit the settings for this installation</td></tr></table>';
    exit;
  }
}

if(isset($_GET['submit'])) {
  if(isset($_GET["caddy_pwd"])) {
    $caddy_pwd = $_GET["caddy_pwd"];
	  saveSetting('CADDY_PWD', "\"$caddy_pwd\"",'update_caddyfile');
  }

  if(isset($_GET["ice_pwd"])) {
    $ice_pwd = $_GET["ice_pwd"];
	saveSetting('ICE_PWD', $ice_pwd);
  }

  if(isset($_GET["birdnetpi_url"])) {
    $birdnetpi_url = $_GET["birdnetpi_url"];
    // remove trailing slash to prevent conf from becoming broken
    $birdnetpi_url = rtrim($birdnetpi_url, '/');
	saveSetting('BIRDNETPI_URL', $birdnetpi_url,'update_caddyfile');
  }

  if(isset($_GET["rtsp_stream"])) {
    $rtsp_stream = str_replace("\r\n", ",", $_GET["rtsp_stream"]);
	  saveSetting('RTSP_STREAM', "\"$rtsp_stream\"", ['restart birdnet_recording', 'restart livestream']);
  }

  if (isset($_GET["rtsp_stream_to_livestream"])) {
    $rtsp_stream_selected = trim($_GET["rtsp_stream_to_livestream"]);
	saveSetting('RTSP_STREAM_TO_LIVESTREAM', "\"$rtsp_stream_selected\"", 'restart livestream');
  }
  
  if(isset($_GET["overlap"])) {
    $overlap = $_GET["overlap"];
	saveSetting('OVERLAP', $overlap);
  }

  if(isset($_GET["confidence"])) {
    $confidence = $_GET["confidence"];
	saveSetting('CONFIDENCE', $confidence);
  }

  if(isset($_GET["sensitivity"])) {
    $sensitivity = $_GET["sensitivity"];
	saveSetting('SENSITIVITY', $sensitivity);
  }

  if(isset($_GET["freqshift_hi"]) && is_numeric($_GET['freqshift_hi'])) {
    $freqshift_hi = $_GET["freqshift_hi"];
	saveSetting('FREQSHIFT_HI', $freqshift_hi);
  }

  if(isset($_GET["freqshift_lo"]) && is_numeric($_GET['freqshift_lo'])) {
    $freqshift_lo = $_GET["freqshift_lo"];
	saveSetting('FREQSHIFT_HI', $freqshift_hi);
  }

  if(isset($_GET["freqshift_pitch"]) && is_numeric($_GET['freqshift_pitch'])) {
    $freqshift_pitch = $_GET["freqshift_pitch"];
	saveSetting('FREQSHIFT_PITCH', $freqshift_pitch);
  }

  if(isset($_GET["freqshift_tool"])) {
    $freqshift_tool = $_GET["freqshift_tool"];
	saveSetting('FREQSHIFT_TOOL', $freqshift_tool);
  }

  if(isset($_GET["full_disk"])) {
    $full_disk = $_GET["full_disk"];
	saveSetting('FULL_DISK', $full_disk);
  }

  if(isset($_GET["privacy_threshold"])) {
    $privacy_threshold = $_GET["privacy_threshold"];
	saveSetting('PRIVACY_THRESHOLD', $privacy_threshold,'restart_services');
  }

  if(isset($_GET["rec_card"])) {
    $rec_card = trim($_GET["rec_card"]);
	saveSetting('REC_CARD', "\"$rec_card\"");
  }

  if(isset($_GET["channels"])) {
    $channels = $_GET["channels"];
	saveSetting('CHANNELS', $channels);
  }

  if(isset($_GET["recording_length"])) {
    $recording_length = $_GET["recording_length"];
	saveSetting('RECORDING_LENGTH', $recording_length);
  }

  if(isset($_GET["extraction_length"])) {
    $extraction_length = $_GET["extraction_length"];
	saveSetting('EXTRACTION_LENGTH', $extraction_length);
  }

  if(isset($_GET["audiofmt"])) {
    $audiofmt = $_GET["audiofmt"];
	saveSetting('AUDIOFMT', $audiofmt);
  }
  if(isset($_GET["silence_update_indicator"])) {
    $silence_update_indicator = 1;
	saveSetting('SILENCE_UPDATE_INDICATOR', $silence_update_indicator);
  } else {
	saveSetting('SILENCE_UPDATE_INDICATOR', 0);
  }

  if(isset($_GET["raw_spectrogram"])) {
    $raw_spectrogram = 1;
	saveSetting('RAW_SPECTROGRAM', $raw_spectrogram);
  } else {
	saveSetting('RAW_SPECTROGRAM', 0);
  }

  if(isset($_GET["custom_image"])) {
    $custom_image = $_GET["custom_image"];

    saveSetting('CUSTOM_IMAGE', $custom_image);
  }

  if(isset($_GET["custom_image_label"])) {
    $custom_image_label = $_GET["custom_image_label"];

	saveSetting('CUSTOM_IMAGE_TITLE', "\"$custom_image_label\"");
  }

	if (isset($_GET["LogLevel_BirdnetRecordingService"])) {
		$birdnet_recording_service_log_level = trim($_GET["LogLevel_BirdnetRecordingService"]);

		saveSetting('LogLevel_BirdnetRecordingService', "\"$birdnet_recording_service_log_level\"",'restart birdnet_recording');
	}

	if (isset($_GET["LogLevel_SpectrogramViewerService"])) {
		$spectrogram_viewer_service_log_level = trim($_GET["LogLevel_SpectrogramViewerService"]);

		saveSetting('LogLevel_SpectrogramViewerService', "\"$spectrogram_viewer_service_log_level\"",'restart spectrogram_viewer');
	}

	if (isset($_GET["LogLevel_LiveAudioStreamService"])) {
		$livestream_audio_service_log_level = trim($_GET["LogLevel_LiveAudioStreamService"]);

		saveSetting('LogLevel_LiveAudioStreamService', "\"$livestream_audio_service_log_level\"",['restart livestream','restart icecast2']);
	}
}

$count = getLabelsCount();
?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
  </style>
  </head>
<div class="settings">

<?php
$newconfig = $config;
?>
      <div class="brbanner"><h1>Advanced Settings</h1></div><br>
    <form action="" method="GET">
      <table class="settingstable"><tr><td>
      <h2>Privacy Threshold</h2>
      <div class="slidecontainer">
        <input name="privacy_threshold" type="range" min="0" max="3" value="<?php print($newconfig['PRIVACY_THRESHOLD']);?>" class="slider" id="privacy_threshold">
        <p>Value: <span id="threshold_value"></span>%</p>
      </div>
      <script>
      var slider = document.getElementById("privacy_threshold");
      var output = document.getElementById("threshold_value");
      output.innerHTML = slider.value; // Display the default slider value
      
      // Update the current slider value (each time you drag the slider handle)
      slider.oninput = function() {
        output.innerHTML = this.value;
        document.getElementById("predictionCount").innerHTML = parseInt((this.value * <?php echo $count; ?>)/100);
      }
      </script>
      <p>If a Human is predicted anywhere among the top <span id="predictionCount"><?php echo $newconfig['PRIVACY_THRESHOLD'] == 0 ? "threshold % of" : intval(($newconfig['PRIVACY_THRESHOLD'] * $count)/100); ?></span> predictions, the sample will be considered of human origin and no data will be collected. Start with 1% and move up as needed.</p>
      </td></tr></table><br>
      
      <table class="settingstable"><tr><td>
      <h2>Full Disk Behaviour</h2>
      <label for="purge">
      <input name="full_disk" type="radio" id="purge" value="purge" <?php if (strcmp($newconfig['FULL_DISK'], "purge") == 0) { echo "checked"; }?>>Purge</label>
      <label for="keep">
      <input name="full_disk" type="radio" id="keep" value="keep" <?php if (strcmp($newconfig['FULL_DISK'], "keep") == 0) { echo "checked"; }?>>Keep</label>
      <p>When the disk becomes full, you can choose to 'purge' old files to make room for new ones or 'keep' your data and stop all services instead.<br>Note: you can exclude specific files from 'purge' on the Recordings page.</p>
      </td></tr></table><br>
      <table class="settingstable"><tr><td>

      <h2>Audio Settings</h2>
      <label for="rec_card">Audio Card: </label>
      <input name="rec_card" type="text" value="<?php print($newconfig['REC_CARD']);?>" required/><br>
      <p>Set Audio Card to 'default' to use PulseAudio (always recommended), or an ALSA recognized sound card device from the output of `arecord -L`. Choose the `dsnoop` device if it is available</p>
      <label for="channels">Audio Channels: </label>
      <input name="channels" type="number" min="1" max="32" step="1" value="<?php print($newconfig['CHANNELS']);?>" required/><br>
      <p>Set Channels to the number of channels supported by your sound card. 32 max.</p>
      <label for="recording_length">Recording Length: </label>
      <input name="recording_length" oninput="document.getElementsByName('extraction_length')[0].setAttribute('max', this.value);" type="number" min="3" max="60" step="1" value="<?php print($newconfig['RECORDING_LENGTH']);?>" required/><br>
      <p>Set Recording Length in seconds between 6 and 60. Multiples of 3 are recommended, as BirdNET analyzes in 3-second chunks.</p> 
      <label for="extraction_length">Extraction Length: </label>
      <input name="extraction_length" oninput="this.setAttribute('max', document.getElementsByName('recording_length')[0].value);" type="number" min="3" value="<?php print($newconfig['EXTRACTION_LENGTH']);?>" /><br>
      <p>Set Extraction Length to something less than your Recording Length. Min=3 Max=Recording Length</p>
      <label for="audiofmt">Extractions Audio Format</label>
      <select name="audiofmt">
      <option selected="<?php print($newconfig['AUDIOFMT']);?>"><?php print($newconfig['AUDIOFMT']);?></option>
<?php
foreach($audio_formats as $format){
  echo "<option value='$format'>$format</option>";
}
?>
      </select>
      <br><br>
      
      <label id="rtsp_stream_input_label" for="rtsp_stream">RTSP Stream: </label>
      <br>
      <input style="display: none;" name="rtsp_stream" type="url" value="">
      <input style="display: none;" id="rtsp_stream_url_placeholder" name="rtsp_stream_placeholder" type="url" size="60" value="">
        <?php
        //Print out the rtsp urls in their own input fields
		//Explode the stream into an array at the comma
		$rtsp_streams = explode(",", $newconfig['RTSP_STREAM']);
		//Print out existing streams
		foreach ($rtsp_streams as $stream_idx => $stream_url) {
            //For the first input keep the element mostly the same as the original but without styling to align it
			if ($stream_idx === 0) {
				?>
                <input id="rtsp_stream_url_0" name="rtsp_stream_0" type="url" size="60" value="<?php echo $stream_url; ?>">
                <br>
				<?php
			} else {
                //For every other input field, change the id to reflect the URL's index in the array
				?>
                <input id="rtsp_stream_url_<?php echo $stream_idx; ?>" name="rtsp_stream_<?php echo $stream_idx; ?>" type="url" size="60"
                       value="<?php echo $stream_url; ?>">
                <br>
				<?php
			}
		}
        ?>
      <div id="newrtspstream_button_container">
        <br>
        <span id="newrtspstream" onclick="addNewrtspInput();">Add</span><br>
      </div>
      <script>
                      //Keep track of how many new input fields were added
                      var number_of_new_rtsp_urls_added = 0;

                      //Function to insert new input fields
                      function addNewrtspInput() {
                          //Find the placeholder input field
                          var url_template_element = document.getElementById('rtsp_stream_url_placeholder');
                          var new_url_input_template = url_template_element.cloneNode();
                          var br_seperator = document.createElement("BR");

                          //Fix up the new element so it's visible, set the style so it's sligned correctly
                          new_url_input_template.setAttribute("id", "rtsp_stream_url_new_" + number_of_new_rtsp_urls_added);
                          new_url_input_template.setAttribute("name", "rtsp_stream_new_" + number_of_new_rtsp_urls_added);
                          new_url_input_template.removeAttribute("style");

                          //Insert the new input field before the button to add new urls
                          var newrtspstream_button = document.getElementById('newrtspstream_button_container');
                          //Insert the new input element before the newrtspstream button
                          newrtspstream_button.parentNode.insertBefore(new_url_input_template, newrtspstream_button);
                          //Add a separator before the button
                          newrtspstream_button.parentNode.insertBefore(br_seperator, newrtspstream_button);

                          //Increment the counter
                          number_of_new_rtsp_urls_added++;
                      }

                      var rtsp_stream_string = "";
                      var rtsp_stream_string_array = [];

                      //Collect all the rtsp urls that have been set, concat them into a single string and set it into the rtsp_stream input field so it gets saved
                      function collectrtspUrls() {
                          //Reset the array and string so we don't get duplicates
                          rtsp_stream_string = "";
                          rtsp_stream_string_array = [];

                          //Get the inputs by name (which is similar across
                          var existing_rtsp_stream_urls = document.querySelectorAll('[name^="rtsp_stream_"]');
                          //Loop over the result and get the values
                          for (let i = 0; i < existing_rtsp_stream_urls.length; i++) {
                              //Only collect results that re not empty and add them to the array
                              if (existing_rtsp_stream_urls[i].value !== 'undefined' && existing_rtsp_stream_urls[i].value !== "") {
                                  rtsp_stream_string_array.push(existing_rtsp_stream_urls[i].value.trim());
                              }
                          }

                          //if the array is not empty, then implode the array joining all the values by a comma
                          if (rtsp_stream_string_array.length !== 0) {
                              rtsp_stream_string = rtsp_stream_string_array.join(',');
                              //Locate the hidden rtsp_stream input field that we'll populate with the full string which will get saved to the config file
                              var rtsp_stream_input = document.querySelector('[name=rtsp_stream]');
                              rtsp_stream_input.setAttribute('value', rtsp_stream_string);
                          }
                      }
      </script>
      <p>If you place an RTSP stream URL here, BirdNET-Pi will use that as its audio source.<br>Multiple streams are allowed but may have a impact on rPi performance.<br>Analyze ffmpeg CPU/Memory usage with <b>top</b> or <b>htop</b> if necessary.<br>To remove all and use the soundcard again, just delete the RTSP entries and click Save at the bottom.</p>
      </td></tr></table><br>
      <table class="settingstable"><tr><td>
      <h2>BirdNET-Pi Password</h2>
      <p>This password will protect your "Tools" page and "Live Audio" stream.</p>
      <label for="caddy_pwd">Password: </label>
      <input style="width:40ch" name="caddy_pwd" id="caddy_pwd" type="password" pattern="[A-Za-z0-9]+" title="Password must be alphanumeric (A-Z, 0-9)" value="<?php print($newconfig['CADDY_PWD']);?>" /><span id="showpassword" onmouseover="document.getElementById('caddy_pwd').type='text';" onmouseout="document.getElementById('caddy_pwd').type='password';">show</span><br>
      </td></tr></table><br>
      <table class="settingstable"><tr><td>
      <h2>Custom URL</h2>
      <p>When you update the URL below, the web server will reload, so be sure to wait at least 30 seconds and then go to your new URL.</p>
      <label for="birdnetpi_url">BirdNET-Pi URL: </label>
      <input style="width:40ch;" name="birdnetpi_url" type="url" value="<?php print($newconfig['BIRDNETPI_URL']);?>" /><br>
      <p>The BirdNET-Pi URL is how the main page will be reached. If you want your installation to respond to an IP address, place that here, but be sure to indicate "<i>http://</i>".<br>Example for IP: <i>http://192.168.0.109</i><br>Example if you own your own domain: <i>https://virginia.birdnetpi.com</i></p>
      </td></tr></table><br>
      <table class="settingstable"><tr><td>
      <h2>Options</h2>
      <label for="silence_update_indicator">Silence Update Indicator: </label>
      <input type="checkbox" name="silence_update_indicator" <?php if($newconfig['SILENCE_UPDATE_INDICATOR'] == 1) { echo "checked"; };?> ><br>
      <p>This allows you to quiet the display of how many commits your installation is behind by relative to the Github repo. This number appears next to "Tools" when you're 50 or more commits behind.</p>

      <label for="raw_spectrogram">Minimalist Spectrograms: </label>
      <input type="checkbox" name="raw_spectrogram" <?php if($newconfig['RAW_SPECTROGRAM'] == 1) { echo "checked"; };?> ><br>
      <p>This allows you to remove the axes and labels of the spectrograms that are generated by Sox for each detection for a cleaner appearance.</p>
      </td></tr></table><br>

      <table class="settingstable"><tr><td>
      <h2>Custom Image</h2>
      <label for="custom_image">Custom Image Absolute Path: </label>
        <input name="custom_image" type="text" value="<?php print($newconfig['CUSTOM_IMAGE']);?>"/><br>

      <label for="custom_image_label">Custom Image Label: </label>
      <input name="custom_image_label" type="text" value="<?php print($newconfig['CUSTOM_IMAGE_TITLE']);?>"/><br>

      <p>These allow you to show a custom image on the Overview page of your BirdNET-Pi. This can be used to show a dynamically updating picture of your garden, for example.</p>
	  </td></tr></table><br>

      <table class="settingstable"><tr><td>
      <h2>BirdNET-Lite Settings</h2>

      <p>
        <label for="overlap">Overlap: </label>
        <input name="overlap" type="number" min="0.0" max="2.9" step="0.1" value="<?php print($newconfig['OVERLAP']);?>" required/><br>
  &nbsp;&nbsp;&nbsp;&nbsp;Min=0.0, Max=2.9
      </p>
      <p>
        <label for="confidence">Minimum Confidence: </label>
        <input name="confidence" type="number" min="0.01" max="0.99" step="0.01" value="<?php print($newconfig['CONFIDENCE']);?>" required/><br>
        &nbsp;&nbsp;&nbsp;&nbsp;Min=0.01, Max=0.99
      </p>
      <p>
        <label for="sensitivity">Sigmoid Sensitivity: </label>
        <input name="sensitivity" type="number" min="0.5" max="1.5" step="0.01" value="<?php print($newconfig['SENSITIVITY']);?>" required/><br>
  &nbsp;&nbsp;&nbsp;&nbsp;Min=0.5, Max=1.5
      </p>
      </td></tr></table><br>

      <table class="settingstable"><tr><td>
      <h2>Accessibility Settings</h2>

      <p>Birdsongs Frequency shifting configuration:<br>
        This can be useful for hearing impaired people. Note: audio files will only be pitch shifted when the "FREQ SHIFT" button is manually clicked for a detection on the "Recordings" page.<br>

        <p style="margin-left: 40px">

      <label for="freqshift_tool">Shifting tool: </label>
      <select name="freqshift_tool">
            <option selected="<?php print($newconfig['FREQSHIFT_TOOL']);?>"><?php print($newconfig['FREQSHIFT_TOOL']);?></option>
      <?php

	  $freqshift_tools = array_diff($freqshift_tools, array($newconfig['FREQSHIFT_TOOL']));
      foreach($freqshift_tools as $format){
        echo "<option value='$format'>$format</option>";
      }
      ?>
      </select>

        Choose the shifting tool here.<br>
        </p>

        <p style="margin-left: 40px">
        Using ffmpeg:
        e.g. origin=6000, target=4000, performs a shift of 2000 Hz down.<br>
        <label for="freqshift_hi">Origin [Hz]: </label>
        <input name="freqshift_hi" type="number" min="0" max="20000" step="1" value="<?php print($newconfig['FREQSHIFT_HI']);?>" required/><br>
        <label for="freqshift_lo">Target [Hz]: </label>
        <input name="freqshift_lo" type="number" min="0" max="20000" step="1" value="<?php print($newconfig['FREQSHIFT_LO']);?>" required/>
        </p>

        <p style="margin-left: 40px">
        Using sox:
        e.g. shiftPitch=-1200 performs a shift of 1 octave down. This value is in 100ths of a semitone.<br>
        <label for="freqshift_pitch">Pitch shift: </label>
        <input name="freqshift_pitch" type="number" min="-4000" max="4000" step="1" value="<?php print($newconfig['FREQSHIFT_PITCH']);?>" required/><br>
        </p>
		</td></tr></table><br>

        <table class="settingstable">
            <tr>
                <td>
                    <h2>Logging</h2>
                    <div class="callout callout-warning">
                        <b>Note:</b>
                        It is recommended that the Log Level be set to <b>Error</b> on production systems to keep output
                        manageable, by only reporting errors.
                        <br>
                        Not all components support the log level option at this time.
                    </div>
                </td>
            </tr>
            <tr>
                <td>Birdnet Recording:
                    <select id="LogLevel_BirdnetRecordingService" name="LogLevel_BirdnetRecordingService">
                        <option value="error" <?php echo $newconfig['LogLevel_BirdnetRecordingService'] == "error" || !array_key_exists('LogLevel_BirdnetRecordingService', $newconfig) ? "selected=''" : "" ?>>
                            Errors Only
                        </option>
                        <option value="warning" <?php echo $newconfig['LogLevel_BirdnetRecordingService'] == "warning" ? "selected=''" : "" ?>>
                            Warning
                        </option>
                        <option value="info" <?php echo $newconfig['LogLevel_BirdnetRecordingService'] == "info" ? "selected=''" : "" ?>>
                            Info
                        </option>
                        <option value="debug" <?php echo $newconfig['LogLevel_BirdnetRecordingService'] == "debug" ? "selected=''" : "" ?>>
                            Debug
                        </option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Live Audio Stream:
                    <select id="LogLevel_LiveAudioStreamService" name="LogLevel_LiveAudioStreamService">
                        <option value="error" <?php echo $newconfig['LogLevel_LiveAudioStreamService'] == "error" || !array_key_exists('LogLevel_LiveAudioStreamService', $newconfig) ? "selected=''" : "" ?>>
                            Errors Only
                        </option>
                        <option value="warning" <?php echo $newconfig['LogLevel_LiveAudioStreamService'] == "warning" ? "selected=''" : "" ?>>
                            Warning
                        </option>
                        <option value="info" <?php echo $newconfig['LogLevel_LiveAudioStreamService'] == "info" ? "selected=''" : "" ?>>
                            Info
                        </option>
                        <option value="debug" <?php echo $newconfig['LogLevel_LiveAudioStreamService'] == "debug" ? "selected=''" : "" ?>>
                            Debug
                        </option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Spectrogram Service:
                    <select id="LogLevel_SpectrogramViewerService" name="LogLevel_SpectrogramViewerService">
                        <option value="error" <?php echo $newconfig['LogLevel_SpectrogramViewerService'] == "error" || !array_key_exists('LogLevel_SpectrogramViewerService', $newconfig) ? "selected=''" : "" ?>>
                            Errors Only
                        </option>
                        <option value="warning" <?php echo $newconfig['LogLevel_SpectrogramViewerService'] == "warning" ? "selected=''" : "" ?>>
                            Warning
                        </option>
                        <option value="info" <?php echo $newconfig['LogLevel_SpectrogramViewerService'] == "info" ? "selected=''" : "" ?>>
                            Info
                        </option>
                        <option value="debug" <?php echo $newconfig['LogLevel_SpectrogramViewerService'] == "debug" ? "selected=''" : "" ?>>
                            Debug
                        </option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <small>
                        <b>'error'</b> - Show all errors, including ones which can be recovered from. <b>This is the default value.</b><br>
                        <b>'warning'</b> - Show all warnings and errors. Any message related to possibly incorrect or unexpected events will be shown.<br>
                        <b>'info'</b> - Show informative messages and output during processing. This is in addition to warnings and errors. This will produce more output, use this for initial debugging.<br>
                        <b>'debug'</b> - Show everything, including debugging information. Produces a lot of output.<br>
                    </small>
                </td>
            </tr>
        </table>
      <br><br>
      <input type="hidden" name="view" value="Advanced">
        <button onclick="if(<?php print($newconfig['PRIVACY_THRESHOLD']); ?> != document.getElementById('privacy_threshold').value){collectrtspUrls(); return confirm('This will take about 90 seconds.');}else{collectrtspUrls();}" type="submit" name="submit" value="advanced">
<?php
if(isset($_GET['submit'])){
  echo "Success!";
} else {
  echo "Update Settings";
}
?>
      </button>
      <br>
      </form>
      <form action="" method="GET">
        <button type="submit" name="view" value="Settings">Basic Settings</button>
      </form>
</div>
