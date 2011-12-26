var CodeManager = {};

/* Hide all of the code files */
CodeManager.hide_all_files = function(){
    $('.code_container').hide();
}

/* 
 * Display an individual file with the given name
 * @param   file    {string}    the name of the file
 */
CodeManager.display_file = function(file){
    CodeManager.hide_all_files();
    $('.code_container[data-name="'+file+'"]').show();
}

/* 
 * Return the name of a file based on its data-name attribute
 * @param   elem    {elem}      the DOM element
 */
CodeManager.get_name = function(elem){
    return $(elem).attr('data-name');
}

CodeManager.setup_file_selection = function(){
    $('.filelink a').each(function(idx, elem){
        var name = CodeManager.get_name(elem);
        $(this).click(function(e){
            e.preventDefault();
            CodeManager.display_file(name);
        });
    });
}


$(function(){
   CodeManager.setup_file_selection(); 
});