<?php
class AssignmentHandler extends ToroHandler {
  
  public function get($assignment) {
    //for now get users in a hacky way, because the database isn't hooked in
    //$dirname="/afs/ir.stanford.edu/class/cs106x/submissions/".$sl."/";
    $dirname = SUBMISSIONS_DIR . "/" . USERNAME . "/";
    $students = $this->getDirEntries($dirname . $assignment);
    print_r($students);
    $this->smarty->assign("students", $students);
    $this->smarty->assign("assignment", $assignment);

    // display the template
    $this->smarty->display('assignment.html');
  }
}
?>