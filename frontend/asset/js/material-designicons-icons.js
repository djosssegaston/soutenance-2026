var iconlist = ['mdi-access-point', 'mdi-access-point-network', 'mdi-account', 'mdi-account-alert', 'mdi-account-box', 'mdi-account-box-outline', 'mdi-account-card-details', 'mdi-account-check', 'mdi-account-circle', 'mdi-account-convert', 'mdi-account-edit', 'mdi-account-key', 'mdi-account-location', 'mdi-account-minus', 'mdi-account-multiple', 'mdi-account-multiple-minus', 'mdi-account-multiple-outline', 'mdi-account-multiple-plus', 'mdi-account-network', 'mdi-account-off', 'mdi-account-outline', 'mdi-account-plus', 'mdi-account-remove', 'mdi-account-search', 'mdi-account-settings', 'mdi-account-settings-variant', 'mdi-account-star', 'mdi-account-switch', 'mdi-adjust', 'mdi-air-conditioner', 'mdi-airballoon', 'mdi-airplane', 'mdi-airplane-landing', 'mdi-airplane-off', 'mdi-airplane-takeoff', 'mdi-airplay', 'mdi-alarm', 'mdi-alarm-bell', 'mdi-alarm-check', 'mdi-alarm-light', 'mdi-alarm-multiple', 'mdi-alarm-off', 'mdi-alarm-plus', 'mdi-alarm-snooze', 'mdi-album', 'mdi-alert', 'mdi-alert-box', 'mdi-alert-circle', 'mdi-alert-circle-outline', 'mdi-alert-decagram', 'mdi-alert-octagon', 'mdi-alert-octagram', 'mdi-alert-outline', 'mdi-all-inclusive', 'mdi-alpha', 'mdi-alphabetical', 'mdi-altimeter', 'mdi-amazon', 'mdi-amazon-clouddrive', 'mdi-ambulance', 'mdi-amplifier', 'mdi-anchor', 'mdi-android', 'mdi-android-debug-bridge', 'mdi-android-head', 'mdi-android-studio', 'mdi-angular', 'mdi-angularjs', 'mdi-animation', 'mdi-apple', 'mdi-apple-finder', 'mdi-apple-ios', 'mdi-apple-keyboard-caps', 'mdi-apple-keyboard-command', 'mdi-apple-keyboard-control', 'mdi-apple-keyboard-option', 'mdi-apple-keyboard-shift', 'mdi-apple-mobileme', 'mdi-apple-safari', 'mdi-application', 'mdi-approval', 'mdi-apps', 'mdi-archive', 'mdi-arrange-bring-forward', 'mdi-arrange-send-to-back', 'mdi-arrange-bring-to-front', 'mdi-arrange-send-backward', 'mdi-arrow-all', 'mdi-arrow-bottom-left', 'mdi-arrow-bottom-right', 'mdi-arrow-collapse', 'mdi-arrow-collapse-all', 'mdi-arrow-collapse-down', 'mdi-arrow-collapse-left', 'mdi-arrow-collapse-right', 'mdi-arrow-collapse-up', 'mdi-arrow-down', 'mdi-arrow-down-bold', 'mdi-arrow-down-bold-box', 'mdi-arrow-down-bold-box-outline', 'mdi-arrow-down-bold-circle', 'mdi-arrow-down-bold-circle-outline', 'mdi-arrow-down-bold-hexagon-outline', 'mdi-arrow-down-box', 'mdi-arrow-down-drop-circle', 'mdi-arrow-down-drop-circle-outline', 'mdi-arrow-down-thick', 'mdi-arrow-expand', 'mdi-arrow-expand-all', 'mdi-arrow-expand-down', 'mdi-arrow-expand-left', 'mdi-arrow-expand-right', 'mdi-arrow-expand-up', 'mdi-arrow-left', 'mdi-arrow-left-bold', 'mdi-arrow-left-bold-box', 'mdi-arrow-left-bold-box-outline', 'mdi-arrow-left-bold-circle', 'mdi-arrow-left-bold-circle-outline', 'mdi-arrow-left-bold-hexagon-outline', 'mdi-arrow-left-box', 'mdi-arrow-left-drop-circle', 'mdi-arrow-left-drop-circle-outline', 'mdi-arrow-left-thick', 'mdi-arrow-right', 'mdi-arrow-right-bold', 'mdi-arrow-right-bold-box', 'mdi-arrow-right-bold-box-outline', 'mdi-arrow-right-bold-circle', 'mdi-arrow-right-bold-circle-outline', 'mdi-arrow-right-bold-hexagon-outline', 'mdi-arrow-right-box', 'mdi-arrow-right-drop-circle', 'mdi-arrow-right-drop-circle-outline', 'mdi-arrow-right-thick', 'mdi-arrow-top-left', 'mdi-arrow-top-right', 'mdi-arrow-up', 'mdi-arrow-up-bold', 'mdi-arrow-up-bold-box', 'mdi-arrow-up-bold-box-outline', 'mdi-arrow-up-bold-circle', 'mdi-arrow-up-bold-circle-outline', 'mdi-arrow-up-bold-hexagon-outline', 'mdi-arrow-up-box', 'mdi-arrow-up-drop-circle', 'mdi-arrow-up-drop-circle-outline', 'mdi-arrow-up-thick', 'mdi-assistant', 'mdi-asterisk', 'mdi-at', 'mdi-atom', 'mdi-attachment', 'mdi-audiobook', 'mdi-auto-fix', 'mdi-auto-upload', 'mdi-autorenew', 'mdi-av-timer', 'mdi-baby', 'mdi-baby-buggy', 'mdi-backburger', 'mdi-backspace', 'mdi-backup-restore', 'mdi-bandcamp', 'mdi-bank', 'mdi-barcode', 'mdi-barcode-scan', 'mdi-barley', 'mdi-barrel', 'mdi-basecamp', 'mdi-basket', 'mdi-basket-fill', 'mdi-basket-unfill', 'mdi-battery', 'mdi-battery-10', 'mdi-battery-20', 'mdi-battery-30', 'mdi-battery-40', 'mdi-battery-50', 'mdi-battery-60', 'mdi-battery-70', 'mdi-battery-80', 'mdi-battery-90', 'mdi-battery-alert', 'mdi-battery-charging', 'mdi-battery-charging-100', 'mdi-battery-charging-20', 'mdi-battery-charging-30', 'mdi-battery-charging-40', 'mdi-battery-charging-60', 'mdi-battery-charging-80', 'mdi-battery-charging-90', 'mdi-battery-minus', 'mdi-battery-negative', 'mdi-battery-outline', 'mdi-battery-plus', 'mdi-battery-unknown', 'mdi-beach', 'mdi-blank', 'mdi-youtube-play', 'mdi-yin-yang', 'mdi-yelp', 'mdi-yammer', 'mdi-xmpp', 'mdi-xml', 'mdi-wrench', 'mdi-wrap', 'mdi-wunderlist', 'mdi-wrench', 'mdi-vpn', 'mdi-volume-off', 'mdi-volume-mute', 'mdi-vlc', 'mdi-video', 'mdi-usb', 'mdi-upload-network'];
var iconlistzmdi = ['zmdi-group','zmdi-rss','zmdi-shape','zmdi-spinner','zmdi-ungroup','zmdi-500px','zmdi-8tracks','zmdi-amazon','zmdi-blogger','zmdi-delicious','zmdi-disqus','zmdi-flattr','zmdi-flickr','zmdi-github-alt','zmdi-google-old','zmdi-linkedin','zmdi-odnoklassniki','zmdi-outlook','zmdi-paypal-alt','zmdi-pinterest','zmdi-playstation','zmdi-reddit','zmdi-skype','zmdi-slideshare','zmdi-soundcloud','zmdi-tumblr','zmdi-twitch','zmdi-vimeo','zmdi-whatsapp','zmdi-xbox','zmdi-yahoo','zmdi-youtube-play','zmdi-youtube','zmdi-3d-rotation','zmdi-airplane-off','zmdi-airplane','zmdi-album','zmdi-archive','zmdi-assignment-account','zmdi-assignment-alert','zmdi-assignment-check','zmdi-assignment-o','zmdi-assignment-return','zmdi-assignment-returned','zmdi-assignment','zmdi-attachment-alt','zmdi-attachment','zmdi-audio','zmdi-badge-check','zmdi-balance-wallet','zmdi-balance','zmdi-battery-alert','zmdi-battery-flash','zmdi-battery-unknown','zmdi-battery','zmdi-bike','zmdi-block-alt','zmdi-block','zmdi-boat','zmdi-book-image','zmdi-book','zmdi-bookmark-outline','zmdi-bookmark','zmdi-brush','zmdi-bug','zmdi-bus','zmdi-cake','zmdi-car-taxi','zmdi-car-wash','zmdi-car','zmdi-card-giftcard','zmdi-card-membership','zmdi-card-travel','zmdi-card','zmdi-case-check','zmdi-case-download','zmdi-case-play','zmdi-case','zmdi-cast-connected','zmdi-cast','zmdi-chart-donut','zmdi-chart','zmdi-city-alt','zmdi-city','zmdi-close-circle-o','zmdi-close-circle','zmdi-close','zmdi-cocktail','zmdi-code-setting','zmdi-code-smartphone','zmdi-code','zmdi-coffee','zmdi-collection-bookmark','zmdi-collection-case-play','zmdi-collection-folder-image','zmdi-collection-image-o','zmdi-collection-image','zmdi-collection-item-1','zmdi-collection-item-2','zmdi-collection-item-3','zmdi-collection-item-4','zmdi-collection-item-5','zmdi-collection-item-6','zmdi-collection-item-7','zmdi-collection-item-8','zmdi-collection-item-9-plus','zmdi-collection-item-9','zmdi-collection-item','zmdi-collection-music','zmdi-collection-pdf','zmdi-collection-plus','zmdi-collection-speaker','zmdi-collection-text','zmdi-collection-video','zmdi-cutlery','zmdi-delete','zmdi-dialpad','zmdi-dns','zmdi-drink','zmdi-edit','zmdi-email-open','zmdi-email','zmdi-eye-off','zmdi-eye','zmdi-eyedropper','zmdi-favorite-outline','zmdi-favorite','zmdi-filter-list','zmdi-fire','zmdi-flag','zmdi-flare','zmdi-flash-auto','zmdi-flash-off','zmdi-flash','zmdi-flip','zmdi-flower-alt','zmdi-flower','zmdi-font','zmdi-fullscreen-alt','zmdi-fullscreen-exit','zmdi-fullscreen','zmdi-functions','zmdi-gas-station','zmdi-gesture','zmdi-globe-alt','zmdi-globe-lock','zmdi-globe','zmdi-graduation-cap','zmdi-group','zmdi-home','zmdi-hospital-alt','zmdi-hospital','zmdi-hotel','zmdi-hourglass-alt','zmdi-hourglass-outline','zmdi-hourglass','zmdi-http','zmdi-image-alt','zmdi-image-o','zmdi-image','zmdi-inbox','zmdi-invert-colors-off','zmdi-invert-colors','zmdi-key','zmdi-label-alt-outline','zmdi-label-alt','zmdi-label-heart','zmdi-label','zmdi-labels','zmdi-lamp','zmdi-landscape','zmdi-layers-off','zmdi-layers','zmdi-library','zmdi-link','zmdi-lock-open','zmdi-lock-outline','zmdi-lock','zmdi-mail-reply-all','zmdi-mail-reply','zmdi-mail-send','zmdi-mall','zmdi-map','zmdi-menu','zmdi-money-box','zmdi-money-off','zmdi-money','zmdi-more-vert','zmdi-more','zmdi-movie-alt','zmdi-nature-people','zmdi-nature','zmdi-navigation','zmdi-open-in-browser','zmdi-open-in-new','zmdi-palette','zmdi-parking','zmdi-pin-account','zmdi-pin-assistant','zmdi-pin-drop','zmdi-pin-help','zmdi-pin-off','zmdi-pin','zmdi-pizza','zmdi-plaster','zmdi-power-setting','zmdi-power','zmdi-print','zmdi-puzzle-piece','zmdi-quote','zmdi-railway','zmdi-receipt','zmdi-refresh-alt'];
for (var i = 0, l = iconlist.length; i < l; i++) {
    let iconblock = document.createElement('div');
    iconblock.setAttribute('class', 'i-block');
    iconblock.setAttribute('data-clipboard-text', 'mdi ' + iconlist[i]);
    iconblock.setAttribute('data-bs-toggle', 'tooltip');
    iconblock.setAttribute('data-filter', iconlist[i]);
    iconblock.setAttribute('title', 'mdi ' + iconlist[i]);

    let iconmain = document.createElement('i');
    iconmain.setAttribute('class', 'mdi ' + iconlist[i]);
    iconblock.appendChild(iconmain);
    document.querySelector('#icon-wrapper').append(iconblock);
}
for (var i = 0, l = iconlistzmdi.length; i < l; i++) {
    let iconblock = document.createElement('div');
    iconblock.setAttribute('class', 'i-block');
    iconblock.setAttribute('data-clipboard-text', 'zmdi ' + iconlistzmdi[i]);
    iconblock.setAttribute('data-bs-toggle', 'tooltip');
    iconblock.setAttribute('data-filter', iconlistzmdi[i]);
    iconblock.setAttribute('title', 'zmdi ' + iconlistzmdi[i]);

    let iconmain = document.createElement('i');
    iconmain.setAttribute('class', 'zmdi ' + iconlistzmdi[i]);
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