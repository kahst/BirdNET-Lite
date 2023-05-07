<?php
ini_set('session.gc_maxlifetime', 7200);
ini_set('user_agent', 'PHP_Flickr/1.0');
ini_set('display_errors', 1);
error_reporting(E_ERROR);

//Get the user and home directory for the first user in the system (userid 1000)
$user = shell_exec("awk -F: '/1000/{print $1}' /etc/passwd");
$home = shell_exec("awk -F: '/1000/{print $6}' /etc/passwd");
$home = trim($home);

////////// SET TIMEZONE //////////
// If we can get the timezome from the systems timezone file ust that
$sys_timezone = "";
if (file_exists('/etc/timezone')) {
	$tz_data = file_get_contents('/etc/timezone');
	if ($tz_data !== false) {
		$sys_timezone = trim($tz_data);
	}
} else {
// Else get timezone from the timedatectl command
	$tz_data = shell_exec('timedatectl show');
	$tz_data_array = parse_ini_string($tz_data);
	if (is_array($tz_data_array) && array_key_exists('Timezone', $tz_data_array)) {
		$sys_timezone = $tz_data_array['Timezone'];
	}
}
//Finally if we have a valid timezone, set it as the one PHP uses
if ($sys_timezone !== "") {
	date_default_timezone_set($sys_timezone);
}

//Setup the session if we're not using the API, since the API endpoint sets up it's own session
if (!isset($api_incl)) {
	$api_incl = false;
	session_set_cookie_params(7200);
	session_start();
}

////////// PARSES THE CONFIG FILE //////////
if (file_exists('./scripts/thisrun.txt')) {
	$config = parse_ini_file('./scripts/thisrun.txt');
} elseif (file_exists('./scripts/firstrun.ini')) {
	$config = parse_ini_file('./scripts/firstrun.ini');
}

//Set a default site name if nothing iss configured
if ($config["SITE_NAME"] == "") {
	$site_name = "BirdNET-Pi";
} else {
	$site_name = $config['SITE_NAME'];
}


//Reference to database connection
$DB_CONN = null;

/**
 * Connects to the bird.db SQLite3 database
 * @return SQLite3
 */
function connect_to_birdsdb()
{
	global $DB_CONN, $api_incl;

	//Initially check to see if the DB is already connected, it will not be null if it has
	if ($DB_CONN == null) {
		try {
			$DB_CONN = new SQLite3(getFilePath('birds.db'), SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
			if ($DB_CONN == False) {
				if (!$api_incl) {
					echo "connect_to_birdsdb:: Database is busy";
					header("refresh: 0;");
				}
				birdnet_error_log("connect_to_birdsdb:: birds.db database is busy");
			}
		} catch (Exception $sql_exec) {
			birdnet_error_log("connect_to_birdsdb:: Exception occurred while trying to open birds.db - " . $sql_exec->getMessage());
		}
	}

	return $DB_CONN;
}

/**
 * Disconnects the database
 *
 * @return void
 */
function disconnect_from_birdsdb()
{
	global $DB_CONN;

	if ($DB_CONN != null) {
		return $DB_CONN->close();
	}
}

/**
 * Executes the supplied query and returns all results
 *
 * @param $query string The query to execute
 * @param $bind_params string Any values that should be bound into the query
 * @param $fetchAllRecords string Any values that should be bound into the query
 * @param $fetchMode string Controls how result data is returned, default is @->default('SQLITE3_ASSOC') Associative Array;
 * @return array
 */
function db_execute_query($query, $bind_params = [], $fetchAllRecords = false, $fetchMode = SQLITE3_ASSOC)
{
	global $DB_CONN, $api_incl;
	$success = false;
	$message = '';
	$data_to_return = null;

	//Connect to the DB
	connect_to_birdsdb();
	try {
		$stmt = $DB_CONN->prepare($query);
		//
		if (!empty($bind_params)) {
			//Loop over the bind values and add them
			foreach ($bind_params as $bind_key => $bind_value) {
				$stmt->bindValue($bind_key, $bind_value);
			}
		}

		if ($stmt == False) {
			//get caller's function name
			$caller_func_name = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];
			$error_msg = "$caller_func_name => db_execute_custom_query:: birds.db database is busy or a query error occurred";
			//
			$success = false;
			$message = $error_msg;
			$data_to_return = null;

			//Log the error message
			birdnet_error_log($error_msg);
		} else {
			$result = $stmt->execute();
			//Initial result collection
			$resultArray = $result->fetchArray($fetchMode);

			if ($fetchAllRecords) {
				$multiArray = array(); //array to store all rows
				//Loop over the results to collect them all
				while ($resultArray !== false) {
					array_push($multiArray, $resultArray); //insert all rows to $multiArray
					$resultArray = $result->fetchArray($fetchMode);
				}
				unset($resultArray); //unset temporary variable

				$data_to_return = $multiArray;
			} else {
				$data_to_return = $resultArray;
			}

			//
			$success = true;
			$message = 'Ok';
		}
	} catch (Exception $sql_exec) {
		$caller_func_name = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];
		$error_msg = "$caller_func_name => db_execute_custom_query:: Exception occurred while executing query - " . $sql_exec->getMessage();
		//
		$success = false;
		$message = $error_msg;
		$data_to_return = null;

		birdnet_error_log($error_msg);
	}

	return array('success' => $success, 'message' => $message, 'data' => $data_to_return);
}

