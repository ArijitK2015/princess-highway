// If the cookie is present, the popup is showed
if(!jQuery.cookie("hasSubscribed") && !jQuery.cookie("hasRefused"))
{
	jQuery(document).ready(function(){jQuery("#popup").fadeIn();});
}