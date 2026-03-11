(function($) {
	"use strict";
	/*----------------------------------------------------
	*  Background Image 
	*  Search Icon 
	*  Sidebar Popup 
	*  Responsive Menu 
	*  Header Sticky 
	*  Banner Three Slider 
	*  Project Slider 	
	*  Project Two 	
	*  Progress Bar 
	*  Features Active Hover 
	*  Testimonial 
	*  Testimonial Two 
	*  Testimonial Three 
	*  Testimonial Four 
	*  Services Two 
	*  Services Three 
	*  Blog One 
	*  Blog Three 
	*  Team One
	*  Project Active Hover
	*  CounterUp
	*  Video Popup
	*  Image Popup
	*  FAQ One
	*  Checkout page quantity selector
	*  Theme Loader
	*  Scroll To Top
	*  Horizontal Scroll
	*  Fancy Box
	*  Drag Cursor
	----------------------------------------------------*/

	///=============  Background Image  =============\\\
    $("[data-background]").each(function() {
        $(this).css("background-image", "url(" + $(this).attr("data-background") + ")");
    });

	///=============  Search Icon  =============\\\
    $(document).on('click', '.header__area-menubar-right-box-search-icon.open', function() {
        $('.header__area-menubar-right-box-search-box').fadeIn().addClass('active');
    });
    $(document).on('click', '.header__area-menubar-right-box-search-box-icon i', function() {
        $('.header__area-menubar-right-box-search-box').fadeOut().removeClass('active');
    });
    $(document).on('click', '.header__area-menubar-right-box-search-box form', function(e) {
        e.stopPropagation();
    });

	///=============  Sidebar Popup  =============\\\
    $(document).on('click', '.header__area-menubar-right-box-sidebar-popup-icon', function() {
        $('.header__area-menubar-right-sidebar-popup').addClass('active');
        $('.sidebar-overlay').addClass('show');
    });
    $(document).on('click', '.header__area-menubar-right-sidebar-popup .sidebar-close-btn, .sidebar-overlay', function() {
        $('.header__area-menubar-right-sidebar-popup').removeClass('active');
        $('.sidebar-overlay').removeClass('show');
    });

	///=============  Responsive Menu  =============\\\
    $(document).on('click', '.sidebar-menu-show-hide', function() {
        $('.responsive__menu').addClass('show');
        $('.menu-overlay').addClass('show');
    });
    $(document).on('click', '.menu-overlay, .close-hide-show', function() {
        $('.responsive__menu').removeClass('show');
        $('.menu-overlay').removeClass('show');
    });
    $(document).on('click', '.has-dropdown > a', function(e) {
        e.preventDefault();
        let $parent = $(this).parent();
        let $siblings = $parent.siblings(".has-dropdown");

        $siblings.removeClass("active").children(".responsive-sidebar-submenu").slideUp(200);

        if ($parent.hasClass("active")) {
            $parent.removeClass("active").children(".responsive-sidebar-submenu").slideUp(200);
        } else {
            $parent.addClass("active").children(".responsive-sidebar-submenu").slideDown(200);
        }
    });
		
	///============= Header Sticky =============\\\
	$(function() {
		let $window = $(window);
		let $header = $(".header__sticky");

		$window.on("scroll", function() {
			let scrollDown = parseInt($window.scrollTop(), 10); // Explicit radix added
			$header.toggleClass("header__sticky-sticky-menu", scrollDown >= 135);
		});
	});


	///=============  Banner Three Slider  =============\\\
    let bannerThree = '.banner__three-slider';
    let sliderThree = new Swiper(bannerThree, {
        loop: true,
        slidesPerView: 1,
        effect: 'fade',
        fadeEffect: {
            crossFade: false,
        },
        speed: 1000,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        navigation: {
            nextEl: '.banner__three .swiper-button-next',
            prevEl: '.banner__three .swiper-button-prev',
        },
        pagination: {
            el: ".banner-pagination",
            clickable: true,
        },
        observer: true,
        observeParents: true,
    });
    function animated_swiper(selector, init) {
        let animated = function animated() {
            $(selector + " .swiper-slide-active [data-animation]").each(function() {
                let anim = $(this).data("animation");
                let delay = $(this).data("delay");
                let duration = $(this).data("duration");
                $(this)
                    .removeClass(anim + " animated")
                    .addClass(anim + " animated")
                    .css({
                        webkitAnimationDelay: delay,
                        animationDelay: delay,
                        webkitAnimationDuration: duration,
                        animationDuration: duration,
                    })
                    .one("animationend", function() {
                        $(this).removeClass(anim + " animated");
                    });
            });
        };
        animated();
        init.on("slideChangeTransitionStart", function() {
            $(selector + " [data-animation]").removeClass("animated");
        });
        init.on("slideChangeTransitionEnd", animated);
    }
    animated_swiper(bannerThree, sliderThree);

	/*==========  Brand  ==========*/
	var swiper = new Swiper(".brand-slider-active", {
		loop: true,
		speed: 1500,
		spaceBetween: 30,
		autoplay: true,
		autoplay: {
			delay: 0,
		},
		breakpoints: {
			0: {
				slidesPerView: 2
			},
			575: {
				slidesPerView: 3
			},
			992: {
				slidesPerView: 4
			},
			1200: {
				slidesPerView: 6
			},
		}
	});

	/*==========  Service  ==========*/
	var swiper = new Swiper(".service-one__slider-active", {
		loop: true,
		speed: 2000,
		spaceBetween: 25,
		autoplay: true,
		autoplay: {
			delay: 3500,
		},
		breakpoints: {
			0: {
				slidesPerView: 1
			},
			766: {
				slidesPerView: 2
			},
			992: {
				slidesPerView: 2
			},
			1200: {
				slidesPerView: 3
			},
		}
	});

	///=============  Project Slider  =============\\\	
	var swiper = new Swiper(".project__one-slider", {
		spaceBetween: 25,
		speed: 2500,
		slidesPerView: 4,
		centeredSlides: true,
		loop: true,
		autoplay: {
			delay: 3000,
		},
		breakpoints: {
			0: {
				slidesPerView: 1
			},
			575: {
				slidesPerView: 2
			},
			992: {
				slidesPerView: 2
			},
			1200: {
				slidesPerView: 4
			},
		}
	});

	///=============  Project Two  =============\\\	
	var swiper = new Swiper(".project__two-slider", {
		spaceBetween: 25,
		speed: 2500,
		slidesPerView: 4,
		centeredSlides: true,
		loop: true,
		autoplay: {
			delay: 3000,
		},
		breakpoints: {
			0: {
				slidesPerView: 1
			},
			575: {
				slidesPerView: 2
			},
			992: {
				slidesPerView: 2
			},
			1200: {
				slidesPerView: 4
			},
		}
	});

    ///=============  Progress Bar  =============\\\
    if ($('.progress-bar-track-inner').length) {
        $('.progress-bar-track-inner').appear(function() {
            let el = $(this);
            let percent = parseInt(el.data('width'), 10);
            $(el).css('width', percent + '%');
        }, {
            accY: 0
        });
    }

    ///=============  Features Active Hover  =============\\\
    $(document).on('mouseenter', '.features-area-item', function() {
        $(".features-area-item").removeClass("features-area-item-hover");
        $(this).addClass("features-area-item-hover");
    });

	///=============  Testimonial  =============\\\
	var swiper = new Swiper(".testimonial__one-slider-active", {
		loop: true,
		spaceBetween: 30,
		slidesPerView: 1,
		fadeIn: true,
		speed: 1500,
		nav: true,
		navigation: {
			nextEl: '.testimonial__two .swiper-button-next',
			prevEl: '.testimonial__two .swiper-button-prev',
		},
		breakpoints: {
			768: {
				slidesPerView: 1,
				spaceBetween: 30,
			},
			1200: {
				slidesPerView: 1,
				spaceBetween: 30,
			},
		}
	});

	///=============  Testimonial Two  =============\\\
	var swiper = new Swiper(".testimonial__two-slider-active", {
		loop: true,
		spaceBetween: 30,
		slidesPerView: 1,
		fadeIn: true,
		speed: 1500,
		nav: true,
		navigation: {
			nextEl: '.testimonial__three .swiper-button-next',
			prevEl: '.testimonial__three .swiper-button-prev',
		},
		breakpoints: {
			768: {
				slidesPerView: 1,
				spaceBetween: 30,
			},
			1400: {
				slidesPerView: 2,
				spaceBetween: 30,
			},
		}
	});

	///=============  Testimonial Three  =============\\\
	var swiper = new Swiper(".testimonial__three-slider-active", {
		loop: true,
		spaceBetween: 30,
		slidesPerView: 1,
		fadeIn: true,
		speed: 1500,
		nav: true,
		navigation: {
			nextEl: '.swiper-button-next',
			prevEl: '.swiper-button-prev',
		},
		breakpoints: {
			768: {
				slidesPerView: 2,
				spaceBetween: 30,
			},
			1400: {
				slidesPerView: 3,
				spaceBetween: 30,
			},
		}
	});

	///=============  Testimonial Four  =============\\\
	var swiper = new Swiper(".testimonial__four-slider-active", {
		loop: true,
		spaceBetween: 30,
		slidesPerView: 1,
		centeredSlides: true,
		fadeIn: true,
		speed: 1500,
		breakpoints: {
			0: {
				slidesPerView: 1,
			},
			768: {
				slidesPerView: 1,
			},
			1025: {
				slidesPerView: 3,
			},
			1600: {
				slidesPerView: 4,
			},
		},
	});

	///=============  Services Two  =============\\\
	var swiper = new Swiper(".services__two-slider", {
		slidesPerView: 4,
		loop: true,
		speed: 1500,
		spaceBetween: 30,
		navigation: {
			nextEl: '.swiper-button-next',
			prevEl: '.swiper-button-prev',
		},
		breakpoints: {
			0: {
				slidesPerView: 1,
				spaceBetween: 20,
			},
			576: {
				slidesPerView: 2,
				spaceBetween: 20,
			},
			992: {
				slidesPerView: 3,
				spaceBetween: 20,
			},
			1400: {
				slidesPerView: 4,
				spaceBetween: 30,
			},
		}
	});

	///=============  Services Three  =============\\\
	var swiper = new Swiper(".services__three-slider", {
		slidesPerView: 3,
		loop: true,
		speed: 1500,
		spaceBetween: 30,
		breakpoints: {
			0: {
				slidesPerView: 1,
			},
			768: {
				slidesPerView: 2,
				spaceBetween: 20,
			},
			992: {
				slidesPerView: 3,
			},
		}
	});

	///=============  Blog One  =============\\\
	var swiper = new Swiper(".blog__one-slide-active", {
		slidesPerView: 3,
		loop: true,
		speed: 1500,
		spaceBetween: 30,
		breakpoints: {
			0: {
				slidesPerView: 1,
			},
			768: {
				slidesPerView: 2,
				spaceBetween: 20,
			},
			992: {
				slidesPerView: 2,
			},
			1400: {
				slidesPerView: 3,
			}
		}
	});

	///=============  Blog Three  =============\\\
	var swiper = new Swiper(".blog__three-slide-active", {
		slidesPerView: 2,
		loop: true,
		speed: 1500,
		spaceBetween: 30,
		breakpoints: {
			0: {
				slidesPerView: 1,
			},
			768: {
				slidesPerView: 1,
				spaceBetween: 20,
			},
			992: {
				slidesPerView: 2,
			},
			1400: {
				slidesPerView: 2,
			}
		}
	});

	///=============  Team One =============\\\
	var swiper = new Swiper(".team__one-slider-active", {
		slidesPerView: 3,
		loop: true,
		speed: 1500,
		spaceBetween: 30,
		navigation: {
			nextEl: '.swiper-button-next',
			prevEl: '.swiper-button-prev',
		},
		breakpoints: {
			0: {
				slidesPerView: 1,
			},
			768: {
				slidesPerView: 2,
				spaceBetween: 20,
			},
			992: {
				slidesPerView: 3,
			},
			1400: {
				slidesPerView: 3,
			}
		}
	});

    ///============= Project Active Hover =============\\\
    $(".project__three-single-card").on("mouseenter", function() {
        $(".project__three-single-card").removeClass("active");
        $(this).addClass("active");
    });

    ///============= CounterUp =============\\\
    var counter = $('.counter');
    counter.counterUp({
        time: 2500,
        delay: 100
    });

    ///============= Video Popup =============\\\
    $('.video-popup').magnificPopup({
        type: 'iframe'
    });

    ///============= Image Popup =============\\\
    $('.img-popup').magnificPopup({
        type: 'image',
        gallery: {
            enabled: true
        }
    });

    ///============= FAQ One =============\\\
    $(".faq-collapse-item-card-header").on("click", function() {
        if ($(this).next(".faq-collapse-item-card-header-content").hasClass("active")) {
            $(this).next(".faq-collapse-item-card-header-content").removeClass("active").slideUp()
            $(this).children("i").removeClass("fas fa-minus-circle").addClass("fas fa-plus-circle")
        } else {
            $(".faq-collapse-item-card-header-content").removeClass("active").slideUp()
            $(".faq-collapse-item-card-header i").removeClass("fas fa-minus-circle").addClass("fas fa-plus-circle");
            $(this).next(".faq-collapse-item-card-header-content").addClass("active").slideDown()
            $(this).children("i").removeClass("fas fa-plus-circle").addClass("fas fa-minus-circle")
        }
    });
    $(".faq-collapse-item-card").on("click", function() {
        $('.faq-collapse-item-card').removeClass('active');
        $(this).addClass('active');
    });

	///============= Checkout page quantity selector =============\\\
	$(function() {
		$(document).on("click", ".quantity-increaser", function() {
			let $selector = $(this).closest(".quantity-selector");
			let $quantity = $selector.find(".quantity");
			let currentValue = parseInt($quantity.text(), 10); // Explicit radix added
			let max = 20;
			
			if (currentValue < max) {
				$quantity.text(currentValue + 1);
			}
		});

		$(document).on("click", ".quantity-decreaser", function() {
			let $selector = $(this).closest(".quantity-selector");
			let $quantity = $selector.find(".quantity");
			let currentValue = parseInt($quantity.text(), 10); // Explicit radix added
			let min = 1;

			if (currentValue > min) {
				$quantity.text(currentValue - 1);
			}
		});
	});


    ///============= Theme Loader =============\\\
    $(window).on("load", function() {
        $(".loader").fadeOut(500);
    });

	///=============  Isotope  =============\\\
	$(window).on('load', function(){
		var $grid = $('.project-filter-active').isotope();
		$('.project__six-filter-button').on('click', 'button', function () {
			var filterValue = $(this).attr('data-filter');
			$grid.isotope({
				filter: filterValue
			});
			$(this).siblings('.active').removeClass('active');
			$(this).addClass('active');
		});
   });	
    ///============= Scroll To Top =============\\\
    var scrollPath = document.querySelector('.scroll-up path');
    var pathLength = scrollPath.getTotalLength();
    scrollPath.style.transition = scrollPath.style.WebkitTransition = 'none';
    scrollPath.style.strokeDasharray = pathLength + ' ' + pathLength;
    scrollPath.style.strokeDashoffset = pathLength;
    scrollPath.getBoundingClientRect();
    scrollPath.style.transition = scrollPath.style.WebkitTransition = 'stroke-dashoffset 10ms linear';
    var updatescroll = function() {
        var scroll = $(window).scrollTop();
        var height = $(document).height() - $(window).height();
        var scroll = pathLength - (scroll * pathLength / height);
        scrollPath.style.strokeDashoffset = scroll;
    }
    updatescroll();
    $(window).scroll(updatescroll);
    var offset = 50;
    var duration = 950;
    jQuery(window).on('scroll', function() {
        if (jQuery(this).scrollTop() > offset) {
            jQuery('.scroll-up').addClass('active-scroll');
        } else {
            jQuery('.scroll-up').removeClass('active-scroll');
        }
    });
    jQuery('.scroll-up').on('click', function(event) {
        event.preventDefault();
        jQuery('html, body').animate({
            scrollTop: 0
        }, duration);
        return false;
    });

    ///============= Horizontal Scroll =============\\\
    $('.horizontal-scroll-active').slick({
        speed: 2500,
        autoplay: true,
        autoplaySpeed: 0,
        centerMode: true,
        cssEase: 'linear',
        slidesToShow: 1,
        slidesToScroll: 1,
        variableWidth: true,
        infinite: true,
        initialSlide: 1,
        arrows: false,
        buttons: false,
        responsive: [{
                breakpoint: 1200,
                settings: {}
            },
            {
                breakpoint: 992,
                settings: {}
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                }
            }
        ]
    });

    ///============= Fancy Box =============\\\
    $('[data-fancybox]').fancybox({
        toolbar: true,
        smallBtn: false,
        iframe: {
            preload: true
        },
        buttons: [
            "zoom",
            "share",
            "slideShow",
            "fullScreen",
            "thumbs",
            "close"
        ],
    });
    ///============= Drag Cursor =============\\\
    const $dragCursor = $('.drag-cursor');
    $(window).on('mousemove', (e) => {
        $dragCursor.css({
            left: e.clientX + 'px',
            top: e.clientY + 'px',
        });
    });
    const $Atags = $('a');
    $Atags.on('mouseover', () => {
        $dragCursor.css('display', 'none');
    });
    $Atags.on('mouseout', () => {
        $dragCursor.css('display', 'flex');
    });
})(jQuery);
