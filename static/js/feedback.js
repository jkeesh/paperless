code_files = [];
dragging_in_file = null;
current_dialog = null; // we will only have one dialog at a time
current_range = null;
globalSubmitComment = null;
current_file_id = 0;
shortcuts_added = false;
themes = new Array('shCoreDefault.css', 'shCoreMDUltra.css' ,'shCoreMidnight.css', 'shCoreDjango.css', 'shCoreRDark.css', 'shCoreEclipse.css', 'shCoreEmacs.css', 'shCoreFadeToGrey.css');	
themeID = 0;


function removeDialog(){
	if( $('textarea')) $('textarea').remove();
	if(current_dialog == null) return;
	current_dialog.dialog("close");
	current_dialog.dialog("destroy");
	current_dialog = null;
}

/* Creates a safe function that cannot be called more than once in
 * a period of 100 milliseconds. */
function SafeFunction(func){
	this.time = null;
	return function(){
		if(this.time != null){
			var now = new Date();
			var diff = now.getTime() - this.time.getTime();
			if( diff < 100) {
				return;
			}
		}
		this.time = new Date();
		func();
	}
}

function addShortcuts(){
	shortcut.add("tab", new SafeFunction ( function() {
										 // console.log('hit tab');
										  var code_file = code_files[current_file_id];
										  //if(current_range && current_dialog) {
										  var comment = code_file.getCommentFromID("c" + current_range.toString());
										  code_file.comment_list.push(comment);
										  comment.submit();
										  //}
										  })
				 );
	
	shortcut.add("ctrl+0", new SafeFunction ( function(){
											  var code_file = code_files[current_file_id];
											  if(current_range && current_dialog) {
											  var comment = code_file.getCommentFromID("c" + current_range.toString());
											  code_file.removeCommentFromID("c" + current_range.toString());
											  comment.remove();
											  }
											  })
				 );
	
	shortcut.add("ctrl+z", new SafeFunction ( function(){
											 var code_file = code_files[current_file_id];
											 if(code_file.last_comment == null) return;
											 code_file.last_comment.edit();
											 code_file.last_comment = null;
											 })
				 );
	
	
	shortcut.add("ctrl+2", new SafeFunction ( function(){
											 themeID++;
											 var themeIndex = themeID % themes.length; 
											 var newTheme = root_url +'/static/js/syntaxhighlighter/styles/' + themes[themeIndex];
											 $('#syntaxStylesheet').attr('href', newTheme);
											 })
				 );
	
	
	shortcut.add("ctrl+1", new SafeFunction ( function(){
											 if($("#shortcuts").html() != null){
											 $("#shortcuts").remove();
											 }else{
											 $("body").append("<div id='shortcuts' style='width: 400px; font-family: Arial; position:absolute; top: 100px; left: 400px; -moz-border-radius: 8px; border-radius: 8px;"
															  +" font-size: 16px; background-color: black; color: white; padding: 60px; opacity:0.8;'>"
															  +	"<div class='keyboardTitle'>Keyboard Shortcuts</div>"
															  +	"<div><span class='keyboardShortcuts'>&lt;Tab&gt;: </span><span class='keyboardAction'>Submit</span></div>"
															  +	"<div><span class='keyboardShortcuts'>&lt;Ctrl&gt;+0: </span><span class='keyboardAction'>Delete</span></div>"
															  +   "<div><span class='keyboardShortcuts'>&lt;Ctrl&gt;+z:</span><span class='keyboardAction'> Edit Last Comment</span></div>"
															  +	"<div><span class='keyboardShortcuts'>&lt;Ctrl&gt;+1: </span><span class='keyboardAction'>Toggle Shortcuts</span></div>"
															  +   "<div><span class='keyboardShortcuts'>&lt;Ctrl&gt;+2: </span><span class='keyboardAction'>Change Theme</span></div>"
															  +	"</div>");
											 }
											 } )
				 );
}

$(document).mouseup(function() {
					if (dragging_in_file == null) {
					return;
					}
					
					var range = new LineRange(dragging_in_file.selected_range_start, dragging_in_file.selected_range_end);
					var comment = new Comment("", range, dragging_in_file);
					comment.get();
					
					dragging_in_file.comment_list.push(comment);
					dragging_in_file.selected_ranges.push(range);
					
					dragging_in_file = null;
					});
