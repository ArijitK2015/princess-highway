$(document).ready(function() {

    $(".cart_item_scoll").mCustomScrollbar({
        autoHideScrollbar: true
    });

    $('.responsive_nav_ico').on('click', function(){
        $('.responsive_menu').slideToggle(200);
        $(this).toggleClass('nav_active');
    });

    $('.responsive_menu > ul > li.hav_r_dropdown > a').on('click', function(e){
        e.preventDefault();
        $(this).parent().parent().find('ul').not($(this).next('ul')).slideUp(200);
        $(this).next('ul').slideToggle(200);

        $(this).parent().parent().find('li').not($(this).parent()).removeClass('drop_inner_active');
        $(this).parent().toggleClass('drop_inner_active');
    });

    $('.responsive_footer > ul > li.have_footer_drop > a').on('click', function(){
        $(this).next('ul').slideToggle(200);
        $(this).parent().parent().find('li').not($(this).parent()).removeClass('active');
        $(this).parent().toggleClass('active');
    });

    $('.responsive_cart_ico').on('click', function(){
        $(this).parent().toggleClass('open');
    });

    $(window).resize(function(){
        if($(window).width() >= 768){
            $('body').addClass('disable_responsive_menu');
        }
        else{
            $('body').removeClass('disable_responsive_menu');
        }
    });

    $(window).on('scroll', function(){
        if($(window).scrollTop() >= 100){
            $('.goto_top_wrap').addClass('active');
        }
        else{
            $('.goto_top_wrap').removeClass('active');
        }
    });

    $('.goto_top').on('click', function(){
        $('body, html').animate({
            scrollTop: 0
        }, 500)
    });

    $('.imgToggler').on('mouseover', function(){
        $(this).slick("slickSetOption", "autoplay", true, true);
    });
    $('.imgToggler').on('mouseout', function(){
        $(this).slick("slickSetOption", "autoplay", false, true);
    });
    $('.imgToggler').each(function(){
        var p_slide_length = $(this).find('.imgtoggler_each').length;
        var _this = $(this);
        for(var i = 1; i <= p_slide_length; i++){
            if(i == 1){
                _this.next('.append_dots').append('<button type="button" class="active">'+ i +'</button>');
            }
            else{
                _this.next('.append_dots').append('<button type="button">'+ i +'</button>');
            }
        }
    });
    $('.imgToggler').on('beforeChange', function(event, slick, currentSlide, nextSlide){
        var currentIndex = $(this).slick('slickCurrentSlide');
        var buttonSelector = $(this).next('.append_dots').find('button');
        buttonSelector.removeClass('active');
        buttonSelector.eq(currentIndex-1).addClass('active');
    });
    $('.append_dots button').on('click', function(){
        var thisIndex = $(this).index();
        $(this).parent('.append_dots').prev('.imgToggler').slick('slickGoTo', thisIndex);
    });
    var imgToggler = $('.imgToggler').slick({
        fade: true,
        arrows: false,
        dots: false,
        pauseOnFocus: false,
        pauseOnHover: false,
        autoplaySpeed: 500,
        swipe: false,
        swipeToSlide: false
    });

    $('.f_anc, .remove_filter').on('click', function(){
        $('body').toggleClass('filter_active');
    });

    $('.p_thumbnail_slider').slick({
        vertical: true,
        verticalSwiping: true,
        slidesToShow: 4,
        slidesToScroll: 1,
        adaptiveHeight: true,
        focusOnSelect: true,
//        asNavFor: '.product_hero_slider',
        responsive: [
            {
                breakpoint: 992,
                settings: {
                    vertical: false,
                    verticalSwiping: false,
                }
            }
        ]
    });
    $('.product_hero_slider').slick({
        fade: true,
        arrows: false,
        asNavFor: '.p_thumbnail_slider',
        responsive: [
            {
                breakpoint: 569,
                settings: {
                    dots: true
                }
            }
        ]
    });

    $('#continue_to_step2').on('click', function(){
        $('#c2').collapse('show');
    });

    $('#subscribePopup').modal('show');

});