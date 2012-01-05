/*
*	Upload files to the server using HTML 5 Drag and drop the folders on your local computer
*
*	Tested on:
*	Mozilla Firefox 3.6.12
*	Google Chrome 7.0.517.41
*	Safari 5.0.2
*	Safari na iPad
*	WebKit r70732
*
*	The current version does not work on:
*	Opera 10.63 
*	Opera 11 alpha
*	IE 6+
*/

// Setup a simple uploader, and give us a reference to the upload function  
function setup_simple_uploader(upload){
    
    $("#simple_title").click(function(){
        $("#full_simple_upload").show();
    });
    
    // https://developer.mozilla.org/en/DOM/FileList
    $("#uploadbutton").click(function(){
        var elem = document.getElementById('simpleupload');
        var files = elem.files;
        for(var i = 0; i < files.length; i++){
            // let system respond
            setTimeout(upload, 2000*i, files[i]);
        }
    });
}

function delete_code_file(e){
    var elem = $(this);
    e.preventDefault();
    D.log(e);
    D.log($(this));
    var file_to_delete = $(this).attr('data-filename');
    var assn = $(this).attr('data-assn');
    D.log(assn);
    D.log(file_to_delete);
    
    $.ajax({
        type: 'POST',
        dataType: 'JSON',
        data : {
            file: file_to_delete,
            assn: assn,
            action: 'delete_file'
        },
        success: function(resp){
            D.log(resp);
            if(resp.status == 'ok' && resp.remove){
                D.log("removing");
                D.log($(elem));
                $(elem).parent().fadeOut();
            }
        },
        error: function(jqXHR, status, error){
            D.log(jqXHR);
            D.log(status);
            D.log(error);
        }
    });
}

/*
 * Show the result of upload, and bind allowing this file to be deleted.
 *
 * Jeremy Keeshin Jan 5, 2012
 * @param   options optiosn object
 *      - show  location to add this
 *      - file  file information
 */
function show_result_and_allow_removal(options){
    // Create removal link    	
    D.log(options);			
	var remove = $('<a href="#" class="remove_file">').html("Remove");
	$(remove).attr('data-filename', options.file.name);
	$(remove).attr('data-assn', options.assn);

	$(remove).click(delete_code_file)

	var result = $('<div>').html("Loaded " + options.file.name + " ").append(remove);
	$('#'+options.show).append(result);

	D.log(result);
}


