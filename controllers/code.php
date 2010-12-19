<?php
require_once("models/AssignmentFile.php");
require_once("models/AssignmentComment.php");

class CodeHandler extends ToroHandler {
    // shouldn't be hardcoded
    
    
    // Given a filename, tests if this is a good file type for the class
    // i.e cs106a only gets java files and 106bx gets cpp/h
    private function isCodeFile($filename, $class){
       $ext = pathinfo($filename, PATHINFO_EXTENSION);
       if($class == "cs106a") {
          return $ext == "java";
       } else {
          return $ext == "cpp" || $ext == "h";
       }
    }
    
    private function getAssignmentFiles($student, $assignment) {
        $dirname = SUBMISSIONS_DIR . "/" . USERNAME . "/". $assignment . "/" . $student . "/"; 
        if(!is_dir($dirname)) return null;
        
        $dir = opendir($dirname);
        $files = array();
        $file_contents = array();
        $assignment_files = array();
        
        while($file = readdir($dir)) {
           if($this->isCodeFile($file, CLASSNAME)) {
             $assignmentFile = AssignmentFile::load(array("FilePath" => $dirname . $file));
             if(!$assignmentFile) {
               $assignmentFile = AssignmentFile::create(0, $dirname . $file); // TODO associate this with an actual GradedAssignment
               $assignmentFile->save();
             }
             
             $assignment_files[] = $assignmentFile;
             $files[] = $file;
             $file_contents[] = htmlentities(file_get_contents($dirname . $file));
           }
        }
        return array($files, $file_contents, $assignment_files);
    }

    public function get($student, $assignment) {
        
        list($files, $file_contents, $assignment_files) = $this->getAssignmentFiles($student, $assignment);

        // assign template vars
        $this->smarty->assign("code", true);
        $this->smarty->assign("student", htmlentities($student));
        $this->smarty->assign("assignment", htmlentities($assignment));
        $this->smarty->assign("files", $files);
        $this->smarty->assign("file_contents", $file_contents);
        $this->smarty->assign("assignment_files", $assignment_files);
      
        // display the template
        $this->smarty->display("code.html");
    }
    
    // response for posting ajax, must output a valid json string
    public function post_xhr($student, $assignment) {
      // TODO this shouldn't be hard coded
      $dirname = SUBMISSIONS_DIR . "/" . USERNAME . "/". $assignment . "/" . $student . "/";
      $curFile = AssignmentFile::load(array("FilePath" => $dirname . $_POST['filename']));
      if(!$curFile) return; // TODO LOG AN ERROR HERE.. we should have a valid path here
      
      $newComment = AssignmentComment::create($curFile->getID(), $_POST['rangeLower'], $_POST['rangeHigher'], $_POST['text']);
      $newComment->save();
    }
}
?>