<?php

var_dump($_POST);
var_dump($_FILES);

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
		mkdir($_POST["up_dir"], 0775, true);
		if (!move_uploaded_file($tmp, $_POST["up_dir"]."/".$file['name'])){
			echo 'error !';
			// file_put_contents("xyz.txt", "bad3");
			var_dump("error - cannot move uploaded file");
		}
		// file_put_contents("xyz.txt", "good1");
		var_dump("success - good upload");
	} else {
		echo 'Upload failed !';
		// file_put_contents("xyz.txt", "bad4");
		var_dump("error - upload failed");
	}

}

?>