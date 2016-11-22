function toggleCaption(id){
    if(jQuery('#toggle-'+id).hasClass('show')) {
        jQuery('#toggle-'+id).removeClass('show');
        jQuery('#toggle-'+id).addClass('hide');
        jQuery('#toggle-'+id).text('Hide Caption');
    } else {
        jQuery('#toggle-'+id).addClass('show');
        jQuery('#toggle-'+id).removeClass('hide');
        jQuery('#toggle-'+id).text('Show Caption');
    }
    jQuery('#'+id+' .caption-text').toggle();
}

function showCaptions(){
    jQuery('.caption-text').show();
}

function hideCaptions(){
    jQuery('.caption-text').hide();
}

function editCaption(id){
    var captionEle = jQuery('#'+id+' .caption-text');
    var oldCaption = captionEle.html();
    var newCaption = prompt("Enter new caption",oldCaption);
    if (newCaption != oldCaption){
        captionEle.html(newCaption);
        captionEle.addClass('updated');
    }
}

function approveImage(id, url) {
    new Ajax.Request(url, {
        parameters: {isAjax: 1, method: 'POST', id: id},
        onSuccess: function(transport) {
            try{
                response = eval('(' + transport.responseText + ')');
            } catch (e) {
                response = {};
            }
            if (response.success) {
                $(id).remove();
            } else {
                var msg = response.error_messages;
                if (typeof(msg)=='object') {
                    msg = msg.join("\n");
                }
                if (msg) {
                    $('review-please-wait').hide();
                    alert(msg);
                    return;
                }
            }
            $('review-please-wait').hide();
            alert('Unknown Error. Please try again later.');
            return;
        },
        onFailure: function(){
            alert('Server Error. Please try again.');
            $('review-please-wait').hide();
        }
    });
    return false;
}

function deleteImage(id,url) {
    if (confirm("Delete this Instagram photo?")){
        new Ajax.Request(url, {
            parameters: {isAjax: 1, method: 'POST', id: id},
            onSuccess: function (transport) {
                try {
                    response = eval('(' + transport.responseText + ')');
                } catch (e) {
                    response = {};
                }
                if (response.success) {
                    $(id).remove();
                } else {
                    var msg = response.error_messages;
                    if (typeof(msg) == 'object') {
                        msg = msg.join("\n");
                    }
                    if (msg) {
                        $('review-please-wait').hide();
                        alert(msg);
                        return;
                    }
                }
                $('review-please-wait').hide();
                alert('Unknown Error. Please try again later.');
                return;
            },
            onFailure: function () {
                alert('Server Error. Please try again.');
                $('review-please-wait').hide();
            }
        });
    }
    return false;
}

function checkUrl(link) {
    if (((link.toLowerCase().indexOf("http://") != 0) && (link.toLowerCase().indexOf("https://") != 0)) || (link.toLowerCase().indexOf("sid=") != -1)) {
        return false;
    }
    return true;
}

function submitMerchandising() {
    jQuery('input.invalid').removeClass('invalid');
    jQuery('#image_data').attr('value', jQuery('ul.sortable').sortable('toArray'));
    var links = {};
    var captions = {};
    var flag = true;
    jQuery('li.item').each(function () {
        var image_id = jQuery(this).attr('id');
        var link = jQuery(this).children('.link').children('input').val();
        if ((link) && !checkUrl(link)){
            jQuery(this).children('.link').children('input').addClass('invalid');
            flag = false;
        }
        links[image_id] = link;
        var updated_caption = jQuery(this).children('.caption-text.updated');
        if (updated_caption.length > 0){
            captions[image_id] = updated_caption.html();
        }
    });
    jQuery('#links_data').val(JSON.stringify(links));
    jQuery('#captions_data').val(JSON.stringify(captions));

    if (flag){
        jQuery('#pos_form').submit();
    }
}

jQuery(window).load(function() {
    var list = jQuery('ul.sortable');
    jQuery('#instagram_tabs_approved_tab').on('click', function () {
        list.imagesLoaded(function () {
            list.isotope({
                transformsEnabled: false,
                filter: '.item',
                layoutMode: 'masonry',
                masonry: {
                    gutter: 10
                },
                transitionDuration: 0,
                itemSelector: '.isotopey',
                onLayout: function () {
                    //list.css('overflow', 'visible');
                }
            });
            list.sortable({
                cursor: 'move'
                //, tolerance: 'intersection'  //'pointer' is too janky
                , start: function (event, ui) {
                    //add grabbing and moving classes as user has begun
                    //REMOVE isotopey so that isotope does not try to sort our item,
                    //resulting in the item moving around and flickering on 'change'
                    ui.item.addClass('grabbing moving').removeClass('isotopey');

                    ui.placeholder
                        .addClass('starting') //adding the 'starting' class removes the transitions from the placeholder.
                        //remove 'moving' class because if the user clicks on a tile they just moved,
                        //the placeholder will have 'moving' class and it will mess with the transitions
                        .removeClass('moving')
                        //put placeholder directly below tile. 'starting' class ensures the
                        //placeholder simply appears and does not 'fly' into place
                        .css({
                            top: ui.originalPosition.top
                            , left: ui.originalPosition.left
                        })
                    ;
                    //reload the items in their current state to override any previous
                    //sorting and to include placeholder, but do NOT call a re-layout
                    list.isotope('reloadItems');
                }
                , change: function (event, ui) {
                    //change only fires when the DOM is changed. the DOM changes when
                    //the placeholder moves up or down in the document order
                    //within the sortable container

                    //remove 'starting' class so that placeholder can now move smoothly
                    //with the interface
                    ui.placeholder.removeClass('starting');
                    //reload items to include the placeholder's new position in the DOM.
                    //then when you sort, everything around the placeholder moves as
                    //though the item were moving it.
                    list
                        .isotope('reloadItems')
                        .isotope({sortBy: 'original-order'})
                    ;
                }
                , beforeStop: function (event, ui) {
                    //in this event, you still have access to the placeholder. this means
                    //you know exactly where in the DOM you're going to place your element.
                    //place it right next to the placeholder. jQuery UI Sortable removes the
                    //placeholder for you after this event, and actually if you try to remove
                    //it in this step it will throw an error.
                    ui.placeholder.after(ui.item);
                }
                , stop: function (event, ui) {
                    //user has chosen their location! remove the 'grabbing' class, but don't
                    //kill the 'moving' class right away. because the 'moving' class is
                    //preventing your item from having transitions, you should keep it on
                    //until isotope is done moving everything around. it will "snap" into place
                    //right where your placeholder was.

                    //also, you must add the 'isotopey' class back to the box so that isotope
                    //will again include your item in its sorting list
                    ui.item.removeClass('grabbing').addClass('isotopey');
                    //reload the items again so that your item is included in the DOM order
                    //for isotope to do its final sort, which actually won't move anything
                    //but ensure that your item is in the right place
                    list
                        .isotope('reloadItems')
                        .isotope({sortBy: 'original-order'}, function () {
                            //finally, after sorting is done, take the 'moving' class off.
                            //doing it here ensures that your item "snaps" and isn't resorted
                            //from its original position. since this happens on callback,
                            //if the user grabbed the tile again before callback is fired,
                            //don't remove the moving class in mid-grab

                            //for some reason in this code pen, the callback isn't firing predictably
                            console.log(ui.item.is('.grabbing'));
                            if (!ui.item.is('.grabbing')) {
                                ui.item.removeClass('moving');
                            }
                        });
                }
            });

        });
    });
});