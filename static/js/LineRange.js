function /* class */ LineRange(a, b) {
	this.lower = Math.min(a, b);
	this.higher = Math.max(a, b);
		
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
		return this.lower + "-" + this.higher;
	}
	
	this.equals = function(other){
		return other.lower == this.lower && other.higher == this.higher;
	}
	
}

function stringToRange(str){
	console.log(str);
	var pattern = /(\d+)-(\d+)/;
	var result = pattern.exec(str);
	var start = parseInt(result[1]);
	var end = parseInt(result[2]);
	return new LineRange(start, end);
}