/**
 * Returns a count of detections in the database
 * @return array
 */
function getDetectionCountAll()
{
	return db_execute_query('SELECT COUNT(*) FROM detections');
}

/**
 * Returns today's count of detections in the database
 * @return array
 */
function getDetectionCountToday()
{
	return db_execute_query('SELECT COUNT(*) FROM detections WHERE Date == DATE(\'now\', \'localtime\')');
}

/**
 * Returns a count of detections in the past hour
 * @return array
 */
function getDetectionCountLastHour()
{
	return db_execute_query('SELECT COUNT(*) FROM detections WHERE Date == Date(\'now\', \'localtime\') AND TIME >= TIME(\'now\', \'localtime\', \'-1 hour\')');
}

/**
 * Returns the most recent detection
 * @return array|string
 */
function getMostRecentDetection($limit = 1)
{
	return db_execute_query('SELECT Com_Name, Sci_Name, Date, Time, Confidence, File_Name  FROM detections ORDER BY Date DESC, Time DESC LIMIT :limit', [':limit' => $limit], true);
}

/**
 * Returns a talley for todays detected species
 * @return array
 */
function getSpeciesTalley($range = "today", $start_date = null, $end_date = null)
{
	$range = strtolower($range);

	if ($range == "today") {
		$result = db_execute_query('SELECT COUNT(DISTINCT(Com_Name)) FROM detections WHERE Date == Date(\'now\', \'localtime\')');
	} else if ($range == "custom") {
		$result = db_execute_query('SELECT COUNT(DISTINCT(Com_Name)) FROM detections WHERE Date == :start_date', [':start_date' => $start_date], false);
	} else if ($range == "range") {
		$result = db_execute_query('SELECT COUNT(DISTINCT(Com_Name)) FROM detections WHERE Date BETWEEN :start_date AND :end_date', [':start_date' => $start_date, ':end_date' => $end_date]);
	}

	return $result;
}

/**
 * Returns a species talley from ALL detections
 * @return array
 */
function getAllSpeciesTalley()
{
	return db_execute_query('SELECT COUNT(DISTINCT(Com_Name)) FROM detections');
}

/**
 * Returns the number of detections on the specified date
 *
 * @param $date string Date to count detections for
 * @return array
 */
function getDetectionCountByDate($date, $date_range = false)
{
	return db_execute_query("SELECT COUNT(*) FROM detections WHERE Date == :date", [':date' => $date], false);
}

/**
 * Returns detections and counts for the specified date and start & finish times
 *
 * @param $date string Date to count detections for
 * @param $starttime string Search for detections after this time
 * @param $endtime string And detections up to this time
 * @return array
 */
function getDetectionBreakdownByTime($date, $starttime, $endtime)
{
	return db_execute_query('SELECT DISTINCT(Com_Name), COUNT(*) FROM detections WHERE Date == :date AND Time > :start_time AND Time < :end_time AND Confidence > 0.75 GROUP By Com_Name ORDER BY COUNT(*) DESC',
		[
			':date' => $date,
			':start_time' => $starttime,
			':end_time' => $endtime
		],
		true);
}


/**
 * Returns the detection count for a specified bird
 * @param $birdName string Species Name to get stats for
 * @return array
 */
function getBirdDetectionStats($birdName)
{
	//Cleanup the bird/species name
	$birdName = str_replace("_", " ", $birdName);

	$birdDetections = db_execute_query('SELECT Date, COUNT(*) AS Detections FROM detections WHERE Com_Name = :com_name AND Date BETWEEN DATE("now", "-30 days") AND DATE("now") GROUP BY Date', [':com_name' => $birdName], true);

	// Fetch the result set as an associative array
	$data = array();
	foreach ($birdDetections as $birdDetection) {
		$data[$birdDetection['Date']] = $birdDetection['Detections'];
	}

	// Create an array of all dates in the last 14 days
	$last14Days = array();
	for ($i = 0; $i < 31; $i++) {
		$last14Days[] = date('Y-m-d', strtotime("-$i days"));
	}

	// Merge the data array with the last14Days array
	$data = array_merge(array_fill_keys($last14Days, 0), $data);

	// Sort the data by date in ascending order
	ksort($data);

	// Convert the data to an array of objects
	$data = array_map(function ($date, $count) {
		return array('date' => $date, 'count' => $count);
	}, array_keys($data), $data);

	// Return the data as JSON - overwrite the result data
	$birdDetections['data'] = json_encode($data);

	return $birdDetections;
}

/**
 * Returns detection info for the specified
 * @param $birdName string Species name to get detection info on
 * @param $date string OPTIONAL - The date on which to list species on
 * @param $sort string OPTIONAL - Whether to sort the species by confidence value (so species ranked by detection accuracy/confidence)
 * @return array|string
 */
