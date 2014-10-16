<?php

set_time_limit(0);

switch ($_POST['type']) {

	case "scan":

     	$dir = dirname(dirname(__DIR__))."/uploads/locals/local_list.csv";
     	
		$csv = file_get_contents($dir);
		$rows = array_map("str_getcsv", explode("\n", $csv));
		$end = count($rows)-1;
		if ($rows[$end][0] == NULL){
			array_pop($rows);
		}
		foreach ($rows as $index => $row) {
			$item[] = $row[0] ."/". $row[1] ."/data/rasters/". $row[2] ."/". $row[3] ."/". $row[4];    
		}
		$out = json_encode($item);
		echo $out;
		break;

	// case "meta":
	// 	// var_dump($_POST["raster"]);
	// 	echo file_get_contents("../../resources/" . $_POST["raster"] . "/meta_info.json");
	// 	break;

	case "edit":
		parse_str($_POST['data'], $contents);
		$contents["modified"] = time();
		
		file_put_contents(dirname(dirname(__DIR__)) ."/resources/". $_POST["raster"] ."/meta_info.json", json_encode($contents));
		
		echo json_encode("meta updated");
		break;
}

?>