jQuery(document).ready(function(){
    jQuery('.menu-image').each(function(){
        var padding = jQuery(this).innerHeight() - jQuery(this).height();
        jQuery(this).parent().height(jQuery(this).find('img').height() + padding);
    });
});