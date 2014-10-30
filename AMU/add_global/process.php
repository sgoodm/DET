<?php

set_time_limit(0);

switch ($_POST['type']) {
	//returns directory contents
	case 'crop':

		//read inputs
		parse_str($_POST['stuff'], $contents);

		//add any additional fields to $contents 
		$contents["created"] = time();
		$contents["modified"] = time();

		//create directory for new global (rasters/globals/pending/type_sub_year_name)
		$newPath = "../../uploads/globals/pending/" . $contents["raster_type"] ."__". $contents["raster_sub"] ."__". $contents["raster_year"];
		if ( !is_dir($newPath) ){
			$old_mask = umask(0);
			mkdir($newPath, 0775, true);
		}

		//move global raster into directory
		//rename("globals/raw/" . $_POST["up_file"], $newPath ."/". $_POST["up_file"]);
		rename("../../uploads/globals/raw/" . $_POST["up_file"], $newPath ."/". $_POST["up_file"]);

		//create global meta_info.json
		file_put_contents($newPath . "/meta_info.json", json_encode($contents));

		echo "done";
	
		break;
}

?>