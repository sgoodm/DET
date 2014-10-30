<?php

set_time_limit(0);

switch ($_POST['type']) {
	//returns directory contents
	case 'add':

		//read inputs
		parse_str($_POST['stuff'], $contents);

		//add any additional fields to $contents 
		$contents["created"] = time();
		$contents["modified"] = time();

		//add meta
		file_put_contents($_POST["path"] . "/meta_info.json", json_encode($contents));

		// //add meta for sub type if it does not already exist
		// $f_meta = dirname($_POST["path"]) . "/meta_info.txt";
		// if (!file_exists($f_meta)){
		// 	file_put_contents($f_meta, $contents["meta_summary"]);
		// }
		
		// //add local to local_list.csv
		// $locals = fopen("../rasters/globals/local_list.csv", "a");
		// fputcsv($locals, array($_POST["continent"],$_POST["country"],$contents["raster_type"],$contents["raster_sub"],$contents["raster_year"]));
		// fclose($locals);

		echo "done";
		break;


	case 'exists':
		echo is_dir($_POST["path"]);
		break;
}

?>