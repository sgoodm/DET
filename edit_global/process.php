<?php

set_time_limit(0);

switch ($_POST['type']) {

	case "scan":

		$dir = $_POST["dir"];
		$rscan = scandir($dir);
		$scan = array_diff($rscan, array('.', '..'));
		$out = json_encode($scan);
		echo $out;
		break;

	// case "meta":
	// 	// var_dump($_POST["raster"]);
	// 	echo file_get_contents("../../uploads/globals/processed/" . $_POST["raster"] . "/meta_info.json");
	// 	break;

	case "edit":
		parse_str($_POST['data'], $contents);
		$contents["modified"] = time();
		
		file_put_contents("../../uploads/globals/processed/". $_POST["raster"] ."/meta_info.json", json_encode($contents));
		
		$country_info = fopen("../../uploads/globals/processed/". $_POST["raster"] ."/country_info.csv", "r");
		while ($cRow = fgetcsv($country_info)){
			$continent = $cRow[0];
			$country = $cRow[1];
			file_put_contents("../../resources/".$continent."/".$country."/data/rasters/".$contents["raster_type"]."/".$contents["raster_sub"]."/".$contents["raster_year"]."/meta_info.json", json_encode($contents));
		}
		echo "meta updated";
		break;
}

?>