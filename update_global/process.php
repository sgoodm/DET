<?php

set_time_limit(0);


$COM_DIR = "/var/www/html/aiddata/DET";
$MAIL_DIR = "128.239.119.254/aiddata/DET";

switch ($_POST["type"]) {

	case 'recrop':
		//in "global_folder_name"
		$global = $_POST["global"];

		//raster path =
		$p_raster = "../../uploads/globals/processed/" . $global; 
		$pr_raster = "/uploads/globals/processed/" . $global; 

		//raster file name = 
		foreach (array_diff( scandir($p_raster), array('.', '..')) as $index => $file) {
			if (strpos($file,".json") === false && strpos($file,".csv") === false){
				$f_raster = $file;
			}
		}			

		//raster type, sub, year from meta info
		$meta = json_decode(file_get_contents($p_raster . "/meta_info.json"), true);
		$type = $meta["raster_type"];
		$sub = $meta["raster_sub"];
		$year = $meta["raster_year"];
			
		//in "continent/country"
		$cc = $_POST["cc"];
		$cc_ex = explode("/", $cc);
		$continent = $cc_ex[0];
		$country = $cc_ex[1];
		//shapefile path = 
		$p_shapefile = "../../resources/".$cc."/shapefiles";
		$pr_shapefile = "/resources/".$cc."/shapefiles";
		//shapefile file name = 
		foreach (array_diff( scandir($p_shapefile), array('.', '..')) as $index => $file) {
			if (strpos($file,".shp") !== false){
				$f_shapefile = substr($file,0,-4);
			}
		}	
		//output path = 
		$p_output = "../../resources/".$cc."/data/rasters/".$type."/".$sub."/".$year;
		$pr_output = "/resources/".$cc."/data/rasters/".$type."/".$sub."/".$year;
		//output file name =
		$f_output =  substr($f_raster, 0,-4) . ".tif";
		//create output dir
		$old_mask = umask(0);
		mkdir($p_output, 0775, true);
		//crop with R
		$r_vars = $pr_raster ." ". $f_raster ." ". $pr_shapefile ." ". $f_shapefile ." ". $pr_output ." ". $f_output ." ". $COM_DIR;
		var_dump($r_vars);

		exec("/usr/bin/Rscript ".$COM_DIR."/AMU/update_global/rasterCrop.R $r_vars");

		//move meta
		copy($p_raster . "/meta_info.json", $p_output . "/meta_info.json");

		//add meta for sub type if it does not already exist
		$f_meta = "../../resources/".$cc."/data/rasters/".$type."/".$sub."/meta_info.txt";
		if (!file_exists($f_meta)){
			file_put_contents($f_meta, $meta["meta_summary"]);
		}

		//update global folder country_info.csv  
		$country_info = fopen($p_raster . "/country_info.csv", "a");
		fputcsv($country_info, array($continent,$country));
		fclose($country_info);

		echo json_encode("done");

		break;

	case "scan":

		$dir = $_POST["dir"];
		$rscan = scandir($dir);
		$scan = array_diff($rscan, array('.', '..'));
		$out = json_encode($scan);
		echo $out;
		break;


	case "check":
		$country_list = fopen("../../uploads/globals/country_list.csv", "r");
		$country_info = file_get_contents("../../uploads/globals/processed/". $_POST["global"] ."/country_info.csv");
		$country_diff = array();

		while ($cRow = fgetcsv($country_list)){
			$cc = $cRow[0] .",". $cRow[1];

			if (strpos($country_info, $cc) === false){
				$country_diff[] = $cRow[0] ."/". $cRow[1];
			}
			// file_put_contents("../../resources/".$continent."/".$country."/Data/Raster/".$contents["raster_type"]."/".$contents["raster_sub"]."/".$contents["raster_year"]."/meta_info.json", json_encode($contents));
		}
		fclose($country_list);
		echo json_encode($country_diff);
		break;
		
}

?>