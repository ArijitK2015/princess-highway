<?php
$m = intval(self::$options->getValue('selectors-margin'));
$wm = intval(self::$options->getValue('thumb-max-width'));
?>

<!-- Begin magiczoom -->
<div class="MagicToolboxContainer" style="max-width: <?php echo $wm?>px">
	<div id="toolboxmessages">
		<?php if($main) echo $main?>

		<?php if(isset($message)):?>
			<div class="MagicToolboxMessage"><?php echo $message?></div>
		<?php endif?>
		
		<?php echo '<span class="MagicToolboxMessage2"><a href="javascript:void(0)" onclick="javascript:return showBig();">Click here for full size image</a></span>'; ?>
	</div>
	
    <?php if(count($thumbs) > 1):?>
    <div id="MagicToolboxSelectors<?php echo $pid?>" class="MagicToolboxSelectorsContainer<?php echo $magicscroll;?>" style="margin-top: <?php echo $m;?>px">
        <?php echo join("\n\t",$thumbs)?>
        <div style="clear: both"></div>
    </div>
    <?php if(!empty($magicscroll)): ?>
        <script type="text/javascript">
            MagicScroll.extraOptions.MagicToolboxSelectors<?php echo $pid?> = MagicScroll.extraOptions.MagicToolboxSelectors<?php echo $pid?> || {};
            MagicScroll.extraOptions.MagicToolboxSelectors<?php echo $pid?>.direction = 'right';
            <?php if(self::$options->checkValue('width', 0)): ?>
            MagicScroll.extraOptions.MagicToolboxSelectors<?php echo $pid?>.width = <?php echo $wm?>;
            <?php endif?>
        </script>
    <?php endif?>
    <?php endif?>

</div>
<!-- End magiczoom -->
<script type="text/javascript">
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
