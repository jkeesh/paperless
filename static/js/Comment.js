function /* class */ Comment(ctext, crange, code_file) {
	this.range = crange;
	this.text = ctext;
	this.code_file = code_file;
	this.filename = code_file.filename;
	var self = this;
	
	this.filter = function(text){
//		text = text.replace(/<script>/g,'').replace(/<\/script>/g,'');
		text = text.replace(/</g, '&lt;').replace(/>/g, '&gt;');
		return text;
	}
	
	this.unfilter = function(text){
		return text;
	}
	
	this.submit = function() {
		commentOpen = false;
		this.code_file.last_comment_range = this.range;
		var commentText = $("textarea").val();
		commentText = this.filter(commentText);
		removeDialog();
		if(commentText.length == 0) {
			this.code_file.unhighlightRange(range);
		} else {
			this.text = commentText;
			this.code_file.addCommentDiv(commentText, self.range);
			this.code_file.last_comment = self;
			commentText = encodeURIComponent(commentText); //needed to keep +, other special chars sent in url
			//console.log(commentText);
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
	}
	
	this.remove = function() {
		commentOpen = false;
		var elem = "#e"+self.range.toString();
		$(elem).remove(); // remove the comment
		this.code_file.unhighlightRange(self.range);
		
		//remove it from selected ranges
		for (i = 0; i < this.code_file.selected_ranges.length; i++) {
			var saved_range = this.code_file.selected_ranges[i];
			if (saved_range && saved_range.lower == this.range.lower && saved_range.higher == this.range.higher) {
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
		commentOpen = true;
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
		
		if (current_dialog != null){
			return;
		}
		
		var unformatted = $('#htext'+this.range.toString());
		var text = $('#htext' + this.range.toString()).html();			
		text = this.unfilter(text);	
		
		var elem = "#e"+self.range.toString();
		console.log($(elem));
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
				height: 225,
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
	
	this.cancel = function(range){
		commentOpen = false;
		this.code_file.unhighlightRange(this.range);
		removeDialog();
	}
}