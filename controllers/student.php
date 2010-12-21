<?php
require_once('index.php');
class StudentHandler extends ToroHandler {

  public function get($class, $student) {
    
    $string = explode("_", $student); // if it was student_1 just take student
    $student = $string[0];

    if(IS_STUDENT_ONLY) {
      if($student != USERNAME) {
        echo "You don't have permission to view this";
        return;
      }
    }

    // for now hard coded .. need to setup DB access
    $dirname = SUBMISSIONS_DIR . "/" . SECTION_LEADER ."/";
    $assns = $this->getDirEntries($dirname);

    //information will be an associative array where index i holds
    //the assignment and student directory information as keys
    $information = array();

    $i = 0;
    //for every assignment, go find ones that belong to the student
    //we will save the submission with the highest number.
    foreach($assns as $assn) {
      $dir = SUBMISSIONS_DIR ."/". SECTION_LEADER ."/" . $assn ."/";
      $student_submissions = $this->getDirEntries($dir);

      $information[$i]['assignment'] = $assn;
      foreach($student_submissions as $submission) {
        if(strpos($submission, $student) !== false) {
          $information[$i]['studentdir'] = $submission;
        }
      }
      $i++;
    }

    // assign template vars
    $this->smarty->assign("information", $information);
    $this->smarty->assign("class", $class);
    
    // display the template
    $this->smarty->display("student.html");
  }
}
?>
