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

}

?>