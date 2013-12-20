<script>
		alert ("wwwwwwwwwwwwwwwwwwwwwww  www");
function showBig() {
	src = jQuery('.MagicZoomBigImageCont img').attr('src');
	w = jQuery('.MagicZoomBigImageCont img').width();
	h = jQuery('.MagicZoomBigImageCont img').height();
	if (w > 800) {
		p = (800 / w);
		w = 800;
		h = h * p;
	}
	newWin = window.open(src,'bigImage','height=' + h + ',width=' + w);
	if (window.focus) {
		newWin.focus();
	}
	return false;
}
</script>
<?php
$skin = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN);
$theme = "frontend/default/princess";

$margin = 0;
$marginTop = intval(self::$options->getValue('selectors-margin'));
$thumbWidth = intval(self::$options->getValue('thumb-max-width'));
$count = 0;
?>

<!-- Begin magiczoom -->
<div class="MagicToolboxContainer" style="width:<?php echo $thumbWidth; ?>px;">
		<div id="toolboxmessages">
		    <?php if($main) echo $main; ?>
		
		    <?php if(isset($message)):?>
		        <div class="MagicToolboxMessage"><?php echo $message?></div>
		    <?php endif?>
			
			<?php echo '<span class="MagicToolboxMessage2"><a href="javascript:void(0)" onclick="javascript:return showBig();">Click here for full size image</a></span>'; ?>

		</div>
    
    
    <?php if(count($thumbs)):?>
    <div class="MagicToolboxContainerThumbs">
		<button class="button carousel-button" id="prev">
			<img alt="prev image" src="<?php echo sprintf('%s/%s/images/slider/arrow-prev.png', $skin, $theme); ?>" />
		</button>
		<div id="MagicToolboxSelectors<?php echo $pid?>" class="more-views MagicToolboxSelectorsContainer" style="margin-top: <?php echo $marginTop;?>px">
		    <center><nobr>
			<?php //<h4>echo $moviews</h4> ?>
			<ul style="text-align:center;">
			<?php foreach($thumbs as $thumb):?>
				<?php $count++; ?>
				<li style="display:inline;"><?php echo $thumb?></li>
                                <?php 
                                    /* Custom code: Shows only 5 thumbnails */
                                    if ($count >=5) {                                      
                                        break; 
                                    }                                
                                ?>
			<?php endforeach?>
			</ul>
			</nobr></center>
		</div>
		<button class="button carousel-button" id="next">
			<img alt="next image" src="<?php echo sprintf('%s/%s/images/slider/arrow-next.png', $skin, $theme); ?>" />	
		</button>
	</div>
    <?php endif?>
</div>
<script type="text/javascript">
jQuery(function() {
	if (typeof jQuery('#MagicToolboxSelectors<?php echo $pid?>') != 'undefined') {
		jQuery('#MagicToolboxSelectors<?php echo $pid?>').jCarouselLite({
			btnNext: "#next",
			btnPrev: "#prev",
			circular: true,
			visible: <?php echo ($count<4)?$count:4; ?>
		});
		<?php // Remove the title to hide the name of the thumbnails ?>
		jQuery('.MagicToolboxSelectorsContainer a').removeAttr("title");
		<?php if ($count<4): ?>
			<?php $margin = (4-$count)*35.5; ?>
			jQuery('#MagicToolboxSelectors<?php echo $pid?>').css("margin-left","<?php echo $margin?>px").css("margin-right","<?php echo $margin?>px");
		<?php endif; ?>
	}
});
</script>
<!-- End  -->