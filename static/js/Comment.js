function removeDialog(){
	if( $('textarea')) $('textarea').remove();
	if(current_dialog == null) return;
	current_dialog.dialog("close");
	current_dialog.dialog("destroy");
	current_dialog = null;
}

function /* class */ Comment(ctext, crange, code_file, id) {
	this.range = crange;
	this.text = ctext;
	this.code_file = code_file;
	this.filename = code_file.filename;
	var self = this;
	this.id = id;
	
	this.filter = function(text){
		text = text.replace(/&/g, '&amp;');		
		text = text.replace(/</g, '&lt;').replace(/>/g, '&gt;');
		return text;
	}
	
	this.unfilter = function(text){
		return text;
	}
	
	this.ajax = function(action){
		$.ajax({
			   type: 'POST',
			   url: window.location.pathname, // post to current location url
			   data: "action="+action+"&text=" + encodeURIComponent(this.text) + "&rangeLower=" + this.range.lower + "&rangeHigher=" + this.range.higher + "&filename=" + this.filename,
			   success: function(data) {
					//TODO
			   },
			   error: function(jqXHR, textStatus, errorThrown) {
			        console.log(jqXHR.responseText);
			  		//alert("There was an error with the last comment. Please refresh the page.");
			   }
			   });
	}
	
	this.submit = function() {
		// console.log("submit");
		// console.log(this.code_file.comment_list);

		commentOpen = false;
		this.code_file.currentComment = null;
		this.code_file.last_comment_range = this.range;
		var commentText = $("textarea").val();
		commentText = this.filter(commentText);
		removeDialog();
		if(commentText.length == 0) {
			this.code_file.unhighlightRange(range);
		} else {			
			this.code_file.highlightRange(self.range);
			this.text = commentText;
			this.id = this.code_file.commentID;
			this.code_file.addCommentDiv(commentText, self.range, true, this.id);
			this.code_file.commentID++;
			this.code_file.last_comment = self;
			this.ajax("create");
		}
		
		// console.log("submit finish");
		// console.log(this.code_file.comment_list);
		
	}
	
	this.remove = function() {
		// console.log("remove");
		// console.log(this.code_file.comment_list);
		
		
		commentOpen = false;
		this.code_file.currentComment = null;
		var elem = ".e"+self.range.toString();
		var commentID = ".comment"+self.id;
		var fullClass = elem+commentID;
		$(elem+commentID).remove();
		this.code_file.unhighlightRange(this.range);		
		$('textArea').remove();
		removeDialog();		
		var commentID = "c"+self.range.toString();
		this.code_file.removeComment(this);
		$("." + commentID).remove();
		
		// console.log("remove finish");
		// console.log(this.code_file.comment_list);
		
	}
	
	this.get = function() {
		commentOpen = true;
		this.code_file.currentComment = this;
		var id = "#file" + this.code_file.fileID;
		var theclass = '.number' + this.range.lower;
		var commentLocation = $(theclass, id);
		current_range = this.range;
		
		current_dialog = $('<div></div>')
		.html('<textarea></textarea>')
		.dialog({
				autoOpen: true,
				title: 'Enter Comment',
				width: 350,
				height: 225,
				focus: true,
				open: function(event, ui) { $(".ui-dialog-titlebar-close").hide();},
				closeOnEscape: false,
				buttons: { 
				"Submit": function() { self.submit(); },
				"Cancel": function() { self.cancel(); },
				}
				});
		
		$("textarea").focus();
	}
	
	this.edit = function() {
		// console.log("edit");
		// console.log(this.code_file.comment_list);
		
		
		if(commentOpen) return;
		commentOpen = true;
		
		
		current_range = this.range;
		current_file_id = this.code_file.fileID;
		this.code_file.currentComment = this;
		
		if (current_dialog != null){
			return;
		}
		text = this.text;
		var commentID = ".comment"+self.id;
		var elem = ".e"+self.range.toString();
		var fullClass = elem+commentID;
		var thisCommentBox = $(elem+commentID);

		$(thisCommentBox).remove(); // remove the comment
		
		this.ajax("delete");
		
		current_dialog = $('<div></div>')
		.html('<textarea>' + text +'</textarea>')
		.dialog({
				autoOpen: true,
				title: 'Enter Comment',
				width: 350,
				height: 225,
				focus: true,
				open: function(event, ui) { $(".ui-dialog-titlebar-close").hide();},
				closeOnEscape: false,
				buttons: { 
				"Submit":  function() { self.submit(); }, 
				"Delete":  function() { self.remove(); },
				//"Cancel":  function() { self.submit(); },
				}
				});
		
		$("textarea").focus();
		
		// console.log("edit finish");
		// console.log(this.code_file.comment_list);
		
	}
	
	this.cancel = function(range){
		commentOpen = false;
		this.code_file.unhighlightRange(this.range);
		removeDialog();
	}
}