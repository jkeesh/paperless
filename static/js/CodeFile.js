

function CodeFile(options){
//function /* class */ CodeFile(filename, id_number, interactive, user) {
	this.interactive = options.interactive;
	this.editable = options.interactive;
	this.displayed = false;
	//alert("interactive: " + this.interactive);
	
    // if(!shortcuts_added)
    //  addShortcuts();
	
	this.comment_list = new Array(); // TODO make this hold a list of Comment objects
	this.filename = options.filename;
	this.fileID = options.id_number;
	
	this.selected_range_start;
	this.selected_range_end;
	this.selected_ranges = [];
	this.last_comment = null;
	this.user = options.user;
	
	this.highlights = new Array();
	
	this.currentComment = null;
	
	var self = this;
	
	this.commentID = 0;
	
	this.get_comment_by_db_id = function(id){
	    for(var i = 0; i < this.comment_list.length; i++){
	        if(this.comment_list[i].db_id == id){
	            return this.comment_list[i];
	        }
	    }
	    return null;
	}
	
	this.setupComments = function(){
	    var commentLocation = $('.comments_holder[data-id="'+this.fileID+'"]');
	    D.log(commentLocation);
//		var commentLocation = $('#comments'+this.fileID);
		var children = commentLocation.children();
		D.log($(children));
		for(var i = 0; i < children.length; i++){
			var curComment = children[i];
			var cur = $(curComment);
			var rangeString = cur.attr('data-range');
			D.log(rangeString);			
			var text = $(".comment", cur).html();
			D.log(text);
			var range = LineRange.stringToRange(rangeString);
			var commenter = $(".commenter", cur).html();
			D.log(commenter);
			this.highlightRange(range);
			
			// The id of the comment in the database
			var db_id = cur.attr('data-id');
			
			var newComment = new Comment(text, range, this, this.commentID, commenter, db_id);
			this.addComment(newComment);
			
		}
		D.log(this);
	}
	
	this.showComments = function(){
	    D.log('showing comments...');
        if(this.displayed) return;
		for(var i = 0; i < this.comment_list.length; i++){
			var comment = this.comment_list[i];
			D.log(comment);
			this.addCommentDiv(comment.text, comment.commenter, comment.range, this.interactive, comment.id, comment.db_id);
			D.log('added???');
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
	    D.log('adding handlers...');
		if(!this.interactive) return;

        D.log('continuing to add...');
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
	    var file_selector = '.code_container[data-id="'+this.fileID+'"]';
	    
        // var id = "#file" + this.fileID;
		var theclass = '.number' + line_no;
		return $(theclass, file_selector);
	}
	
	this.getLineNumber = function(line) {
		var newline = $(line).attr("class");
		var pattern = /line number(\d+) .*/;
		var result = pattern.exec(newline)[1];
		return parseInt(result);
	}
	
	this.hiliteLineNo = function(line_no) {
        // var id = "#file" + this.fileID;
        // var theclass = '.number' + line_no;   
        var line = this.getLine(line_no); 
		$(line).addClass('highlighted');
	}
	
	this.unhiliteLineNo = function(line_no) {
        // var id = "#file" + this.fileID;
        // var theclass = '.number' + line_no;
        var line = this.getLine(line_no); 
		$(line).removeClass('highlighted');
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
	
	this.getCurrentComment = function(){
	    this.currentComment.commenter = this.user;
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
	
	this.addCommentDiv = function(text, commenter, range, isEditable, commentID, db_id){
		D.log('adding comment div');
		if(isEditable == undefined) isEditable = true;
		var range_text = range.toString();
		formattedText = converter.makeHtml(text);	
		formattedText = formattedText.replace(/&amp;/g, '&');		
		
		var data = {
            range_text: range_text,
            commentID: commentID,
            fileID: this.fileID,
            text: text,
            formattedText: formattedText,
            commenter: commenter,
            editable: isEditable,
            db_id: db_id
        };
        //console.log(data);
        var html = $('#commentTemplate').tmpl(data);
        console.log(html);

		var commentLocation = $('.code_container[data-id="'+this.fileID+'"] .code .number'+range.higher);
		commentLocation.after(html);	
		
		CodeManager.bind_editing();	
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