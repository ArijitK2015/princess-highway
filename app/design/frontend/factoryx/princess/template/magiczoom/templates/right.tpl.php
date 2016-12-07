<?php
$m = intval(self::$options->getValue('selectors-margin'));
$ws = intval(self::$options->getValue('selector-max-width'));
$wm = intval(self::$options->getValue('thumb-max-width'));
$hm = intval(self::$options->getValue('thumb-max-height'));

$squareImage = true;
$visibleXsImageW = 640;
$visibleXsImageH = 960;
?>
<!-- Begin magiczoom -->
<div class="MagicToolboxContainer row" <?php /*style="width: <?php echo ($wm + $ws + $m)?>px"*/ ?>>
    <!--
    Extra small devices
    Phones (<768px)
    use this onsale-product-container on xs devices as MagicToolboxMainContainer is hidden
    -->
    <div class="onsale-product-container hidden-sm hidden-md hidden-lg">
        <?php echo Mage::helper('onsale')->getProductLabelHtml(Mage::registry('current_product')); ?>
    </div>
    <!--
    Small + Medium + Large devices
    Tablets (>=768px)
    -->
    <div class="MagicToolboxMainContainer col-md-9 col-lg-9 col-sm-9 col-xs-12 hidden-xs" <?php /*style="width: <?php echo $wm?>px" */ ?>>
        <?php
        /*
        // not sure about this ???
        if (count($thumbs) > 1): ?>
            <button type="button" data-toggle="offcanvas" data-target="#thumbsMenu" data-canvas="body" class="btn btn-primary full-width-button hidden-md hidden-lg hidden-sm" id="toggleThumbsMenu">
                <?php echo 'More Images'; ?>
            </button>
        <?php endif;
        */
        ?>
        <div class="onsale-product-container row">
            <?php echo Mage::helper('onsale')->getProductLabelHtml(Mage::registry('current_product')); ?>
            <?php echo $main?>
        </div>
        <?php if(isset($message)):?>
            <div class="MagicToolboxMessage"><?php echo $message?></div>
        <?php endif?>

        <div class="hidden">
            Click <a id="lightboxLink" class="lightbox" href="" rel="lightBoxMagicZoom">here</a> for full size images
        </div>

        <div class="hidden" id="linksContainer"></div>
    </div>
    <!--
    Extra small devices
    Phones (<768px)
    -->
    <div class="swiper-container hidden-sm hidden-md hidden-lg">
        <div class="swiper-wrapper">
            <div class="loading">
                <img src="/skin/frontend/base/default/css/magiczoom/graphics/loader.gif" /><div style="margin:10px 3px;">loading images...</div>
            </div>
        <?php foreach(Mage::registry('current_product')->getMediaGalleryImages() as $_image): ?>
            <?php if (!preg_match('/swatch/i',$_image->getLabel())): ?>
                <div class="swiper-slide">
                    <img src="<?php 
                        if ($squareImage) {
                            echo Mage::helper('catalog/image')->init(Mage::registry('current_product'), 'thumbnail', $_image->getFile())->resize($visibleXsImageW);
                        }
                        else {
                            echo Mage::helper('catalog/image')->init(Mage::registry('current_product'), 'thumbnail', $_image->getFile())->resize($visibleXsImageW, $visibleXsImageH);
                        }
                        ?>"
                        alt="<?php echo Mage::helper('catalog')->htmlEscape($_image->getLabel()) ?>" title="<?php echo Mage::helper('catalog')->htmlEscape($_image->getLabel()) ?>" />
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        </div>
        <div class="pagination-wrapper pagination-fontawesome">
            <div class="pagination"></div>
        </div>
    </div>
    <?php if(count($thumbs) > 1):?>
        <div class="product-image-thumbs col-md-3 col-lg-3 col-sm-3 hidden-xs">
            <div id="thumbCarousel" class="carousel vertical slide <?php if (count($thumbs) <= 4): ?>noslide<?php endif; ?>">
                <?php if (count($thumbs) > 4): ?>
                    <a class="left carousel-control" href="#thumbCarousel" data-slide="prev"><i class="fa fa-angle-up fa-lg"></i></a>
                <?php endif; ?>
                <div class="carousel-inner">
                    <?php $active = false; ?>
                    <?php $i = 0; ?>
                    <?php foreach($thumbs as $thumb): ?>
                        <?php if (!$i): ?>
                            <div class="item<?php if (!$active){ echo ' active'; $active = true; } ?>">
                                <div class="row-fluid">
                                    <table class="center-block">
                                        <tbody class="center-block">
                        <?php endif; ?>
                            <tr class="center-block">
                                <td class="center-block">
                                    <span>
                                        <?php echo $thumb; ?>
                                    </span>
                                </td>
                            </tr>

                        <?php if (++$i == 4): ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php $i = 0; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if ($i): ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if (count($thumbs) > 4): ?>
                    <a class="right carousel-control" href="#thumbCarousel" data-slide="next"><i class="fa fa-angle-down fa-lg"></i></a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif?>
    <div class="clearfix"></div>
</div>
<!-- End magiczoom -->
<script type="text/javascript">
jQuery(window).load(function() {
    try {
        jQuery("#lightboxLink").attr('href',jQuery('.MagicZoomBigImageCont img').attr('src'));
        jQuery('#thumbCarousel a:not(.carousel-control)').clone().appendTo('#linksContainer');
        jQuery('#linksContainer a').addClass('lightbox');
        jQuery('#linksContainer a').attr('rel','lightBoxMagicZoom');
        jQuery('a.lightbox').lightbox({fitToScreen:true});
        jQuery('#lightboxLink').parent().removeClass('hidden');
    }
    catch(err) {
        console.log(err.message);
    }
});
jQuery('.product-image-thumbs a').on('click',function(){
    jQuery("#lightboxLink").attr('href',jQuery(this).attr('href'));
});
jQuery(window).load(function(){
    jQuery('#thumbCarousel a > img').addClass('img-responsive');
    var mySwiper = new Swiper('.swiper-container',{
        mode:'horizontal',
        speed : 500,
        calculateHeight: 'true',
        loop: true,
        pagination: '.pagination',
        paginationClickable: true,
        paginationBulletRender: function (index, className) {
            return '<i class="fa fa-circle ' + className + '"></i>';
        }
    });
    jQuery('.swiper-container.hidden-sm.hidden-md.hidden-lg .swiper-wrapper .loading').hide();
    jQuery('.swiper-container').css("zIndex", 1);
    jQuery('.swiper-container.hidden-sm.hidden-md.hidden-lg .swiper-wrapper .swiper-slide > img').fadeIn(1000);
});

</script>
<style>
.swiper-container {width:98%}
.swiper-slide img {width:100%}
.swiper-wrapper .loading {
    width: 100%;
    height: 60px;
    background-color: #fff;
    padding-top: 20px
}
/* go +1 above onsale-product-container */
.swiper-container.hidden-sm.hidden-md.hidden-lg .swiper-wrapper .swiper-slide > img {
    display:none
}
.swiper-container {
    z-index: 101
}
</style>