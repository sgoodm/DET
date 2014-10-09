<?php

set_time_limit(0);

if (strpos(strtolower(PHP_OS), "win") !== false){
	$COM_DIR = substr($_SERVER["DOCUMENT_ROOT"], 0, 1) . ":\/xampp\htdocs\aiddata\DET";
	$DRIVE = substr($_SERVER["DOCUMENT_ROOT"], 0, 1);
	$os = "win";
} else {
	$COM_DIR = "/var/www/html/aiddata/DET";
	$os = "lin";
}

function pDelete($path){
    if (is_dir($path) === true)    {
        $files = array_diff(scandir($path), array('.', '..'));

        foreach ($files as $file)        {
            pDelete(realpath($path) . '/' . $file);
        }
        return rmdir($path);
    }

    else if (is_file($path) === true)    {
        return unlink($path);
    }
    return false;
}

switch ($_POST['type']) {

	case "scan":

		$dir = $_POST["dir"];
		$rscan = scandir($dir);
		$scan = array_diff($rscan, array('.', '..'));
		$out = json_encode($scan);
		echo $out;
		break;

	case "meta":
		// var_dump($_POST["global"]);
		echo file_get_contents("../../uploads/globals/pending/" . $_POST["global"] . "/meta_info.json");
		break;

	//returns directory contents
	case 'crop':

		//read inputs
		parse_str($_POST['data'], $contents);

		//add any additional fields to $contents 
		$contents["modified"] = time();

		//create directory for new global (globals/processed/type_sub_year_name)
		$newPath = "../../uploads/globals/processed/" . $contents["raster_type"] ."__". $contents["raster_sub"] ."__". $contents["raster_year"];
		if ( !is_dir($newPath) ){
			$old_mask = umask(0);
			mkdir($newPath, 0775, true);
		}

		//move global raster into directory
		//rename("globals/raw/" . $file, $newPath ."/". $file);
		$oldPath = "../../uploads/globals/pending/" . $_POST["global"];
		//$file = array_diff(scandir($oldPath, array('.', '..', 'meta_info.json')))[0];
		$file = $_POST["file"];
		rename($oldPath ."/". $file, $newPath ."/". $file);

		//create global meta_info.json
		file_put_contents($newPath . "/meta_info.json", json_encode($contents));

		//create (empty) country_info.csv
		$country_info = fopen($newPath . "/country_info.csv", "w");
		// fputcsv($country_info, array("continent", "country"));

		//add to global_list.csv
		$global_list = fopen("../../uploads/globals/global_list.csv", "a");
		// if ($raster_name == ""){
			$raster_name = $file;
		// } 
		fputcsv($global_list, array($contents["raster_type"], $contents["raster_sub"], $contents["raster_year"], $raster_name));
		fclose($global_list);

		//crop global, update country_info.csv, add meta_info.json to country directories

		//raster path
		//$p_raster = "/AMU/globals/raw";
		$p_raster = "/AMU/" . substr($newPath, 3);

		//raster file
		$f_raster = $file;

		//get continents
		$dir_continent = "../../resources";
		$rscan_1 = scandir($dir_continent);
		$scan_1 = array_diff($rscan_1, array('.', '..'));
		
		//for each continent
		foreach ($scan_1 as $key_1 => $continent) {
			if (strpos($continent, ".") == FALSE){
				//get countries
				$dir_country = $dir_continent ."/". $continent;
				$rscan_2 = scandir($dir_country);
				$scan_2 = array_diff($rscan_2, array('.', '..'));
				
				//for each country
				foreach ($scan_2 as $key_2 => $country) {
					if (strpos($continent, ".") == FALSE){

						//update country_info.csv
						fputcsv($country_info, array($continent, $country));

						//shp path
						$p_shapefile = $dir_country ."/". $country ."/shapefiles" ;
						
						//get files
						$rscan_3 = scandir($p_shapefile);
						$scan_3 = array_diff($rscan_3, array('.', '..'));

						//find file in $scan with .shp extension, for each in scan if str has .shp... 
						foreach ($scan_3 as $key => $value) {
							if ( strpos($value, ".shp") != FALSE ){
								//shp file
								$f_shapefile = substr($value,0,-4);
							}
						}
						
						//output path
						$p_output = $dir_country . "/" . $country . "/data/rasters/" . $contents["raster_type"] ."/". $contents["raster_sub"] ."/". $contents["raster_year"];

						//outputfile
						$f_output =  substr($raster_name, 0,-4) . ".tif";
						

						//build output directory if needed
						if ( !is_dir($p_output) ){
							$old_mask = umask(0);
							mkdir($p_output,0775,true);
						}

						//add meta_info.json to ..country/../type/sub/year
						file_put_contents($p_output."/meta_info.json", json_encode($contents));

						//add meta for sub type if it does not already exist
						$f_meta = $dir_country . "/" . $country . "/data/rasters/" . $contents["raster_type"] ."/". $contents["raster_sub"] . "/meta_info.txt";
						if (!file_exists($f_meta)){
							file_put_contents($f_meta, $contents["meta_summary"]);
						}
						
						//create variable for R and run script
						$r_vars = $p_raster ." ". $f_raster ." ". substr($p_shapefile,5) ." ". $f_shapefile ." ". substr($p_output,5) ." ". $f_output ." ". $COM_DIR;
						if ($os == "lin"){
							exec("/usr/bin/Rscript /var/www/html/aiddata/DET/AMU/approve_global/rasterCrop.R $r_vars");
						} else if ($os == "win"){
							$rx = exec($DRIVE.":\/xampp\htdocs\aiddata\DET\R\bin\Rscript ".$DRIVE.":\/xampp\htdocs\aiddata\DET\/AMU\approve_global\/rasterCrop.R $r_vars");
						}
						
					}
				}
			}
		}
		fclose($country_info);
		pDelete($oldPath);
		echo "global approve: done";
	
		break;

	case 'reject':

		//create directory for new global (globals/processed/type_sub_year_name)
		$newPath = "../../uploads/globals/rejected/" . $_POST["global"];
		if ( !is_dir($newPath) ){
			$old_mask = umask(0);
			mkdir($newPath, 0775, true);
		}

		//move global raster into directory
		//rename("globals/raw/" . $file, $newPath ."/". $file);
		$oldPath = "../../uploads/globals/pending/" . $_POST["global"];
		//$file = array_diff(scandir($oldPath, array('.', '..', 'meta_info.json')))[0];
		$file = $_POST["file"];
		rename($oldPath ."/". $file, $newPath ."/". $file);
		rename($oldPath ."/meta_info.json", $newPath ."/meta_info.json");
		file_put_contents($newPath . "/reason.txt", $_POST["reason"]);
		pDelete($oldPath);

		echo "global reject: done";
		break;

}




?>