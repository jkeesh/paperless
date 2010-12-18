<?php
class CodeHandler extends ToroHandler {
    
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
        // shouldn't be hardcoded
        $dirname = SUBMISSIONS_DIR . "/" . USERNAME . "/". $assignment . "/" . $student . "/"; 
        if(!is_dir($dirname)) return null;
        
        $dir = opendir($dirname);
        $files = array();
        $file_contents = array();
        
        while($file = readdir($dir)) {
           if($this->isCodeFile($file, CLASSNAME)) {
             $files[] = $file;
             $file_contents[] = htmlentities(file_get_contents($dirname . $file));
           }
        }
        return array($files, $file_contents);
    }

    public function get($student, $assignment) {
        
        list($files, $file_contents) = $this->getAssignmentFiles($student, $assignment);

        // assign template vars
        $this->smarty->assign("code", true);
        $this->smarty->assign("student", htmlentities($student));
        $this->smarty->assign("assignment", htmlentities($assignment));
        $this->smarty->assign("files", $files);
        $this->smarty->assign("file_contents", $file_contents);
        
        // display the template
        $this->smarty->display("code.html");
    }
}
?>