<?php
require_once('index.php');
class StudentHandler extends ToroHandler {

    public function get($student) {
        // for now hard coded .. need to setup DB access
        $dirname = SUBMISSIONS_DIR . "/" . USERNAME ."/";
        $assns = $this->getDirEntries($dirname);
        
        // assign template vars
        $this->smarty->assign("student", htmlentities($student));
        $this->smarty->assign("assignments", $assns);
        
        // display the template
        $this->smarty->display("student.html");
    }
}
?>
