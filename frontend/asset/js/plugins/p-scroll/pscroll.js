(function($) {
    "use strict";

    function initPS(selector, opts) {
        var el = document.querySelector(selector);
        if (el) new PerfectScrollbar(el, opts);
    }
    initPS('.header-dropdown-list', { useBothWheelAxes: true, suppressScrollX: true, suppressScrollY: false });
    initPS('.notifications-menu', { useBothWheelAxes: true, suppressScrollX: true, suppressScrollY: false });
    initPS('.message-menu-scroll', { useBothWheelAxes: true, suppressScrollX: true, suppressScrollY: false });

})(jQuery);