function getSpeciesDetectionInfo($birdName, $date = null, $sort = null)
{
	//If no date is supplied, then list a unique list of detection dates in the DB, sorted descending
	if ($date == null) {
		//No date set so automatically search by date and time if no sort value is set
		if (isset($sort) && $sort == "confidence") {
			$detectionInfo = db_execute_query("SELECT * FROM detections where Com_Name == :birdname ORDER BY Confidence DESC", [':birdname' => $birdName], true);
		} else {
			$detectionInfo = db_execute_query("SELECT * FROM detections where Com_Name == :birdname ORDER BY Date DESC, Time DESC", [':birdname' => $birdName], true);
		}
	} else {
		//Date set so use that to filter the results depending on the sort value
		if (isset($sort) && $sort == "confidence") {
			$detectionInfo = db_execute_query("SELECT * FROM detections where Com_Name == :birdname AND Date == :date ORDER BY Confidence DESC", [':birdname' => $birdName, ':date' => $date], true);
		} else {
			$detectionInfo = db_execute_query("SELECT * FROM detections where Com_Name == :birdname AND Date == :date ORDER BY Time DESC", [':birdname' => $birdName, ':date' => $date], true);
		}
	}

	return $detectionInfo;
}

/**
 * Returns the detections for a specified filename
 *
 * @param $filename string The filename on which to get detections for
 * @return array|string
 */
function getDetectionsByFilename($filename)
{
	return db_execute_query("SELECT * FROM detections where File_name == :filename ORDER BY Date DESC, Time DESC", [':filename' => $filename], true);
}

/**
 * Returns a list of specie names on a specified date if supplied, else lists valid dates which can be passed again for specific list of species on that date
 *
 * @param $date string OPTIONAL - The date on which to list species on
 * @param $sort string OPTIONAL - Whether to sort the species by occurrence (so species ranked by number of detections)
 * @return array|string
 */
function getDetectionsByDate($date = null, $sort = null)
{

	//If no date is supplied, then list a unique list of species in the DB
	if ($date == null) {
		$detectionsByDateResult = db_execute_query('SELECT DISTINCT(Date) FROM detections GROUP BY Date ORDER BY Date DESC', null, true);
	} else {
		//Else a date was supplied, first check the sort order if any
		if (isset($sort) && $sort == "occurrences") {
			$detectionsByDateResult = db_execute_query("SELECT DISTINCT(Com_Name) FROM detections WHERE Date == :date GROUP BY Com_Name ORDER BY COUNT(*) DESC", [':date' => $date], true);
		} else {
			$detectionsByDateResult = db_execute_query("SELECT DISTINCT(Com_Name) FROM detections WHERE Date == :date ORDER BY Com_Name", [':date' => $date], true);
		}
	}

	return $detectionsByDateResult;
}

/**
 * Returns a list detections for a species if supplied, else lists all detected species by name
 *
 * @param $species_name string OPTIONAL - List detections for this specific species
 * @param $sort string OPTIONAL - Whether to sort the species by occurrence (so species ranked by number of detections)
 * @return array
 */
function getDetectionsBySpecies($species_name = null, $sort = null)
{
	//If no species is  is supplied, then list all species
	if (!isset($species_name)) {
		if (isset($sort) && $sort == "occurrences") {
			//Sort by occurrences
			$speciesDetections = db_execute_query('SELECT DISTINCT(Com_Name) FROM detections GROUP BY Com_Name ORDER BY COUNT(*) DESC', null, true);
		} else {
			//Don't sort by occurrences
			$speciesDetections = db_execute_query('SELECT DISTINCT(Com_Name) FROM detections ORDER BY Com_Name ASC', null, true);
		}
	} else {
		//Else a species name was supplied, first check the sort order if any
		$speciesDetections = db_execute_query("SELECT * FROM detections WHERE Com_Name == :species_name ORDER BY Com_Name", [':species_name' => $species_name], true);
		//Also get the highest confidence record for this species
		$speciesDetections_MaxConf = db_execute_query("SELECT Date, Time, Sci_Name, MAX(Confidence), File_Name FROM detections WHERE Com_Name == :species_name ORDER BY Com_Name", [':species_name' => $species_name], true);
	}

	//Rearrange data
	$newReturnData = [];
	$newReturnData['species'] = $speciesDetections;
	//Check to see if we have to get max confidence results for the species also
	if (isset($speciesDetections_MaxConf)) {
		$newReturnData['species_MaxConf'] = $speciesDetections_MaxConf;
	}

	$speciesDetections['data'] = $newReturnData;

	return $speciesDetections;
}

/**
 * Returns a list of species either ordered alphabetically (default) or ordered by the number of detections the species has
 *
 * @param $sort string OPTIONAL - Sort result alphabetically (supply null) or by number of occurrences (supply "occurrences"
 * @return array
 */
function getSpeciesBestRecordingList($sort = null)
{
	if (isset($sort) && $sort == "occurrences") {
		//Sort by occurrences
		$speciesBestRecording = db_execute_query('SELECT Date, Time, File_Name, Com_Name, COUNT(*), MAX(Confidence) FROM detections GROUP BY Com_Name ORDER BY COUNT(*) DESC', null, true);

	} else {
		//Don't sort by occurrences, sort by alphabetical
		$speciesBestRecording = db_execute_query('SELECT Date, Time, File_Name, Com_Name, COUNT(*), MAX(Confidence) FROM detections GROUP BY Com_Name ORDER BY Com_Name ASC', null, true);
	}

	return $speciesBestRecording;
}

/**
 * Returns a list of best recordings for a supplied species
 *
 * @param $species_name string Name of the species
 * @return array
 */
