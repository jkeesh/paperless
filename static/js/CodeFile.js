function /* class */ CodeFile(filename, id_number, interactive) {
	this.interactive = interactive;
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
	
	this.addComment = function(comment, isEditable) {
		if(isEditable == undefined) isEditable = true;
		this.comment_list.push(comment);
		this.addCommentDiv(comment.text, comment.range, isEditable);
	}
	
	this.addCommentDiv = function(text, range, isEditable){
		if(isEditable == undefined) isEditable = true;
		//console.log(text + ", editable: " + isEditable);
		var range_text = range.toString();
		var comment_id = "c" + range_text;
		var element_id = "e" + range_text;
		var top_offset = 12.5 * (range.lower) + 213; //200 is codeposition offset in style.css , 17 is line height, 0.75 since we use a slightly smaller font
		var style_position = "position:absolute; top:"+ top_offset +"px; right: 100px;";
		
		var toAdd = "<div id='"+ element_id +"' style='" + style_position +"'>";
		if(isEditable) toAdd += "<a href=\"javascript:edit("+ this.fileID + ",'" + comment_id + "')\">";
		toAdd += 	" <div id='" + comment_id +"' class='commentbox'><span id='ctext" + range_text + "'>" + text + "</span></div>";
		if(isEditable) toAdd += "</a>";
		toAdd += "</div>";
		$('#comments' + this.fileID).append(toAdd);
	}
	
	this.addHandlers();
}

CodeFile.mousePressed = function(event) {
	code_file = event.data.code_file;
	if (dragging_in_file != null && dragging_in_file != code_file) {
		return false;
	}
	
	var line_no = code_file.getLineNumber(this);
	if (current_dialog != null || code_file.isLineSelected(line_no)) {
		return false;
	}
	
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
	
	// Trim range so it doesn't overlap any already selected lines.
	var increment = (line_no >= code_file.selected_range_start ? 1 : -1);
	for (var i = code_file.selected_range_start; !(code_file.isLineSelected(i)); i += increment) {
		new_line_no = i;
		if (i == line_no) {
			break;
		}
	}
	line_no = new_line_no;
	
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