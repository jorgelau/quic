<?php

if($_SERVER["argc"] == 1) {
	$path = ".";
}else if($_SERVER["argc"] == 2){
	$path = $_SERVER["argv"][1];
	if(!is_dir($path)) {
		echo "argument is not directory.".PHP_EOL;
		exit(1);
	}
}else {
	echo "argument error.".PHP_EOL;
	exit(1);
}

$root_path = "/home/ljz/quic";

function make_Makefile($path){
	global $root_path;
	$dir = opendir($path);
	$base_path = realpath($path);
	$makefile = $path."/Makefile";
	if(file_exists($makefile)) {
		unlink($makefile);
	}
	$pmf = fopen($makefile, "w");
	$all = "all: ";

	$include = "-I.";
	$back = "../";
	$cpath = $path;
	while(realpath($cpath) != $root_path) {
		$include = $include." -I".$back;
		$back .= "../";
		$cpath .= "/..";
	}

	while($filedir = readdir($dir)) {
		if($filedir == "." || $filedir == ".." || $filedir[0] == '.') {
			continue;
		}
		if(is_dir($base_path."/".$filedir)) {
			make_Makefile($base_path."/".$filedir);
			continue;
		}
		if(substr($filedir, strlen($filedir)-3) == ".cc") {
			$target = substr($filedir, 0, strlen($filedir)-3).".o ";
			$all = $all.$target;
			$array[] = $target.":".$filedir;
			$array[] = "\tg++ -c ".$filedir." $< ".$include;
		} else if(substr($filedir, strlen($filedir)-2) == ".c") {
			$target = substr($filedir, 0, strlen($filedir)-2).".o ";
			$all = $all.$target;
			$array[] = $target.":".$filedir;
			$array[] = "\tgcc -c ".$filedir." $< ".$include;
		}else if(substr($filedir, strlen($filedir)-4) == ".cpp") {
			$target = substr($filedir, 0, strlen($filedir)-4).".o ";
			$all = $all.$target;
			$array[] = $target.":".$filedir;
			$array[] = "\tg++ -c ".$filedir." $< ".$include;
		}
	}
	if(isset($array)) {
		fwrite($pmf, $all.PHP_EOL);
		foreach($array as $str) {
			fwrite($pmf, $str.PHP_EOL);
		}
		fwrite($pmf, "clean:".PHP_EOL."\trm *.o");
	}
	fclose($pmf);
	if(!isset($array)){
		unlink($makefile);
	}
	closedir($dir);
}


make_Makefile($path);
