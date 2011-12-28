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
	
	/*
	 * This method returns all of the files in this submission folder for a student.
	 * We are given the path to the submission folder. We only return files
	 * that are not directories, not hidden, and not the paperless release file
	 *
	 * @param	$dirname	{string}	the name of the assignment directory
	 *
	 * @return 	an array of the files in this directory
	 * @author	Jeremy Keeshin	December 25, 2011
	 */
	public static function get_all_files($dirname){
		if(!is_dir($dirname)){
			return null;
		}
		$files = array();
		$dir = opendir($dirname);
		while($file = readdir($dir)) {
			if(!is_dir($dirname.$file) && $file[0] != '.' && $file != 'release'){
				$files []= $file;
			}
		}
		return $files;
	}
	
	
	/*
	 * Determine whether or not the current submission's comments have been released to the student.
	 *
	 * @param	$dirname	{string}	the path to the submission directory
	 *
	 * @return 	{bool}	whether or no the release existed
	 * @author	Jeremy Keeshin	December 25, 2011
	 */
	public static function release_exists($dirname){
		return file_exists($dirname.'release');
	}
	
	/*
	 * Get all of the code files and related information for a given (student, assignment, submission).
	 *
	 * @param	$course		{Object}	The Course object
	 * @param	$student	{Object}	the Student object
	 * @param	$assignment	{string}	the name of the assignment
	 * @param	$dirname	{string}	the full path to the submission
	 * @param	$files		{array}		the list of all the acceptable files in the directory
	 * @param	$submission_number {string}	the number of this submission
	 *
	 * @return 	{array}, an associative array mapping from the filename to an array of all of the file contents 
	 *			and AssignmentFile objects for each valid code file.
	 * @author	Jeremy Keeshin	December 25, 2011
	 */
	public static function get_code_files($course, $student, $assignment, $dirname, $files, $submission_number){
		$paperless_assignment = PaperlessAssignment::from_course_and_assignment($course, $assignment);	
		$file_info = array();
		
		foreach($files as $file){
			if($course->code_file_is_valid($file)){
				$assn = AssignmentFile::load_file($student, $paperless_assignment, $file, $submission_number);
				
				if(is_null($assn)){
					$assn = AssignmentFile::create_file($course, $paperless_assignment, $student, $file, $submission_number);
					$assn->saveFile();					
				}					
				$file_info[$file] = array('contents' => htmlentities(file_get_contents($dirname . $file)),
										  'assn' => $assn);				
			}
		}
		return $file_info;
	}
}



/* 
	* This function returns an array of the accepted file types for a class
	* It gets this information from the class configuration file
	*/
function getFileTypesForClass($class){
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