function getBestRecordingsForSpecies($species_name)
{
	return db_execute_query("SELECT Com_Name, Sci_Name, COUNT(*), MAX(Confidence), File_Name, Date, Time from detections WHERE Com_Name = :species_name", [':species_name' => $species_name], true);
}

/**
 * Get a list of todays detections
 *
 * @param $display_limit string Number of results to return
 * @param $search_term string OPTIONAL: Return results that match the supplied term
 * @param $hard_limit string OPTIONAL: Return a fix number of results
 * @return array
 */
function getTodaysDetections($display_limit, $search_term = null, $hard_limit = null)
{
	$bind_params = [];

	if (isset($search_term)) {
		if (strtolower(explode(" ", $search_term)[0]) == "not") {
			$not = "NOT ";
			$operator = "AND";
			$search_term = str_replace("not ", "", $search_term);
			$search_term = str_replace("NOT ", "", $search_term);
		} else {
			$not = "";
			$operator = "OR";
		}
		$searchquery = "AND (Com_name " . $not . "LIKE :search_term " .
			$operator . " Sci_name " . $not . "LIKE :search_term " .
			$operator . " Confidence " . $not . "LIKE :search_term " .
			$operator . " File_Name " . $not . "LIKE :search_term " .
			$operator . " Time " . $not . "LIKE :search_term)";

		$bind_params = [':search_term' => '%' . $search_term . '%'];
	} else {
		$searchquery = "";
	}

	if (isset($display_limit) && is_numeric($display_limit)) {
		$bind_params[':display_limit'] = (intval($display_limit) - 40);
		$result = db_execute_query('SELECT Date, Time, Com_Name, Sci_Name, Confidence, File_Name FROM detections WHERE Date == Date(\'now\', \'localtime\') ' . $searchquery . ' ORDER BY Time DESC LIMIT :display_limit,40', $bind_params, true);
	} else {
		// legacy mode
		if (isset($hard_limit) && is_numeric($hard_limit)) {
			$bind_params[':hard_limit'] = $hard_limit;
			$result = db_execute_query('SELECT Date, Time, Com_Name, Sci_Name, Confidence, File_Name FROM detections WHERE Date == Date(\'now\', \'localtime\') ' . $searchquery . ' ORDER BY Time DESC LIMIT :hard_limit', $bind_params, true);
		} else {
			$result = db_execute_query('SELECT Date, Time, Com_Name, Sci_Name, Confidence, File_Name FROM detections WHERE Date == Date(\'now\', \'localtime\') ' . $searchquery . ' ORDER BY Time DESC', $bind_params, true);
		}
	}

	return $result;
}

/**
 * Returns the species talley for last week and the week prior
 *
 * @return array[]
 */
function getWeeklyReportSpeciesTalley()
{
	$last_week_dates = getLastWeekDates();
	$startdate = $last_week_dates['start_date'];
	$enddate = $last_week_dates['end_date'];


	$totalspeciestally = getSpeciesTalley('range', date("Y-m-d", $startdate), date("Y-m-d", $enddate));
	$priortotalspeciestally = getSpeciesTalley('range', date("Y-m-d", $startdate - (7 * 86400)), date("Y-m-d", $enddate - (7 * 86400)));

	return ['totalspeciestally' => $totalspeciestally, 'priortotalspeciestally' => $priortotalspeciestally];
}

/**
 * Returns the species talley for last week and the week prior
 *
 * @return array[]
 */
function getWeeklyReportSpeciesDetectionCounts($detections_asc = false)
{
	$last_week_dates = getLastWeekDates();
	$startdate = $last_week_dates['start_date'];
	$enddate = $last_week_dates['end_date'];


	$sort_order = $detections_asc ? 'ASC' : 'DESC';

	$detections = db_execute_query('SELECT DISTINCT(Com_Name), COUNT(*) FROM detections WHERE Date BETWEEN :start_date AND :end_date GROUP By Com_Name ORDER BY COUNT(*) ' . $sort_order, [':start_date' => date("Y-m-d", $startdate), ':end_date' => date("Y-m-d", $enddate)], true);
	$totalcount = db_execute_query('SELECT DISTINCT(Com_Name), COUNT(*) FROM detections WHERE Date BETWEEN :start_date AND :end_date', [':start_date' => date("Y-m-d", $startdate), ':end_date' => date("Y-m-d", $enddate)], true);
	$priortotalcount = db_execute_query('SELECT DISTINCT(Com_Name), COUNT(*) FROM detections WHERE Date BETWEEN :start_date AND :end_date', [':start_date' => date("Y-m-d", date("Y-m-d", $startdate - (7 * 86400))), ':end_date' => date("Y-m-d", date("Y-m-d", $enddate - (7 * 86400)))], true);

	return ['detections' => $detections, 'totalcount' => $totalcount, 'priortotalcount' => $priortotalcount];
}

/**
 * Returns last weeks detection count for the specified species
 *
 * @param string $species_name Species to get last weeks detection count for
 * @param bool $this_week Whether to find detections in or outside last week
 * @return array
 */
