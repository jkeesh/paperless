<?php
	/*
	 * This reads of the assignment configuration information for a class
	 * as specified by a csv file in the class configuration directory.
	 */
	function getAssnsForClass($class) {
		$assignments_file = getConfigFileForClass($class);
		$assn_data = fopen($assignments_file, "r");
		
		$arr = array();
		
		fgetcsv($assn_data); //read off first three lines, not assignment data
		fgetcsv($assn_data);
		fgetcsv($assn_data);
		
		while (! feof($assn_data)) {
			$info = fgetcsv($assn_data);
			if ($info == NULL) {
				continue;
			}
			$arr[$info[0]] = array("Name" => $info[1], "DueDate" => date_create($info[2]));
		}
		return $arr;
	}
	
	function getConfigFileForClass($class){
		return CLASS_CONFIG_DIR . "/" . $class . ".csv";
	}
	
	/* 
	 * This function returns an array of the accepted file types for a class
	 * It gets this information from the class configuration file
	 */
	function getFileTypesForClass($class){
		$config = getConfigFileForClass($class);
		$file = fopen($config, "r");
		fgetcsv($file); //read off first line lines, not file data
		$info = fgetcsv($file);
		return $info;
	}
	?>