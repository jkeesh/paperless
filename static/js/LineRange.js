function /* class */ LineRange(a, b) {
	this.lower = Math.min(a, b);
	this.higher = Math.max(a, b);
	
	this.toString = function() {
		return "(" + this.lower + "," + this.higher + ")";
	}
	
	this.contains = function(num) {
		if (num >= lower && num <= higher) {
			return true;
		}
		return false;
	}
	
	this.subtract = function(other) {
		return new LineRange(Math.max(this.lower, other.lower), Math.min(this.higher, other.higher));
	}
	
	this.each = function(callback) {
		for (var i = this.lower; i <= this.higher; i++) {
			callback(i);
		}
	}
	
	this.toString = function(){
		var range_text = this.lower + "-" + this.higher;
		if(this.lower == this.higher) range_text = this.lower;
		return range_text;
	}
}