function getWeeklyReportSpeciesDetection($species_name, $this_week = true)
{
	$last_week_dates = getLastWeekDates();
	$startdate = $last_week_dates['start_date'];
	$enddate = $last_week_dates['end_date'];

	if ($this_week) {
		$result = db_execute_query('SELECT COUNT(*) FROM detections WHERE Com_Name == :species_name AND Date BETWEEN :start_date AND :end_date', [':species_name' => $species_name, ':start_date' => date("Y-m-d", $startdate - (7 * 86400)), ':end_date' => date("Y-m-d", $enddate - (7 * 86400))], false);
	} else {
		$result = db_execute_query('SELECT COUNT(*) FROM detections WHERE Com_Name == :species_name AND Date NOT BETWEEN :start_date AND :end_date', [':species_name' => $species_name, ':start_date' => date("Y-m-d", $startdate), ':end_date' => date("Y-m-d", $enddate)], false);
	}

	return $result;
}

/**
 * Deletes a specified detection by filename
 *
 * @param $filename string The filename of the detection e.g 2023-04-25/Pacific_Koel/Pacific_Koel-76-2023-04-25-birdnet-RTSP_2-16:24:05.mp3
 * @return array
 */
function deleteDetection($filename)
{
	global $DB_CONN, $api_incl;
	$success = false;
	$message = '';
	$data_to_return = null;

	$filename_exploded = explode("/", $filename);
	$actual_filename = $filename_exploded[2];


	//If the detection was successfully deleted, remove the mp3 and png spectrogram
	$file_pointer = getDirectory('home') . "/BirdSongs/Extracted/By_Date/" . $filename;
	if (!exec("sudo rm $file_pointer && sudo rm $file_pointer.png")) {
		$message = "OK";
		$statement = db_execute_query('DELETE FROM detections WHERE File_Name = :filename LIMIT 1', [':filename' => $actual_filename]);
	} else {
		$message = "Error";
	}

	//
	$success = true;
	//Message set above
	$data_to_return = $statement;

	return array('success' => $success, 'message' => $message, 'data' => $data_to_return);
}

/**
 * Protects the specified file from deletion
 *
 * @param $mode string Set the mode to add (protect) to remove (unprotect) the specified filepath from protection
 * @param $filename_to_protect string The filename of the detection to protect e.g 2023-04-25/Pacific_Koel/Pacific_Koel-76-2023-04-25-birdnet-RTSP_2-16:24:05.mp3
 * @return array
 */
function protectDetectionFromDeletion($mode, $filename_to_protect)
{
	global $api_incl;
	$success = false;
	$message = '';
	$data_to_return = null;

	$mode = strtolower($mode);

	$scripts_dir = getDirectory('scripts');
	$disk_check_exclude_file = $scripts_dir . "/disk_check_exclude.txt";

	//Initially create the file if it doesn't exist
	if (!file_exists($disk_check_exclude_file)) {
		file_put_contents($disk_check_exclude_file, "##start\n##end\n");
	}

	if ($mode == "protect") {
		// load the data and delete the line from the array which is ##end,
		// so that all excluded files sit between the ##start and ##end tags
		$lines = file($disk_check_exclude_file);
		$last = sizeof($lines) - 1;
		unset($lines[$last]);

		//Open the file and truncate contents
		if (($myfile = fopen($disk_check_exclude_file, "w")) !== false) {
			$txt = $filename_to_protect;
			//Write all existing lines
			fwrite($myfile, implode("", $lines));
			//Write out the lines for the file were trying to exclude
			fwrite($myfile, $txt . "\n");
			fwrite($myfile, $txt . ".png\n");
			//Insert the end tag again to make it the last line
			fwrite($myfile, "##end\n");

			fclose($myfile);
			//
			$success = true;
			$message = "OK";
		} else {
			$success = false;
			$message = "Unable to open file! " . $disk_check_exclude_file;
		}
	} else if ($mode == "unprotect") {
		$lines = file($disk_check_exclude_file);
		$search = $filename_to_protect;

		$result = '';
		foreach ($lines as $line) {
			if (stripos($line, $search) === false && stripos($line, $search . ".png") === false) {
				$result .= $line;
			}
		}
		if (file_put_contents($disk_check_exclude_file, $result) !== false) {
			$success = true;
			$message = "OK";
		} else {
			$success = true;
			$message = "Failed writing contents after removing file from protection, back to file $disk_check_exclude_file";
		}
	}

	return array('success' => $success, 'message' => $message, 'data' => $data_to_return);
}

/**
 * Frequency shifts the specified audio file to aid listening for hearing impaired people
 *
 * @param $filename
 * @param $performShift
 * @return array
 */
function frequencyShiftDetectionAudio($filename, $performShift = null)
{
	global $config, $api_incl;
	$success = false;
	$message = '';
	$data_to_return = null;

	$shifted_path = getDirectory('shifted_audio') . '/';

	$pp = pathinfo($filename);
	$dir = $pp['dirname'];
	$fn = $pp['filename'];
	$ext = $pp['extension'];
	$pi = getDirectory('extracted_bydate') . '/';

	if (isset($performShift) && $performShift == true) {
		$freqshift_tool = $config['FREQSHIFT_TOOL'];

		if ($freqshift_tool == "ffmpeg") {
			$cmd = "sudo /usr/bin/nohup /usr/bin/ffmpeg -y -i \"" . $pi . $filename . "\" -af \"rubberband=pitch=" . $config['FREQSHIFT_LO'] . "/" . $config['FREQSHIFT_HI'] . "\" \"" . $shifted_path . $filename . "\"";
			shell_exec("sudo mkdir -p " . $shifted_path . $dir . " && " . $cmd);

		} else if ($freqshift_tool == "sox") {
			//linux.die.net/man/1/sox
			$soxopt = "-q";
			$soxpitch = $config['FREQSHIFT_PITCH'];
			$cmd = "sudo /usr/bin/nohup /usr/bin/sox \"" . $pi . $filename . "\" \"" . $shifted_path . $filename . "\" pitch " . $soxopt . " " . $soxpitch;
			shell_exec("sudo mkdir -p " . $shifted_path . $dir . " && " . $cmd);
		}
	} else {
		$cmd = "sudo rm -f " . $shifted_path . $filename;
		shell_exec($cmd);
	}

	//
	$success = true;
	$message = "OK";

	return array('success' => $success, 'message' => $message, 'data' => $data_to_return);
}

