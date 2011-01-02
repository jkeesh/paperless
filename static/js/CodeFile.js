function /* class */ CodeFile(filename, id_number, interactive) {
	this.interactive = interactive;
	this.editable = interactive;
	this.displayed = false;
	//alert("interactive: " + this.interactive);
	
	if(!shortcuts_added)
		addShortcuts();
	
	this.comment_list = new Array(); // TODO make this hold a list of Comment objects
	this.filename = filename;
	this.fileID = id_number;
	
	this.selected_range_start;
	this.selected_range_end;
	this.selected_ranges = [];
	this.last_comment = null;
	
	var self = this;
	
	this.showComments = function(){
		for(var i = 0; i < this.comment_list.length; i++){
			var comment = this.comment_list[i];
			this.addCommentDiv(comment.text, comment.range, this.interactive);
		}
		this.displayed = true;
	}
	
	this.isLineSelected = function(line_no) {		
		for (var i = 0; i < this.selected_ranges.length; i++) {
			var range = this.selected_ranges[i];
			if (line_no >= range.lower && line_no <= range.higher) {
				return true;
			}
		}
		
		return false;
	}
	
	this.addHandlers = function() {
		if(!this.interactive) return;
		
		var line_no = 1;
		do {
			var line = this.getLine(line_no);
			line.bind("mousedown", {code_file : this}, CodeFile.mousePressed);
			line.bind("mouseenter", {code_file : this}, CodeFile.mouseEntered);
			line_no++;
		} while(line.size() != 0);
	}
	
	this.getLine = function(line_no) {
		var id = "#file" + this.fileID;
		var theclass = '.number' + line_no;
		return $(theclass, id);
	}
	
	this.getLineNumber = function(line) {
		var newline = $(line).attr("class");
		var pattern = /line number(\d+) .*/;
		var result = pattern.exec(newline)[1];
		return parseInt(result);
	}
	
	this.hiliteLineNo = function(line_no) {
		var id = "#file" + this.fileID;
		var theclass = '.number' + line_no;
		$(theclass, id).addClass('highlighted');
	}
	
	this.unhiliteLineNo = function(line_no) {
		var id = "#file" + this.fileID;
		var theclass = '.number' + line_no;
		$(theclass, id).removeClass('highlighted');
	}
	
	this.hiliteLine = function (line) {
		var line_no = this.getLineNumber(line);
		$(line).addClass("highlighted");
	}
	
	this.unhiliteLine = function (line) {
		$(line).removeClass("highlighted");
	}
	
	this.highlightRange = function(range){
		for (var i = range.lower; i <= range.higher; i++) {
			this.hiliteLineNo(i);
		}  
	}
	
	/* Unhighlights the range passed in as a parameter */
	this.unhighlightRange = function(range){
		for (var i = range.lower; i <= range.higher; i++) {
			this.unhiliteLineNo(i);
		}  
	}
	
	this.getCommentFromID = function(commentID) {
		for(var i = 0; i < this.comment_list.length; i++) {
			if(commentID == "c" + this.comment_list[i].range.toString())
				return this.comment_list[i];
		}
		return null;
	}
	
	this.removeCommentFromID = function(commentID) {
		for(var i = 0; i < this.comment_list.length; i++) {
			if(commentID == "c" + this.comment_list[i].range.toString())
				this.comment_list.splice(i, i);
		}
	}
	
	this.addComment = function(comment) {
		this.comment_list.push(comment);
	}
	
	this.addCommentDiv = function(text, range, isEditable){
		if(isEditable == undefined) isEditable = true;
		//console.log(text + ", editable: " + isEditable);
		var range_text = range.toString();
		var comment_id = "c" + range_text;
		var element_id = "e" + range_text;


		var id = "#file" + this.fileID;
		var theclass = '.number' + range.lower;
		var elem = $(theclass, id)[1];
		//console.log('file '+ this.fileID);
		//console.log(elem);

		var top_offset = 0;
		if(elem) top_offset = $(elem).offset().top;
		//console.log(top_offset);
//		var style_position = "position:absolute; top:"+ top_offset +"px; right: 100px;";
		var style_position = "background-color: red; z-index: 5;";
		
		var toAdd = "<div id='"+ element_id +"' class='inlineComment'>";
//		var toAdd = "</pre><div id='"+ element_id +"' style='" + style_position +"'>";

		if(isEditable) toAdd += "<a href=\"javascript:edit("+ this.fileID + ",'" + comment_id + "')\">";
		toAdd += 	" <div id='" + comment_id +"' class='commentbox'><span class='inlineCommentText' id='ctext" + range_text + "'>" + text + "</span></div>";
		if(isEditable) toAdd += "</a>";
		toAdd += "</div>";
		//$('#comments' + this.fileID).append(toAdd);
		//$('#container .index'+range.lower).append(toAdd);
//		var commentLocation = $('#file'+ this.fileID + ' .code .number'+range.lower);
//		var lineLocation = $('#file'+this.fileID + ' .gutter .number'+range.lower);
//		console.log(commentLocation);
//		console.log(lineLocation);
//		commentLocation.before(toAdd);

		var commentLocation = $('#file'+ this.fileID + ' .code .number'+range.higher);
		var lineLocation = $('#file'+this.fileID + ' .gutter .number'+range.higher);
//		console.log(commentLocation);
//		console.log(lineLocation);
		commentLocation.after(toAdd);		
		
		
		var justAdded = $('#'+element_id);
//		console.log(justAdded);
//		console.log(justAdded.height());
	//	var height = toAdd.style.pixelHeight;
	//	console.log(height);
		var height = justAdded.height() + 44;
//		lineLocation.before('<div style="height:'+height + 'px;">&nbsp;</div>');
	}
	
	this.addHandlers();
}

CodeFile.mousePressed = function(event) {
	code_file = event.data.code_file;
	if (dragging_in_file != null && dragging_in_file != code_file) {
		return false;
	}
	
	var line_no = code_file.getLineNumber(this);
	
	dragging_in_file = code_file;
	code_file.selected_range_start = code_file.selected_range_end = line_no;
	code_file.hiliteLine(this);
	event.data.code_file.dragging = true;
	return false;
}

CodeFile.mouseEntered = function(event) {
	code_file = event.data.code_file;
	if (dragging_in_file != code_file) {
		return;
	}
	
	var line_no = code_file.getLineNumber(this);
	
	old_range = new LineRange(code_file.selected_range_start, code_file.selected_range_end);
	range = new LineRange(code_file.selected_range_start, line_no);
	range.each(function(line_no) {
			   code_file.hiliteLineNo(line_no);
			   });
	
	for (var i = old_range.lower; i < range.lower; i++) {
		code_file.unfLineNo(i);
	}
	for (var i = old_range.higher; i > range.higher; i--) {
		code_file.unhiliteLineNo(i);
	}
	
	code_file.selected_range_end = line_no;
}