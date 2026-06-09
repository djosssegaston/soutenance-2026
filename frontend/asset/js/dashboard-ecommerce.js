function statsPerWeekChart() {
  var monthlyData = [{ 
      name: "Monthly", 
      data: [5000, 3500, 7000, 10000, 7500, 8000, 12000, 15000, 14000, 17500, 22000, 18000] 
  }];

  var options = {
      chart: { 
          height: 400, 
          type: "bar", 
          toolbar: { show: false } 
      },
      dataLabels: { enabled: false },
      stroke: { width: 2, curve: "smooth" },
      series: monthlyData,
      xaxis: {
          categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "July", "Aug", "Sep", "Oct", "Nov", "Dec"],
          tooltip: { enabled: false }
      },
      colors: ['var(--primary-bg-color)'],
      fill: { 
          type: "gradient", 
          gradient: { 
              type: "vertical", 
              shadeIntensity: 1, 
              inverseColors: false, 
              opacityFrom: 0.45, 
              opacityTo: 0.05, 
              stops: [100, 100] 
          }
      },
      tooltip: { 
          theme: "dark", 
          x: { show: false }, 
          marker: { show: false } 
      }
  };
  
  // Ensure the chart renders in the correct element
  var chart = new ApexCharts(document.querySelector("#sales-order"), options);
  chart.render();
}

// Call the function to render the chart
statsPerWeekChart();


function revenueCat() {
  var ctx = document.getElementById("cat-sale").getContext('2d');
  var e = {
      type: "doughnut",
      data: {
          labels: ["Men", "Women", "Accessories", "Children", "Apparel"],
          datasets: [{
              label: "Doughnut chart",
              data: [20000, 40000, 55000, 10000, 30000],
              backgroundColor: ['#75ba55', '#f7b731', '#1170e4', '#EC6900', '#1caf9f'],
              borderWidth: 0
          }]
      },
      options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
              legend: {
                  position: "bottom"
              }
          },
          cutout: '60%' // Adjusted for newer Chart.js versions
      }
  };
  new Chart(ctx, e);
}

// Call the function to render the chart
revenueCat();


function customerAcquisitionChart(e, t, a) {
  var o = [],
      r = { xaxis: { categories: ["2012", "2013", "2014", "2015", "2016", "2017", "2018", "2019", "2020"] } };
  if (
      ("yearly" === e
          ? ((o = [
                { name: "Previous Year", data: [5e3, 25e3, 5e4, 15e3, 2e4, 3e4, 5e4, 35e3] },
                { name: "Current Year", data: [3e4, 45e3, 9e3, 3e4, 45e3, 5e4, 0, 0] },
            ]),
            (r.xaxis = { categories: ["2012", "2013", "2014", "2015", "2016", "2017", "2018", "2019", "2020"] }))
          : (o =
                "monthly" === e
                    ? ((r.xaxis = { categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "July", "Aug", "Sep", "Oct", "Nov", "Dec"] }),
                      [
                          { name: "Previous Month", data: [3e3, 2500, 6e3, 8e3, 9500, 1e4, 5e3, 4e3, 1e4, 16500, 16e3, 14e3] },
                          { name: "Current Month", data: [6e3, 4e3, 6500, 9e3, 8500, 1e4, 13e3, 12e3, 16e3, 18500, 0, 0] },
                      ])
                    : ((r.xaxis = { categories: ["Mon", "Tue", "Wed", "Thr", "Fri", "Sat", "Sun"] }),
                      [
                          { name: "Previous Week", data: [500, 200, 1e3, 700, 650, 1500, 1800] },
                          { name: "Current Week", data: [1e3, 500, 1500, 2e3, 700, 750, 0] },
                      ])),
      !t)
  )
      return (
          (e = {
              chart: { height: 335, type: "area", toolbar: { show: !1 } },
              dataLabels: { enabled: !1 },
              stroke: { width: 2, curve: "smooth" },
              series: o,
              xaxis: { categories: ["Mon", "Tue", "Wed", "Thr", "Fri", "Sat", "Sun"], tooltip: { enabled: !1, offsetX: 0 } },
              colors: ['#75ba55', '#f7b731'],
              fill: { type: "gradient", gradient: { type: "vertical", shadeIntensity: 1, inverseColors: !1, opacityFrom: 0.45, opacityTo: 0.05, stops: [45, 100] } },
              legend: { fontFamily: "Nunito Sans, sans-serif", itemMargin: { vertical: 10, horizontal: 10 } },
              tooltip: { theme: "dark", x: { show: !1 }, marker: { show: !1 } },
          }),
          (a = new ApexCharts(document.querySelector("#customer-aqu"), e)).render(),
          a
      );
  a.updateOptions(r), a.updateSeries(o);
}

$(function () {
  var a;
  
  // Initialize chart with default view
  a = customerAcquisitionChart("weekly", false, undefined);
  
  // Handle dropdown item click to update the chart based on selected timeframe
  $(".stats-dropdown .dropdown-item").on("click", function () {
      var e = $(this).attr("data-type"), // Get the data-type value (weekly, monthly, yearly)
          t = $(this).text(); // Get the text to display as selected
          
      $(this).parents(".dropdown").find(".update-text").text(t); // Update dropdown text to reflect the selected option
      
      // Update the chart with the new data-type (weekly, monthly, yearly)
      customerAcquisitionChart(e, true, a);
  });
});