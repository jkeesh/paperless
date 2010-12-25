<?php
	
	class UploadHandler extends ToroHandler {
		
		function unzip($filename) {
			$zip = new ZipArchive;
			if ($zip->open($filename) === TRUE) {
				$zip->extractTo(substr($filename, 0, strlen($filename) - 4));
				$zip->close();
				echo " success!</li>";
				return true;
			} else {
				echo " failure!  Please try again, or contact the course staff for assistance.</li>";
				return false;
			}
        }
		
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
		
		private function getAssns($assignments_file) {
			$assn_data = fopen($assignments_file, "r");
			
			$arr = array();
			
			while (! feof($assn_data)) {
				$info = fgetcsv($assn_data);
				if ($info == NULL) {
					continue;
				}
				$arr[$info[0]] = array("Name" => $info[1], "DueDate" => date_create($info[2]));
			}
			return $arr;
		}
		
		public function get(){
			echo "get";
		}
		
		public function post($class) {
			$assn_dir = $_POST['assignment'];
			$assignments_file = ROOT_URL. "controllers/assignments.csv";
			$assns = $this->getAssns($assignments_file);
			$assn_name = $assns[$assn_dir]["Name"];
			$assn_date = $assns[$assn_dir]["DueDate"];
			
			
			$sl_id = Model::getSectionLeaderForStudent(USERNAME);
			$dirname = SUBMISSIONS_PREFIX . "/" . $class . "/" . SUBMISSIONS_DIR . "/" . $sl_id . "/" . $assn_dir . "/";
			
			/* create directory, if necessary */
			$target_dir = $dirname;
			if (!file_exists($target_dir)) {
				echo "making dir";
				mkdir($target_dir, 0777, true);
			}else{
				echo "dir exsits";
			}
			
			/* append index (submission number) */
			$idx = 1;
			do {
				$dest_dir = $target_dir . USERNAME . "_" . $idx;
				echo "<br/>DEST: " .$dest_dir;
				$idx++;
			} while (file_exists($dest_dir));
			$target = $dest_dir . ".zip";
			echo "<br/>Target: " .$target;
			
			$ok = 1;
			/* size check */
			if ($_FILES['uploaded']['size'] > 2000000) { 
				echo "<li>Your file is too large.</li>"; 
				$ok = 0; 
			}else{
				echo "<br/>file size ok";
			}
			
			/* type check */
			if ($_FILES['uploaded']['type'] != "application/x-zip-compressed" && $_FILES['uploaded']['type'] != "application/zip") { 
				/* if filename ends in ".zip", then we'll take it */
				$name = $_FILES['uploaded']['name'];
				if (strtolower(substr($name, strlen($name) - 4)) != ".zip") {
					echo "<li>Please submit a zip file! Type detected: " . $_FILES['uploaded']['type'] . " </li>";
					$ok = 0; 
				}
			}else{
				echo "<br/> found zip";
			}
			
			if ($ok==0) { 
				echo "Sorry, your file was not uploaded.  Please try again, or contact the course staff for assistance"; 
			} else { 
				echo "<br/> target is ". $target;
				if(move_uploaded_file($_FILES['uploaded']['tmp_name'], $target)) {
					echo "<li>The file you uploaded has been saved as " . $target . "<br/></li>";
					echo "<li>Attempting unzip...";
					$success = $this->unzip($target);
					if ($success) {
						echo "unzipped";
						unlink($target);
						$late_days_file = $dest_dir . "/lateDays.txt";
						echo "<br/>".$late_days_file;
						$late_days = fopen($late_days_file, "w");
						$this->write_late_days_file($late_days, $assn_date);
						fclose($late_days);
						echo "<li><b>" . $assn_name . "</b> was successfully submitted at " . date("d/M/Y H:i:s") . ".<br/></li>";
					}else{
						echo "no success unzipping";
					}
				} else { 
					echo "<li>Sorry, there was a problem uploading your file.  Please try again, or contact the course staff for assistance.</li>"; 
				}
			}
			

			$this->smarty->assign("name", Model::getDisplayName(USERNAME));
			$this->smarty->assign("class", $class);
			$this->smarty->assign("dir", $dirname);
			$this->smarty->assign("assn", $assn_name);
			
			$this->smarty->display('upload.html');
		}
	}
	?>