function uploader(place, status, targetPHP, show, assndir) {
	
	// Upload image files
	upload = function(file) {
		//console.log('uploading the file');
		//console.log(file);
		// Firefox 3.6, Chrome 6, WebKit
		if(window.FileReader) { 
				
			// Once the process of reading file
			this.loadEnd = function() {
				bin = reader.result;				
				xhr = new XMLHttpRequest();
				xhr.open('POST', targetPHP+'?up=true&assndir='+assndir, true);
				var boundary = 'xxxxxxxxx';
	 			var body = '--' + boundary + "\r\n";  
				body += "Content-Disposition: form-data; name='upload'; filename='" + file.name + "'\r\n";  
				body += "Content-Type: application/octet-stream\r\n\r\n";  
				body += bin + "\r\n";  
				body += '--' + boundary + '--';      
				xhr.setRequestHeader('content-type', 'multipart/form-data; boundary=' + boundary);
				// Firefox 3.6 provides a feature sendAsBinary ()
				if(xhr.sendAsBinary != null) { 
					xhr.sendAsBinary(body); 
				// Chrome 7 sends data but you must use the base64_decode on the PHP side
				} else {
					xhr.open('POST', targetPHP+'?up=true&base64=true&assndir='+assndir, true);
					xhr.setRequestHeader('UP-FILENAME', file.name);
					xhr.setRequestHeader('UP-SIZE', file.size);
					xhr.setRequestHeader('UP-TYPE', file.type);
					xhr.send(window.btoa(bin));
				}
				if (show) {
				    // Added by Jeremy
					show_result_and_allow_removal({
					    show: show,
					    file: file,
					    assn: assndir
					});
	
				}
				if (status) {
					document.getElementById(status).innerHTML = 'Loaded : 100%<br/>Next file ...';
				}
				
				xhr.onreadystatechange = function() {
				        //console.log(xhr.readyState);
						if(xhr.readyState == 4){
							if(xhr.status == 200){
								//console.log('success');
								//console.log(xhr.responseXML);
								//console.log(xhr.getAllResponseHeaders());
								//console.log("RESPONSE TEXT: " + xhr.responseText);
							}else{
								//console.log('fail');
							}

						}
				};
			}
				
			// Loading errors
			this.loadError = function(event) {
				switch(event.target.error.code) {
					case event.target.error.NOT_FOUND_ERR:
						document.getElementById(status).innerHTML = 'File not found!';
					break;
					case event.target.error.NOT_READABLE_ERR:
						document.getElementById(status).innerHTML = 'File not readable!';
					break;
					case event.target.error.ABORT_ERR:
					break; 
					default:
						document.getElementById(status).innerHTML = 'Read error.';
				}	
			}
		
			// Reading Progress
			this.loadProgress = function(event) {
				if (event.lengthComputable) {
					var percentage = Math.round((event.loaded * 100) / event.total);
					document.getElementById(status).innerHTML = 'Loaded : '+percentage+'%';
				}				
			}
				
			// Preview images
			this.previewNow = function(event) {		
				bin = preview.result;
				// var img = document.createElement("img"); 
				// img.className = 'addedIMG';
				// 			    img.file = file;   
				// 			    img.src = bin;
				// document.getElementById(show).appendChild(img);
				
				// var prev = document.createElement("div"); 
				// prev.className = 'addedIMG';
				// prev.innerHTML = file;
				// document.getElementById(show).appendChild(prev);
			}

		reader = new FileReader();
		// Firefox 3.6, WebKit
		if(reader.addEventListener) { 
			reader.addEventListener('loadend', this.loadEnd, false);
			if (status != null) 
			{
				reader.addEventListener('error', this.loadError, false);
				reader.addEventListener('progress', this.loadProgress, false);
			}
		
		// Chrome 7
		} else { 
			reader.onloadend = this.loadEnd;
			if (status != null) 
			{
				reader.onerror = this.loadError;
				reader.onprogress = this.loadProgress;
			}
		}
		var preview = new FileReader();
		// Firefox 3.6, WebKit
		if(preview.addEventListener) { 
			preview.addEventListener('loadend', this.previewNow, false);
		// Chrome 7	
		} else { 
			preview.onloadend = this.previewNow;
		}
		
		// The function that starts reading the file as a binary string
     	reader.readAsBinaryString(file);
	     
    	// Preview uploaded files
    	if (show) {
	     	preview.readAsDataURL(file);
	 	}
		
  		// Safari 5 does not support FileReader
		} else {
			xhr = new XMLHttpRequest();
			xhr.open('POST', targetPHP+'?up=true&assndir='+assndir, true);
			xhr.setRequestHeader('UP-FILENAME', file.name);
			xhr.setRequestHeader('UP-SIZE', file.size);
			xhr.setRequestHeader('UP-TYPE', file.type);
			xhr.send(file); 
			
			if (status) {
				document.getElementById(status).innerHTML = 'Loaded : 100%';
			}
			if (show) {
			    // added by jeremy				
				show_result_and_allow_removal({
				    show: show,
				    file: file,
				    assn: assndir
				});
			}	
		}				
	}




	// Function drop file
	this.drop = function(event) {
		event.preventDefault();
		
		//console.log('drop event');
		$("#drop").removeClass("thick");
		
	 	var dt = event.dataTransfer;
	 	var files = dt.files;
		//console.log("num files " + files.length);
	 	for (var i = 0; i<files.length; i++) {
			var file = files[i];
			D.log(file);
			//pass the parameters to upload as the final arguments of setTimeout
			//we will wait 2 seconds between calls to the let the system respond.
	 		setTimeout(upload, 2000*i, file);
		}
	
	}
	
	// The inclusion of the event listeners (DragOver and drop)

	this.uploadPlace =  document.getElementById(place);
	this.uploadPlace.addEventListener("dragover", function(event) {
		//console.log('drag over event');
		$("#drop").addClass("thick");
		event.stopPropagation(); 
		event.preventDefault();
	}, true);

	this.uploadPlace.addEventListener("drop", this.drop, false); 
	
    setup_simple_uploader(upload);
}

	