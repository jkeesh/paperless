<?php
	
	require_once('utils.php');
	
	class DragDropUploadHandler extends ToroHandler {
		
		function write_late_days_file($file_handle, $due_date) {
			
			$now = new DateTime();
			$now_timestamp = (int)($now->format("U"));
			$due_timestamp = (int)($due_date->format("U"));
			$days_late = (float)($now_timestamp - $due_timestamp) / 3600. / 24.;
			$days_late = max(0, (int)(ceil($days_late)));
			
			$data = "student_submission_time: " . $now->format("d/M/Y H:i:s") . "\n" .
			"assignment_due_time: " . $due_date->format("d/M/Y H:i:s") . "\n" .
			"calendar_days_late: " . $days_late;
			
			fwrite($file_handle, $data) . "\n";
        }
		
		public function post_xhr($class) {
			
			return json_encode($class);
			
			//return json_encode($_POST);
			
			
			// $role = Model::getRoleForClass(USERNAME, $class);			
			// 			if($role == POSITION_STUDENT){
			// 				$this->smarty->assign("student_class", $class);
			// 			}
			
			//$dirname = SUBMISSIONS_PREFIX . "/" . $class . "/" . SUBMISSIONS_DIR . "/" . $sl_id . "/tester";
			////
			$dirname = SUBMISSIONS_PREFIX ."/cs106b/submissions/jkeeshin/test";
			//$dirname = SUBMISSIONS_PREFIX . "/" . $class . "/" . SUBMISSIONS_DIR . "/" . $sl_id . "/" . $assn . "/";
			$late_days_file = $dirname . "/lateDays.txt";
			echo "<br/>".$late_days_file;
			$late_days = fopen($late_days_file, "w");
			$this->write_late_days_file($late_days, $assn_date);
			
			return;
			////
			
			$assn_dir = $_POST['assignment'];
			$assns = getAssnsForClass($class);
			
			
			$assn_name = $assns[$assn_dir]["Name"];
			$assn_date = $assns[$assn_dir]["DueDate"];
			
			
			$sl_id = Model::getSectionLeaderForStudent(USERNAME);
			$dirname = SUBMISSIONS_PREFIX . "/" . $class . "/" . SUBMISSIONS_DIR . "/" . $sl_id . "/" . $assn_dir . "/";
			
			$message = "";
			
			/* create directory, if necessary */
			$target_dir = $dirname;
			if (!file_exists($target_dir)) {
				//echo "making dir";
				mkdir($target_dir, 0777, true);
			}else{
				//echo "dir exsits";
			}
			
			/* append index (submission number) */
			$idx = 1;
			do {
				$dest_dir = $target_dir . USERNAME . "_" . $idx;
				$cur_submission = USERNAME . "_" . $idx;
				$idx++;
			} while (file_exists($dest_dir));
			
			$dirname = $dest_dir; //for output
			$target = $dest_dir . ".zip";
			
			$ok = 1;
			/* size check */
			if ($_FILES['uploaded']['size'] > 2000000) { 
				$message .= "<div class='padded error'>Your file is too large.</div>"; 
				$ok = 0; 
			}
			
			/* type check */
			if ($_FILES['uploaded']['type'] != "application/x-zip-compressed" && $_FILES['uploaded']['type'] != "application/zip") { 
				/* if filename ends in ".zip", then we'll take it */
				$name = $_FILES['uploaded']['name'];
				if (strtolower(substr($name, strlen($name) - 4)) != ".zip") {
					$message .= "<div class='padded error'>Please submit a zip file! Type detected: " . $_FILES['uploaded']['type'] . " </div>";
					$ok = 0; 
				}
			}
			
			if ($ok==0) { 
				//$message .= "<div class='padded'>Sorry, your file was not uploaded.</div>"; 
			} else { 
				//echo "<br/> target is ". $target;
				if(move_uploaded_file($_FILES['uploaded']['tmp_name'], $target)) {
					//$message .= "<div class='padded'>The file you uploaded has been saved as " . $target . "<br/></div>";
					//$message .= "<div class='padded'>Attempting unzip...</div>";
					$success = $this->unzip($target);

					$pos = strpos($target, ".zip");
					$dirname = substr($target, 0, $pos);
					if(!is_dir($dirname)) return null; // TODO handle error
					$dir = opendir($dirname);
					$fileList = array();
					$foundCode = False;
					$allDirectories = True;
					while($file = readdir($dir)) {
					 	if($file != "." && $file != ".." && $file != "__MACOSX"){
							$fileList []= $file;
							if(isCodeFileForClass($file, $class)){
								$foundCode = True;
							}
							$fullpath = $dirname."/". $file;
							//echo $fullpath;
							if(!is_dir($fullpath)){
								$allDirectories = False;
								//echo $file . " is not a directory";
							}else{
								//echo $file . "IS A DIRECTORY";
							}
						}
					}
					
					if($allDirectories){
						$success = False;
						$ok = 0;
						$message .="<div class='padded error'>Error. It looks like you zipped a directory instead of just zipping files.</div>";
					}
					
					if(!$foundCode){
						$success = False;
						$ok = 0;
						$legalCodeFiles = getFileTypesForClass($class);
						$message .= "<div class='padded error'>Error. There were no code files of the proper type submitted.</div>";
					}
					if ($success) {
						//echo "unzipped";
						//unlink($target);
						$late_days_file = $dest_dir . "/lateDays.txt";
						//echo "<br/>".$late_days_file;
						$late_days = fopen($late_days_file, "w");
						$this->write_late_days_file($late_days, $assn_date);
						fclose($late_days);
						$message .= "<div class='padded'><b>" . $assn_name . "</b> was successfully submitted at " . date("d/M/Y H:i:s") . ".<br/></div>";
					}
				} else { 
					$ok = 0;
					$message .= "<div class='padded error'>Sorry, there was a problem uploading your file.  Please try again, or contact the course staff for assistance.</div>"; 
				}
			}
			

			$this->smarty->assign("name", Model::getDisplayName(USERNAME));
			$this->smarty->assign("class", $class);
			$this->smarty->assign("dir", $cur_submission);
			$this->smarty->assign("assn", $assn_name);
			$this->smarty->assign("assn_dir", $assn_dir);
			$this->smarty->assign("student", USERNAME);
			$this->smarty->assign("message", $message);
			$this->smarty->assign("ok", $ok);
			$this->smarty->assign("fileList", $fileList);
			
			$this->smarty->display('upload.html');
		}
	}
	?>