var AjaxManager = {};

/*
 * Let us easily add and manage ajax events.
 *
 * @param   options     the options for the ajax event
 *      url     the callback url
 *      event   the event to respond to
 *      elem    the element to respond to
 *      data    any ajax data
 *      
 *      remove  (optional) selector of elem to remove on success
 */
AjaxManager.add_ajax_event = function(options){
    $(options.elem).bind(options.event, function(){
        $.ajax({
            url : options.url,
            data : options.data,
            success: function(resp){
                if(options.remove){
                    $(options.remove).fadeOut();
                }
            }
        });
    });
}