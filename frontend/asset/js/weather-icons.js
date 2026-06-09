var iconlist = ['wi-day-cloudy-high','wi-moonrise','wi-na','wi-volcano','wi-day-light-wind','wi-moonset','wi-flood','wi-train','wi-day-sleet','wi-night-sleet','wi-sandstorm','wi-small-craft-advisory','wi-day-haze','wi-night-alt-sleet','wi-tsunami','wi-gale-warning','wi-night-cloudy-high','wi-raindrop','wi-earthquake','wi-storm-warning','wi-night-alt-partly-cloudy','wi-barometer','wi-fire','wi-hurricane-warning','wi-sleet','wi-humidity','wi-day-sunny','wi-day-cloudy','wi-day-cloudy-gusts','wi-day-cloudy-windy','wi-day-fog','wi-day-hail','wi-day-haze','wi-day-lightning','wi-day-rain','wi-day-rain-mix','wi-day-rain-wind','wi-day-showers','wi-day-sleet','wi-day-sleet-storm','wi-day-snow','wi-day-snow-thunderstorm','wi-day-snow-wind','wi-day-sprinkle','wi-day-storm-showers','wi-day-sunny-overcast','wi-day-thunderstorm','wi-day-windy','wi-solar-eclipse','wi-hot','wi-day-cloudy-high','wi-day-light-wind','wi-night-clear','wi-night-alt-cloudy','wi-night-alt-cloudy-gusts','wi-night-alt-cloudy-windy','wi-night-alt-hail','wi-night-alt-lightning','wi-night-alt-rain','wi-night-alt-rain-mix','wi-night-alt-rain-wind','wi-night-alt-showers','wi-night-alt-sleet','wi-night-alt-sleet-storm','wi-night-alt-snow','wi-night-alt-snow-thunderstorm','wi-night-alt-snow-wind','wi-night-alt-sprinkle','wi-night-alt-storm-showers','wi-night-alt-thunderstorm','wi-night-cloudy','wi-night-cloudy-gusts','wi-night-cloudy-windy','wi-night-fog','wi-night-hail','wi-night-lightning','wi-night-partly-cloudy','wi-night-rain','wi-night-rain-mix','wi-night-rain-wind','wi-night-showers','wi-night-sleet','wi-night-sleet-storm','wi-night-snow','wi-night-snow-thunderstorm','wi-night-snow-wind','wi-night-sprinkle','wi-night-storm-showers','wi-night-thunderstorm','wi-lunar-eclipse','wi-stars','wi-storm-showers','wi-night-alt-cloudy-high','wi-night-cloudy-high','wi-night-alt-partly-cloudy','wi-cloud','wi-cloudy','wi-cloudy-gusts','wi-cloudy-windy','wi-fog','wi-hail','wi-rain','wi-rain-mix','wi-rain-wind','wi-showers','wi-sleet','wi-snow','wi-sprinkle','wi-storm-showers','wi-thunderstorm','wi-snow-wind','wi-snow','wi-smog','wi-smoke','wi-lightning','wi-raindrops','wi-raindrop','wi-snowflake-cold','wi-windy','wi-strong-wind','wi-sandstorm','wi-earthquake','wi-fire','wi-flood','wi-meteor','wi-tsunami','wi-volcano','wi-hurricane','wi-tornado','wi-small-craft-advisory','wi-gale-warning','wi-storm-warning','wi-hurricane-warning','wi-wind-direction','wi-alien','wi-celsius','wi-fahrenheit','wi-degrees','wi-thermometer','wi-thermometer-exterior','wi-thermometer-internal','wi-cloud-down','wi-cloud-up','wi-cloud-refresh','wi-horizon','wi-horizon-alt','wi-sunrise','wi-sunset','wi-moonrise','wi-moonset','wi-refresh','wi-refresh-alt','wi-umbrella','wi-barometer','wi-humidity','wi-na','wi-train',];
for (var i = 0, l = iconlist.length; i < l; i++) {
    let iconblock = document.createElement('div');
    iconblock.setAttribute('class', 'i-block');
    iconblock.setAttribute('data-clipboard-text', 'wi ' + iconlist[i]);
    iconblock.setAttribute('data-bs-toggle', 'tooltip');
    iconblock.setAttribute('data-filter', iconlist[i]);
    iconblock.setAttribute('title', 'wi ' + iconlist[i]);

    let iconmain = document.createElement('i');
    iconmain.setAttribute('class', 'wi ' + iconlist[i]);
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