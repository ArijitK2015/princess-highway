jQuery(document).ready(function(){
    jQuery('.menu-image').each(function(){
        jQuery(this).css('left',jQuery(this).parent().width());
        var padding = jQuery(this).innerWidth() - jQuery(this).width();
        jQuery(this).find('img').css('height',jQuery(this).parent().height() - padding);

    });
});