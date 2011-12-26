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
	 * We are given the sl and assignment and submission name. We only return files
	 * that are not directories, not hidden, and not the paperless release file
	 *
	 * @param	$sl				{Object}	the SectionLeader object
	 * @param	$assignment		{string}	the string name of the assignment
	 * @param	$submission		{string}	the submission directory, which is of the form
	 *										<sunetid>_<submission#>
	 *
	 * @return 	an array of the files in this directory
	 */
	public static function get_all_files($sl, $assignment, $submission){
		$dirname = $sl->get_base_directory() . "/". $assignment . "/" . $submission . "/"; 
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
	
	
	
	// list($error, $files, $file_contents, $assignment_files, $release) = $this->getAssignmentFiles($class, $student, $assignment, $sl, $the_student, $the_sl, $this->course);
	
	public static function get_assignment_files($course, $student, $assignment, $sl, $submission){
		$dirname = $sl->get_base_directory() . "/". $assignment . "/" . $submission . "/"; 
		
		$error = null;
		if(!is_dir($dirname)){
			$error = "This was not a valid directory.";
			return array($error);
		}
		$dir = opendir($dirname);
		
		$paperless_assignment = PaperlessAssignment::from_course_and_assignment($course, $assignment);
	
		$file_info = array();
		
		$string = explode("_", $submission); // if it was student_1 just take student
		$student_suid = $string[0];
		$submission_number = $string[1];
		
		$class = $course->name;
		
		$release = False;
		while($file = readdir($dir)) {
			if($file == "release"){
				$release = True;
			}else if($course->code_file_is_valid($file)) {
				//public static function load_file($course, $student, $assignment, $file, $number){
				$assn = AssignmentFile::load_file($student, $paperless_assignment, $file, $submission_number);
				
				// If we could load it by submission number, we are done
				if(is_null($assn)){
					//It wasn't so create a new one.
					$assn = AssignmentFile::createFile($class, $assignment, $student_suid, $file, $submission_number);
					$assn->saveFile();
				}
				
				$file_info[$file] = array('contents' => htmlentities(file_get_contents($dirname . $file)),
										  'assn_file' => $assn);
				$assignment_files[] = $assn;
				$files[] = $file;
				$file_contents[] = htmlentities(file_get_contents($dirname . $file));
			}
		}
		
		//print_r($file_info);
		
		// echo "Course\n";
		// print_r($course);
		// 
		// echo "Student\n";
		// print_r($student);
		// 
		// echo "Assn\n";
		// echo $assignment;
		// 
		// echo "SL\n";
		// print_r($sl);
		// 
		// echo "Submission\n";
		// echo $submission;
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