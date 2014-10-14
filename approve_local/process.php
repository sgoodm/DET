<?php

set_time_limit(0);


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
		// var_dump($_POST["local"]);
		echo file_get_contents("../../uploads/locals/pending/" . $_POST["local"] . "/meta_info.json");
		break;

	//returns directory contents
	case 'crop':

		//read inputs
		parse_str($_POST['data'], $contents);

		//add any additional fields to $contents 
		$contents["modified"] = time();


		$file = $_POST["file"];
		$local = $_POST["local"];
		$path = explode("__", $local);

		$oldPath = "../../uploads/locals/pending/" . $local;
		$newPath = array();		
		$newPath[0] = "../../uploads/locals/processed/" . $path[0] ."__". $path[1] ."__". $contents["raster_type"] ."__". $contents["raster_sub"] ."__". $contents["raster_year"];
		$sub = "../../resources/" . $path[0] ."/". $path[1] ."/data/rasters/". $contents["raster_type"] ."/". $contents["raster_sub"];
		$newPath[1] = $sub ."/". $contents["raster_year"];
		

		for ($p=0; $p<count($newPath);$p++){

			if ( !is_dir($newPath[$p]) ){
				$old_mask = umask(0);
				mkdir($newPath[$p], 0775, true);
			}
			copy($oldPath ."/". $file, $newPath[$p] ."/". $file);
			file_put_contents($newPath[$p] . "/meta_info.json", json_encode($contents));

		}


		//add local to local_list.csv
		$local_list = fopen("../../uploads/locals/local_list.csv", "a");
		fputcsv($local_list, array($path[0], $path[1], $contents["raster_type"], $contents["raster_sub"], $contents["raster_year"]));
		fclose($local_list);

		//add meta for sub type if it does not already exist
		$sub_meta = $sub ."/meta_info.txt";
		if (!file_exists($sub_meta)){
			file_put_contents($sub_meta, $contents["meta_summary"]);
		}
				
		pDelete($oldPath);
		echo "local approve: done";
	
		break;

	case 'reject':
		
		$file = $_POST["file"];
		$local = $_POST["local"];

		$oldPath = "../../uploads/locals/pending/" . $local;
		$newPath = "../../uploads/locals/rejected/" . $local;
		
		if ( !is_dir($newPath) ){
			$old_mask = umask(0);
			mkdir($newPath, 0775, true);
		}
		
		rename($oldPath ."/". $file, $newPath ."/". $file);
		rename($oldPath ."/meta_info.json", $newPath ."/meta_info.json");
		file_put_contents($newPath . "/reason.txt", $_POST["reason"]);
		
		pDelete($oldPath);
		echo "local reject: done";
		break;

}




?>