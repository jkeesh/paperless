/*
 * LineRange
 * =====================
 * This class defines a line range in a code file. A line range has 
 * a start and end, and contains several functions to compare with 
 * other lines ranges, iterate through the lines, and produce a string
 * representation.
 *
 * Parameters
 * a        lower bound of range, inclusive
 * b        upper bound of range, inclusive
 */
function /* class */ LineRange(a, b) {
	this.lower = Math.min(a, b);
	this.higher = Math.max(a, b);
		
	/*
	 * this.contains
	 * ==================
	 * Return whether or not the parameter num is in the current line range
	 */
	this.contains = function(num) {
		if (num >= lower && num <= higher) {
			return true;
		}
		return false;
	}
	
	/*
	 * this.each
	 * ==================
	 * A function that processes a call back on all of the lines in the range
	 */	
	this.each = function(callback) {
		for (var i = this.lower; i <= this.higher; i++) {
			callback(i);
		}
	}
	
	/*
	 * this.toString
	 * ==================
	 * Returns a string representation of the range. This important toString
	 * method is used in class names throughout the template to help identify
	 * the method in the html
	 */
	this.toString = function(){
		return this.lower + "-" + this.higher;
	}
	
	/*
	 * this.equals
	 *===================
	 * This compares a LineRange to a LineRange other
	 */
	this.equals = function(other){
		return other.lower == this.lower && other.higher == this.higher;
	}
	
}


/*
 * LineRange.stringToRange
 * ===========================
 * This method takes a string which represents a line range in the format
 * LOWER#-HIGHER#, and parses it and returns a new LineRange. This is a class
 * method and is used at the initial setup which adds the comment divs
 * for existing comments
 *
 * Parameter
 * str      the string representation of the LineRage to be parsed
 */
LineRange.stringToRange = function(str){
	var pattern = /(\d+)-(\d+)/;
	var result = pattern.exec(str);
	var start = parseInt(result[1]);
	var end = parseInt(result[2]);
	return new LineRange(start, end);
}
