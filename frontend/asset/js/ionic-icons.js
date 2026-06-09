var iconlist = ['ion-ionic','ion-arrow-up-a','ion-arrow-right-a','ion-arrow-down-a','ion-arrow-left-a','ion-arrow-up-b','ion-arrow-right-b','ion-arrow-down-b','ion-arrow-left-b','ion-arrow-up-c','ion-arrow-right-c','ion-arrow-down-c','ion-arrow-left-c','ion-arrow-return-right','ion-arrow-return-left','ion-arrow-swap','ion-arrow-shrink','ion-arrow-expand','ion-arrow-move','ion-arrow-resize','ion-chevron-up','ion-chevron-right','ion-chevron-down','ion-chevron-left','ion-navicon-round','ion-navicon','ion-drag','ion-log-in','ion-log-out','ion-checkmark-round','ion-checkmark','ion-checkmark-circled','ion-close-round','ion-close','ion-close-circled','ion-plus-round','ion-plus','ion-plus-circled','ion-minus-round','ion-minus','ion-minus-circled','ion-information','ion-information-circled','ion-help','ion-help-circled','ion-help-buoy','ion-asterisk','ion-alert','ion-alert-circled','ion-refresh','ion-loop','ion-shuffle','ion-home','ion-search','ion-flag','ion-star','ion-heart','ion-heart-broken','ion-gear-a','ion-gear-b','ion-toggle-filled','ion-toggle','ion-settings','ion-wrench','ion-folder','ion-hammer','ion-edit','ion-trash-a','ion-trash-b','ion-document','ion-document-text','ion-clipboard','ion-scissors','ion-funnel','ion-bookmark','ion-email','ion-folder','ion-filing','ion-archive','ion-reply','ion-reply-all','ion-forward','ion-share','ion-paper-airplane','ion-link','ion-paperclip','ion-compose','ion-briefcase','ion-medkit','ion-at','ion-pound','ion-quote','ion-cloud','ion-upload','ion-more','ion-grid','ion-calendar','ion-clock','ion-compass','ion-pinpoint','ion-pin','ion-navigate','ion-location','ion-map','ion-model-s','ion-locked','ion-unlocked','ion-key','ion-arrow-graph-up-right','ion-arrow-graph-down-right','ion-arrow-graph-down-left','ion-stats-bars','ion-connection-bars','ion-pie-graph','ion-chatbubble','ion-chatbubble-working','ion-chatbubbles','ion-chatbox','ion-chatbox-working','ion-chatboxes','ion-person','ion-person-add','ion-person-stalker','ion-woman','ion-man','ion-female','ion-male','ion-fork','ion-knife','ion-spoon','ion-beer','ion-wineglass','ion-coffee','ion-icecream','ion-pizza','ion-power','ion-mouse','ion-battery-full','ion-battery-half','ion-battery-low','ion-battery-empty','ion-battery-charging','ion-bluetooth','ion-calculator','ion-camera','ion-eye','ion-eye-disabled','ion-flash','ion-flash-off','ion-qr-scanner','ion-image','ion-images','ion-contrast','ion-wand','ion-aperture','ion-monitor','ion-laptop','ion-ipad','ion-iphone','ion-ipod','ion-printer','ion-usb','ion-outlet','ion-bug','ion-code','ion-code-working','ion-code-download','ion-fork-repo','ion-network','ion-pull-request','ion-merge','ion-game-controller-a','ion-game-controller-b','ion-xbox','ion-playstation','ion-steam','ion-closed-captioning','ion-videocamera','ion-film-marker','ion-disc','ion-headphone','ion-music-note','ion-radio-waves','ion-speakerphone','ion-mic-a','ion-mic-b','ion-mic-c','ion-volume-high','ion-volume-medium','ion-volume-low','ion-volume-mute','ion-levels','ion-play','ion-pause','ion-stop','ion-record','ion-skip-forward','ion-skip-backward','ion-eject','ion-bag','ion-card','ion-cash','ion-pricetag','ion-pricetags','ion-thumbsup','ion-thumbsdown','ion-happy','ion-sad','ion-trophy','ion-podium','ion-ribbon-a','ion-ribbon-b','ion-university','ion-magnet','ion-beaker','ion-flask','ion-egg','ion-earth','ion-planet','ion-lightbulb','ion-cube','ion-leaf','ion-waterdrop','ion-flame','ion-fireball','ion-bonfire','ion-umbrella','ion-nuclear','ion-no-smoking','ion-thermometer','ion-speedometer','ion-plane','ion-jet','ion-load-a','ion-load-b','ion-load-c','ion-load-d','ion-ios7-ionic-outline','ion-ios7-arrow-back','ion-ios7-arrow-forward','ion-ios7-arrow-up','ion-ios7-arrow-right','ion-ios7-arrow-down','ion-ios7-arrow-left','ion-ios7-arrow-thin-up','ion-ios7-arrow-thin-right','ion-ios7-arrow-thin-down','ion-ios7-arrow-thin-left','ion-ios7-circle-filled','ion-ios7-circle-outline','ion-ios7-checkmark-empty','ion-ios7-checkmark-outline','ion-ios7-checkmark','ion-ios7-plus-empty','ion-ios7-plus-outline','ion-ios7-plus','ion-ios7-close-empty','ion-ios7-close-outline','ion-ios7-close','ion-ios7-minus-empty','ion-ios7-minus-outline','ion-ios7-minus','ion-ios7-information-empty','ion-ios7-information-outline','ion-ios7-information','ion-ios7-help-empty','ion-ios7-help-outline','ion-ios7-help','ion-ios7-search','ion-ios7-search-strong','ion-ios7-star','ion-ios7-star-half','ion-ios7-star-outline','ion-ios7-heart','ion-ios7-heart-outline','ion-ios7-more','ion-ios7-more-outline','ion-ios7-home','ion-ios7-home-outline','ion-ios7-cloud','ion-ios7-cloud-outline','ion-ios7-cloud-upload','ion-ios7-cloud-upload-outline','ion-ios7-cloud-download','ion-ios7-cloud-download-outline','ion-ios7-upload','ion-ios7-upload-outline','ion-ios7-download','ion-ios7-refresh','ion-ios7-refresh-outline','ion-ios7-refresh-empty','ion-ios7-reload','ion-ios7-loop-strong','ion-ios7-loop','ion-ios7-bookmarks','ion-ios7-bookmarks-outline'];
for (var i = 0, l = iconlist.length; i < l; i++) {
    let iconblock = document.createElement('div');
    iconblock.setAttribute('class', 'i-block');
    iconblock.setAttribute('data-clipboard-text', ' ' + iconlist[i]);
    iconblock.setAttribute('data-bs-toggle', 'tooltip');
    iconblock.setAttribute('data-filter', iconlist[i]);
    iconblock.setAttribute('title', ' ' + iconlist[i]);

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