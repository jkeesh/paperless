/*
 * removeDialog
 * ======================
 * This function removes a modal dialog from the view, but is
 * safe because it checks the existence of a textarea on the 
 * screen before removing
 */
function removeDialog(){
	if( $('textarea')) $('textarea').remove();
	if(current_dialog == null) return;
	current_dialog.dialog("close");
	current_dialog.dialog("destroy");
	current_dialog = null;
}

/*
 * Comment
 * ======================
 * This class handles all of the functionality for a comment, including all of
 * the main actions associated with a comment. These correspond generally to the
 * actions that the section leader can take on a comment. You can submit a comment,
 * edit a comment, delete a comment, or cancel the changes you make to a comment. 
 * These all generally correspond to the functions available in the comment class,
 * with a few utility functions as well as a general ajax function which allows
 * for comment modification requests with different actions
 *
 *
 * Parameters
 * ctext        the text of the comment
 * crange       the range of the comment
 * code_file    the code_file object that this comment belongs to
 * id           the id of the comment within the code file
 * commenter    the person who created this comment
 */
function /* class */ Comment(ctext, crange, code_file, id, commenter) {
	this.range = crange;
	this.text = ctext;
	this.code_file = code_file;
	this.filename = code_file.filename;
	var self = this;
	this.id = id;
	this.commenter = commenter;
	
	this.filter = function(text){
		text = text.replace(/&/g, '&amp;');		
		text = text.replace(/</g, '&lt;').replace(/>/g, '&gt;');
		return text;
	}
	
	this.unfilter = function(text){
		return text;
	}
	
	
	/* 
	 * this.ajax
	 * ==========================
	 * This function handles the ajax calls to modify a comment. It takes as a
	 * parameter the action to be taken on the comment, and checks for the results
	 * of the request before taking the appropriate action, or notifying the user.
	 *
	 * Parameters
	 * action       the action to be taken on the comment (create, delete)
	 */
	this.ajax = function(action){
		$.ajax({
			   type: 'POST',
			   url: window.location.pathname, // post to current location url
			   data: "action="+action+"&text=" + encodeURIComponent(this.text) + "&rangeLower=" + this.range.lower + "&rangeHigher=" + this.range.higher + "&filename=" + this.filename,
			   success: function(response) {
				    if(response && response.status == "ok"){ 
				        if(response.action == "create")
                            self.code_file.addCommentDiv(self.text, self.code_file.user, self.range, true, self.id);
				    }else{
					    alert("There was an error with this comment. Try refreshing the page.");
                    }        			
			   },
			   error: function(jqXHR, textStatus, errorThrown) {
			        alert("There was an error with this comment. Try refreshing the page.");
			   }
			   });
	}
	
	this.submit = function() {
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
			this.code_file.commentID++;
			this.code_file.last_comment = self;
			this.ajax("create");
		}	
	}
	
	this.remove = function() {
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
	}
	
	this.cancel = function(range){
		commentOpen = false;
		this.code_file.unhighlightRange(this.range);
		removeDialog();
	}
}