var iconlist = ['typcn-chart-pie-outline','typcn-chart-pie','typcn-chevron-left-outline','typcn-chevron-left','typcn-chevron-right-outline','typcn-chevron-right','typcn-clipboard','typcn-cloud-storage','typcn-cloud-storage-outline','typcn-code-outline','typcn-code','typcn-coffee','typcn-cog-outline','typcn-cog','typcn-compass','typcn-contacts','typcn-credit-card','typcn-css3','typcn-database','typcn-delete-outline','typcn-delete','typcn-device-desktop','typcn-device-laptop','typcn-device-phone','typcn-device-tablet','typcn-directions','typcn-divide-outline','typcn-divide','typcn-document-add','typcn-document-delete','typcn-document-text','typcn-document','typcn-download-outline','typcn-download','typcn-dropbox','typcn-edit','typcn-eject-outline','typcn-eject','typcn-equals-outline','typcn-equals','typcn-export-outline','typcn-export','typcn-eye-outline','typcn-eye','typcn-feather','typcn-film','typcn-filter','typcn-flag-outline','typcn-flag','typcn-flash-outline','typcn-flash','typcn-flow-children','typcn-flow-merge','typcn-flow-parallel','typcn-flow-switch','typcn-folder-add','typcn-folder-delete','typcn-folder-open','typcn-folder','typcn-gift','typcn-globe-outline','typcn-globe','typcn-group-outline','typcn-group','typcn-headphones','typcn-heart-full-outline','typcn-heart-half-outline','typcn-heart-outline','typcn-heart','typcn-home-outline','typcn-home','typcn-html5','typcn-image-outline','typcn-image','typcn-infinity-outline','typcn-info-large-outline','typcn-info-large','typcn-info-outline','typcn-info','typcn-input-checked-outline','typcn-input-checked','typcn-key-outline','typcn-key','typcn-keyboard','typcn-leaf','typcn-lightbulb','typcn-link-outline','typcn-link','typcn-location-arrow-outline','typcn-location-arrow','typcn-location-outline','typcn-location','typcn-lock-closed-outline','typcn-lock-closed','typcn-lock-open-outline','typcn-lock-open','typcn-mail','typcn-map','typcn-media-eject-outline','typcn-media-eject','typcn-media-fast-forward-outline','typcn-media-fast-forward','typcn-media-pause-outline','typcn-media-pause','typcn-media-play-outline','typcn-media-play-reverse-outline','typcn-media-play-reverse','typcn-media-play','typcn-media-record-outline','typcn-media-record','typcn-media-rewind-outline','typcn-media-rewind','typcn-media-stop-outline','typcn-media-stop','typcn-message-typing','typcn-message','typcn-messages','typcn-microphone-outline','typcn-microphone','typcn-minus-outline','typcn-minus','typcn-mortar-board','typcn-news','typcn-notes-outline','typcn-notes','typcn-pen','typcn-pencil','typcn-phone-outline','typcn-phone','typcn-pi-outline','typcn-pi','typcn-pin-outline','typcn-pin','typcn-pipette','typcn-plane-outline','typcn-plane','typcn-plug','typcn-plus-outline','typcn-plus','typcn-point-of-interest-outline','typcn-point-of-interest','typcn-power-outline','typcn-power','typcn-printer','typcn-puzzle-outline','typcn-puzzle','typcn-radar-outline','typcn-radar','typcn-refresh-outline','typcn-refresh','typcn-rss-outline','typcn-rss','typcn-scissors-outline','typcn-scissors','typcn-shopping-bag','typcn-shopping-cart','typcn-social-at-circular','typcn-social-dribbble-circular','typcn-social-dribbble','typcn-social-facebook-circular','typcn-social-facebook','typcn-social-flickr-circular','typcn-social-flickr','typcn-social-github-circular','typcn-social-github','typcn-social-google-plus-circular','typcn-social-google-plus','typcn-social-instagram-circular','typcn-social-instagram','typcn-social-last-fm-circular','typcn-social-last-fm','typcn-social-linkedin-circular','typcn-social-linkedin','typcn-social-pinterest-circular','typcn-social-pinterest','typcn-social-skype-outline','typcn-social-skype','typcn-social-tumbler-circular','typcn-social-tumbler','typcn-social-twitter-circular','typcn-social-twitter','typcn-social-vimeo-circular','typcn-social-vimeo','typcn-social-youtube-circular','typcn-social-youtube','typcn-sort-alphabetically-outline','typcn-sort-alphabetically','typcn-sort-numerically-outline','typcn-tabs-outline'];
for (var i = 0, l = iconlist.length; i < l; i++) {
    let iconblock = document.createElement('div');
    iconblock.setAttribute('class', 'i-block');
    iconblock.setAttribute('data-clipboard-text', 'typcn ' + iconlist[i]);
    iconblock.setAttribute('data-bs-toggle', 'tooltip');
    iconblock.setAttribute('data-filter', iconlist[i]);
    iconblock.setAttribute('title', 'typcn ' + iconlist[i]);

    let iconmain = document.createElement('i');
    iconmain.setAttribute('class', 'typcn ' + iconlist[i]);
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