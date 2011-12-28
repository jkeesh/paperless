function CodeFile(options){
	this.interactive = options.interactive;
	this.editable = options.interactive;
	this.displayed = false;
	
    // if(!shortcuts_added)
    //  addShortcuts();
	
	this.comment_list = new Array();
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
		var children = commentLocation.children();
		for(var i = 0; i < children.length; i++){
			var curComment = children[i];
			var cur = $(curComment);
			var rangeString = cur.attr('data-range');
			var text = $(".comment", cur).html();
			var range = LineRange.string_to_range(rangeString);
			var commenter = $(".commenter", cur).html();
			this.highlightRange(range);
			
			// The id of the comment in the database
			var db_id = cur.attr('data-id');
			
            var newComment = new Comment({
                ctext:      text,
                crange:     range,
                code_file:  this,
                commenter:  commenter,
                db_id:      db_id
            });
			
			this.addComment(newComment);			
		}
	}
	
	this.showComments = function(){
        if(this.displayed) return;
		for(var i = 0; i < this.comment_list.length; i++){
			var comment = this.comment_list[i];
			this.addCommentDiv(comment.text, comment.commenter, comment.range, this.interactive, comment.id, comment.db_id);
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
	}
	
	this.getLine = function(line_no) {
	    var file_selector = '.code_container[data-id="'+this.fileID+'"]';
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
        var line = this.getLine(line_no); 
		$(line).addClass('highlighted');
	}
	
	this.unhiliteLineNo = function(line_no) {
        var line = this.getLine(line_no); 
		$(line).removeClass('highlighted');
	}
	
	this.hiliteLine = function (line) {
		$(line).addClass("highlighted");
	}
	
	this.unhiliteLine = function (line) {
		$(line).removeClass("highlighted");
	}
	
	this.highlightRange = function(range){		
		for (var i = range.lower; i <= range.higher; i++) {
			this.hiliteLineNo(i);
			this.highlights[i]++;
		}  
	}
	
	/* Unhighlights the range passed in as a parameter */
	this.unhighlightRange = function(range){		
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
	}
	
	/*
	 * See if this comment should have a color. To specify a color,
	 * you start the comment with 
	 * !color, where color is one of red,green,blue,yellow
	 * If we find that color patter, we take it out of the text.
	 *
	 * @param   text    {string}    the comment text
	 * @return  {Object}            containing the color, and updated text if necessary.
	 * @author  Jeremy Keeshin  December 28, 2011
	 */
	this.getColor = function(text){
	    var color = null;
	    var colors = ['red', 'green', 'blue', 'yellow'];
	    for(var i = 0; i < colors.length; i++){
	        if(text.match(new RegExp('^!'+colors[i] + ' '))){
    	        color = colors[i];
    	        text = text.substring(colors[i].length + 1);
    	        break;
    	    }
	    }
	    return {
	        color: color,
	        text: text
	    }
	}
	
	this.addCommentDiv = function(text, commenter, range, isEditable, commentID, db_id){
		if(isEditable == undefined) isEditable = true;
		var range_text = range.toString();
		
		D.log(text);
		var result = this.getColor(text);
		D.log(result.color);
		formattedText = converter.makeHtml(result.text);	
		formattedText = formattedText.replace(/&amp;/g, '&');		
		
		var data = {
            range_text: range_text,
            fileID: this.fileID,
            text: text,
            formattedText: formattedText,
            commenter: commenter,
            db_id: db_id
        };
        var html = $('#commentTemplate').tmpl(data);
        if(result.color){
            html = $(html).addClass(result.color + 'Color');
        }    
        D.log(html);

		var commentLocation = $('.code_container[data-id="'+this.fileID+'"] .code .number'+range.higher);
		commentLocation.after(html);	
		
		CodeManager.bind_editing();	
	}
	
	if(CodeManager.interactive){
	    this.addHandlers();
	}
}

CodeFile.mousePressed = function(event) {
    if(!CodeManager.is_interactive) return;
    
	if(commentOpen) return;
	
	code_file = event.data.code_file;
	if (CodeManager.dragging_in_file != null && CodeManager.dragging_in_file != code_file) {
		return false;
	}
	
	var line_no = code_file.getLineNumber(this);
	
	CodeManager.dragging_in_file = code_file;
	code_file.selected_range_start = code_file.selected_range_end = line_no;
	code_file.hiliteLine(this);
	event.data.code_file.dragging = true;
	return false;
}

CodeFile.mouseEntered = function(event) {
    if(!CodeManager.is_interactive) return;
    
    
	code_file = event.data.code_file;
	if (CodeManager.dragging_in_file != code_file) {
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