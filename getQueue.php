<?php

// ++ 
// ++ handles queue processing when triggered by Cron jobs
// ++ 

//associate cron job 
// * * * * * flock -n /var/www/html/aiddata/DET/www/getQueue.php /usr/bin/php5 /var/www/html/aiddata/DET/www/getQueue.php

//sets maximum execution time (prevent time out when executing R script)
set_time_limit(0);


$app = basename(dirname(__DIR__));

$os = "lin";
$COM_DIR = dirname(__DIR__);
$MAIL_DIR = $_SERVER['SERVER_ADDR'] . substr($COM_DIR, 13);

if (strpos(strtolower(PHP_OS), "win") !== false){
	$os = "win";
	$DRIVE = substr($_SERVER["DOCUMENT_ROOT"], 0, 1);
	$COM_DIR = $DRIVE . ":\/xampp\htdocs\aiddata\\".$app;
	$MAIL_DIR = "localhost/aiddata/".$app;	
} 

//load queue log and prepare contents
$file = $COM_DIR  . "/queue/pending.csv"; //*****DIRECTORY*****
$csv = file_get_contents($file);
$rows = array_map("str_getcsv", explode("\n", $csv));
$header = array_shift($rows);
$end = count($rows)-1;
if ($rows[$end][0] == NULL){
	array_pop($rows);
}
$r_queue = array();
$r_priority = array();
foreach ($rows as $row) {
	$r_queue[] = $row[0];
	$r_priority[] = $row[1];
}

