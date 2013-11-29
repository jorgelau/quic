<?php

$objs = "";
$dirs = "";

function output($path){
	global $objs;
	global $dirs;

	$dir = opendir($path);
	$base_path = realpath($path);

	while($filedir = readdir($dir)) {
		if($filedir == "." || $filedir == ".." || $filedir[0] == '.') {
			continue;
		}
		if(is_dir($base_path."/".$filedir)) {
			if(file_exists($base_path."/".$filedir."/Makefile")) {
				$dirs .= $base_path."/".$filedir." \\".PHP_EOL;
			}
			output($base_path."/".$filedir);
			continue;
		}
		if(substr($filedir, strlen($filedir)-2) == ".o") {
			$objs .= $base_path."/".$filedir." \\".PHP_EOL; 
		}
	}
}

output(".");

$pmf = fopen("./Makefile", "w");
fwrite($pmf, "SUBDIRS=".$dirs.PHP_EOL);
fwrite($pmf, "OBJS=".$objs.PHP_EOL);

fwrite($pmf, "all: objs quic_server".PHP_EOL);
fwrite($pmf, PHP_EOL);

fwrite($pmf, "objs:".PHP_EOL);
fwrite($pmf, "\t@for dir in $(SUBDIRS); do \\".PHP_EOL);
fwrite($pmf, "\t\t(cd \$\$dir && make); \\".PHP_EOL);
fwrite($pmf, "\tdone".PHP_EOL);
fwrite($pmf, PHP_EOL);

fwrite($pmf, "quic_server: \$(OBJS)".PHP_EOL);
fwrite($pmf, "\tg++ -o $@ $^ -lpthread -lz -lrt -lgtk-x11-2.0 -lgdk-x11-2.0 -lX11 -levent -lglib-2.0 -lcrypto -lssl".PHP_EOL);
fwrite($pmf, PHP_EOL);

fwrite($pmf, ".PHONY: clean".PHP_EOL);
fwrite($pmf, "clean:".PHP_EOL);
fwrite($pmf, "\t@for dir in $(SUBDIRS); do \\".PHP_EOL);
fwrite($pmf, "\t\t(cd \$\$dir && make clean); \\".PHP_EOL);
fwrite($pmf, "\tdone".PHP_EOL);

fclose($pmf);

