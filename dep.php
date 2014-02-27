<?php

if($_SERVER["argc"] == 2){
	$file = $_SERVER["argv"][1];
	if(!is_file($file)) {
		echo "argument is not a file.".PHP_EOL;
		exit(1);
	}
}else {
	echo "arguments error.".PHP_EOL;
	exit(1);
}

$root_path = "/home/ljz/quic";

function get_include($file){
	$pf = fopen($file, "r");
	$regex = '/#include\\s+"(.*?)"/';
	$includes = array();
	while(!feof($pf)){
		$line = fgets($pf);
		$matches = array();
		if(preg_match($regex, $line, $matches)){
			$includes[] = $matches[1];
		}
	}
	fclose($pf);
	return $includes;
}

$all = array();
function find_dep($file_name, $base_path) {
	global $root_path;
	global $all;

	$file = $base_path."/".$file_name;
	$includes = get_include($file);
	foreach($includes as $inc){
		if(in_array($inc, $all)) {
			continue;
		}
		$all[] = $inc;
		echo $inc.PHP_EOL;

		if(file_exists($base_path."/".$inc)) {
			$fname = basename($base_path."/".$inc);
			$bpath = dirname($base_path."/".$inc);
			find_dep($fname, $bpath);
		}else if(file_exists($root_path."/".$inc)){
			$fname = basename($root_path."/".$inc);
			$bpath = dirname($root_path."/".$inc);
			find_dep($fname, $bpath);
		}

		$cc = str_replace(".h", ".cc", $inc);
		if(file_exists($base_path."/".$cc)) {
			$fname = basename($base_path."/".$cc);
			$bpath = dirname($base_path."/".$cc);
			find_dep($fname, $bpath);
		}else if(file_exists($root_path."/".$cc)){
			$fname = basename($root_path."/".$cc);
			$bpath = dirname($root_path."/".$cc);
			find_dep($fname, $bpath);
		}
	}
}

$file_name = basename($file);
$base_path = dirname(realpath($file));

find_dep($file_name, $base_path);

