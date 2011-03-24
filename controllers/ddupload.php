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
		
		
		function getUploadDirectory($class){
			$sl_id = Model::getSectionLeaderForStudent(USERNAME);
			$dirname = SUBMISSIONS_PREFIX . "/" . $class . "/" . SUBMISSIONS_DIR . "/" . $sl_id . "/" . $_GET['assndir'] . "/" . USERNAME . "_1"; 
			// the _1 is hacky to make it backwards compatible with the 106a submission style. 
			// now all 106bx submissions will go in the same directory and students will have only 1 submission folder
			return $dirname;
		}
		
		function lateDays($class, $dirname){
			$late_days_file = $dirname . "/lateDays.txt";
			$assn_dir = $_GET['assndir'];
			$assns = getAssnsForClass($class);
			$assn_date = $assns[$assn_dir]["DueDate"];			
			$late_days = fopen($late_days_file, "w");
			$this->write_late_days_file($late_days, $assn_date);
		}
		
		//public function post_xhr($class) {
		public function post($class){
			$dirname = $this->getUploadDirectory($class);
			if (!file_exists($dirname)) {
				mkdir($dirname, 0777, true);
			}
			
			$this->lateDays($class, $dirname);
			
			// If the browser supports sendAsBinary () can use the array $ _FILES
			if(count($_FILES)>0) { 
				if( move_uploaded_file( $_FILES['upload']['tmp_name'] , $dirname.'/'.$_FILES['upload']['name'] ) ) {
					echo 'done';
				}
				exit();
			} else if(isset($_GET['up'])) {
				// If the browser does not support sendAsBinary ()
				if(isset($_GET['base64'])) {
					$content = base64_decode(file_get_contents('php://input'));
				} else {
					$content = file_get_contents('php://input');
				}
				


				$headers = getallheaders();
				$headers = array_change_key_case($headers, CASE_UPPER);
				
								print_r($headers);

				if(file_put_contents($dirname.'/'.$headers['UP-FILENAME'], $content)) {
					echo 'done';
				}
				exit();
			}

		}
	}
	?>