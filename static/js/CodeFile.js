function /* class */ CodeFile(filename, id_number, interactive, user) {
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
	this.user = user;
	
	this.highlights = new Array();
	
	this.currentComment = null;
	
	var self = this;
	
	this.commentID = 0;
	
	this.setupComments = function(){
		var commentLocation = $('#comments'+this.fileID);
		var children = commentLocation.children();
		for(var i = 0; i < children.length; i++){
			var curComment = children[i];
			var cur = $(curComment);
			var rangeString = cur.attr('id');			
			var text = $(".comment", cur).html();
			var range = stringToRange(rangeString);
			var commenter = $(".commenter", cur).html();
			this.highlightRange(range);
			var newComment = new Comment(text, range, this, this.commentID, commenter);
			this.addComment(newComment);
		}
	}
	
	this.showComments = function(){
		for(var i = 0; i < this.comment_list.length; i++){
			var comment = this.comment_list[i];
			this.addCommentDiv(comment.text, comment.commenter, comment.range, this.interactive, comment.id);
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

		this.highlights.push(0); // just put a zero in index zero-- we want to start from line 1
		var line_no = 1;
		do {
			this.highlights.push(0);
			var line = this.getLine(line_no);
			line.bind("mousedown", {code_file : this}, CodeFile.mousePressed);
			line.bind("mouseenter", {code_file : this}, CodeFile.mouseEntered);
			line_no++;
		} while(line.size() != 0);
		
		//console.log(this.highlights);
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
		//console.log("highlight " + range);
		
		for (var i = range.lower; i <= range.higher; i++) {
			this.hiliteLineNo(i);
			this.highlights[i]++;
		}  
		//console.log(this.highlights);
	}
	
	/* Unhighlights the range passed in as a parameter */
	this.unhighlightRange = function(range){
		//console.log("unlighlighting " + range);
		//console.log(this.highlights);
		
		for (var i = range.lower; i <= range.higher; i++) {
			
			if(this.highlights[i] > 0){
				this.highlights[i]--;
			}


			//only unhighlight if the highlight count is zero. otherwise
			//there are possibly multiple comments on this line
			if(this.highlights[i] == 0){
				this.unhiliteLineNo(i);
			}
			
		}
		
		//console.log(this.highlights);  
	}
	
	
	//var comment = file.getCommentByRangeAndID(commentRange, commentID);
	this.getCommentByRangeAndID = function(commentRange, commentID){
		for(var i = 0; i < this.comment_list.length; i++) {
			var cur = this.comment_list[i];
			
			if(cur.id == commentID && cur.range.toString() == commentRange)
				return this.comment_list[i];
		}
		return null;
	}
	
	this.getCurrentComment = function(){
		return this.currentComment;
	}
	
	this.removeComment = function(comment) {
		for(var i = 0; i < this.comment_list.length; i++) {
			if(this.comment_list[i] == comment){
				this.comment_list.splice(i, 1);
			}
		}
		// console.log(this.comment_list);
	}
	
	this.addComment = function(comment) {
		this.comment_list.push(comment);
		this.commentID++;
	}
	
	this.addCommentDiv = function(text, commenter, range, isEditable, commentID){
		
		if(isEditable == undefined) isEditable = true;
		var range_text = range.toString();
		formattedText = converter.makeHtml(text);	
		formattedText = formattedText.replace(/&amp;/g, '&');		
		
		
		var toAdd = "<div class='inlineComment e"+ range_text +" comment"+commentID+"'>";
		toAdd += "<span class='hiddenPlainText htext" + range_text + "'>" + text + "</span>";
		if(isEditable) toAdd += "<a href=\"javascript:edit("+ this.fileID + ",'" + range_text + "',"+commentID+")\">";
		toAdd += 	" <div class='" + range_text +" commentbox'><span class='inlineCommentText ctext" + range_text + "'>" + formattedText + "</span>";
		toAdd +=	"<div class='commentauthor'>" + commenter + "</div><div style='clear:both'></div>"; // add the author
		toAdd +=    "</div>";
		if(isEditable) toAdd += "</a>";
		toAdd += "</div>";


		var commentLocation = $('#file'+ this.fileID + ' .code .number'+range.higher);
		commentLocation.after(toAdd);		
	}
	
	this.addHandlers();
}

CodeFile.mousePressed = function(event) {
	if(commentOpen) return;
	
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