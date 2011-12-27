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

Comment.get_comment_element = function(db_id){
    return $('.inlineComment[data-id="'+db_id+'"]');
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
//function /* class */ Comment(ctext, crange, code_file, id, commenter, db_id) {


/*
 * @param   options {Object}    the options object
 *  -   ctext       {string}    the comment text
 *  -   crange      {Object}    the LineRange object
 *  -   code_file   {Object}    the CodeFile object this comment belongs to
 *  -   id          {int}       the id of the comment within the code file
 *  -   db_id       {int}       the id of the comment in the database
 *  -   commenter   {string}    the display name of the user who created the comment
 */
function Comment(options){

    this.db_id = options.db_id; // the database id of the comment
    
	this.range = options.crange;
	this.text = options.ctext;
	this.code_file = options.code_file;
	this.filename = options.code_file.filename;
	var self = this;
	this.id = options.id;
	this.commenter = options.commenter;
	
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
	    var self = this;
	    D.log("Before... dbid " + this.db_id);
	    D.log(this.db_id);
		$.ajax({
			   type: 'POST',
			   url: window.location.pathname, // post to current location url
			   data: {
			       action: action,
			       text: this.text,
			       rangeLower: this.range.lower,
			       rangeHigher: this.range.higher,
			       filename: this.filename,
			       db_id: this.db_id
			   },
			   success: function(response) {
			       D.log(response);
				    if(response && response.status == "ok"){ 
				        if(response.action == "create"){
				            self.db_id = response.db_id;
				            self.code_file.addCommentDiv(self.text, self.code_file.user, self.range, true, self.id, response.db_id);
				        }
				    } else {
				        if(response.why){
				            alert(response.why);
				        }else{
				            alert("There was an error with this comment. Try refreshing the page.");
				        }
                    }        			
			   },
			   error: function(jqXHR, textStatus, errorThrown) {
			        alert("There was an error with this comment. Try refreshing the page.");
			   }
		});
		
		CodeManager.bind_editing();
		
	}
	
	/*
	* this.submit
	* =======================
	* Handle submitting a comment, highlighting the appropriate
	* range, and saving to the database.
	*/
	this.submit = function() {
	   // set state -- modal dialog no longer open
		commentOpen = false;
		this.code_file.currentComment = null;
		
		// remove the modal dialog
		var commentText = $("textarea").val();
		commentText = this.filter(commentText);
		removeDialog();
		
		if(commentText.length == 0) {
		   // no comment entered, unhighlight selected range
			this.code_file.unhighlightRange(self.range);
		} else {
		   // set state for this comment object
			this.text = commentText;
			this.id = this.code_file.commentID;
			
			// add this comment to the code file object
			this.code_file.highlightRange(self.range);
			this.code_file.commentID++;
			this.code_file.last_comment = self;
			
			// save this comment to database
			this.ajax("create");
		}
	}
	
	/*
	* this.remove
	* =====================
	* Remove this comment from the code view.  
	*/
	this.remove = function() {
	   // set state -- modal window no longer open
		commentOpen = false;
		this.code_file.currentComment = null;
		
		// remove the modal dialog box
		removeDialog();
		
		// remove comment highlighting and div
		this.code_file.unhighlightRange(self.range);
		this.code_file.removeComment(this);
		
		Comment.get_comment_element(this.db_id).remove();
	}
	
	/*
	* this.get
	* =====================
	* Open the modal dialog for this comment
	*/
	this.get = function() {
	   // set state -- comment dialog open
		commentOpen = true;
		this.code_file.currentComment = this;
		
		// add the modal dialog textarea
		current_range = this.range;
        var range_last_line = this.code_file.getLine(this.range.higher).first();
        var range_viewport_y = range_last_line.offset().top - window.pageYOffset;
		current_dialog = $('<div></div>')
		.html('<textarea></textarea><div class="modalMessage">Comments are formatted using <a target="_blank" href="http://daringfireball.net/projects/markdown/syntax">Markdown.</a><br/>  Ctrl+3 For simple markdown reference.</div>')
		.dialog({
				autoOpen: true,
				title: 'Enter Comment',
				width: 350,
				height: 250,
                position: ['center', range_viewport_y + 30],
				focus: true,
				open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); },
				closeOnEscape: false,
				buttons: { 
				   "Submit": function() { self.submit(); },
				   "Cancel": function() { self.cancel(); },
				}
		   });
		
		$("textarea").focus();
	}
	
	/*
	* this.edit
	* =====================
	* Open the modal dialog for an existing comment to
	* allow editing.
	*/
	this.edit = function() {
		if(commentOpen) return;
		   commentOpen = true;

      // set state to current comment
		current_range = this.range;
		current_file_id = this.code_file.fileID;
		this.code_file.currentComment = this;

      // if a dialog is already open, do nothing
		if (current_dialog != null) {
			return;
		}
		text = this.text;
		
		Comment.get_comment_element(this.db_id).remove();
		
		// delete this comment server-side
		this.ajax("delete");

        var range_last_line = this.code_file.getLine(this.range.higher).first();
        var range_viewport_y = range_last_line.offset().top - window.pageYOffset;
		
		// setup the new dialog with the text of the current comement
		current_dialog = $('<div></div>')
		.html('<textarea>' + text +'</textarea><div class="modalMessage">Comments are formatted using ' +
	         '<a target="_blank" href="http://daringfireball.net/projects/markdown/syntax">Markdown.</a><br/>' +
	          'Ctrl+3 For simple markdown reference.</div>')
		.dialog({
				autoOpen: true,
				title: 'Enter Comment',
				width: 350,
				height: 250,
                position: ['center', range_viewport_y + 30],
				focus: true,
				open: function(event, ui) { $(".ui-dialog-titlebar-close").hide();},
				closeOnEscape: false,
				buttons: { 
				   "Submit":  function() { self.submit(); }, 
				   "Delete":  function() { self.remove(); },
				}
		});
		
		$("textarea").focus();		
	}
	
	/*
	* this.cancel
	* =====================
	* Cancel the addition of a new comment
	*/
	this.cancel = function(range){
		commentOpen = false;
		this.code_file.unhighlightRange(this.range);
		removeDialog();
	}
}