/**
 * Adds the supplied Flickr Image Id to the list of blacklisted images
 *
 * @param $imageID
 * @return array
 */
function blacklistFlickrImage($imageID)
{
	$success = false;
	$message = '';
	$data_to_return = null;

	$scripts_dir = getDirectory('scripts');

	//Append the Flickr Image ID the blacklist file
	if (($file_handle = fopen($scripts_dir . "/blacklisted_images.txt", 'a+')) !== false) {
		fwrite($file_handle, $imageID . "\n");
		fclose($file_handle);
		//
		$success = true;
		$message = "Ok";
	} else {
		$success = false;
		$message = "Failed";
	}

	return array('success' => $success, 'message' => $message, 'data' => $data_to_return);
}

/**
 * Finds and returns a flicker image for the supplied detection, detection data must have the following columns Com_Name, Sci_Name, Date, Time, Confidence, File_Name
 * getMostRecentDetections
 *
 * @param $detection_data
 * @param bool $getAll
 * @return string[][]
 */
function getFlickrImage($detection_data, $getAll = false)
{
	global $config;

	$success = false;
	$message = '';
	$data_to_return = null;

	$flickr_data_to_return = array(
		'Com_Name' => 'N/A',
		'Sci_Name' => 'N/A',
		'Date' => 'N/A',
		'Time' => 'N/A',
		'Confidence' => '0',
		'File_Name' => 'N/A',
		'Com_Name_clean' => 'N/A',
		'Sci_Name_clean' => 'N/A',
		'photos' => null,
		'filename_path' => 'N/A',
		'filename_formatted' => 'N/A'
	);
	$foundImage = false;

	if (isset($detection_data)) {
		$extracted_dir = getDirectory('extracted');
		$home_dir = getDirectory('home');

		//Setup out flickr image cache in the session
		if (!isset($_SESSION['images'])) {
			$_SESSION['images'] = [];
		}

		//Cleanup the Birds common and scientic name
		$comname_clean = preg_replace('/ /', '_', $detection_data['Com_Name']);
		$sciname_clean = preg_replace('/ /', '_', $detection_data['Sci_Name']);
		$comname_clean = preg_replace('/\'/', '', $comname_clean);
		$filename_path = "/By_Date/" . $detection_data['Date'] . "/" . $comname_clean . "/" . $detection_data['File_Name'];
		$filename_formatted = $detection_data['Date'] . "/" . $comname_clean . "/" . $detection_data['File_Name'];
		$args = "&license=2%2C3%2C4%2C5%2C6%2C9&orientation=square,portrait";
		$comnameprefix = "%20bird";

		if (!empty($config["FLICKR_API_KEY"])) {

			if (!empty($config["FLICKR_FILTER_EMAIL"])) {
				if (!isset($_SESSION["FLICKR_FILTER_EMAIL"])) {
					unset($_SESSION['images']);
					$_SESSION['FLICKR_FILTER_EMAIL'] = json_decode(file_get_contents("https://www.flickr.com/services/rest/?method=flickr.people.findByEmail&api_key=" . $config["FLICKR_API_KEY"] . "&find_email=" . $config["FLICKR_FILTER_EMAIL"] . "&format=json&nojsoncallback=1"), true)["user"]["nsid"];
				}
				$args = "&user_id=" . $_SESSION['FLICKR_FILTER_EMAIL'];
				$comnameprefix = "";
			} else {
				if (isset($_SESSION["FLICKR_FILTER_EMAIL"])) {
					unset($_SESSION["FLICKR_FILTER_EMAIL"]);
					unset($_SESSION['images']);
				}
			}


			// if we already searched flickr for this species before, use the previous image rather than doing an unneccesary api call
			$key = array_search($comname_clean, array_column($_SESSION['images'], 0));
			if ($key !== false && !$getAll) {
				$flickr_image_data = $_SESSION['images'][$key];

				$foundImage = true;
				//Extract data out of the image data, either in our $_SESSION var already is recently pushed (new data)
				$flickr_data_to_return['photos'][0]['image_url'] = $flickr_image_data[1];
				$flickr_data_to_return['photos'][0]['photo_title'] = $flickr_image_data[2];
				$flickr_data_to_return['photos'][0]['modal_text'] = $flickr_image_data[3];
				$flickr_data_to_return['photos'][0]['author_link'] = $flickr_image_data[4];
				$flickr_data_to_return['photos'][0]['license_url'] = $flickr_image_data[5];
			} else {
				// Get license information if we haven't already
				if (empty($licenses_urls)) {
					$licenses_url = "https://api.flickr.com/services/rest/?method=flickr.photos.licenses.getInfo&api_key=" . $config["FLICKR_API_KEY"] . "&format=json&nojsoncallback=1";
					$licenses_response = file_get_contents($licenses_url);
					$licenses_data = json_decode($licenses_response, true)["licenses"]["license"];
					foreach ($licenses_data as $license) {
						$license_id = $license["id"];
						$license_name = $license["name"];
						$license_url = $license["url"];
						$licenses_urls[$license_id] = $license_url;
					}
				}

				// only open the file once per script execution
				if (!isset($lines)) {
					$lines = file($home_dir . "/BirdNET-Pi/model/labels_flickr.txt");
				}
				// convert sci name to English name
				foreach ($lines as $line) {
					if (strpos($line, $detection_data['Sci_Name']) !== false) {
						$sci_name_as_english_name = trim(explode("_", $line)[1]);
						break;
					}
				}

				// Read the blacklisted image ids from the file into an array
				$blacklisted_ids = array_map('trim', file($home_dir . "/BirdNET-Pi/scripts/blacklisted_images.txt"));

				// Make the API call
				$flickrjson = json_decode(file_get_contents("https://www.flickr.com/services/rest/?method=flickr.photos.search&api_key=" . $config["FLICKR_API_KEY"] . "&text=" . str_replace(" ", "%20", $sci_name_as_english_name) . $comnameprefix . "&sort=relevance" . $args . "&per_page=5&media=photos&format=json&nojsoncallback=1"), true)["photos"]["photo"];

				// Find the first photo that is not blacklisted or is not the specific blacklisted id
				$photo = null;
				foreach ($flickrjson as $flickrphoto) {
					if ($flickrphoto["id"] !== "4892923285" && !in_array($flickrphoto["id"], $blacklisted_ids)) {
						$photo = $flickrphoto;

						$license_url = "https://api.flickr.com/services/rest/?method=flickr.photos.getInfo&api_key=" . $config["FLICKR_API_KEY"] . "&photo_id=" . $photo["id"] . "&format=json&nojsoncallback=1";
						$license_response = file_get_contents($license_url);
						$license_info = json_decode($license_response, true)["photo"]["license"];
						$license_url = $licenses_urls[$license_info];

						$modaltext = "https://flickr.com/photos/" . $photo["owner"] . "/" . $photo["id"];
						$authorlink = "https://flickr.com/people/" . $photo["owner"];
						$imageurl = 'https://farm' . $photo["farm"] . '.static.flickr.com/' . $photo["server"] . '/' . $photo["id"] . '_' . $photo["secret"] . '.jpg';

						if (!$getAll) {
							//Get single image then break the loop
							array_push($_SESSION['images'], array($comname_clean, $imageurl, $photo["title"], $modaltext, $authorlink, $license_url));
							$flickr_image_data = $_SESSION['images'][count($_SESSION['images']) - 1];

							//Extract data out of the image data, either in our $_SESSION var already is recently pushed (new data)
							$flickr_data_to_return['photos'][0]['image_url'] = $flickr_image_data[1];
							$flickr_data_to_return['photos'][0]['photo_title'] = $flickr_image_data[2];
							$flickr_data_to_return['photos'][0]['modal_text'] = $flickr_image_data[3];
							$flickr_data_to_return['photos'][0]['author_link'] = $flickr_image_data[4];
							$flickr_data_to_return['photos'][0]['license_url'] = $flickr_image_data[5];

							//Break out of the loop
							break;
						} else {
							//Get all images
							//Extract data out of the image data, either in our $_SESSION var already is recently pushed (new data)
							$flickr_data_to_return['photos'][(int)$flickrphoto["id"]]['image_url'] = $imageurl;
							$flickr_data_to_return['photos'][(int)$flickrphoto["id"]]['photo_title'] = $photo["title"];
							$flickr_data_to_return['photos'][(int)$flickrphoto["id"]]['modal_text'] = $modaltext;
							$flickr_data_to_return['photos'][(int)$flickrphoto["id"]]['author_link'] = $authorlink;
							$flickr_data_to_return['photos'][(int)$flickrphoto["id"]]['license_url'] = $license_url;
						}
					}
				}

				$foundImage = true;
				$success = true;
				$message = 'Flickr image data successfully downloaded';
			}

			//Fill out all other common data
			$flickr_data_to_return['Com_Name'] = $detection_data['Com_Name'];
			$flickr_data_to_return['Sci_Name'] = $detection_data['Sci_Name'];
			$flickr_data_to_return['Date'] = $detection_data['Date'];
			$flickr_data_to_return['Time'] = $detection_data['Time'];
			$flickr_data_to_return['Confidence'] = array_key_exists('Confidence', $detection_data) ? $detection_data['Confidence'] : 0;
			$flickr_data_to_return['File_Name'] = $detection_data['File_Name'];

			$flickr_data_to_return['Com_Name_clean'] = $comname_clean;
			$flickr_data_to_return['Sci_Name_clean'] = $sciname_clean;

			$flickr_data_to_return['filename_path'] = $filename_path;
			$flickr_data_to_return['filename_formatted'] = $filename_formatted;
		}
	}

	return array('success' => $success, 'message' => $message, 'image_found' => $foundImage, 'data' => $flickr_data_to_return);
}


