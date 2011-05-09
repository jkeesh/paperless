<?php

// Where dir is the path submissions/class/sl/assn/user/
function getLastSubmissionNumber($dir){
	$idx = 0;
	while(true){
		$idx++;
		$dest_dir = $dir . "_" . $idx;
		if(!file_exists($dest_dir)) break;
	}
	return $idx - 1;
}

function isEmptyDir($dir){ 
	return (($files = @scandir($dir)) && count($files) <= 2); 
}

/*
	* This reads of the assignment configuration information for a class
	* as specified by a csv file in the class configuration directory.
	*/
function getAssnsForClass($class) {
	$assignments_file = getConfigFileForClass($class);
	$assn_data = fopen($assignments_file, "r");

	$arr = array();

	fgetcsv($assn_data); //read off first three lines, not assignment data
	fgetcsv($assn_data);

	while (! feof($assn_data)) {
		$info = fgetcsv($assn_data);
		if ($info == NULL) {
			continue;
		}
		$arr[$info[0]] = array("Name" => $info[1], "DueDate" => date_create($info[2]));
	}
	return $arr;
}

function getConfigFileForClass($class){
	return CLASS_CONFIG_DIR . "/" . $class . ".csv";
}

function createRelease($dir){
	$file = $dir."release";
	$release = fopen($file, "w");
	fclose($release);
}

function deleteRelease($dir){
	$file = $dir."release";
	if(is_file($file)){
		return unlink($file);
	}
}

/* 
	* This function returns an array of the accepted file types for a class
	* It gets this information from the class configuration file
	*/
function getFileTypesForClass($class){
	// $config = getConfigFileForClass($class);
	// $file = fopen($config, "r");
	// $info = fgetcsv($file);
	// return $info;

	if($class == "cs106b" || $class == "cs106x" || $class == "cs106l"){
		return array("cpp","h", "txt", "cc");
	}
	if($class == "cs106a"){
		return array("java", "txt");
	}

	if($class == "cs109l"){
		return array("r", "txt");
	}


}

function getBlacklist(){
	return array("HangmanLexicon.txt", "ShorterLexicon.txt");
}

function isCodeFileForClass($filename, $class){
	$ext = pathinfo($filename, PATHINFO_EXTENSION);
	$ext = strtolower($ext);
	$filetypes = getFileTypesForClass($class);
	return in_array($ext, $filetypes) && !in_array($filename, getBlacklist());
}

function usingIE(){
	$u_agent = $_SERVER['HTTP_USER_AGENT']; 
	$ub = False; 
	if(preg_match('/MSIE/i',$u_agent)) { 
		$ub = True; 
	} 
	return $ub; 
}

/**
	* This takes a directory of the form student_#
* and returns an array of the two values
	*/
function splitDirectory($dir){
	return explode("_", $dir);
}

function getSubmissionNumber($dir){
	$string = explode("_", $dir); // if it was student_1 just take student
	return $string[1];
}


?>