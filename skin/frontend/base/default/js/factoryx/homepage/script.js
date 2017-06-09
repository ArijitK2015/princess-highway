// Responsive code begin
function ScaleHomepage() {
    var parentWidth = jQuery("#homepages").parent().width();
    jQuery('#homepages table').css('width',parentWidth);
}
ScaleHomepage();

jQuery(window).bind("load", ScaleHomepage);
jQuery(window).bind("resize", ScaleHomepage);
jQuery(window).bind("orientationchange", ScaleHomepage);
// Responsive code end

// Hover Image System
jQuery(document).ready(function(){
    jQuery('#homepages td').each(function(){
        jQuery(this).hoverdir();
    });
});