//check if there is a request in queue
if (count($r_queue) > 0){

	//determine next request to handle
	$high_priority = array_keys($r_priority, 1);
	if (count($high_priority) > 0){
		$high_priority_keys = array_intersect_key($r_queue, array_flip($high_priority));
		$next_request = min($high_priority_keys);
		$priority = 1;
	} else {
		$next_request = min($r_queue);
		$priority = 0;
	}

	//get request info for selected request
	$q_file = $COM_DIR ."/queue/pending/". $next_request .".json"; //*****DIRECTORY*****
	$q_raw = file_get_contents($q_file);
	$q_data = json_decode($q_raw,true);

	//for each raster requested: build cache ID, check if cache exists and run R script if cache does not exist (and log R run times)
	$cacheList = [];
	$path_shapefile = $q_data["parent"];
	$file_shapefile = $q_data["file"];
	for ($i=0; $i<count($q_data["raster"]); $i++) {
		$path_raster = $q_data["rparent"][$i];
		$file_raster = $q_data["rfile"][$i];
		$path_cache = $q_data["continent"]."/".$q_data["country"]."/cache";
		$file_cache = $q_data["continent"]."__".$q_data["country"]."__".$q_data["level"]."__".$q_data["year"]."__".$q_data["rtype"][$i]."__".$q_data["rsub"][$i]."__".$q_data["ryear"][$i].".csv";
		$cacheList[] = $path_cache ."/". $file_cache;
		$meta_grab = json_decode(file_get_contents($COM_DIR."/resources/".$path_raster."/meta_info.json"),true);
		if (array_key_exists("meta_extract_type", $meta_grab)){
			$extract_type = $meta_grab["meta_extract_type"];
		} else {
			$extract_type = "mean";
		}
		$bounds = "FALSE";
		$lower_bound = "x";
		$upper_bound = "x";
		if (array_key_exists("meta_lower_bound",$meta_grab) && array_key_exists("meta_upper_bound", $meta_grab) && $meta_grab["meta_lower_bound"] != "" && $meta_grab["meta_upper_bound"] != ""){
			$bounds = "TRUE";
			$lower_bound = floatval($meta_grab["meta_lower_bound"]);
			$upper_bound = floatval($meta_grab["meta_upper_bound"]);
			// file_put_contents("/var/www/html/aiddata/testphpbounds.txt", $lower_bound ." ". $upper_bound);
		}

		if ( $priority == 0 && !file_exists($COM_DIR ."/resources/". $path_cache ."/". $file_cache) ){
			$r_vars = $path_shapefile ." ". $file_shapefile ." ".  $path_raster ." ".  $file_raster ." ". $path_cache . " " . $file_cache ." ". $COM_DIR ." ". $extract_type ." ". $bounds ." ". $lower_bound ." ". $upper_bound;
			$start_time = time();
			if ($os == "win"){
				exec($COM_DIR."\R\bin\Rscript ".$COM_DIR."\www\det.R $r_vars"); //*****DIRECTORY*****
			} else {
				exec("/usr/bin/Rscript ".$COM_DIR."/www/det.R $r_vars"); //*****DIRECTORY*****
			} 
	
			$end_time = time();
			$run_time = $end_time - $start_time;
			$timeHandle = fopen($COM_DIR ."/resources/". $path_cache ."/run_times.csv", "a"); //*****DIRECTORY*****
			$timeData = array($file_cache, $run_time);
			fputcsv($timeHandle, $timeData);
		}
	}

	//--------------------------------------------------

	//create directory and file for request output
	$outAvailable = $COM_DIR ."/queue/available/". $q_data["queue"] ."/". $q_data["queue"] . ".csv"; //*****DIRECTORY*****
	$outDir = dirname($outAvailable);
	if (!is_dir($outDir)){
		$old_mask = umask(0);
		mkdir($outDir,0775,true);
	}
	$outHandle = fopen($outAvailable, "w");

	//open cache files
	$handles = array();
	foreach ($cacheList as $key => $value) {
		$handles[$key] = fopen( $COM_DIR ."/resources/". $value, "r" ); //*****DIRECTORY*****
	}

	//join cache data and put in output file
	while ($fRow = fgetcsv($handles[0])){
		$outRow = $fRow;
		for ($i=1; $i<count($handles); $i++){
			$outRow[] = fgetcsv($handles[$i])[count($fRow)-1];
		}
		fputcsv($outHandle, $outRow);
	}

	//close files
	foreach ($handles as $key => $value) {
		fclose( $value );
	}
	fclose($outHandle);

	//--------------------------------------------------

	//add request to available list
	$avail_handle = fopen($COM_DIR ."/queue/available.csv", "a"); //*****DIRECTORY*****
	$time = time();
	$duration = (60*60*24*3);
	$avail_data = array($q_data["queue"], $q_data["priority"], $q_data["request"], $time, $time+$duration, $q_data["email"]);
	fputcsv($avail_handle, $avail_data);
	fclose($avail_handle);

	//move pending request file to available folder
	$move_file = $COM_DIR ."/queue/pending/". $q_data["queue"] . ".json"; //*****DIRECTORY*****
	$move_data = file_get_contents($COM_DIR ."/queue/pending/". $q_data["queue"] . ".json"); //*****DIRECTORY*****
	file_put_contents($COM_DIR ."/queue/available/". $q_data["queue"] ."/". $q_data["queue"] . ".json", $move_data); //*****DIRECTORY*****
	unlink($move_file);

	//remove request from pending list
	$pending_handle = fopen($COM_DIR . "/queue/pending.csv", "r"); //*****DIRECTORY*****
	$temp_pending_handle = fopen($COM_DIR . "/queue/pending_temp.csv", "w"); //*****DIRECTORY*****
	while ($pRow = fgetcsv($pending_handle)){
		if ($pRow[0] != $q_data["queue"]){
			fputcsv($temp_pending_handle, $pRow);
		}	
	}
	fclose($pending_handle);
	fclose($temp_pending_handle);
	$temp_pending_contents = file_get_contents($COM_DIR . "/queue/pending_temp.csv"); //*****DIRECTORY*****
	file_put_contents($COM_DIR . "/queue/pending.csv", $temp_pending_contents); //*****DIRECTORY*****
	unlink($COM_DIR . "/queue/pending_temp.csv"); //*****DIRECTORY*****

	//update request in log list with completion time
	$log_handle = fopen($COM_DIR . "/queue/log.csv", "r"); //*****DIRECTORY*****
	$temp_log_handle = fopen($COM_DIR . "/queue/log_temp.csv", "w"); //*****DIRECTORY*****
	while ($logRow = fgetcsv($log_handle)){
		if ($logRow[0] == $q_data["queue"]){
			$logRow[3] = $time;
			$logRow[4] = $time+$duration;
			fputcsv($temp_log_handle, $logRow);
		} else {
			fputcsv($temp_log_handle, $logRow);
		}
	}
	fclose($log_handle);
	fclose($temp_log_handle);
	$temp_log_contents = file_get_contents($COM_DIR . "/queue/log_temp.csv"); //*****DIRECTORY*****
	file_put_contents($COM_DIR . "/queue/log.csv", $temp_log_contents); //*****DIRECTORY*****
	unlink($COM_DIR . "/queue/log_temp.csv"); //*****DIRECTORY*****

	//--------------------------------------------------

	//create zip files (shapfile and full results)

	$zipBase = $COM_DIR ."/queue/available/". $q_data["queue"] ."/"; //*****DIRECTORY*****
	
	$dirShp = $COM_DIR .'/resources/'. $q_data["parent"]; //*****DIRECTORY*****
	$sterms = false;
	$sterms_meta = json_decode(file_get_contents($dirShp."/meta_info.json"), true);
	$sterms = $sterms_meta["terms"];

	if ($q_data["raw"] == true && $sterms == "true"){
		$zipShp = new ZipArchive();
		$zipShp->open($zipBase . $q_data["country"] .'__'. $q_data["level"] .'__'. $q_data["year"] . ".zip", ZipArchive::CREATE);

		$scanShp = array_diff(scandir($dirShp), array('.', '..'));
		foreach ($scanShp as $scan) {
			$zipShp->addFile( $dirShp ."/". $scan, $scan);
		}

		$zipShp->close();
	}


	$zipAll = new ZipArchive();
	$zipAll->open($zipBase. $q_data["queue"] . ".zip", ZipArchive::CREATE);

	//result
	$zipAll->addFile($zipBase . $q_data["queue"] . ".csv", $q_data["queue"] . ".csv");

	if ($q_data["raw"] == true){
		//shp
		if ($sterms == "true"){
			foreach ($scanShp as $scan) {
				$zipAll->addFile( $dirShp ."/". $scan, "raw_data/shapefile/".$scan);
			}
		}

		//raw
		$rterms = array();
		foreach ($q_data["raster"] as $r => $raster){
			$rterms[$r] = "false";
			$rterms_meta = json_decode(file_get_contents($COM_DIR ."/resources/". $q_data["rparent"][$r]."/meta_info.json"), true);
			$rterms[$r] = $rterms_meta["meta_license_terms"];
			
			if ($rterms[$r] == "true"){
				$zipAll->addFile($COM_DIR .'/resources/'. $raster, "raw_data/rasters/" . $q_data["rfile"][$r]); //*****DIRECTORY*****
			}
		}
	}
		
	//request
	$zipAll->addFile($zipBase . $q_data["queue"] . ".json", $q_data["queue"] . ".json");

	//generate documentation
	include 'parse.php';
	//add documentation to zip
	$zipAll->addFile($zipBase . "documentation.pdf", "documentation.pdf");

	$zipAll->close();

	//--------------------------------------------------

	//create page for user access
	$result_page = '
	<!DOCTYPE html>
	<html>

	<head>
	    <meta charset="UTF-8">
	    <title>gisResults</title> 
	</head>

	<body>

		<h2>Request Results</h2><br>

		User: ' . $q_data["email"] .'<br>
		Request #: ' . $q_data["queue"] .'<br><br>

		<a href="'. $q_data["queue"].'.zip' .'"> All Data (.zip) </a><br><br><br>

		Contents - <br><br>

		<a href="documentation.pdf"> Documentation (.pdf)</a><br>
		<a href="'. $q_data["queue"] . ".csv" .'"> Results (.csv)</a><br><br>';

	if ($q_data["raw"] == true){
		if ($sterms == "true"){
			$result_page .= '<a href="'. $q_data["country"] .'__'. $q_data["level"] .'__'. $q_data["year"] .'.zip' .'"> Shapefile - '. $q_data["country"] .' '. $q_data["level"] .' '. $q_data["year"] .' (.zip)</a><br>';
		}

		foreach ($q_data["raster"] as $r => $raster){
			if ($rterms[$r] == "true"){
				$result_page .= '<a href="'. "../../../resources/" . $raster .'"> Raster- '. $q_data["rtype"][$r]  .' '. $q_data["rsub"][$r]  .' '. $q_data["ryear"][$r]  .'</a><br>';
			}
		}
	}

	$result_page .= '
		<br><a href="'. $q_data["queue"] . ".json"  .'"> Request #'.$q_data["queue"].' Info (.json) </a><br><br><br>

		Request Submitted on: '. gmdate("M d Y H:i:s", $q_data["request"]) .' GMT<br>
		Request Processed on: '. gmdate("M d Y H:i:s", $time) .' GMT<br>
		Request Expires on: '. gmdate("M d Y H:i:s", $time+$duration) .' GMT<br>

	</body>

	</html>
	';

	file_put_contents($COM_DIR ."/queue/available/". $q_data["queue"] ."/". $q_data["queue"] . ".html", $result_page); //*****DIRECTORY*****

	//--------------------------------------------------

	//send email to user with results page
	$mail_to = $q_data["email"];
	$mail_subject = "AidData - Data Request Results #".$q_data["queue"];
	$mail_message = "Your data request has been processed and can be accessed using the link below. <br><br>";
	$mail_message .= "<a href='".$MAIL_DIR."/queue/available/".$q_data["queue"]."/".$q_data["queue"].".html'>Request #".$q_data["queue"]."</a>";
	$mail_headers = 'MIME-Version: 1.0' . "\r\n";
	$mail_headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	mail($mail_to, $mail_subject, $mail_message, $mail_headers);

}

?>