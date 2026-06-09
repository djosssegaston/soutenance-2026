var iconlist = ['payment-2checkout','payment-alipay','payment-amazon','payment-americanexpress','payment-applepay','payment-bancontact','payment-bitcoin','payment-bitpay','payment-cirrus','payment-clickandbuy','payment-coinkite','payment-dinersclub','payment-directdebit','payment-discover','payment-dwolla','payment-ebay','payment-eway','payment-giropay','payment-googlewallet','payment-ingenico','payment-jcb','payment-klarna','payment-laser','payment-maestro','payment-mastercard','payment-monero','payment-neteller','payment-ogone','payment-okpay','payment-paybox','payment-paymill','payment-payone','payment-payoneer','payment-paypal','payment-paysafecard','payment-payu','payment-payza','payment-ripple','payment-sage','payment-sepa','payment-shopify','payment-skrill','payment-solo','payment-square','payment-stripe','payment-switch','payment-ukash','payment-unionpay','payment-verifone','payment-verisign','payment-visa','payment-webmoney','payment-westernunion','payment-worldpay','payment-2checkout-dark','payment-alipay-dark','payment-amazon-dark','payment-americanexpress-dark','payment-applepay-dark','payment-bancontact-dark','payment-bitcoin-dark','payment-bitpay-dark','payment-cirrus-dark','payment-clickandbuy-dark','payment-coinkite-dark','payment-dinersclub-dark','payment-directdebit-dark','payment-discover-dark','payment-dwolla-dark','payment-ebay-dark','payment-eway-dark','payment-giropay-dark','payment-googlewallet-dark','payment-ingenico-dark','payment-jcb-dark','payment-klarna-dark','payment-laser-dark','payment-maestro-dark','payment-mastercard-dark','payment-monero-dark','payment-neteller-dark','payment-ogone-dark','payment-okpay-dark','payment-paybox-dark','payment-paymill-dark','payment-payone-dark','payment-payoneer-dark','payment-paypal-dark','payment-paysafecard-dark','payment-payu-dark','payment-payza-dark','payment-ripple-dark','payment-sage-dark','payment-sepa-dark','payment-shopify-dark','payment-skrill-dark','payment-solo-dark','payment-square-dark','payment-stripe-dark','payment-switch-dark','payment-ukash-dark','payment-unionpay-dark','payment-verifone-dark','payment-verisign-dark','payment-visa-dark','payment-webmoney-dark','payment-westernunion-dark','payment-worldpay-dark'];
for (var i = 0, l = iconlist.length; i < l; i++) {
    let iconblock = document.createElement('div');
    iconblock.setAttribute('class', 'i-block');
    iconblock.setAttribute('data-clipboard-text', 'payment ' + iconlist[i]);
    iconblock.setAttribute('data-bs-toggle', 'tooltip');
    iconblock.setAttribute('data-filter', iconlist[i]);
    iconblock.setAttribute('title', 'payment ' + iconlist[i]);

    let iconmain = document.createElement('i');
    iconmain.setAttribute('class', 'payment ' + iconlist[i]);
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