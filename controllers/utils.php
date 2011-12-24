<?php


class Utilities {	

	// Where dir is the path submissions/class/sl/assn/user/
	public static function getLastSubmissionNumber($dir){
		$idx = 0;
		while(true){
			$idx++;
			$dest_dir = $dir . "_" . $idx;
			if(!file_exists($dest_dir)) break;
		}
		return $idx - 1;
	}
	
	public static function isEmptyDir($dir){ 
		return (($files = @scandir($dir)) && count($files) <= 2); 
	}
	
	public static function create_release($dir){
		$file = $dir."release";
		$release = fopen($file, "w");
		fclose($release);
	}

	public static function delete_release($dir){
		$file = $dir."release";
		if(is_file($file)){
			return unlink($file);
		}
	}
	
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

	if($class == "cs143"){
		return array("cpp","h", "hh", "cc", "c", "l", "y", "txt", "final");		
	}
}

function getBlacklist(){
	return array("HangmanLexicon.txt", "ShorterLexicon.txt");
}

function valid_size($dir, $filename){
	// echo $dir.$filename;
	// echo filesize($dir.$filename);
	
	if( filesize($dir.$filename) >= 100000 ){
		echo "A file was too large and hidden from this assignment view.<br/>";
	}
	
	// echo "<br/>";
	// must be smaller than 100 kb
	return filesize($dir.$filename) < 100000;
}

/*
 * We will accept files called README or files with
 * no extension that have a file name length longer 
 * than 2
 */
function special_accept($fname, $ext, $class){
	if(strtolower($fname) == "readme") return true;

	if(strlen($ext) == 0 and strlen($fname) > 2) return true;
	
	return false;
}

function isCodeFileForClass($filename, $class){
	$ext = pathinfo($filename, PATHINFO_EXTENSION);
	$ext = strtolower($ext);
	
	$fname = pathinfo($filename, PATHINFO_FILENAME);
	
	$filetypes = getFileTypesForClass($class);
	if(special_accept($fname, $ext, $class)) return true;
	
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