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

	case "build":
		$old_mask = umask(0);
		mkdir($_POST["cache"]."/geojsons",0775,true);
		file_put_contents($_POST["cache"] . "/run_times.csv", "");
		mkdir($_POST["rasters"],0775,true);
		mkdir($_POST["shapefiles"],0775,true);
		$country_list = fopen("../../uploads/globals/country_list.csv", "a");
		fputcsv($country_list, array($_POST["continent"], $_POST["country"]) );
		fclose($country_list);
		break;

}

?>