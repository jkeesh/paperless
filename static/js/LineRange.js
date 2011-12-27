/*
 * LineRange
 * =====================
 * This class defines a line range in a code file. A line range has 
 * a start and end, and contains several functions to compare with 
 * other lines ranges, iterate through the lines, and produce a string
 * representation.
 *
 * 
 * @param   a   {int}       lower bound of range, inclusive
 * @param   b   {int}       upper bound of range, inclusive
 * @author  Jake Becker     
 * @edited  Jeremy Keeshin  December 26, 2011
 *                          convert to prototype style
 */
function /* class */ LineRange(a, b) {
	this.lower = Math.min(a, b);
	this.higher = Math.max(a, b);
}		

/*
 * Return whether or not the parameter num is in the current line range
 * @param   num {int}       the number we are testing
 */
LineRange.prototype.contains = function(num) {
	if (num >= lower && num <= higher) {
		return true;
	}
	return false;
}
	
/*
 * A function that processes a call back on all of the lines in the range
 * @param   callback    {fn}    the callback function
 */	
LineRange.prototype.each = function(callback) {
	for (var i = this.lower; i <= this.higher; i++) {
		callback(i);
	}
}
	
/*
 * Returns a string representation of the range. This important toString
 * method is used in class names throughout the template to help identify
 * the method in the html
 */
LineRange.prototype.toString = function(){
	return this.lower + "-" + this.higher;
}
	
/*
 * This compares a LineRange to a LineRange other
 * @param   other   {Object}    the LineRange object to compare against.
 */
LineRange.prototype.equals = function(other){
	return other.lower == this.lower && other.higher == this.higher;
}


/*
 * This method takes a string which represents a line range in the format
 * LOWER#-HIGHER#, and parses it and returns a new LineRange. This is a class
 * method and is used at the initial setup which adds the comment divs
 * for existing comments
 *
 * @param   str     {string}      the string representation of the LineRage to be parsed
 */
LineRange.stringToRange = function(str){
	var pattern = /(\d+)-(\d+)/;
	var result = pattern.exec(str);
	var start = parseInt(result[1]);
	var end = parseInt(result[2]);
	return new LineRange(start, end);
}
