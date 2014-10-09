<?php

set_time_limit(0);

$app = basename(dirname(dirname(__DIR__)));
if (strpos(strtolower(PHP_OS), "win") !== false){
	$DRIVE = substr($_SERVER["DOCUMENT_ROOT"], 0, 1);
	$COM_DIR = $DRIVE . ":\/xampp\htdocs\aiddata\\".$app;
	$MAIL_DIR = "localhost/aiddata/".$app;	
	$os = "win";
} else {
	$COM_DIR = "/var/www/html/aiddata/".$app;
	$MAIL_DIR = "128.239.119.254/aiddata/".$app;
	$os = "lin";
} 

// var_dump($_POST);
// var_dump($_FILES);

foreach ($_FILES as $index => $file) {

	if($file['error'] > 0) {
		// file_put_contents("xyz.txt", "bad1");
		var_dump("error - general");
	}

	if(empty($file['name'])) {
		// file_put_contents("xyz.txt", "bad2");
		var_dump("error - empty name");
	}

	$tmp = $file['tmp_name'];

	if (is_uploaded_file($tmp)){
		$old_mask = umask(0);
		mkdir($_POST["dir"], 0775, true);
		if (!move_uploaded_file($tmp, $_POST["dir"]."/".$file['name'])){
			echo 'error !';
			// file_put_contents("xyz.txt", "bad3");
			var_dump("error - cannot move uploaded file");
		}
		$name = substr($file['name'], 0, -4);
		// file_put_contents("xyz.txt", "good1");
		var_dump("success - good upload");
	} else {
		echo 'Upload failed !';
		// file_put_contents("xyz.txt", "bad4");
		var_dump("error - upload failed");
	}

}

$leaf_vars = $COM_DIR."/resources" ." ". $_POST["p_shp"] ." ". $name ." ". $_POST["p_leaf"];
if ($os == "win"){
	// var_dump($os);
	exec($COM_DIR."\R\bin\/x64\Rscript ".$COM_DIR."\/AMU\add_country\leaflet.R $leaf_vars");
} else {
	exec("/usr/bin/Rscript ".$COM_DIR."/AMU/add_country/leaflet.R $leaf_vars");
} 

parse_str($_POST["meta"], $contents);
file_put_contents($COM_DIR."/resources".$_POST["p_shp"]."/meta_info.json", json_encode($contents));

?>