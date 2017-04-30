jQuery(document).ready(function() {
    jQuery('#thumbCarousel').carousel({
        interval: false
    });
    jQuery('#thumbCarouselOffcanvas').carousel({
        interval: false
    });
});
jQuery(window).on('load', function(){
    jQuery('#thumbCarousel').height(jQuery('#thumbCarousel div.item').first().height());
    jQuery('#thumbCarouselOffcanvas a').each(function(){
        var onClick = jQuery(this).attr('onclick');
        onClick += "jQuery('#toggleThumbsMenu').trigger('click');";
        jQuery(this).attr('onclick',onClick);
    });
});