/**
 * Returns the filename of todays species chart for the main page
 *
 * @return string
 */
function getChartString()
{
	$myDate = date('Y-m-d');
	$chart = "Combo-$myDate.png";
	return $chart;
}

/**
 * Returns the start and end dates for last week
 *
 * @return string[]
 */
function getLastWeekDates()
{
	$startdate = strtotime('last sunday') - (7 * 86400);
	$enddate = strtotime('last sunday') - (1 * 86400);

	return ['start_date' => $startdate, 'end_date' => $enddate];
}

/**
 * Directory Path Helper, returns a full directory path for a supplied directory name e.g home, processed, extracted
 *
 * @param string $dir The directory to obtain a path for
 * @return string
 */
function getDirectory($dir)
{
	global $config, $home;
	$dir = strtolower($dir);

	if ($dir == "home") {
		return $home;
	} else if ($dir == "birdnet-pi" || $dir == "birdnet_pi") {
		return getDirectory('home') . '/BirdNET-Pi';
	} else if ($dir == "recs_dir" || $dir == "recordings_dir") {
		$recs_dir_setting = $config['RECS_DIR'];
		return str_replace('$HOME', getDirectory('home'), $recs_dir_setting);
	} else if ($dir == "processed") {
		$processed_dir_setting = $config['PROCESSED'];
		return getDirectory('recs_dir') . str_replace('${RECS_DIR}', '', $processed_dir_setting);
	} else if ($dir == "extracted") {
		$extracted_dir_setting = $config['EXTRACTED'];
		return getDirectory('recs_dir') . str_replace('${RECS_DIR}', '', $extracted_dir_setting);
	} elseif ($dir == "extracted_bydate") {
		return getDirectory('extracted') . '/By_Date';
	} elseif ($dir == "shifted_audio") {
		return getDirectory('home') . '/BirdSongs/Extracted/By_Date/shifted';
	} elseif ($dir == "database") {
		// NOT USED
		return getDirectory('birdnet_pi') . '/database';
	} elseif ($dir == "config") {
		// NOT USED
		return getDirectory('birdnet_pi') . '/config';
	} elseif ($dir == "models" || $dir == "model") {
		return getDirectory('birdnet_pi') . '/model';
	} elseif ($dir == "scripts") {
		return getDirectory('birdnet_pi') . '/scripts';
	} elseif ($dir == "templates") {
		return getDirectory('birdnet_pi') . '/templates';
	} elseif ($dir == "web" || $dir == "www") {
		return getDirectory('birdnet_pi') . '/homepage';
	}

	return "";
}

