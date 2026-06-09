var iconlist = ['flag-ad','flag-ae','flag-af','flag-ag','flag-ai','flag-al','flag-am','flag-ao','flag-aq','flag-ar','flag-as','flag-at','flag-au','flag-aw','flag-ax','flag-az','flag-ba','flag-bb','flag-bd','flag-be','flag-bf','flag-bg','flag-bh','flag-bi','flag-bj','flag-bl','flag-bm','flag-bn','flag-bo','flag-bq','flag-br','flag-bs','flag-bt','flag-bv','flag-bw','flag-by','flag-bz','flag-ca','flag-cc','flag-cd','flag-cf','flag-cg','flag-ch','flag-ci','flag-ck','flag-cl','flag-cm','flag-cn','flag-co','flag-cr','flag-cu','flag-cv','flag-cw','flag-cx','flag-cy','flag-cz','flag-de','flag-dj','flag-dk','flag-dm','flag-do','flag-dz','flag-ec','flag-ee','flag-eg','flag-eh','flag-er','flag-es','flag-et','flag-eu','flag-fi','flag-fj','flag-fk','flag-fm','flag-fo','flag-fr','flag-ga','flag-gb','flag-gb-eng','flag-gb-nir','flag-gb-sct','flag-gb-wls','flag-gd','flag-ge','flag-gf','flag-gg','flag-gh','flag-gi','flag-gl','flag-gm','flag-gn','flag-gp','flag-gq','flag-gr','flag-gs','flag-gt','flag-gu','flag-gw','flag-gy','flag-hk','flag-hm','flag-hn','flag-hr','flag-ht','flag-hu','flag-id','flag-ie','flag-il','flag-im','flag-in','flag-io','flag-iq','flag-ir','flag-is','flag-it','flag-je','flag-jm','flag-jo','flag-jp','flag-ke','flag-kg','flag-kh','flag-ki','flag-km','flag-kn','flag-kp','flag-kr','flag-kw','flag-ky','flag-kz','flag-la','flag-lb','flag-lc','flag-li','flag-lk','flag-lr','flag-ls','flag-lt','flag-lu','flag-lv','flag-ly','flag-ma','flag-mc','flag-md','flag-me','flag-mf','flag-mg','flag-mh','flag-mk','flag-ml','flag-mm','flag-mn','flag-mo','flag-mp','flag-mq','flag-mr','flag-ms','flag-mt','flag-mu','flag-mv','flag-mw','flag-mx','flag-my','flag-mz','flag-na','flag-nc','flag-ne','flag-nf','flag-ng','flag-ni','flag-nl','flag-no','flag-np','flag-nr','flag-nu','flag-nz','flag-om','flag-pa','flag-pe','flag-pf','flag-pg','flag-ph','flag-pk','flag-pl','flag-pm','flag-pn','flag-pr','flag-ps','flag-pt','flag-pw','flag-py','flag-qa','flag-re','flag-ro','flag-rs','flag-ru','flag-rw','flag-sa','flag-sb','flag-sc','flag-sd','flag-se','flag-sg','flag-sh','flag-si','flag-sj','flag-sk','flag-sl','flag-sm','flag-sn','flag-so','flag-sr','flag-ss','flag-st','flag-sv','flag-sx','flag-sy','flag-sz','flag-tc','flag-td','flag-tf','flag-tg','flag-th','flag-tj','flag-tk','flag-tl','flag-tm','flag-tn','flag-to','flag-tr','flag-tt','flag-tv','flag-tw','flag-tz','flag-ua','flag-ug','flag-um','flag-un','flag-us','flag-uy','flag-uz','flag-va','flag-vc','flag-ve','flag-vg','flag-vi','flag-vn','flag-vu','flag-wf','flag-ws','flag-ye','flag-yt','flag-za','flag-zm','flag-zw'];
for (var i = 0, l = iconlist.length; i < l; i++) {
    let iconblock = document.createElement('div');
    iconblock.setAttribute('class', 'i-block');
    iconblock.setAttribute('data-clipboard-text', 'flag ' + iconlist[i]);
    iconblock.setAttribute('data-bs-toggle', 'tooltip');
    iconblock.setAttribute('data-filter', iconlist[i]);
    iconblock.setAttribute('title', 'flag ' + iconlist[i]);

    let iconmain = document.createElement('i');
    iconmain.setAttribute('class', 'flag ' + iconlist[i]);
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