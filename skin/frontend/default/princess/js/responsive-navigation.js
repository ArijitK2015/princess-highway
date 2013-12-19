/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

    
 function runEffect(menu_panel_id) {
     
      $selected_panel = jQuery(menu_panel_id);
         
       // get effect type from
       var selectedEffect = "drop";
       // most effect types need no options passed by default
       var options = {};
       // some effects have required parameters
       if ( selectedEffect === "scale" ) {
           options = { percent: 0 };
       } else if ( selectedEffect === "size" ) {
           options = { to: { width: 200, height: 100 } };
       }

       // run the effect


       //alert('?? ! = ' + menu_panel_id);

       //$selected_panel.toggle( selectedEffect, options, 500 );
       $selected_panel.toggle("drop");
       //$selected_panel.toggle( selectedEffect, options, 500 );
     
}

function setMenu(menu_id, menu_panel_id) { 
    //alert('wewewewewewew! = ' + menu_id);
    //var clearTimeout;
    jQuery("#" + menu_id).click(function(){
        
        if(jQuery(window).width() > 962)
        {
        
            console.log('> 480');
            $clicked = jQuery(this);
            $selected_panel = jQuery("#" + menu_panel_id);

            jQuery(".sub").each(function() {
                if (jQuery(this).attr('id') != $selected_panel.attr('id')) { 
                   if (jQuery(this).is(':visible'))
                   {                

                     jQuery(this).toggle();
                   }

                }                                     
            });

            runEffect($selected_panel);

            $selected_panel.animate({
                opacity: 1,
            }, 400 );

            return false;
        }
    });
}


jQuery(document).ready(function(){
    
    // bind event to rsp-nav-toggle class in list_original.phtml
    jQuery(".rsp-nav-toggle").click(function(){                
        if(jQuery(window).width() < 979)
        {                    
            $clicked = jQuery(this);
            $selected_panel = jQuery(".rsp-nav-menu-toggle");
            $selected_panel.slideToggle("slow");
        }
    });    
});	
   