/**
 * Returns the full filepath for the specified filename
 *
 * @param $filename
 * @return string
 */
function getFilePath($filename)
{
	if ($filename == "analyzing_now.txt") {
		return getDirectory('birdnet_pi') . "/analyzing_now.txt";
	} else if ($filename == "apprise.txt") {
		return getDirectory('birdnet_pi') . "/apprise.txt";
	} else if ($filename == "birdnet.conf") {
		return getDirectory('birdnet_pi') . "/birdnet.conf";
	} else if ($filename == "BirdDB.txt") {
		return getDirectory('birdnet_pi') . "/BirdDB.txt";
	} else if ($filename == "birds.db") {
		return getDirectory('scripts') . "/birds.db";
	} else if ($filename == "blacklisted_images.txt") {
		return getDirectory('scripts') . "/blacklisted_images.txt";
	} else if ($filename == "disk_check_exclude.txt") {
		return getDirectory('scripts') . "/disk_check_exclude.txt";
	} else if ($filename == "exclude_species_list.txt") {
		return getDirectory('home') . "/exclude_species_list.txt";
	} else if ($filename == "firstrun.ini") {
		return getDirectory('home') . "/firstrun.ini";
	} else if ($filename == "HUMAN.txt") {
		return getDirectory('birdnet_pi') . "/HUMAN.txt";
	} else if ($filename == "include_species_list.txt") {
		return getDirectory('birdnet_pi') . "/include_species_list.txt";
	} else if ($filename == "labels.txt") {
		return getDirectory('model') . "/labels.txt";
	} else if ($filename == "labels_flickr.txt") {
		return getDirectory('model') . "/labels_flickr.txt";
	} else if ($filename == "labels_l18n.zip") {
		return getDirectory('model') . "/labels_l18n.zip";
	} else if ($filename == "labels_lang.txt") {
		return getDirectory('model') . "/labels_lang.txt";
	} else if ($filename == "labels_nm.zip") {
		return getDirectory('model') . "/labels_nm.zip";
	} else if ($filename == "lastrun.txt") {
		return getDirectory('scripts') . "/lastrun.txt";
	} else if ($filename == "thisrun.txt") {
		return getDirectory('scripts') . "/thisrun.txt";
	}

	return "";
}

/**
 * Logging helper to accept a message and process it accordingly
 * @param $message
 * @param $level
 * @return void
 */
function birdnet_error_log($message, $level = 'error')
{
	$logfile_path = "./scripts/web-ui-error.log";

	if (!file_exists($logfile_path)) {
		touch($logfile_path);
	}

	error_log($message . "\r\n", 3, $logfile_path);
}


register_shutdown_function('disconnect_from_birdsdb');