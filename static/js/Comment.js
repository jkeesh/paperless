function /* class */ Comment(ctext, crange, code_file) {
	this.range = crange;
	this.text = ctext;
	this.code_file = code_file;
	this.filename = code_file.filename;
	var self = this;
	
	this.submit = function() {
		this.code_file.last_comment_range = this.range;
		var commentText = $("textarea").val();
		removeDialog();
		if(commentText.length == 0) {
			this.code_file.unhighlightRange(range);
		} else {
			this.text = commentText;
			this.code_file.addCommentDiv(commentText, self.range);
			this.code_file.last_comment = self;
			
			$.ajax({
				   type: 'POST',
				   url: window.location.pathname, // post to current location url
				   data: "action=create&text=" + commentText + "&rangeLower=" + this.range.lower + "&rangeHigher=" + this.range.higher + "&filename=" + this.filename,
				   success: function(data) {
				   //TODO
				   },
				   error: function(XMLHttpRequest, textStatus, errorThrown) {
				   //TODO
				   }
				   });

		}
		
		var elemname = '#ctext' + this.range.toString();
		//console.log("sumbit : " + elemname);
		//console.log( $(elemname) );
		
	}
	
	this.remove = function() {
		
		var elem = "#e"+self.range.toString();
		$(elem).remove(); // remove the comment
		this.code_file.unhighlightRange(self.range);
		
		//remove it from selected ranges
		for (i = 0; i < this.code_file.selected_ranges.length; i++) {
			var saved_range = this.code_file.selected_ranges[i];
			if (saved_range.lower == range.lower && saved_range.higher == range.higher) {
				this.code_file.selected_ranges.splice(i, 1);
			}
		}
		
		$('textArea').remove();
		removeDialog();
		
		var commentID = "c"+self.range.toString();
		this.code_file.removeCommentFromID(commentID);
		$("#" + commentID).remove();
	}
	
	this.get = function() {
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
				height: 250,
				focus: true,
				open: function(event, ui) { $(".ui-dialog-titlebar-close").hide();},
				closeOnEscape: false,
				buttons: { 
				"Submit": function() { self.submit(); },
				"Cancel": function() { self.cancel(); },
				},
				});
		
		$("textarea").focus();
	}
	
	this.edit = function() {
		
		current_range = this.range;
		current_file_id = this.code_file.fileID;
		
		if (current_dialog != null){
			return;
		}
		
		var text = $('#ctext' + this.range.toString()).html();		
		var elem = "#e"+self.range.toString();
		$(elem).remove(); // remove the comment
		
		// remove the old comment     
		$.ajax({
			   type: 'POST',
			   url: window.location.pathname, // post to current location url
			   data: "action=delete&rangeLower=" + this.range.lower + "&rangeHigher=" + this.range.higher + "&filename=" + this.filename,
			   success: function(data) { /* TODO */ },
			   error: function(XMLHttpRequest, textStatus, errorThrown) { /* TODO */ }
			   });
		
		current_dialog = $('<div></div>')
		.html('<textarea>' + text +'</textarea>')
		.dialog({
				autoOpen: true,
				title: 'Enter Comment',
				width: 350,
				height: 250,
				focus: true,
				open: function(event, ui) { $(".ui-dialog-titlebar-close").hide();},
				closeOnEscape: false,
				buttons: { 
				"Submit":  function() { self.submit(); }, 
				"Delete":  function() { self.remove(); }
				}, 
				});
		
		$("textarea").focus();
	}
	
	this.cancel = function(range){
		this.code_file.unhighlightRange(this.range);
		removeDialog();
	}
}