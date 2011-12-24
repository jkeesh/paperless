<?php
require_once("models/AssignmentFile.php");
require_once("models/AssignmentComment.php");
require_once("models/Model.php");
require_once("permissions.php");
require_once("utils.php");

/*
	* Controller that handles the syntax highlighted code view
	* for a student, assignment pair and other ajax actions.
	*/
class DownloadHandler extends ToroHandler {

	/*
		* Gets the file contents, file names, and appropriate AssignmentFile model objects
		* corresponding to a given student, assignment pair.
		* TODO: Don't use a hardcoded path / we need to allow multiple base search paths
		* TODO: Handle error when a pathname is not found
		*/

	private function getAssignmentFiles($class, $student, $assignment, $sl) {

		$dirname = SUBMISSIONS_PREFIX . "/" . $class . "/" . SUBMISSIONS_DIR . "/" . $sl . "/". $assignment . "/" . $student . "/"; 
		if(!is_dir($dirname)) return null; // TODO handle error

		$dir = opendir($dirname);
		$files = array();
		$file_contents = array();
		$assignment_files = array();

		$string = explode("_", $student); // if it was student_1 just take student
		$student_suid = $string[0];
		$submission_number = $string[1];
		
		$last_dir = SUBMISSIONS_PREFIX . "/" . $class . "/" . SUBMISSIONS_DIR . "/" . $sl . "/". $assignment . "/" . $student_suid; 

		$last_submission = getLastSubmissionNumber($last_dir);
		
		$release = False;
		while($file = readdir($dir)) {
			if($file == "release"){
				$release = True;
			}else if(isCodeFileForClass($file, $class)) {
				$assn = AssignmentFile::loadFile($class, $student, $assignment, $file, $submission_number);
				// If we could load it by submission number, we are done
				if(is_null($assn)){
					$assn = AssignmentFile::loadFile($class, $student, $assignment, $file);
					// Now we try to load and see if an old file was there.

					if(is_null($assn)){
						//It wasn't so create a new one.
						$assn = AssignmentFile::createFile($class, $assignment, $student_suid, $file, $submission_number);
						$assn->saveFile();
					}else{
						//If it was, update the old one.
						if($submission_number == $last_submission){
							//echo "set submission number";
							$assn->setSubmissionNumber($submission_number);
							//print_r($assn);
						}else{
							$assn = AssignmentFile::createFile($class, $assignment, $student_suid, $file, $submission_number);
							//echo "created new file";
						}
						$assn->saveFile();					
					}

				}
				$assignment_files[] = $assn;
				$files[] = $file;
				$file_contents[] = file_get_contents($dirname . $file);
			}
		}

		return array($files, $file_contents, $assignment_files, $release);
	}

	/*
		* Displays the syntax highlighted code for a student, assignment pair
		*/
	public function get($qid, $class, $assignment, $student, $file="GRADE.txt") {	
		$suid = explode("_", $student); // if it was student_1 just take student
		$suid = $suid[0];

		// if the username is something other than the owner of these files, require
		// it to be a SL
		if($suid != USERNAME) {
			$role = Permissions::requireRole(POSITION_SECTION_LEADER, $class);
		}else{
			$role = Model::getRoleForClass(USERNAME, $class);
		}

		header("Content-Type: plain/text");
		$sl = Model::getSectionLeaderForStudent($suid, $class);

		list($files, $file_contents, $assignment_files, $release) = $this->getAssignmentFiles($class, $student, $assignment, $sl);
		
		$index = 0;
		$finals = array();
		foreach($file_contents as $file){

			$file = explode("\n", $file);
			$comments = $assignment_files[$index]->getAssignmentComments();
			foreach($comments as $comment){
				$endLine = $comment->getEndLine();
				//echo $comment->getCommentText();
				$file[$endLine] .= "\n=================================================================================================";
				$file[$endLine] .= "\n=================================================================================================\n\n";
				$file[$endLine] .= $comment->getCommentText();
				$file[$endLine] .= "\n\n=================================================================================================";
				$file[$endLine] .= "\n=================================================================================================\n";
				
			}
			//print_r($comments);
			
			//print_r($file);
			$index++;
			$finals []= $file;
		}
		
		// $rtfheader = "{\\rtf1\ansi\ansicpg1252\cocoartf1038\cocoasubrtf350\n{\\fonttbl\\f0\\fnil\\fcharset0 Monaco;}\n{\\colortbl;\\red255\\green255\\blue255;}\n\\paperw15840\\paperh12240\\margl1440\\margr1440\\vieww21440\\viewh14120\\viewkind0\\pard\\tx720\\tx1440\\tx2160\\tx2880\\tx3600\\tx4320\\tx5040\\tx5760\\tx6480\\tx7200\\tx7920\\tx8640\\ql\\qnatural\\pardirnatural\\f0\\fs24 \\cf0";
		// echo $rtfheader;
		
		$index = 0;
		foreach($finals as $file){
			echo "================================================================================================\n";
			echo $files[$index] . "\n";
			echo "\n=================================================================================================\n";
			
			foreach($file as $line){
				echo $line . "\n";
				//echo $line;// . "\\";
				//echo "\\";
				
				// echo strpos($line, "\r");
				// 				echo "\n";
				//$line = str_replace("{", "\{", $line);
				//$line = str_replace("}", "\}", $line);
				//echo str_replace("\r", "\r\ \r", $line);
				// echo "\\ \n";
			}
			$index++;

		}
		echo "}";
	}


}

?>