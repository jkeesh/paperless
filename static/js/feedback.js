code_files = [];
dragging_in_file = null;
current_dialog = null; // we will only have one dialog at a time
current_range = null;
globalSubmitComment = null;

function /* class */ CodeFile(filename, prefix) {
	this.filename = filename;
	this.prefix = prefix;
	this.fileID = this.prefix; // right now pass the filename, and file id

	this.selected_range_start;
	this.selected_range_end;
	this.selected_ranges = [];
	this.last_comment_range = null;
	
	var self = this;
	
	shortcut.add("tab", function(data) {
		var code_file = code_files[self.fileID];
		if(current_range && current_dialog)
			code_file.removeAndSubmitComment(current_range);
	});

	shortcut.add("shift+0", function(){
		var code_file = code_files[self.fileID];
		if(current_range && current_dialog)
			code_file.removeComment(current_range);
	});
	
	shortcut.add("ctrl+z", function(){
		if(self.last_comment_range === undefined || self.last_comment_range == null) return;
                self.last_comment_range = null;
		self.editComment(self.last_comment_range, self.fileID);
	});
	
   	this.themes = new Array('shCoreDefault.css', 'shCoreMDUltra.css' ,'shThemeEmacs.css','shCoreMidnight.css',  'shThemeFadeToGrey.css','shCoreDjango.css', 'shCoreRDark.css', 'shThemeMDUltra.css','shCoreEclipse.css', 'shThemeDefault.css','shThemeMidnight.css', 'shCoreEmacs.css', 'shThemeDjango.css', 'shThemeRDark.css', 'shCoreFadeToGrey.css', 'shThemeEclipse.css');
	
	
	this.timeTheme = null
	this.themeID = 0;
	/// this is all complicated because chrome registered about 10 events on this listener so we need a buffer
	shortcut.add("ctrl+2", function(){
		if(this.timeTheme != null){
			var now = new Date();
			var diff = now.getTime() - this.timeTheme.getTime();
			//console.log(diff);
			if( diff < 100) {
				return;
			}
		}
		this.timeTheme = new Date();
		//console.log('theme switch');
		self.themeID++;
		//console.log(self.themID);
		var themeIndex = self.themeID % self.themes.length; 
		//console.log(self.themeIndex);
		//alert(document.location.pathname);
		var newTheme = root_url +'/static/js/syntaxhighlighter/styles/' + self.themes[themeIndex];
		
		$('#syntaxStylesheet').attr('href', newTheme);
	});
	
	this.time = null
	/// this is all complicated because chrome registered about 10 events on this listener so we need a buffer
	shortcut.add("ctrl+1", function(){
		if(this.time != null){
			var now = new Date();
			var diff = now.getTime() - this.time.getTime();
			if( diff < 100) {
				return;
			}
		}
		this.time = new Date();
		
		if($("#shortcuts").html() != null){
			$("#shortcuts").remove();
		}else{
		 	$("body").append("<div id='shortcuts' style='width: 400px; font-family: Arial; position:absolute; top: 100px; left: 400px; -moz-border-radius: 8px; border-radius: 8px;"
			+" font-size: 16px; background-color: black; color: white; padding: 60px; opacity:0.8;'>"
					+	"<div class='keyboardTitle'>Keyboard Shortcuts</div>"
					+	"<div><span class='keyboardShortcuts'>&lt;Tab&gt;: </span><span class='keyboardAction'>Submit</span></div>"
					+	"<div><span class='keyboardShortcuts'>&lt;Shift&gt;+0: </span><span class='keyboardAction'>Delete</span></div>"
					+   "<div><span class='keyboardShortcuts'>&lt;Ctrl&gt;+z:</span><span class='keyboardAction'> Edit Last Comment</span></div>"
					+	"<div><span class='keyboardShortcuts'>&lt;Ctrl&gt;+1: </span><span class='keyboardAction'>Toggle Shortcuts</span></div>"
					+   "<div><span class='keyboardShortcuts'>&lt;Ctrl&gt;+2: </span><span class='keyboardAction'>Change Theme</span></div>"
					+	"</div>");
		}
	});

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

	// Generate IDs for elements. Decomposing this out helps keep things consistent.
	this.commentTrId = function(range) {
		theid =  this.prefix + "comment_" + range.lower; //+ "-" + range.higher;
		//console.log(theid);
		return theid;
	}

	this.commentTdId = function(range) {
		theid=  this.prefix + "comment_" + range.lower;// + "-" + range.higher;
		//console.log(theid);
		return theid;
	}

	this.commentTextAreaId = function(range) {
		theid= this.prefix + "comment_textarea_" + range.lower; // + "-" + range.higher;
		//console.log(theid);
		return theid;
	}

	this.commentTextId = function(range) {
		theid= this.prefix + "comment_text_" + range.lower; // + "-" + range.higher;
	//	console.log(theid);
		return theid;
	}
	
	this.submitComment = function(range){
		this.last_comment_range = range;
		var commentText = $("textarea").val();
		//console.log(commentText);
		$('textarea').remove();
		//console.log(commentText);
		current_dialog.dialog("close");
		current_dialog.dialog("destroy");
		current_dialog = null;
		var highlight_length = range.higher - range.lower + 1;
		//console.log("Comment " + commentText + " for line " + range.lower + " highlight length " + highlight_length);
		this.addCommentDiv(commentText, range);
	}
	
        this.cancelComment = function(range){
	    for (var i = range.lower; i <= range.higher; i++) {
                this.unhiliteLineNo(i);
            }
            $('textarea').remove();
            current_dialog.dialog("close");
            current_dialog.dialog("destroy");
            current_dialog = null;
        }


	this.removeAndSubmitComment = function(range){
		var elem = "#e"+this.rangeToString(range);
		if(elem) $("#e"+this.rangeToString(range)).remove(); // remove the comment
		this.submitComment(range);
	}
	
	this.addCommentDiv = function(text, range){
		var range_text = this.rangeToString(range);
		var comment_id = "c" + this.rangeToString(range);
		var element_id = "e" + this.rangeToString(range);
		var top_offset = 17.6 * (range.lower) + 200; //200 is codeposition offset in style.css , 17 is line height;
		var style_position = "position:absolute; top:"+ top_offset +"px; right: 100px;";
		

		$('#comments' + this.fileID).append("<div id='"+ element_id +"'><a href=\"javascript:edit("+ this.fileID + ",'" + comment_id + "')\"> <div id='" + comment_id
											+"' style='" + style_position +"' class='commentbox'>"
										//	+ " File: " + this.filename 
										//	+ " Line: "+ range_text + ": "
											+ "<span id='ctext" + this.rangeToString(range) + "'>" + text + "</span>" 
											+ "</div></a></div>");
	}

	this.addCommentBox = function(range) {
		var id = "#file" + this.fileID;
		var theclass = '.number' + range.lower;
		var commentLocation = $(theclass, id);
		current_range = range;
		
		var comment = this;

		current_dialog = $('<div></div>')
                        .html('<textarea></textarea>')
                        .dialog({
                                autoOpen: true,
	                        title: 'Enter Comment',
				width: 350,
                                height: 250,
		                focus: true,
	                        open: function(event, ui) { $(".ui-dialog-titlebar-close").hide();},
                                closeOnEscape: false,
		                buttons: { "Submit":
                                           function() {
                                        comment.submitComment(range); //because 'this' now refers to the dialog                                                                                        
                                    },
                                           "Cancel":
                                        function() {
	                                       comment.cancelComment(range);
                                           },
                                },
	        });

		$("textarea").focus();
	}
	
	
	/* Turns a Range into a string. If the range is one line, like 15-15, it returns 15
	 * otherwise, it will return a string lower-higher, like 5-8 */
	this.rangeToString = function(range){
		var range_text = range.lower + "-" + range.higher;
		if(range.lower == range.higher) range_text = range.lower;
		return range_text;
	}

	
	this.removeComment = function(range) {
		var elem = "#e"+this.rangeToString(range);
		$("#e"+this.rangeToString(range)).remove(); // remove the comment
		
		//unhighlight the lines
		for (var i = range.lower; i <= range.higher; i++) {
			this.unhiliteLineNo(i);
		}

		//remove it from selected ranges
		for (i = 0; i < this.selected_ranges.length; i++) {
			var saved_range = this.selected_ranges[i];
			if (saved_range.lower == range.lower && saved_range.higher == range.higher) {
				this.selected_ranges.splice(i, 1);
			}
		}
		
                $('textArea').remove();

		current_dialog.dialog("close");
		current_dialog.dialog("destroy");
		current_dialog = null;
	}
	
	this.editComment = function(range, fileID) {
                if (current_dialog != null){
                        return;
                }
		var text = $('#ctext' + this.rangeToString(range)).html();
		current_dialog = $('<div></div>')
			.html('<textarea>' + text +'</textarea>')
			.dialog({
				autoOpen: true,
				title: 'Enter Comment',
				width: 350,
				height: 250,
				focus: true,
				buttons: { "Submit": 
						function() {
							comment = code_files[fileID];
							comment.removeAndSubmitComment(range); 
						}, "Delete":
						function() {
							comment = code_files[fileID];
							comment.removeComment(range);
						}
						}, 
		});
		$("textarea").focus();
	}

	this.addHandlers();
}

// CodeFile.closeButtonClicked = function(event) {
// 	var range = event.data.range;
// 	event.data.code_file.removeComment(range);
// 
// 	return false;
// }

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
		code_file.unhiliteLineNo(i);
	}
	for (var i = old_range.higher; i > range.higher; i--) {
		code_file.unhiliteLineNo(i);
	}

	code_file.selected_range_end = line_no;
}

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
}

$(document).mouseup(function() {
	if (dragging_in_file == null) {
		return;
	}

	var range = new LineRange(dragging_in_file.selected_range_start, dragging_in_file.selected_range_end);
	dragging_in_file.addCommentBox(range);
	dragging_in_file.selected_ranges.push(range);
	dragging_in_file = null;
});
