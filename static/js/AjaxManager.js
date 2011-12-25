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
    $(document).ready(function(){
        $(options.elem).bind(options.event, function(e){
            e.preventDefault();
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url : options.url,
                data : options.data,
                success: function(resp){
                    D.log(resp);
                    if(options.remove){
                        $(options.remove).fadeOut('slow');
                    }
                },
                error: function(jqXHR, status, error){
                    D.log(jqXHR);
                    D.log(status);
                    D.log(error);
                }
            });
        });
    });
}