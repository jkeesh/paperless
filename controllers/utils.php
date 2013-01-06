<?php


class Utilities {	
	
	/*
	 * Based on: http://snippets.dzone.com/posts/show/155, but significantly modified
	 * 
	 * @param 	$directory {string} 	directory to search through
	 * @param	$recursive {boolean} 	whether or not to be recursive
	 * @param	$prefix_len {int}		length of the starting directory path so it can be chopped
	 *										off and we can print relative paths, and not recompute every time
	 */
	public static function directory_to_array($directory, $recursive, $prefix_len) {		
		$array_items = array();
		if ($handle = opendir($directory)) {
			while (false !== ($file = readdir($handle))) {
				// Ignore the '.' and '..' directory entries and also invisible files
				if ($file != "." && $file != ".." && $file[0] != ".") {
					if (is_dir($directory. "/" . $file)) {
						if($recursive) {
							$array_items = array_merge($array_items, 
								Utilities::directory_to_array($directory. "/" . $file, $recursive, $prefix_len));
						}
					}else{
						$file = $directory . "/" . $file;
						$relative = substr($file, $prefix_len + 1);
						$array_items[] = $relative;
					}
					
				}
			}
			closedir($handle);
		}
		return $array_items;
	}
	

	/*
	 * Deletes a code file from a submission.
	 * @param	$assn {string}	name of the assignment with the directory name and student and submission number
	 * 							e.g		life/jdoe_15
	 * @param	$file {string}	name of file to be deleted 
	 * 							e.g. 	life.cpp
	 * @param	$that {Object}	the information for the current request (normally $this from the AjaxHandler)
	 *							we keep this for easy reference to user information
	 * 
	 * We assume you can only delete for the current quarter
	 *
	 */
	public static function delete_code_file($assn, $file, $that){
		$user = $that->user;
		$course = $that->course;
		
		$file_to_delete = $course->get_base_directory() . '/' . $assn .'/' . $file;
		if(is_file($file_to_delete)){
			unlink($file_to_delete);
			return true;
		}
		return false;
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
	 * Determine whether the file is a valid size
	 * 
	 * @author	Jeremy Keeshin	February 5, 2012
	 */
	public static function valid_size($file){
		return filesize($file) < 100000;
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
	 *
	 * @author	Jeremy Keeshin	December 25, 2011
	 * @updated	Jeremy Keeshin	February 5, 2012	make sure files are valid size
	 */
	public static function get_code_files($course, $student, $assignment, $dirname, $files, $submission_number){
		$paperless_assignment = PaperlessAssignment::from_course_and_assignment($course, $assignment);	
		$file_info = array();
		
		foreach($files as $file){
			if($course->code_file_is_valid($file) && Utilities::valid_size($dirname . $file)){
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

function getBlacklist(){
	return array("HangmanLexicon.txt", "ShorterLexicon.txt");
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