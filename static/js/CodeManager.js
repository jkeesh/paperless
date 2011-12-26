var CodeManager = {};

/* Hide all of the code files */
CodeManager.hide_all_files = function(){
    $('.code_container').hide();
    $('.selectedFile').removeClass('selectedFile');
}

/* 
 * Display an individual file with the given name
 * @param   file    {string}    the name of the file
 */
CodeManager.display_file = function(id){
    CodeManager.hide_all_files();
    $('.code_container[data-id="'+id+'"]').show();
    $('.filelink[data-id="'+id+'"]').addClass('selectedFile');
}

/*
 * Helper to get a property value from a data-* attribute.
 * @param   prop {string}       the name of the property
 */
CodeManager.get_property = function(elem, prop){
    return $(elem).attr(prop);
}

/* 
 * Return the name of a file based on its data-name attribute
 * @param   elem    {elem}      the DOM element
 */
CodeManager.get_name = function(elem){
    return CodeManager.get_property(elem, 'data-name');
}

CodeManager.get_id = function(elem){
    return CodeManager.get_property(elem, 'data-id');
}



/*
 * Setup the functionality when a user clicks the name of the 
 * file, then we should show that file. We get all of the file links
 * and get their name, and each time they are clicked, display that
 * file and hide the others.
 */
CodeManager.setup_file_selection = function(){
    $('.filelink a').each(function(idx, elem){
        var id = CodeManager.get_id(elem);
        $(this).click(function(e){
            e.preventDefault();
            CodeManager.display_file(id);
        });
    });
    CodeManager.display_file(0); // Display the first file.
}

CodeManager.setup_code_files = function(){
    CodeManager.code_files = [];
    D.log('setting up code files...');
    $('.code_container').each(function(idx, elem){
        var code_file = new CodeFile({
            filename: CodeManager.get_name(elem),
            id_number: CodeManager.get_id(elem),
            interactive: true,
            user: 'XXXXX'
        });
        code_file.setupComments();
        code_file.showComments();
        CodeManager.code_files.push(code_file);
    });
}

$(document).bind("status.finishedSyntaxHighlighting", CodeManager.setup_code_files);
// 
// // we need to wait for the syntax highlighter to finish before we access the modified dom
// $(document).ready(function(){
//      //setTimeout("setupCodeFiles()", 3000);
//      $(':checkbox').change(function() {
//      release();
//  });
// });
// 
// function setupCodeFiles(){
//  var i = {counter start=0};
//     {foreach from=$code_files item=info key=file}
//  var cur_file = new CodeFile('{$file}', {counter}, interactive, '{$display_name|escape}');
//  code_files.push(cur_file);
//  cur_file.setupComments();
//  {/foreach}


$(function(){
   CodeManager.setup_file_selection(); 
});