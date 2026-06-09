$(document).ready(function () {

    // product page
    $('.product-for').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        infinite: true,
        fade: true,
        asNavFor: '.product-to'
    });
    $('.product-to').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        asNavFor: '.product-for',
        dots: false,
        arrows: false,
        vertical: true,
        infinite: true,
        verticalSwiping: true,
        focusOnSelect: true
    });
});