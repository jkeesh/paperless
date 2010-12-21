<?php
require_once('index.php');
class StudentHandler extends ToroHandler {

    public function get($student) {
        // for now hard coded .. need to setup DB access
        $dirname = SUBMISSIONS_DIR . "/" . USERNAME ."/";
		
        $assns = $this->getDirEntries($dirname);

		//information will be an associative array where index i holds
		//the assignment and student directory information as keys
		$information = array();
		
		$i = 0;
        //for every assignment, go find ones that belong to the student
		//we will save the submission with the highest number.
		foreach($assns as $assn){
		 	$dir = SUBMISSIONS_DIR ."/". USERNAME ."/" . $assn ."/";
			$student_submissions = $this->getDirEntries($dir);
			$information[$i]['assignment'] = $assn;
			foreach($student_submissions as $submission){
				if(strpos($submission, $student) !== false){
					$information[$i]['studentdir'] = $submission;
				}
			}
			$i++;
		}

		//print_r($information);

        // assign template vars
 	  	$this->smarty->assign("information", $information);  
        // display the template
        $this->smarty->display("student.html");
    }
}
?>
