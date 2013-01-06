<?php
  require_once('models/Model.php');
  require_once('utils.php');
  require_once('lib/zipstream.php');
  
  /**
   * This class handles the logic for a section leader view of a 
   * single assignment. An assignment view will list all of the submissions
   * for a particular assignment, and if there are multiple for the same student.
   * Using the drag-drop submitter, each student should only have 1 submission,
   * but in the 106A submitter, and older version each submission comes up as
   * sunetid_#.
   */
  class DownloadAssignmentHandler extends ToroHandler {
    
    public function get($qid, $class, $sectionleader, $assignment) {
      $this->basic_setup(func_get_args());
      Permissions::gate(POSITION_SECTION_LEADER, $this->role);
      Permissions::verify(POSITION_SECTION_LEADER, $sectionleader, $this->course);
      
      $sl = new SectionLeader;
      $sl->from_sunetid_and_course($sectionleader, $this->course);
      
      $assn = PaperlessAssignment::from_course_and_assignment($this->course, $assignment);
      
      $dirname = $this->course->get_base_directory() .'/' . $assignment;
                  
      $all_students = $this->getDirEntries($dirname);
      
      $func = function($student){
          return $student->sunetid;
      };
      $student_ids = array_map($func, $sl->get_students_for_assignment($assn));
            
      $zip = new ZipStream($assignment . ".zip");
      foreach($all_students as $student){
        $split = splitDirectory($student);
        if(in_array($split[0], $student_ids)){
          $files = Utilities::get_all_files($dirname . '/' . $student);
          foreach($files as $file){
            $zip->add_file_from_path($assignment . '/' . $student . '/' . $file, $dirname . '/' . $student . '/' . $file);
          }
        }
      }
      $zip->finish();      
    }  
  }
?>