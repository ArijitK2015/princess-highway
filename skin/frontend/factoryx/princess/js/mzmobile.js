function toggleMagicZoom()
{
    var boundWidth = 970;
    if (document.body.clientWidth < boundWidth)
    {
        // Mobile only
        MagicZoom.options = {
            'disable-zoom': true
        };
    }
    else
    {
        // Desktop only
        MagicZoom.options = {
            'disable-zoom': false
        };
    }
    try {
        MagicZoom.refresh();
    }
    catch(err) {
        console.log(err.message);
    }
}

jQuery(window).load(function(){
    toggleMagicZoom();
});
jQuery(window).resize(function(){
    toggleMagicZoom();
});