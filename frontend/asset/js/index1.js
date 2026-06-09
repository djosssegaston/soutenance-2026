function apexRender(selector, config) {
    var el = document.querySelector(selector);
    if (el) new ApexCharts(el, config).render();
}
$(function (e) {
    'use strict'
    $('[data-effect="wave"]').addClass("waves-effect waves-light");
$('[data-effect="wave-dark"]').addClass("waves-effect waves-float");
    function statsChart() {
      var e = {
          chart: { type: "area", height: 150, width: "100%", sparkline: { enabled: !0 }, parentHeightOffset: 0, toolbar: { show: !1 } },
          colors: ["#4ecc48"],
          markers: { size: 0 },
          tooltip: {
              theme: "dark",
              fixed: { enabled: !1 },
              x: { show: !1 },
              y: {
                  title: {
                      formatter: function (e) {
                          return "";
                      },
                  },
              },
              marker: { show: !1 },
          },
          fill: { type: "gradient", gradient: { type: "vertical", shadeIntensity: 1, inverseColors: !1, opacityFrom: 0.45, opacityTo: 0.05, stops: [45, 100] } },
          stroke: { width: 2, curve: "smooth" },
          series: [{ data: [25, 66, 41, 85, 63, 25, 44, 12, 36, 9, 54] }],
      };
      apexRender("#saleschart", e);
  }

  function statsChart2() {
    var e = {
        chart: { type: "area", height: 150, width: "100%", sparkline: { enabled: !0 }, parentHeightOffset: 0, toolbar: { show: !1 } },
        colors: ["#EC6900"],
        markers: { size: 0 },
        tooltip: {
            theme: "dark",
            fixed: { enabled: !1 },
            x: { show: !1 },
            y: {
                title: {
                    formatter: function (e) {
                        return "";
                    },
                },
            },
            marker: { show: !1 },
        },
        fill: { type: "gradient", gradient: { type: "vertical", shadeIntensity: 1, inverseColors: !1, opacityFrom: 0.45, opacityTo: 0.05, stops: [45, 100] } },
        stroke: { width: 2, curve: "smooth" },
        series: [{ data: [25, 66, 41, 85, 63, 25, 44, 12, 36, 9, 54] }],
    };
    apexRender("#TotalOrders", e);
}
function statsChart3() {
  var e = {
      chart: { type: "area", height: 150, width: "100%", sparkline: { enabled: !0 }, parentHeightOffset: 0, toolbar: { show: !1 } },
      colors: ["#28a745"],
      markers: { size: 0 },
      tooltip: {
          theme: "dark",
          fixed: { enabled: !1 },
          x: { show: !1 },
          y: {
              title: {
                  formatter: function (e) {
                      return "";
                  },
              },
          },
          marker: { show: !1 },
      },
      fill: { type: "gradient", gradient: { type: "vertical", shadeIntensity: 1, inverseColors: !1, opacityFrom: 0.45, opacityTo: 0.05, stops: [45, 100] } },
      stroke: { width: 2, curve: "smooth" },
      series: [{ data: [25, 66, 41, 85, 63, 25, 44, 12, 36, 9, 54] }],
  };
  apexRender("#NewUsers", e);
}
function statsChart4() {
  var e = {
      chart: { type: "area", height: 150, width: "100%", sparkline: { enabled: !0 }, parentHeightOffset: 0, toolbar: { show: !1 } },
      colors: ["#1170e4"],
      markers: { size: 0 },
      tooltip: {
          theme: "dark",
          fixed: { enabled: !1 },
          x: { show: !1 },
          y: {
              title: {
                  formatter: function (e) {
                      return "";
                  },
              },
          },
          marker: { show: !1 },
      },
      fill: { type: "gradient", gradient: { type: "vertical", shadeIntensity: 1, inverseColors: !1, opacityFrom: 0.45, opacityTo: 0.05, stops: [45, 100] } },
      stroke: { width: 2, curve: "smooth" },
      series: [{ data: [25, 66, 41, 85, 63, 25, 44, 12, 36, 9, 54] }],
  };
  apexRender("#NewVisitors", e);
}
function statisticChart() {
    var e = {
        chart: { height: 280, type: "bar", redrawOnParentResize: !0, toolbar: { show: !1 } },
        plotOptions: { bar: { horizontal: !1, columnWidth: "30%", endingShape: "rounded" } },
        tooltip: {
            y: {
                formatter: function (e) {
                    return "$" + e + " K";
                },
            },
        },
        dataLabels: { enabled: !1 },
        stroke: { show: !1, width: 1, colors: ["transparent"] },
        grid: { show: !0 },
        series: [
            { name: "Current Year", data: [35, 44, 55, 57, 56, 61, 75, 108, 60, 35, 98, 88] },
            { name: "Previous Year", data: [52, 76, 85, 101, 98, 87, 120, 54, 40, 70, 110, 65] },
        ],
        xaxis: { categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "July", "Aug", "Sep", "Oct", "Nov", "Dec"] },
        legend: { fontFamily: "Nunito Sans, sans-serif", itemMargin: { vertical: 10, horizontal: 10 }, labels: { colors: ["#505d69", "#505d69"] } },
        colors: ["var(--primary-bg-color)", "#007F6E"],
        fill: { opacity: 1 },
    };
    apexRender("#stats-chart", e);
}
function quarterlyChart() {
    var e = {
        chart: { height: 390, type: "bar", stacked: !0, toolbar: { show: !1 } },
        dataLabels: { enabled: !1 },
        stroke: { show: !1, width: 1, colors: ["transparent"] },
        grid: { show: !0 },
        legend: { fontFamily: "Nunito Sans, sans-serif", itemMargin: { vertical: 10, horizontal: 10 } },
        series: [
            { name: "Quarter 1", data: [30, 50, 60, 20] },
            { name: "Quarter 2", data: [40, 30, 40, 40] },
            { name: "Quarter 3", data: [50, 80, 60, 50] },
            { name: "Quarter 4", data: [60, 90, 70, 60] },
        ],
        xaxis: { categories: ["Q1", "Q2", "Q3", "Q4"] },
        colors: ["var(--primary-bg-color)", "#1170e4", "#f7b731","#1caf9f"],
        fill: { opacity: 1 },
    };
    apexRender("#quartly-sale", e);
}
function todaySaleChart() {
    var e = {
        type: "doughnut",
        data: {
            labels: ["Direct sales", "Referral sales", "Afilliate sales", "Indirect sales"],
            datasets: [{ label: "Doughnut chart", data: [50, 40, 30, 10], backgroundColor: ["#4ecc48", "#f7b731", "#1170e4", "#EC6900"], borderWidth: 0 }],
        },
        options: { responsive: !0, maintainAspectRatio: !1, legend: { position: "top", display: !1 }, cutoutPercentage: 70 },
    };
    getCanvas("total-sale", e);
}
function statsPerWeekChart(e, t, r) {
    var a = [],
        a = "monthly" === e ? [{ name: "Monthly", data: [210, 200, 100, 50, 40, 150, 700, 650, 400, 300, 250, 200] }] : [{ name: "Weekly", data: [20, 15, 60, 21, 40, 23, 35, 50, 80, 70, 55, 70] }];
    if (!t)
        return (
            (e = {
                chart: { height: 270, type: "area", toolbar: { show: !1 } },
                dataLabels: { enabled: !1 },
                stroke: { width: 2, curve: "smooth" },
                series: a,
                xaxis: { categories: ["1st", "2nd", "3rd", "4th", "5th", "6th", "7th", "8th", "9th", "10th", "11th", "12th"], tooltip: { enabled: !1, offsetX: 0 } },
                colors: ["#28a745"],
                fill: { type: "gradient", gradient: { type: "vertical", shadeIntensity: 1, inverseColors: !1, opacityFrom: 0.45, opacityTo: 0.05, stops: [45, 100] } },
                tooltip: { theme: "dark", x: { show: !1 }, marker: { show: !1 } },
            }),
            (function(){ var el = document.querySelector("#sales-order"); if(el) { r = new ApexCharts(el, e); r.render(); } })(),
            r
        );
    r.updateSeries(a);
}
var _chartInstances = {};
function getCanvas(id) {
    var ctx = document.getElementById(id);
    if (!ctx) return null;
    if (_chartInstances[id]) { _chartInstances[id].destroy(); }
    _chartInstances[id] = new Chart(ctx, arguments[1] ? arguments[1] : {});
    return _chartInstances[id];
}
function todayRevenue() {
    var e = {
        type: "doughnut",
        data: { datasets: [{ data: [600, 1400], backgroundColor: ["#fff", "rgba(255,255,255,0.3)"], hoverBackgroundColor: ["#fff", "rgba(255,255,255,0.3)"], borderWidth: 0 }] },
        options: { cutoutPercentage: 94, responsive: !0, maintainAspectRatio: !1, scales: { x: { display: !1 }, y: { display: !1 } }, legend: { display: !1 }, tooltips: { enabled: !1 } },
    };
    getCanvas("today-revenue", e);
}
  $(
    (function () {
        var e;
        statsChart(),
            statsChart2(),
            statsChart3(),
            statsChart4(),
            statisticChart(),
            quarterlyChart(),
            todaySaleChart(),
            (e = statsPerWeekChart("weekly", !1, void 0)),
            todayRevenue(),
            $(".earningTabs button").on("click", function () {
                $(this).addClass("btn-primary").removeClass("btn-outline-primary"), $(this).siblings().removeClass("btn-primary").addClass("btn-outline-primary"), statsPerWeekChart($(this).attr("data-type"), !0, e);
            });
    })(jQuery)
);



});




 







  
  
