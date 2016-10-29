
/**
 * Backend Toolbar
 * Register to Backend.Renderer event listener
 *
 * @link http://getbootstrap.com/javascript/#affix
 * @link http://stackoverflow.com/questions/22899844/unaffix-event-for-bootstrap-affix
 * affix.bs.affix => before fixed positioning is applied to an element
 * affixed.bs.affix => after fixed positioning is applied to an element
 * affix-top.bs.affix => before a top element returns to its original (non-fixed) position
 * affixed-top.bs.affix => after a top element returns to its original (non-fixed) position
 * affix-bottom.bs.affix => before a bottom element returns to its original (non-fixed) position
 * affixed-bottom.bs.affix => after a bottom element returns to its original (non-fixed) position
 */
Backend.Renderer.addListener('docready', function(scope) {

    $(scope).find('.htmleditor textarea').off();
    $(scope).find('.htmleditor textarea[data-htmleditor]:not(.htmleditor-loaded)').each(function() {

        var id = $(this).attr('id');
        var configData = $(this).data('htmleditor');
        /*
        var config = {};
        try {
            config = JSON.parse(configData);
        } catch (ex) {
            console.error(ex);
        }
        **/
        console.log("Found HtmlEditor", id, configData);

        $("#" + id).addClass('htmleditor-loaded').tinymce(configData);
    });

});

Backend.Renderer.addListener('unload', function(scope) {
    $(scope).find('.htmleditor textarea.htmleditor-loaded').each(function() {

        var id = $(this).attr('id');
        console.log("Unloading HtmlEditor " + id);
        tinymce.execCommand('mceRemoveControl', true, id);
        $(this).remove();
    })
});
