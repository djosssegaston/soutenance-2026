var iconlist = ['bi-alarm','bi-alarm-fill','bi-align-bottom','bi-align-center','bi-align-end','bi-align-middle','bi-align-start','bi-align-top','bi-alt','bi-app','bi-app-indicator','bi-archive','bi-archive-fill','bi-arrow-90deg-down','bi-arrow-90deg-left','bi-arrow-90deg-right','bi-arrow-90deg-up','bi-arrow-bar-down','bi-arrow-bar-left','bi-arrow-bar-right','bi-arrow-bar-up','bi-arrow-clockwise','bi-arrow-counterclockwise','bi-arrow-down-circle','bi-arrow-down-circle-fill','bi-arrow-down-left','bi-arrow-down-left-circle','bi-arrow-down-left-circle-fill','bi-arrow-down-left-square','bi-arrow-down-left-square-fill','bi-arrow-down-right','bi-arrow-down-right-circle','bi-arrow-down-right-circle-fill','bi-arrow-down-right-square','bi-arrow-down-short','bi-arrow-down-square','bi-arrow-down-square-fill','bi-arrow-down-up','bi-arrow-left','bi-arrow-left-circle','bi-arrow-left-circle-fill','bi-arrow-left-right','bi-arrow-left-short','bi-arrow-left-square','bi-arrow-left-square-fill','bi-arrow-repeat','bi-arrow-return-left','bi-arrow-return-right','bi-arrow-right','bi-arrow-right-circle','bi-arrow-right-circle-fill','bi-arrow-right-short','bi-arrow-right-square','bi-arrow-right-square-fill','bi-arrows-angle-contract','bi-arrow-right-square-fill','bi-arrows-angle-expand','bi-arrows-collapse','bi-arrows-expand','bi-arrows-fullscreen','bi-arrows-move','bi-arrow-up','bi-arrow-up-circle','bi-arrow-up-circle-fill','bi-arrow-up-left','bi-arrow-up-left-circle','bi-arrow-up-left-circle-fill','bi-arrow-up-left-square','bi-arrow-up-left-square-fill','bi-arrow-up-right','bi-arrow-up-right-circle','bi-arrow-up-right-circle-fill','bi-arrow-up-right-square','bi-arrow-up-right-square-fill','bi-arrow-up-short','bi-arrow-up-square','bi-arrow-up-square-fill','bi-aspect-ratio','bi-aspect-ratio-fill','bi-asterisk','bi-at','bi-award','bi-award-fill','bi-back','bi-backspace','bi-backspace-fill','bi-backspace-reverse','bi-backspace-reverse-fill','bi-badge-3d','bi-badge-3d-fill','bi-badge-4k','bi-badge-4k-fill','bi-badge-8k','bi-badge-4k','bi-badge-8k-fill','bi-badge-ad','bi-badge-ad-fill','bi-badge-ar','bi-badge-ar-fill','bi-badge-cc','bi-badge-cc-fill','bi-badge-hd','bi-badge-hd-fill','bi-badge-tm','bi-badge-tm-fill','bi-badge-vo','bi-badge-vo-fill','bi-badge-vr','bi-badge-vr-fill','bi-badge-wc','bi-badge-wc-fill','bi-bag-check','bi-bag-dash','bi-bag-fill','bi-bag-plus','bi-bag-x','bi-bank','bi-bank2','bi-bar-chart','bi-bar-chart-fill','bi-bar-chart-line','bi-bar-chart-steps','bi-basket','bi-basket2','bi-basket3','bi-battery','bi-battery-charging','bi-battery-full','bi-battery-half','bi-bell','bi-bell-fill','bi-bell-slash','bi-bell-slash-fill','bi-book','bi-bookmarks','bi-bookmark-x','bi-bootstrap','bi-bootstrap-fill','bi-calculator','bi-calendar','bi-calendar2','bi-calendar2-check','bi-calendar2-date','bi-calendar2-day','bi-calendar2-event','bi-calendar2-fill','bi-calendar2-minus','bi-calendar2-month','bi-camera','bi-camera2','bi-camera-fill','bi-camera-reels','bi-camera-video','bi-capslock','bi-clock','bi-dash','bi-dash-circle','bi-dash-circle-dotted','bi-dash-circle-fill','bi-dash-lg','bi-diagram-2','bi-diamond','bi-dice-1','bi-display','bi-display-fill','bi-distribute-horizontal','bi-door-closed','bi-door-open','bi-dot','bi-earbuds','bi-easel','bi-egg','bi-eject','bi-emoji-angry','bi-eraser','bi-file','bi-facebook','bi-file-arrow-down','bi-file-arrow-down-fill','bi-file-binary','bi-file-break','bi-file-check','bi-file-code','bi-file-diff','bi-film','bi-gear','bi-gem','bi-gift',];
for (var i = 0, l = iconlist.length; i < l; i++) {
    let iconblock = document.createElement('div');
    iconblock.setAttribute('class', 'i-block');
    iconblock.setAttribute('data-clipboard-text', '' + iconlist[i]);
    iconblock.setAttribute('data-bs-toggle', 'tooltip');
    iconblock.setAttribute('data-filter', iconlist[i]);
    iconblock.setAttribute('title', '' + iconlist[i]);

    let iconmain = document.createElement('i');
    iconmain.setAttribute('class', '' + iconlist[i]);
    iconblock.appendChild(iconmain);
    document.querySelector('#icon-wrapper').append(iconblock);
}
window.addEventListener('load', (event) => {
    var iclp = new ClipboardJS('.i-block');
    iclp.on('success', function (e) {
        var targetElement = e.trigger;
        let iconbadge = document.createElement('span');
        iconbadge.setAttribute('class', 'ic-badge badge bg-success');
        iconbadge.innerHTML = "copied";
        targetElement.append(iconbadge);
        setTimeout(function () {
            targetElement.children[1].remove();
        }, 3000);
    });

    iclp.on('error', function (e) {
        var targetElement = e.trigger;
        let iconbadge = document.createElement('span');
        iconbadge.setAttribute('class', 'ic-badge badge bg-danger');
        iconbadge.innerHTML = "Error";
        targetElement.append(iconbadge);
        setTimeout(function () {
            targetElement.children[1].remove();
        }, 3000);
    });
    document.querySelector("#icon-search").addEventListener("keyup", function () {
        var g = document.querySelector("#icon-search").value.toLowerCase();
        var tc = document.querySelectorAll(".i-main .i-block");
        for (var i = 0; i < tc.length; i++) {
            var c = tc[i];
            var t = c.getAttribute('data-filter');
            if (t) {
                var s = t.toLowerCase();
            }
            if (s) {
                var n = s.indexOf(g);
                if (n !== -1) {
                    c.style.display = "inline-flex";
                } else {
                    c.style.display = "none";
                }
            }
        }
    });
});