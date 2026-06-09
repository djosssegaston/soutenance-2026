(function() {
    'use strict'

    // Ne pas exécuter sur mobile/tablette : le header y est position:relative
    if ($(window).width() <= 991) return;

    // Ne pas exécuter si le header n'existe pas
    if (!$(".sticky").length) return;

    let stickyElement = $(".sticky"),
     stickyClass = "sticky-pin",
     stickyPos = 66, //Distance from the top of the window.
     stickyHeight;


    ///Create a negative margin to prevent content 'jumps':
    stickyElement.after('<div class="jumps-prevent"></div>');

    function jumpsPrevent() {
        stickyHeight = stickyElement.innerHeight();
        stickyElement.css({ "margin-bottom": "-" + stickyHeight + "px" });
        stickyElement.next().css({ "padding-top": +stickyHeight + "px" });
    };
    jumpsPrevent(); //Run.

    // Recalculate after all assets (images) have loaded
    $(window).on('load', function() {
        jumpsPrevent();
    });

    //Function trigger:
    $(window).on('resize', function() {
        jumpsPrevent();

    });

    //Sticker function:
    function stickerFn() {
        let winTop = $(window).scrollTop();
        //Check element position:
        winTop >= stickyPos ?
            stickyElement.addClass(stickyClass) :
            stickyElement.removeClass(stickyClass) //Boolean class switcher.
    };
    stickerFn(); //Run.
    $(window).on('scroll',function() {
        stickerFn();
    });

    $('.app-sidebar').on('scroll', function() {
        let s = $(".app-sidebar .ps__rail-y");
        if (s[0].style.top.split('px')[0] <= 60 ) {
            $('.app-sidebar').removeClass('sidemenu-scroll')
        } else {
            $('.app-sidebar').addClass('sidemenu-scroll')
        }

    })

})();
